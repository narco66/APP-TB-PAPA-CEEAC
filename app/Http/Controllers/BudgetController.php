<?php

namespace App\Http\Controllers;

use App\Models\BudgetPapa;
use App\Models\Papa;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request, Papa $papa)
    {
        $this->authorize('budget.voir');
        abort_unless(Papa::query()->visibleTo($request->user())->whereKey($papa->id)->exists(), 403);

        $query = BudgetPapa::with(['partenaire', 'actionPrioritaire'])
            ->where('papa_id', $papa->id)
            ->visibleTo($request->user());

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
            'prevu' => $budgets->sum('montant_prevu'),
            'engage' => $budgets->sum('montant_engage'),
            'decaisse' => $budgets->sum('montant_decaisse'),
        ];

        $papas = Papa::query()->visibleTo($request->user())->orderByDesc('annee')->get(['id', 'code', 'libelle', 'annee']);
        $actionsPrioritaires = $papa->actionsPrioritaires()->visibleTo($request->user())->orderBy('ordre')->get(['id', 'code', 'libelle']);
        $annees = BudgetPapa::query()->visibleTo($request->user())
            ->where('papa_id', $papa->id)
            ->distinct()
            ->orderBy('annee_budgetaire')
            ->pluck('annee_budgetaire');
        $scopeLabel = $request->user()->scopeLabel();

        return view('budgets.index', compact('papa', 'budgets', 'totaux', 'papas', 'actionsPrioritaires', 'annees', 'scopeLabel'));
    }

    public function print(Request $request, Papa $papa)
    {
        $this->authorize('budget.voir');
        abort_unless(Papa::query()->visibleTo($request->user())->whereKey($papa->id)->exists(), 403);

        $query = BudgetPapa::with(['partenaire', 'actionPrioritaire'])
            ->where('papa_id', $papa->id)
            ->visibleTo($request->user());

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
            'prevu' => $budgets->sum('montant_prevu'),
            'engage' => $budgets->sum('montant_engage'),
            'decaisse' => $budgets->sum('montant_decaisse'),
        ];

        $actionsPrioritaires = $papa->actionsPrioritaires()->visibleTo($request->user())->orderBy('ordre')->get(['id', 'code', 'libelle']);

        return view('budgets.print', [
            'papa' => $papa,
            'budgets' => $budgets,
            'totaux' => $totaux,
            'actionsPrioritaires' => $actionsPrioritaires,
            'scopeLabel' => $request->user()->scopeLabel(),
            'printedAt' => now(),
            'filters' => $request->only(['source_financement', 'action_prioritaire_id', 'annee_budgetaire']),
        ]);
    }

    public function create(Papa $papa)
    {
        $this->authorize('budget.creer');
        abort_unless(Papa::query()->visibleTo(request()->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');

        $actionsPrioritaires = $papa->actionsPrioritaires()->visibleTo(request()->user())->orderBy('ordre')->get(['id', 'code', 'libelle']);
        $scopeLabel = request()->user()->scopeLabel();

        return view('budgets.create', compact('papa', 'actionsPrioritaires', 'scopeLabel'));
    }

    public function store(Request $request, Papa $papa)
    {
        $this->authorize('budget.creer');
        abort_unless(Papa::query()->visibleTo($request->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');

        $data = $request->validate([
            'source_financement' => 'required|in:budget_ceeac,contribution_etat_membre,partenaire_technique_financier,fonds_propres,autre',
            'libelle_ligne' => 'nullable|string|max:300',
            'annee_budgetaire' => 'required|integer|min:2020|max:2040',
            'montant_prevu' => 'required|numeric|min:0',
            'montant_engage' => 'nullable|numeric|min:0',
            'montant_decaisse' => 'nullable|numeric|min:0',
            'action_prioritaire_id' => 'nullable|exists:actions_prioritaires,id',
            'notes' => 'nullable|string',
        ]);

        if (! empty($data['action_prioritaire_id'])) {
            $actionVisible = $papa->actionsPrioritaires()
                ->visibleTo($request->user())
                ->whereKey($data['action_prioritaire_id'])
                ->exists();

            if (! $actionVisible) {
                return back()->withErrors(['action_prioritaire_id' => 'Action prioritaire hors perimetre.'])->withInput();
            }
        }

        $data['papa_id'] = $papa->id;
        $data['created_by'] = $request->user()->id;
        $data['montant_engage'] ??= 0;
        $data['montant_decaisse'] ??= 0;
        $data['montant_solde'] = $data['montant_prevu'] - $data['montant_engage'];

        BudgetPapa::create($data);

        return redirect()
            ->route('budgets.index', $papa)
            ->with('success', 'Ligne budgetaire creee.');
    }

    public function edit(Papa $papa, BudgetPapa $budget)
    {
        $this->authorize('budget.modifier');
        abort_unless(Papa::query()->visibleTo(request()->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');
        abort_if($budget->papa_id !== $papa->id, 404);
        abort_unless($budget->canBeAccessedBy(request()->user()), 403);

        $actionsPrioritaires = $papa->actionsPrioritaires()->visibleTo(request()->user())->orderBy('ordre')->get(['id', 'code', 'libelle']);
        $scopeLabel = request()->user()->scopeLabel();

        return view('budgets.edit', compact('papa', 'budget', 'actionsPrioritaires', 'scopeLabel'));
    }

    public function update(Request $request, Papa $papa, BudgetPapa $budget)
    {
        $this->authorize('budget.modifier');
        abort_unless(Papa::query()->visibleTo($request->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');
        abort_if($budget->papa_id !== $papa->id, 404);
        abort_unless($budget->canBeAccessedBy($request->user()), 403);

        $data = $request->validate([
            'source_financement' => 'required|in:budget_ceeac,contribution_etat_membre,partenaire_technique_financier,fonds_propres,autre',
            'libelle_ligne' => 'nullable|string|max:300',
            'annee_budgetaire' => 'required|integer|min:2020|max:2040',
            'montant_prevu' => 'required|numeric|min:0',
            'montant_engage' => 'nullable|numeric|min:0',
            'montant_decaisse' => 'nullable|numeric|min:0',
            'action_prioritaire_id' => 'nullable|exists:actions_prioritaires,id',
            'notes' => 'nullable|string',
        ]);

        if (! empty($data['action_prioritaire_id'])) {
            $actionVisible = $papa->actionsPrioritaires()
                ->visibleTo($request->user())
                ->whereKey($data['action_prioritaire_id'])
                ->exists();

            if (! $actionVisible) {
                return back()->withErrors(['action_prioritaire_id' => 'Action prioritaire hors perimetre.'])->withInput();
            }
        }

        $data['montant_engage'] ??= 0;
        $data['montant_decaisse'] ??= 0;
        $data['montant_solde'] = $data['montant_prevu'] - $data['montant_engage'];

        $budget->update($data);

        return redirect()
            ->route('budgets.index', $papa)
            ->with('success', 'Ligne budgetaire mise a jour.');
    }

    public function destroy(Papa $papa, BudgetPapa $budget)
    {
        $this->authorize('budget.supprimer');
        abort_unless(Papa::query()->visibleTo(request()->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');
        abort_if($budget->papa_id !== $papa->id, 404);
        abort_unless($budget->canBeAccessedBy(request()->user()), 403);

        $budget->delete();

        return back()->with('success', 'Ligne budgetaire supprimee.');
    }
}
