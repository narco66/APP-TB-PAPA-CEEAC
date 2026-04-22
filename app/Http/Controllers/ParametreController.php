<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\Parametre;
use App\Services\AuditService;
use App\Services\ParametreService;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function __construct(
        private ParametreService $parametreService,
        private AuditService $auditService,
    ) {}

    public function hub(Request $request)
    {
        $this->authorize('parametres.generaux.voir');

        $stats = $this->parametreService->hubStats($request->user());
        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.hub', compact('stats', 'scopeLabel'));
    }

    public function generaux(Request $request)
    {
        $this->authorize('parametres.generaux.voir');

        $parametres = Parametre::query()
            ->where('groupe', 'general')
            ->orderBy('cle')
            ->get()
            ->keyBy('cle');

        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.generaux', compact('parametres', 'scopeLabel'));
    }

    public function saveGeneraux(Request $request)
    {
        $this->authorize('parametres.generaux.modifier');
        $this->ensureGlobalScope($request);

        $data = $request->validate([
            'app_nom' => 'required|string|max:200',
            'app_sigle' => 'required|string|max:20',
            'app_organisation' => 'required|string|max:200',
            'app_langue_defaut' => 'required|in:fr,en',
            'app_fuseau_horaire' => 'required|timezone',
            'app_devise' => 'required|string|max:10',
            'app_format_date' => 'required|in:d/m/Y,Y-m-d,d-m-Y',
            'app_annee_reference' => 'required|integer|min:2020|max:2040',
            'app_pied_page' => 'nullable|string|max:500',
            'app_couleur_primaire' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $avant = $this->parametreService->getGroupe('general');
        $this->parametreService->saveGroupe('general', $data, $request->user());

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'parametres_generaux_modifies',
            auditable: $request->user(),
            acteur: $request->user(),
            action: 'modifier',
            description: 'Modification des parametres generaux',
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', 'Parametres generaux sauvegardes avec succes.');
    }

    public function toggleMaintenance(Request $request)
    {
        $this->authorize('parametres.technique.modifier');
        $this->ensureGlobalScope($request);

        $actuel = (bool) $this->parametreService->get('app_maintenance', false);
        $this->parametreService->set('app_maintenance', ! $actuel ? 'true' : 'false', $request->user());

        $etat = ! $actuel ? 'active' : 'desactive';

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'mode_maintenance_change',
            auditable: $request->user(),
            acteur: $request->user(),
            action: 'modifier',
            description: "Mode maintenance {$etat}",
        );

        return back()->with('success', "Mode maintenance {$etat}.");
    }

    public function journal(Request $request)
    {
        $this->authorize('parametres.journal.voir');

        $query = AuditEvent::query()
            ->with('acteur')
            ->where('module', 'parametres')
            ->latest('created_at');

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->string('event_type'));
        }

        $events = $query->paginate(50);
        $eventTypes = AuditEvent::query()
            ->where('module', 'parametres')
            ->distinct()
            ->pluck('event_type');
        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.journal', compact('events', 'eventTypes', 'scopeLabel'));
    }

    private function ensureGlobalScope(Request $request): void
    {
        abort_unless($request->user()->resolveVisibilityScope()->isGlobal, 403);
    }

    private function scopeLabel(Request $request): string
    {
        return $request->user()->resolveVisibilityScope()->isGlobal
            ? 'Perimetre de donnees : Consolidation institutionnelle'
            : $request->user()->scopeLabel();
    }
}
