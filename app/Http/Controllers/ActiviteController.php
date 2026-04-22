<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActiviteRequest;
use App\Http\Requests\UpdateAvancementActiviteRequest;
use App\Models\Activite;
use App\Models\Direction;
use App\Models\ResultatAttendu;
use App\Models\Service;
use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('activite.voir');
        $user = $request->user();

        $query = Activite::with(['direction', 'resultatAttendu', 'responsable'])
            ->visibleTo($user)
            ->orderBy('date_fin_prevue');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('direction_id') && $user->can('activite.voir_toutes_directions')) {
            $query->where('direction_id', $request->direction_id);
        }
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        if ($request->boolean('en_retard')) {
            $query->enRetard();
        }

        $activites = $query->paginate(20)->withQueryString();
        $directions = Direction::actif()->orderBy('libelle')->get();
        $scopeLabel = $user->scopeLabel();

        return view('activites.index', compact('activites', 'directions', 'scopeLabel'));
    }

    public function create(Request $request)
    {
        $this->authorize('activite.creer');
        $user = $request->user();
        $resolver = app(UserScopeResolver::class);

        $resultatsAttendus = ResultatAttendu::with('objectifImmediats.actionPrioritaire.papa')
            ->visibleTo($user)
            ->orderBy('code')
            ->get();
        $directions = $resolver
            ->applyToQuery(Direction::actif()->orderBy('libelle'), $user, [
                'departement' => 'departement_id',
                'direction' => 'id',
                'service' => null,
            ])
            ->get();
        $users = $resolver
            ->applyToQuery(User::actif()->orderBy('name'), $user, [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get();
        $services = $resolver
            ->applyToQuery(Service::actif()->orderBy('libelle'), $user, [
                'departement' => null,
                'direction' => 'direction_id',
                'service' => 'id',
            ])
            ->get(['id', 'direction_id', 'libelle']);
        $scopeLabel = $user->scopeLabel();

        return view('activites.create', compact('resultatsAttendus', 'directions', 'users', 'services', 'scopeLabel'));
    }

    public function store(StoreActiviteRequest $request)
    {
        $this->authorize('activite.creer');

        $activite = Activite::create(array_merge($request->validated(), [
            'created_by' => $request->user()->id,
        ]));

        return redirect()
            ->route('activites.show', $activite)
            ->with('success', "Activite {$activite->code} creee.");
    }

    public function show(Activite $activite)
    {
        $this->authorize('voir', $activite);
        $user = auth()->user();

        $activite->load([
            'direction.departement',
            'service',
            'resultatAttendu.objectifImmediats.actionPrioritaire.papa',
            'responsable',
            'pointFocal',
            'taches',
            'jalons',
            'budgets.partenaire',
            'engagements',
            'documents' => fn ($query) => $query->visibleTo($user)->with('categorie'),
            'alertes' => fn ($query) => $query->visibleTo($user),
        ]);

        return view('activites.show', [
            'activite' => $activite,
            'scopeLabel' => $user->scopeLabel(),
        ]);
    }

    public function print(Activite $activite)
    {
        $this->authorize('voir', $activite);
        $user = auth()->user();

        $activite->load([
            'direction.departement',
            'service',
            'resultatAttendu.objectifImmediats.actionPrioritaire.papa',
            'responsable',
            'pointFocal',
            'taches.assignee',
            'jalons',
            'budgets.partenaire',
            'engagements',
            'documents' => fn ($query) => $query->visibleTo($user)->with('categorie'),
            'alertes' => fn ($query) => $query->visibleTo($user),
        ]);

        return view('activites.print', [
            'activite' => $activite,
            'scopeLabel' => $user->scopeLabel(),
            'printedAt' => now(),
        ]);
    }

    public function edit(Activite $activite)
    {
        $this->authorize('modifier', $activite);
        $user = request()->user();
        $resolver = app(UserScopeResolver::class);

        $resultatsAttendus = ResultatAttendu::query()
            ->visibleTo($user)
            ->orderBy('code')
            ->get();
        $directions = $resolver
            ->applyToQuery(Direction::actif()->orderBy('libelle'), $user, [
                'departement' => 'departement_id',
                'direction' => 'id',
                'service' => null,
            ])
            ->get();
        $services = $resolver
            ->applyToQuery(Service::actif()->orderBy('libelle'), $user, [
                'departement' => null,
                'direction' => 'direction_id',
                'service' => 'id',
            ])
            ->get();
        $users = $resolver
            ->applyToQuery(User::actif()->orderBy('name'), $user, [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get();
        $scopeLabel = $user->scopeLabel();

        return view('activites.edit', compact('activite', 'resultatsAttendus', 'directions', 'services', 'users', 'scopeLabel'));
    }

    public function update(Request $request, Activite $activite)
    {
        $this->authorize('modifier', $activite);

        $data = $request->validate([
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut_prevue',
            'direction_id' => 'required|exists:directions,id',
            'service_id' => 'nullable|exists:services,id',
            'responsable_id' => 'nullable|exists:users,id',
            'point_focal_id' => 'nullable|exists:users,id',
            'priorite' => 'required|in:critique,haute,normale,basse',
            'budget_prevu' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $resolver = app(UserScopeResolver::class);
        $currentUser = $request->user();
        $direction = Direction::find($data['direction_id']);

        abort_unless(
            $direction && $resolver->canAccessAttributes($currentUser, departementId: $direction->departement_id, directionId: $direction->id),
            403
        );

        if (! empty($data['service_id'])) {
            $service = Service::with('direction')->find($data['service_id']);

            abort_unless(
                $service && $resolver->canAccessAttributes(
                    $currentUser,
                    departementId: $service->direction?->departement_id,
                    directionId: $service->direction_id,
                    serviceId: $service->id,
                ),
                403
            );
        }

        foreach (['responsable_id', 'point_focal_id'] as $field) {
            if (empty($data[$field])) {
                continue;
            }

            $assignee = User::find($data[$field]);

            abort_unless(
                $assignee && $resolver->canAccessAttributes(
                    $currentUser,
                    departementId: $assignee->departement_id ?? $assignee->direction?->departement_id,
                    directionId: $assignee->direction_id,
                    serviceId: $assignee->service_id,
                ),
                403
            );
        }

        $activite->update($data);

        return redirect()
            ->route('activites.show', $activite)
            ->with('success', 'Activite mise a jour.');
    }

    public function mettreAJourAvancement(UpdateAvancementActiviteRequest $request, Activite $activite)
    {
        $activite->update($request->validated());

        return redirect()
            ->route('activites.show', $activite)
            ->with('success', 'Avancement mis a jour.');
    }

    public function destroy(Activite $activite)
    {
        $this->authorize('supprimer', $activite);
        $activite->delete();

        return redirect()
            ->route('activites.index')
            ->with('success', 'Activite supprimee.');
    }

    // La route activites-gantt est désormais gérée par GanttController.
}
