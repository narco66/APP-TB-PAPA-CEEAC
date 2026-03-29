<?php

namespace App\Http\Controllers;

use App\Models\Papa;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Services\WorkflowEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function __construct(private WorkflowEngine $workflowEngine) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', WorkflowInstance::class);

        $query = WorkflowInstance::with(['definition', 'etapeCourante', 'demarrePar', 'papa'])
            ->latest('created_at');

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        if ($request->filled('papa_id')) {
            $query->where('papa_id', $request->integer('papa_id'));
        }

        $instances = $query->paginate(20);

        return view('workflows.index', compact('instances'));
    }

    public function show(WorkflowInstance $workflow)
    {
        $this->authorize('view', $workflow);

        $workflow->load(['definition.steps', 'etapeCourante', 'actions.acteur', 'actions.step', 'papa']);

        return view('workflows.show', ['instance' => $workflow]);
    }

    public function audit(WorkflowInstance $workflow): RedirectResponse
    {
        $this->authorize('view', $workflow);

        return redirect()->route('admin.audit-events', $workflow->auditTrailParams());
    }

    public function demarrerPapa(Request $request, Papa $papa)
    {
        $this->authorize('demarrer', WorkflowInstance::class);

        $data = $request->validate([
            'workflow_code' => 'nullable|string|max:100',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $definition = WorkflowDefinition::with('steps')
            ->where('code', $data['workflow_code'] ?? 'PAPA_VALIDATION_STANDARD')
            ->where('actif', true)
            ->firstOrFail();

        $instance = $this->workflowEngine->demarrer(
            $definition,
            $papa,
            $request->user(),
            $papa,
            ['commentaire_initial' => $data['commentaire'] ?? null]
        );

        return redirect()
            ->route('workflows.show', $instance)
            ->with('success', "Workflow {$definition->libelle} démarré.");
    }

    public function approuver(Request $request, WorkflowInstance $workflow)
    {
        $this->authorize('approuver', $workflow);

        $request->validate([
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $this->workflowEngine->approuver($workflow, $request->user(), $request->string('commentaire')->toString() ?: null);

        return redirect()
            ->route('workflows.show', $workflow)
            ->with('success', 'Étape approuvée.');
    }

    public function rejeter(Request $request, WorkflowInstance $workflow)
    {
        $this->authorize('rejeter', $workflow);

        $data = $request->validate([
            'motif' => 'required|string|max:1000',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $this->workflowEngine->rejeter(
            $workflow,
            $request->user(),
            $data['motif'],
            $data['commentaire'] ?? null,
        );

        return redirect()
            ->route('workflows.show', $workflow)
            ->with('success', 'Workflow rejeté.');
    }

    public function commenter(Request $request, WorkflowInstance $workflow)
    {
        $this->authorize('commenter', $workflow);

        $data = $request->validate([
            'commentaire' => 'required|string|max:1000',
        ]);

        $this->workflowEngine->commenter($workflow, $request->user(), $data['commentaire']);

        return redirect()
            ->route('workflows.show', $workflow)
            ->with('success', 'Commentaire ajouté au workflow.');
    }
}
