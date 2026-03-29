<?php

namespace App\Http\Controllers;

use App\Models\ActionPrioritaire;
use App\Models\BudgetPapa;
use App\Models\Papa;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request, Papa $papa)
    {
        $this->authorize('budget.voir');

        $query = BudgetPapa::with(['partenaire', 'actionPrioritaire'])
            ->where('papa_id', $papa->id);

        if ($request->filled('source_financement')) {
            $query->where('source_financement', $request->source_financement);
        }
        if ($request->filled('action_prioritaire_id')) {
            $query->where('action_prioritaire_id', $request->action_prioritaire_id);
        }
        if ($request->filled('annee_budgetaire')) {
            $query->where('annee_budgetaire', $request->annee_budgetaire);
        }

        $budgets = $query->orderBy('source_financement')->orderBy('annee_budgetaire')->get();

        $totaux = [
            'prevu'    => $budgets->sum('montant_prevu'),
            'engage'   => $budgets->sum('montant_engage'),
            'decaisse' => $budgets->sum('montant_decaisse'),
        ];

        $papas               = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle', 'annee']);
        $actionsPrioritaires = $papa->actionsPrioritaires()->orderBy('ordre')->get(['id', 'code', 'libelle']);
        $annees              = BudgetPapa::where('papa_id', $papa->id)
                                ->distinct()->orderBy('annee_budgetaire')->pluck('annee_budgetaire');

        return view('budgets.index', compact('papa', 'budgets', 'totaux', 'papas', 'actionsPrioritaires', 'annees'));
    }

    public function create(Papa $papa)
    {
        $this->authorize('budget.creer');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');

        $actionsPrioritaires = $papa->actionsPrioritaires()->orderBy('ordre')->get(['id', 'code', 'libelle']);

        return view('budgets.create', compact('papa', 'actionsPrioritaires'));
    }

    public function store(Request $request, Papa $papa)
    {
        $this->authorize('budget.creer');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');

        $data = $request->validate([
            'source_financement'      => 'required|in:budget_ceeac,contribution_etat_membre,partenaire_technique_financier,fonds_propres,autre',
            'libelle_ligne'           => 'nullable|string|max:300',
            'annee_budgetaire'        => 'required|integer|min:2020|max:2040',
            'montant_prevu'           => 'required|numeric|min:0',
            'montant_engage'          => 'nullable|numeric|min:0',
            'montant_decaisse'        => 'nullable|numeric|min:0',
            'action_prioritaire_id'   => 'nullable|exists:actions_prioritaires,id',
            'notes'                   => 'nullable|string',
        ]);

        $data['papa_id']       = $papa->id;
        $data['created_by']    = $request->user()->id;
        $data['montant_engage']   ??= 0;
        $data['montant_decaisse'] ??= 0;
        $data['montant_solde']    = $data['montant_prevu'] - $data['montant_engage'];

        BudgetPapa::create($data);

        return redirect()
            ->route('budgets.index', $papa)
            ->with('success', 'Ligne budgétaire créée.');
    }

    public function edit(Papa $papa, BudgetPapa $budget)
    {
        $this->authorize('budget.modifier');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');
        abort_if($budget->papa_id !== $papa->id, 404);

        $actionsPrioritaires = $papa->actionsPrioritaires()->orderBy('ordre')->get(['id', 'code', 'libelle']);

        return view('budgets.edit', compact('papa', 'budget', 'actionsPrioritaires'));
    }

    public function update(Request $request, Papa $papa, BudgetPapa $budget)
    {
        $this->authorize('budget.modifier');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');
        abort_if($budget->papa_id !== $papa->id, 404);

        $data = $request->validate([
            'source_financement'      => 'required|in:budget_ceeac,contribution_etat_membre,partenaire_technique_financier,fonds_propres,autre',
            'libelle_ligne'           => 'nullable|string|max:300',
            'annee_budgetaire'        => 'required|integer|min:2020|max:2040',
            'montant_prevu'           => 'required|numeric|min:0',
            'montant_engage'          => 'nullable|numeric|min:0',
            'montant_decaisse'        => 'nullable|numeric|min:0',
            'action_prioritaire_id'   => 'nullable|exists:actions_prioritaires,id',
            'notes'                   => 'nullable|string',
        ]);

        $data['montant_engage']   ??= 0;
        $data['montant_decaisse'] ??= 0;
        $data['montant_solde']    = $data['montant_prevu'] - $data['montant_engage'];

        $budget->update($data);

        return redirect()
            ->route('budgets.index', $papa)
            ->with('success', 'Ligne budgétaire mise à jour.');
    }

    public function destroy(Papa $papa, BudgetPapa $budget)
    {
        $this->authorize('budget.supprimer');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');
        abort_if($budget->papa_id !== $papa->id, 404);

        $budget->delete();

        return back()->with('success', 'Ligne budgétaire supprimée.');
    }
}
