<?php

namespace App\Http\Controllers;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ParametreWorkflowController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorize('parametres.workflows.voir');

        $definitions = WorkflowDefinition::withCount('steps')->orderBy('module_cible')->get();
        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.workflows.index', compact('definitions', 'scopeLabel'));
    }

    public function edit(Request $request, WorkflowDefinition $definition)
    {
        $this->authorize('parametres.workflows.voir');

        $definition->load('steps');
        $scopeLabel = $this->scopeLabel($request);

        return view('parametres.workflows.edit', compact('definition', 'scopeLabel'));
    }

    public function update(Request $request, WorkflowDefinition $definition)
    {
        $this->authorize('parametres.workflows.modifier');
        $this->ensureGlobalScope($request);

        $data = $request->validate([
            'libelle' => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'actif' => 'boolean',
        ]);

        $avant = $definition->toArray();
        $definition->update(array_merge($data, ['maj_par' => $request->user()->id]));

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'workflow_definition_modifiee',
            auditable: $definition,
            acteur: $request->user(),
            action: 'modifier',
            description: "Workflow {$definition->code} modifie",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Workflow \"{$definition->libelle}\" mis a jour.");
    }

    public function updateStep(Request $request, WorkflowDefinition $definition, WorkflowStep $step)
    {
        $this->authorize('parametres.workflows.modifier');
        $this->ensureGlobalScope($request);
        abort_if($step->workflow_definition_id !== $definition->id, 404);

        $data = $request->validate([
            'libelle' => 'required|string|max:200',
            'role_requis' => 'nullable|string|max:100',
            'permission_requise' => 'nullable|string|max:100',
            'delai_jours' => 'nullable|integer|min:1',
            'escalade_apres_jours' => 'nullable|integer|min:1',
        ]);

        $avant = $step->toArray();
        $step->update($data);

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'workflow_step_modifie',
            auditable: $definition,
            acteur: $request->user(),
            action: 'modifier',
            description: "Etape {$step->code} du workflow {$definition->code} modifiee",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Etape \"{$step->libelle}\" mise a jour.");
    }

    public function toggleDefinition(Request $request, WorkflowDefinition $definition)
    {
        $this->authorize('parametres.workflows.modifier');
        $this->ensureGlobalScope($request);

        $definition->update(['actif' => ! $definition->actif]);
        $etat = $definition->actif ? 'active' : 'desactive';

        return back()->with('success', "Workflow \"{$definition->libelle}\" {$etat}.");
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
