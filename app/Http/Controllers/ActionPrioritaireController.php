<?php

namespace App\Http\Controllers;

use App\Models\ActionPrioritaire;
use App\Models\Departement;
use App\Models\Papa;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;

class ActionPrioritaireController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('papa.voir');
        $user = $request->user();

        $query = ActionPrioritaire::with(['papa', 'departement'])
            ->visibleTo($user)
            ->orderBy('ordre');

        if ($request->filled('papa_id')) {
            $query->where('papa_id', $request->papa_id);
        }
        if ($request->filled('qualification')) {
            $query->where('qualification', $request->qualification);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $actions = $query->paginate(20)->withQueryString();
        $papas = Papa::query()->visibleTo($user)->orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $papaCourante = $request->filled('papa_id')
            ? Papa::query()->visibleTo($user)->find($request->papa_id)
            : null;
        $scopeLabel = $user->scopeLabel();

        return view('actions_prioritaires.index', compact('actions', 'papas', 'papaCourante', 'scopeLabel'));
    }

    public function printIndex(Request $request)
    {
        $this->authorize('papa.voir');
        $user = $request->user();

        $query = ActionPrioritaire::with(['papa', 'departement'])
            ->visibleTo($user)
            ->orderBy('ordre');

        if ($request->filled('papa_id')) {
            $query->where('papa_id', $request->papa_id);
        }
        if ($request->filled('qualification')) {
            $query->where('qualification', $request->qualification);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $actions = $query->get();
        $papaCourante = $request->filled('papa_id')
            ? Papa::query()->visibleTo($user)->find($request->papa_id)
            : null;

        return view('actions_prioritaires.print-index', [
            'actions' => $actions,
            'papaCourante' => $papaCourante,
            'scopeLabel' => $user->scopeLabel(),
            'filters' => $request->only(['papa_id', 'qualification', 'statut']),
            'printedAt' => now(),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('papa.modifier');

        $user = $request->user();
        $papas = Papa::query()->visibleTo($user)->orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $departements = app(UserScopeResolver::class)
            ->applyToQuery(Departement::actif()->orderBy('libelle'), $user, [
                'departement' => 'id',
                'direction' => null,
                'service' => null,
            ])
            ->get();

        $papaId = $request->integer('papa_id') ?: null;

        if ($papaId) {
            abort_unless(Papa::query()->visibleTo($user)->whereKey($papaId)->exists(), 403);
        }

        $scopeLabel = $user->scopeLabel();

        return view('actions_prioritaires.create', compact('papas', 'departements', 'papaId', 'scopeLabel'));
    }

    public function store(Request $request)
    {
        $this->authorize('papa.modifier');

        $data = $request->validate([
            'papa_id' => 'required|exists:papas,id',
            'departement_id' => 'nullable|exists:departements,id',
            'code' => 'required|string|max:50|unique:actions_prioritaires,code',
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string',
            'qualification' => 'required|in:technique,appui,transversal',
            'priorite' => 'required|in:critique,haute,normale,basse',
            'ordre' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();

        if (! Papa::query()->visibleTo($user)->whereKey($data['papa_id'])->exists()) {
            return back()->withErrors(['papa_id' => 'PAPA hors perimetre.'])->withInput();
        }

        if (! empty($data['departement_id'])) {
            $departementVisible = app(UserScopeResolver::class)
                ->applyToQuery(Departement::query()->whereKey($data['departement_id']), $user, [
                    'departement' => 'id',
                    'direction' => null,
                    'service' => null,
                ])
                ->exists();

            if (! $departementVisible) {
                return back()->withErrors(['departement_id' => 'Departement hors perimetre.'])->withInput();
            }
        }

        $data['created_by'] = $user->id;
        $data['statut'] = 'planifie';

        $ap = ActionPrioritaire::create($data);

        return redirect()
            ->route('actions-prioritaires.show', $ap)
            ->with('success', "Action prioritaire {$ap->code} creee.");
    }

    public function show(ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.voir');
        $user = auth()->user();

        abort_unless($actionsPrioritaire->canBeAccessedBy($user), 403);

        $actionsPrioritaire->load([
            'papa',
            'departement',
            'creePar',
            'objectifsImmediat' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('ordre')
                ->with([
                    'resultatsAttendus' => fn ($resultatQuery) => $resultatQuery
                        ->visibleTo($user)
                        ->orderBy('ordre')
                        ->with([
                            'activites' => fn ($activiteQuery) => $activiteQuery
                                ->visibleTo($user)
                                ->with('direction'),
                        ]),
                ]),
            'indicateurs' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('code'),
        ]);

        $scopeLabel = $user->scopeLabel();

        return view('actions_prioritaires.show', ['ap' => $actionsPrioritaire, 'scopeLabel' => $scopeLabel]);
    }

    public function print(ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.voir');
        $user = auth()->user();

        abort_unless($actionsPrioritaire->canBeAccessedBy($user), 403);

        $actionsPrioritaire->load([
            'papa',
            'departement',
            'creePar',
            'objectifsImmediat' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('ordre')
                ->with([
                    'resultatsAttendus' => fn ($resultatQuery) => $resultatQuery
                        ->visibleTo($user)
                        ->orderBy('ordre')
                        ->with([
                            'activites' => fn ($activiteQuery) => $activiteQuery
                                ->visibleTo($user)
                                ->with('direction'),
                        ]),
                ]),
            'indicateurs' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('code'),
        ]);

        return view('actions_prioritaires.print', [
            'ap' => $actionsPrioritaire,
            'scopeLabel' => $user->scopeLabel(),
            'printedAt' => now(),
        ]);
    }

    public function edit(ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.modifier');
        $user = request()->user();

        abort_unless($actionsPrioritaire->canBeAccessedBy($user), 403);
        abort_if(! $actionsPrioritaire->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $departements = app(UserScopeResolver::class)
            ->applyToQuery(Departement::actif()->orderBy('libelle'), $user, [
                'departement' => 'id',
                'direction' => null,
                'service' => null,
            ])
            ->get();
        $scopeLabel = $user->scopeLabel();

        return view('actions_prioritaires.edit', [
            'ap' => $actionsPrioritaire,
            'departements' => $departements,
            'scopeLabel' => $scopeLabel,
        ]);
    }

    public function update(Request $request, ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.modifier');
        abort_unless($actionsPrioritaire->canBeAccessedBy($request->user()), 403);
        abort_if(! $actionsPrioritaire->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $data = $request->validate([
            'departement_id' => 'nullable|exists:departements,id',
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string',
            'qualification' => 'required|in:technique,appui,transversal',
            'priorite' => 'required|in:critique,haute,normale,basse',
            'statut' => 'required|in:planifie,en_cours,suspendu,termine,abandonne',
            'ordre' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:2000',
        ]);

        if (! empty($data['departement_id'])) {
            $departementVisible = app(UserScopeResolver::class)
                ->applyToQuery(Departement::query()->whereKey($data['departement_id']), $request->user(), [
                    'departement' => 'id',
                    'direction' => null,
                    'service' => null,
                ])
                ->exists();

            if (! $departementVisible) {
                return back()->withErrors(['departement_id' => 'Departement hors perimetre.'])->withInput();
            }
        }

        $actionsPrioritaire->update($data);

        return redirect()
            ->route('actions-prioritaires.show', $actionsPrioritaire)
            ->with('success', 'Action prioritaire mise a jour.');
    }

    public function destroy(ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.supprimer');
        abort_unless($actionsPrioritaire->canBeAccessedBy(request()->user()), 403);
        abort_if(! $actionsPrioritaire->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $actionsPrioritaire->delete();

        return redirect()
            ->route('actions-prioritaires.index')
            ->with('success', 'Action prioritaire supprimee.');
    }
}
