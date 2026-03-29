<?php

namespace App\Http\Controllers;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ParametreWorkflowController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index()
    {
        $this->authorize('parametres.workflows.voir');
        $definitions = WorkflowDefinition::withCount('steps')->orderBy('module_cible')->get();
        return view('parametres.workflows.index', compact('definitions'));
    }

    public function edit(WorkflowDefinition $definition)
    {
        $this->authorize('parametres.workflows.voir');
        $definition->load('steps');
        return view('parametres.workflows.edit', compact('definition'));
    }

    public function update(Request $request, WorkflowDefinition $definition)
    {
        $this->authorize('parametres.workflows.modifier');

        $data = $request->validate([
            'libelle'     => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'actif'       => 'boolean',
        ]);

        $avant = $definition->toArray();
        $definition->update(array_merge($data, ['maj_par' => $request->user()->id]));

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'workflow_definition_modifiee',
            auditable: $definition,
            acteur: $request->user(),
            action: 'modifier',
            description: "Workflow {$definition->code} modifié",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Workflow \"{$definition->libelle}\" mis à jour.");
    }

    public function updateStep(Request $request, WorkflowDefinition $definition, WorkflowStep $step)
    {
        $this->authorize('parametres.workflows.modifier');
        abort_if($step->workflow_definition_id !== $definition->id, 404);

        $data = $request->validate([
            'libelle'              => 'required|string|max:200',
            'role_requis'          => 'nullable|string|max:100',
            'permission_requise'   => 'nullable|string|max:100',
            'delai_jours'          => 'nullable|integer|min:1',
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
            description: "Étape {$step->code} du workflow {$definition->code} modifiée",
            donneesAvant: $avant,
            donneesApres: $data,
        );

        return back()->with('success', "Étape \"{$step->libelle}\" mise à jour.");
    }

    public function toggleDefinition(Request $request, WorkflowDefinition $definition)
    {
        $this->authorize('parametres.workflows.modifier');
        $definition->update(['actif' => !$definition->actif]);
        $etat = $definition->actif ? 'activé' : 'désactivé';
        return back()->with('success', "Workflow \"{$definition->libelle}\" {$etat}.");
    }
}
