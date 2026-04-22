<?php

namespace App\Http\Controllers;

use App\Models\Papa;
use App\Models\Risque;
use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;

class RisqueController extends Controller
{
    public function index(Papa $papa)
    {
        $this->authorize('risque.voir');
        abort_unless(Papa::query()->visibleTo(request()->user())->whereKey($papa->id)->exists(), 403);

        $risques = Risque::query()
            ->where('papa_id', $papa->id)
            ->visibleTo(request()->user())
            ->with('responsable')
            ->orderByDesc('score_risque')
            ->get();

        $probabilites = ['tres_faible', 'faible', 'moyenne', 'elevee', 'tres_elevee'];
        $impacts = ['negligeable', 'mineur', 'modere', 'majeur', 'catastrophique'];

        $matrice = [];
        foreach ($probabilites as $prob) {
            foreach ($impacts as $imp) {
                $matrice[$prob][$imp] = $risques->filter(
                    fn($risque) => $risque->probabilite === $prob && $risque->impact === $imp
                );
            }
        }

        $stats = [
            'rouge' => $risques->where('niveau_risque', 'rouge')->count(),
            'orange' => $risques->where('niveau_risque', 'orange')->count(),
            'jaune' => $risques->where('niveau_risque', 'jaune')->count(),
            'vert' => $risques->where('niveau_risque', 'vert')->count(),
        ];
        $scopeLabel = request()->user()->scopeLabel();

        return view('risques.index', compact('papa', 'risques', 'matrice', 'probabilites', 'impacts', 'stats', 'scopeLabel'));
    }

    public function print(Papa $papa)
    {
        $this->authorize('risque.voir');
        abort_unless(Papa::query()->visibleTo(request()->user())->whereKey($papa->id)->exists(), 403);

        $risques = Risque::query()
            ->where('papa_id', $papa->id)
            ->visibleTo(request()->user())
            ->with('responsable')
            ->orderByDesc('score_risque')
            ->get();

        $probabilites = ['tres_faible', 'faible', 'moyenne', 'elevee', 'tres_elevee'];
        $impacts = ['negligeable', 'mineur', 'modere', 'majeur', 'catastrophique'];

        $matrice = [];
        foreach ($probabilites as $prob) {
            foreach ($impacts as $imp) {
                $matrice[$prob][$imp] = $risques->filter(
                    fn($risque) => $risque->probabilite === $prob && $risque->impact === $imp
                );
            }
        }

        $stats = [
            'rouge' => $risques->where('niveau_risque', 'rouge')->count(),
            'orange' => $risques->where('niveau_risque', 'orange')->count(),
            'jaune' => $risques->where('niveau_risque', 'jaune')->count(),
            'vert' => $risques->where('niveau_risque', 'vert')->count(),
        ];

        return view('risques.print', [
            'papa' => $papa,
            'risques' => $risques,
            'matrice' => $matrice,
            'probabilites' => $probabilites,
            'impacts' => $impacts,
            'stats' => $stats,
            'scopeLabel' => request()->user()->scopeLabel(),
            'printedAt' => now(),
        ]);
    }

