<?php

namespace App\Http\Controllers;

use App\Models\ActionPrioritaire;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;

class ObjectifImmediatsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('papa.voir');

        $query = ObjectifImmediats::with(['actionPrioritaire.papa', 'responsable'])
            ->visibleTo($request->user())
            ->orderBy('code');

        if ($request->filled('papa_id')) {
            $query->whereHas('actionPrioritaire', fn($q) => $q->where('papa_id', $request->papa_id));
        }
        if ($request->filled('action_prioritaire_id')) {
            $query->where('action_prioritaire_id', $request->action_prioritaire_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $objectifs = $query->paginate(20)->withQueryString();
        $papas = Papa::query()->visibleTo($request->user())->orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $actions = $request->filled('papa_id')
            ? ActionPrioritaire::query()->visibleTo($request->user())->where('papa_id', $request->papa_id)->orderBy('ordre')->get(['id', 'code', 'libelle'])
            : collect();
        $scopeLabel = $request->user()->scopeLabel();

        return view('objectifs_immediats.index', compact('objectifs', 'papas', 'actions', 'scopeLabel'));
    }

    public function create(Request $request)
    {
        $this->authorize('papa.modifier');

        $ap = $request->filled('action_prioritaire_id')
            ? ActionPrioritaire::findOrFail($request->action_prioritaire_id)
            : null;

        if ($ap) {
            abort_unless($ap->canBeAccessedBy($request->user()), 403);
            abort_if(! $ap->estEditable(), 403, 'Le PAPA associe est verrouille.');
        }

        $users = app(UserScopeResolver::class)
            ->applyToQuery(User::actif()->orderBy('name'), $request->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get(['id', 'name', 'prenom']);
        $papas = Papa::query()->visibleTo($request->user())->orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $actionsPrioritaires = $ap
            ? ActionPrioritaire::query()->visibleTo($request->user())->where('papa_id', $ap->papa_id)->orderBy('ordre')->get(['id', 'code', 'libelle'])
            : collect();
        $scopeLabel = $request->user()->scopeLabel();

        return view('objectifs_immediats.create', compact('ap', 'users', 'papas', 'actionsPrioritaires', 'scopeLabel'));
    }

    public function store(Request $request)
    {
        $this->authorize('papa.modifier');

        $data = $request->validate([
            'action_prioritaire_id' => 'required|exists:actions_prioritaires,id',
            'code' => 'required|string|max:50|unique:objectifs_immediats,code',
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string',
            'ordre' => 'nullable|integer|min:1',
            'responsable_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:2000',
        ]);

        $action = ActionPrioritaire::findOrFail($data['action_prioritaire_id']);
        abort_unless($action->canBeAccessedBy($request->user()), 403);

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

        $data['statut'] = 'planifie';
        $oi = ObjectifImmediats::create($data);

        return redirect()
            ->route('objectifs-immediats.show', $oi)
            ->with('success', "Objectif immediat {$oi->code} cree.");
    }

    public function show(ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.voir');
        $user = auth()->user();
        abort_unless($objectifsImmediat->canBeAccessedBy($user), 403);

        $objectifsImmediat->load([
            'actionPrioritaire.papa',
            'responsable',
            'resultatsAttendus' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('ordre')
                ->with([
                    'activites' => fn ($activiteQuery) => $activiteQuery
                        ->visibleTo($user)
                        ->with('direction'),
                ]),
            'indicateurs' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('code'),
        ]);

        return view('objectifs_immediats.show', [
            'oi' => $objectifsImmediat,
            'scopeLabel' => $user->scopeLabel(),
        ]);
    }

    public function print(ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.voir');
        $user = auth()->user();
        abort_unless($objectifsImmediat->canBeAccessedBy($user), 403);

        $objectifsImmediat->load([
            'actionPrioritaire.papa',
            'responsable',
            'resultatsAttendus' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('ordre')
                ->with([
                    'activites' => fn ($activiteQuery) => $activiteQuery
                        ->visibleTo($user)
                        ->with('direction'),
                ]),
            'indicateurs' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('code'),
        ]);

        return view('objectifs_immediats.print', [
            'oi' => $objectifsImmediat,
            'scopeLabel' => $user->scopeLabel(),
            'printedAt' => now(),
        ]);
    }

    public function edit(ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.modifier');
        abort_unless($objectifsImmediat->canBeAccessedBy(request()->user()), 403);
        abort_if(! $objectifsImmediat->actionPrioritaire?->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $users = app(UserScopeResolver::class)
            ->applyToQuery(User::actif()->orderBy('name'), request()->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get(['id', 'name', 'prenom']);
        $scopeLabel = request()->user()->scopeLabel();

        return view('objectifs_immediats.edit', ['oi' => $objectifsImmediat, 'users' => $users, 'scopeLabel' => $scopeLabel]);
    }

    public function update(Request $request, ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.modifier');
        abort_unless($objectifsImmediat->canBeAccessedBy($request->user()), 403);
        abort_if(! $objectifsImmediat->actionPrioritaire?->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $data = $request->validate([
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string',
            'statut' => 'required|in:planifie,en_cours,atteint,partiellement_atteint,non_atteint',
            'ordre' => 'nullable|integer|min:1',
            'responsable_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:2000',
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

        $objectifsImmediat->update($data);

        return redirect()
            ->route('objectifs-immediats.show', $objectifsImmediat)
            ->with('success', 'Objectif immediat mis a jour.');
    }

    public function destroy(ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.supprimer');
        abort_unless($objectifsImmediat->canBeAccessedBy(request()->user()), 403);
        abort_if(! $objectifsImmediat->actionPrioritaire?->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $objectifsImmediat->delete();

        return redirect()
            ->route('actions-prioritaires.show', $objectifsImmediat->action_prioritaire_id)
            ->with('success', 'Objectif immediat supprime.');
    }
}
