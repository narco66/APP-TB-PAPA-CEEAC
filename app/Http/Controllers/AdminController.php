<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\Decision;
use App\Models\Direction;
use App\Models\NotificationRule;
use App\Models\Papa;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $query = $this->scopeUsers(
            User::with(['departement', 'direction', 'service', 'roles'])
            ->withTrashed()
            ->orderBy('name'),
            $request
        );

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('prenom', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('matricule', 'like', "%{$s}%"));
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('actif')) {
            $query->where('actif', (bool) $request->actif);
        }

        $users = $query->paginate(25);
        $roles = Role::orderBy('name')->get();
        $directions = $this->scopeDirections(
            Direction::actif()->orderBy('libelle'),
            $request
        )->get(['id', 'code', 'libelle']);
        $scopeLabel = $request->user()->scopeLabel();

        return view('admin.utilisateurs.index', compact('users', 'roles', 'directions', 'scopeLabel'));
    }

    public function create(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $roles = Role::orderBy('name')->get();
        $directions = $this->scopeDirections(
            Direction::actif()->orderBy('libelle'),
            $request
        )->get(['id', 'code', 'libelle']);
        $scopeLabel = $request->user()->scopeLabel();

        return view('admin.utilisateurs.create', compact('roles', 'directions', 'scopeLabel'));
    }

    public function store(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'matricule' => 'nullable|string|max:50|unique:users,matricule',
            'titre' => 'nullable|string|max:100',
            'fonction' => 'nullable|string|max:200',
            'telephone' => 'nullable|string|max:30',
            'direction_id' => 'nullable|exists:directions,id',
            'actif' => 'boolean',
            'role' => 'required|exists:roles,name',
        ]);

        $direction = null;
        $departementId = null;

        if (! empty($data['direction_id'])) {
            $direction = $this->scopeDirections(
                Direction::query()->whereKey($data['direction_id']),
                $request
            )->firstOrFail();

            $departementId = $direction->departement_id;
        }

        $user = User::create([
            'name' => $data['name'],
            'prenom' => $data['prenom'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'matricule' => $data['matricule'] ?? null,
            'titre' => $data['titre'] ?? null,
            'fonction' => $data['fonction'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'departement_id' => $departementId,
            'direction_id' => $direction?->id,
            'service_id' => null,
            'scope_level' => $direction ? 'direction' : null,
            'actif' => $data['actif'] ?? true,
        ]);

        $user->assignRole($data['role']);

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', "Utilisateur {$user->nomComplet()} cree.");
    }

    public function edit(User $user)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->scopeUsers(User::query()->whereKey($user->id), request())->exists(), 403);

        $roles = Role::orderBy('name')->get();
        $directions = $this->scopeDirections(
            Direction::actif()->orderBy('libelle'),
            request()
        )->get(['id', 'code', 'libelle']);
        $scopeLabel = request()->user()->scopeLabel();

        return view('admin.utilisateurs.edit', compact('user', 'roles', 'directions', 'scopeLabel'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->scopeUsers(User::query()->whereKey($user->id), $request)->exists(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'matricule' => 'nullable|string|max:50|unique:users,matricule,' . $user->id,
            'titre' => 'nullable|string|max:100',
            'fonction' => 'nullable|string|max:200',
            'telephone' => 'nullable|string|max:30',
            'direction_id' => 'nullable|exists:directions,id',
            'actif' => 'boolean',
            'role' => 'required|exists:roles,name',
        ]);

        $direction = null;
        $departementId = null;

        if (! empty($data['direction_id'])) {
            $direction = $this->scopeDirections(
                Direction::query()->whereKey($data['direction_id']),
                $request
            )->firstOrFail();

            $departementId = $direction->departement_id;
        }

        $updateData = [
            'name' => $data['name'],
            'prenom' => $data['prenom'] ?? null,
            'email' => $data['email'],
            'matricule' => $data['matricule'] ?? null,
            'titre' => $data['titre'] ?? null,
            'fonction' => $data['fonction'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'departement_id' => $departementId,
            'direction_id' => $direction?->id,
            'service_id' => null,
            'scope_level' => $direction ? 'direction' : null,
            'actif' => $data['actif'] ?? $user->actif,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', "Utilisateur {$user->nomComplet()} mis a jour.");
    }

    public function toggleActif(Request $request, User $user)
    {
        $this->authorize('admin.utilisateurs');
        abort_if($user->id === auth()->id(), 403, 'Vous ne pouvez pas desactiver votre propre compte.');
        abort_unless($this->scopeUsers(User::query()->whereKey($user->id), $request)->exists(), 403);

        $user->update(['actif' => ! $user->actif]);

        $etat = $user->actif ? 'active' : 'desactive';

        return back()->with('success', "Compte {$etat}.");
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('admin.utilisateurs');
        abort_if($user->id === auth()->id(), 403, 'Vous ne pouvez pas supprimer votre propre compte.');
        abort_unless($this->scopeUsers(User::query()->whereKey($user->id), $request)->exists(), 403);

        $user->delete();

        return back()->with('success', 'Utilisateur archive.');
    }

    public function restore(Request $request, User $user)
    {
        $this->authorize('admin.utilisateurs');
        abort_unless($this->scopeUsers(User::withTrashed()->whereKey($user->id), $request)->exists(), 403);

        $user->restore();

        return back()->with('success', 'Utilisateur restaure.');
    }

    public function auditLog(Request $request)
    {
        $this->authorize('admin.audit_log');
        abort_unless($request->user()->resolveVisibilityScope()->isGlobal, 403);

        $query = Activity::with('causer')->latest();

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id)
                ->where('causer_type', User::class);
        }
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $logs = $query->paginate(50)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name', 'prenom']);
        $logNames = Activity::select('log_name')->distinct()->pluck('log_name');
        $scopeLabel = 'Perimetre de donnees : Consolidation institutionnelle';

        return view('admin.audit_log', compact('logs', 'users', 'logNames', 'scopeLabel'));
    }

    public function auditEvents(Request $request)
    {
        $this->authorize('admin.audit_log');
        abort_unless($request->user()->resolveVisibilityScope()->isGlobal, 403);

        $query = $this->buildAuditEventsQuery($request);

        $summary = [
            'total' => (clone $query)->count(),
            'info' => (clone $query)->where('niveau', 'info')->count(),
            'warning' => (clone $query)->where('niveau', 'warning')->count(),
            'critical' => (clone $query)->where('niveau', 'critical')->count(),
        ];

        $events = $query
            ->paginate(50)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'prenom']);
        $papas = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $modules = AuditEvent::query()->select('module')->distinct()->orderBy('module')->pluck('module');
        $eventTypes = AuditEvent::query()->select('event_type')->distinct()->orderBy('event_type')->pluck('event_type');
        $auditableContext = $this->resolveAuditableContext($request);

        return view('admin.audit_events', compact('events', 'users', 'papas', 'modules', 'eventTypes', 'auditableContext', 'summary'));
    }

    public function exportAuditEvents(Request $request): Response
    {
        $this->authorize('admin.audit_log');
        abort_unless($request->user()->resolveVisibilityScope()->isGlobal, 403);

        $events = $this->buildAuditEventsQuery($request)->get();
        $filename = 'audit_metier_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($events): void {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Horodatage',
                'Module',
                'Evenement',
                'Action',
                'Niveau',
                'Acteur',
                'PAPA',
                'Objet',
                'Objet ID',
                'Description',
                'Checksum',
            ], ';');

            foreach ($events as $event) {
                fputcsv($handle, [
                    optional($event->horodate_evenement)->format('d/m/Y H:i:s'),
                    $event->module,
                    $event->event_type,
                    $event->action,
                    $event->niveau,
                    $event->acteur?->nomComplet() ?? 'Systeme',
                    $event->papa?->code ?? '',
                    class_basename($event->auditable_type),
                    $event->auditable_id,
                    $event->description,
                    $event->checksum,
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function notificationRules()
    {
        $this->authorize('notification_rule.gerer');

        $rules = NotificationRule::query()->orderBy('event_type')->orderBy('code')->paginate(30);

        return view('admin.notification_rules', compact('rules'));
    }

    public function updateNotificationRule(Request $request, NotificationRule $notificationRule)
    {
        $this->authorize('notification_rule.gerer');

        $data = $request->validate([
            'libelle' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'canal' => 'required|in:in_app,email,sms',
            'role_cible' => 'nullable|string|max:255',
            'permission_cible' => 'nullable|string|max:255',
            'delai_minutes' => 'nullable|integer|min:0',
            'template_sujet' => 'nullable|string|max:255',
            'template_message' => 'required|string',
            'escalade' => 'nullable|boolean',
            'actif' => 'nullable|boolean',
        ]);

        $notificationRule->update([
            'libelle' => $data['libelle'],
            'event_type' => $data['event_type'],
            'canal' => $data['canal'],
            'role_cible' => $data['role_cible'] ?? null,
            'permission_cible' => $data['permission_cible'] ?? null,
            'delai_minutes' => $data['delai_minutes'] ?? null,
            'template_sujet' => $data['template_sujet'] ?? null,
            'template_message' => $data['template_message'],
            'escalade' => (bool) ($data['escalade'] ?? false),
            'actif' => (bool) ($data['actif'] ?? false),
        ]);

        return back()->with('success', "Regle {$notificationRule->code} mise a jour.");
    }

    private function buildAuditEventsQuery(Request $request)
    {
        $query = AuditEvent::with(['acteur', 'papa'])->latest('horodate_evenement');

        $auditableType = $request->string('auditable_type')->toString();

        if ($request->filled('acteur_id')) {
            $query->where('acteur_id', $request->integer('acteur_id'));
        }
        if ($request->filled('module')) {
            $query->where('module', $request->string('module'));
        }
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->string('event_type'));
        }
        if ($request->filled('niveau')) {
            $query->where('niveau', $request->string('niveau'));
        }
        if ($request->filled('papa_id')) {
            $query->where('papa_id', $request->integer('papa_id'));
        }
        if ($auditableType !== '') {
            $query->where('auditable_type', $auditableType);
        }
        if ($request->filled('auditable_id')) {
            $query->where('auditable_id', $request->integer('auditable_id'));
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('horodate_evenement', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('horodate_evenement', '<=', $request->date_fin);
        }

        return $query;
    }

    private function resolveAuditableContext(Request $request): array
    {
        $auditableType = $request->string('auditable_type')->toString();
        $auditableId = $request->integer('auditable_id');

        if ($auditableType === '' || $auditableId <= 0) {
            return [
                'label' => null,
                'url' => null,
            ];
        }

        return match ($auditableType) {
            WorkflowInstance::class => [
                'label' => 'Retour au workflow',
                'url' => route('workflows.show', $auditableId),
            ],
            Decision::class => [
                'label' => 'Retour a la decision',
                'url' => route('decisions.show', $auditableId),
            ],
            Papa::class => [
                'label' => 'Retour au PAPA',
                'url' => route('papas.show', $auditableId),
            ],
            default => [
                'label' => 'Objet ' . Str::headline(class_basename($auditableType)),
                'url' => null,
            ],
        };
    }

    private function scopeUsers($query, Request $request)
    {
        return app(UserScopeResolver::class)->applyToQuery($query, $request->user(), [
            'departement' => 'departement_id',
            'direction' => 'direction_id',
            'service' => 'service_id',
        ]);
    }

    private function scopeDirections($query, Request $request)
    {
        return app(UserScopeResolver::class)->applyToQuery($query, $request->user(), [
            'departement' => 'departement_id',
            'direction' => 'id',
            'service' => null,
        ]);
    }
}