    public function create(Request $request, Papa $papa)
    {
        $this->authorize('risque.creer');
        abort_unless(Papa::query()->visibleTo($request->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');

        $responsables = app(UserScopeResolver::class)
            ->applyToQuery(User::actif()->orderBy('name'), $request->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get(['id', 'name', 'prenom']);
        $scopeLabel = $request->user()->scopeLabel();

        return view('risques.create', compact('papa', 'responsables', 'scopeLabel'));
    }

    public function store(Request $request, Papa $papa)
    {
        $this->authorize('risque.creer');
        abort_unless(Papa::query()->visibleTo($request->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');

        $data = $request->validate([
            'code' => 'required|string|max:40|unique:risques,code',
            'libelle' => 'required|string|max:400',
            'description' => 'nullable|string',
            'categorie' => 'required|in:strategique,operationnel,financier,juridique,reputationnel,securitaire,naturel,autre',
            'probabilite' => 'required|in:tres_faible,faible,moyenne,elevee,tres_elevee',
            'impact' => 'required|in:negligeable,mineur,modere,majeur,catastrophique',
            'mesures_mitigation' => 'nullable|string',
            'plan_contingence' => 'nullable|string',
            'responsable_id' => 'nullable|exists:users,id',
            'date_echeance_traitement' => 'nullable|date',
        ]);

        if (! empty($data['responsable_id'])) {
            $responsableVisible = app(UserScopeResolver::class)
                ->applyToQuery(User::query()->whereKey($data['responsable_id']), $request->user(), [
                    'departement' => 'departement_id',
                    'direction' => 'direction_id',
                    'service' => 'service_id',
                ])
                ->exists();

            if (! $responsableVisible) {
                return back()->withErrors(['responsable_id' => 'Responsable hors perimetre.'])->withInput();
            }
        }

        $data['papa_id'] = $papa->id;
        $data['entite_type'] = Papa::class;
        $data['entite_id'] = $papa->id;
        $data['created_by'] = $request->user()->id;
        $data['statut'] = 'identifie';

        $risque = new Risque($data);
        $data['score_risque'] = $risque->calculerScore();
        $data['niveau_risque'] = $risque->calculerNiveau();

        Risque::create($data);

        return redirect()
            ->route('risques.index', $papa)
            ->with('success', 'Risque enregistre.');
    }

    public function edit(Papa $papa, Risque $risque)
    {
        $this->authorize('risque.modifier');
        abort_unless(Papa::query()->visibleTo(request()->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');
        abort_if($risque->papa_id !== $papa->id, 404);
        abort_unless($risque->canBeAccessedBy(request()->user()), 403);

        $responsables = app(UserScopeResolver::class)
            ->applyToQuery(User::actif()->orderBy('name'), request()->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get(['id', 'name', 'prenom']);
        $scopeLabel = request()->user()->scopeLabel();

        return view('risques.edit', compact('papa', 'risque', 'responsables', 'scopeLabel'));
    }

    public function update(Request $request, Papa $papa, Risque $risque)
    {
        $this->authorize('risque.modifier');
        abort_unless(Papa::query()->visibleTo($request->user())->whereKey($papa->id)->exists(), 403);
        abort_if(! $papa->estEditable(), 403, 'Ce PAPA est verrouille.');
        abort_if($risque->papa_id !== $papa->id, 404);
        abort_unless($risque->canBeAccessedBy($request->user()), 403);

        $data = $request->validate([
            'libelle' => 'required|string|max:400',
            'description' => 'nullable|string',
            'categorie' => 'required|in:strategique,operationnel,financier,juridique,reputationnel,securitaire,naturel,autre',
            'probabilite' => 'required|in:tres_faible,faible,moyenne,elevee,tres_elevee',
            'impact' => 'required|in:negligeable,mineur,modere,majeur,catastrophique',
            'statut' => 'required|in:identifie,en_traitement,residu,clos',
            'mesures_mitigation' => 'nullable|string',
            'plan_contingence' => 'nullable|string',
            'responsable_id' => 'nullable|exists:users,id',
            'date_echeance_traitement' => 'nullable|date',
            'date_derniere_revue' => 'nullable|date',
        ]);

        if (! empty($data['responsable_id'])) {
            $responsableVisible = app(UserScopeResolver::class)
                ->applyToQuery(User::query()->whereKey($data['responsable_id']), $request->user(), [
                    'departement' => 'departement_id',
                    'direction' => 'direction_id',
                    'service' => 'service_id',
                ])
                ->exists();

            if (! $responsableVisible) {
                return back()->withErrors(['responsable_id' => 'Responsable hors perimetre.'])->withInput();
            }
        }

        $risque->fill($data);
        $data['score_risque'] = $risque->calculerScore();
        $data['niveau_risque'] = $risque->calculerNiveau();

        $risque->update($data);

        return redirect()
            ->route('risques.index', $papa)
            ->with('success', 'Risque mis a jour.');
    }

    public function destroy(Papa $papa, Risque $risque)
    {
        $this->authorize('risque.supprimer');
        abort_unless(Papa::query()->visibleTo(request()->user())->whereKey($papa->id)->exists(), 403);
        abort_if($risque->papa_id !== $papa->id, 404);
        abort_unless($risque->canBeAccessedBy(request()->user()), 403);

        $risque->delete();

        return back()->with('success', 'Risque supprime.');
    }
}
