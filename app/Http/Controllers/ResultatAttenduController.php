<?php

namespace App\Http\Controllers;

use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;

class ResultatAttenduController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('papa.voir');

        $query = ResultatAttendu::with([
                'objectifImmediats.actionPrioritaire.papa',
                'responsable',
            ])
            ->visibleTo($request->user())
            ->orderBy('code');

        if ($request->filled('papa_id')) {
            $query->whereHas('objectifImmediats.actionPrioritaire', fn($q) => $q->where('papa_id', $request->papa_id));
        }
        if ($request->filled('objectif_immediat_id')) {
            $query->where('objectif_immediat_id', $request->objectif_immediat_id);
        }
        if ($request->filled('type_resultat')) {
            $query->where('type_resultat', $request->type_resultat);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $resultats = $query->paginate(20)->withQueryString();
        $papas = Papa::query()->visibleTo($request->user())->orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $objectifs = $request->filled('papa_id')
            ? ObjectifImmediats::query()
                ->visibleTo($request->user())
                ->whereHas('actionPrioritaire', fn($q) => $q->where('papa_id', $request->papa_id))
                ->orderBy('code')
                ->get(['id', 'code', 'libelle'])
            : collect();
        $scopeLabel = $request->user()->scopeLabel();

        return view('resultats_attendus.index', compact('resultats', 'papas', 'objectifs', 'scopeLabel'));
    }

    public function create(Request $request)
    {
        $this->authorize('papa.modifier');

        $oi = $request->filled('objectif_immediat_id')
            ? ObjectifImmediats::with('actionPrioritaire.papa')->findOrFail($request->objectif_immediat_id)
            : null;

        if ($oi) {
            abort_unless($oi->canBeAccessedBy($request->user()), 403);
            abort_if(! $oi->actionPrioritaire?->estEditable(), 403, 'Le PAPA associe est verrouille.');
        }

        $users = app(UserScopeResolver::class)
            ->applyToQuery(User::actif()->orderBy('name'), $request->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get(['id', 'name', 'prenom']);
        $papas = Papa::query()->visibleTo($request->user())->orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $objectifsImmediats = $oi
            ? ObjectifImmediats::query()
                ->visibleTo($request->user())
                ->whereHas('actionPrioritaire', fn($q) => $q->where('papa_id', $oi->actionPrioritaire->papa_id))
                ->orderBy('code')
                ->get(['id', 'code', 'libelle'])
            : collect();
        $scopeLabel = $request->user()->scopeLabel();

        return view('resultats_attendus.create', compact('oi', 'users', 'papas', 'objectifsImmediats', 'scopeLabel'));
    }

    public function store(Request $request)
    {
        $this->authorize('papa.modifier');

        $data = $request->validate([
            'objectif_immediat_id' => 'required|exists:objectifs_immediats,id',
            'code' => 'required|string|max:50|unique:resultats_attendus,code',
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string',
            'type_resultat' => 'required|in:output,outcome,impact',
            'annee_reference' => 'nullable|integer|min:2020|max:2040',
            'ordre' => 'nullable|integer|min:1',
            'responsable_id' => 'nullable|exists:users,id',
            'preuve_requise' => 'boolean',
            'type_preuve_attendue' => 'nullable|string|max:200',
            'notes' => 'nullable|string|max:2000',
        ]);

        $objectif = ObjectifImmediats::query()->findOrFail($data['objectif_immediat_id']);
        abort_unless($objectif->canBeAccessedBy($request->user()), 403);

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
        $ra = ResultatAttendu::create($data);

        return redirect()
            ->route('resultats-attendus.show', $ra)
            ->with('success', "Resultat attendu {$ra->code} cree.");
    }

    public function show(ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.voir');
        $user = auth()->user();
        abort_unless($resultatsAttendu->canBeAccessedBy($user), 403);

        $resultatsAttendu->load([
            'objectifImmediats.actionPrioritaire.papa',
            'responsable',
            'activites' => fn ($query) => $query
                ->visibleTo($user)
                ->with('direction'),
            'indicateurs' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('code'),
            'documents' => fn ($query) => $query
                ->visibleTo($user)
                ->with('categorie'),
        ]);

        return view('resultats_attendus.show', [
            'ra' => $resultatsAttendu,
            'scopeLabel' => $user->scopeLabel(),
        ]);
    }

    public function print(ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.voir');
        $user = auth()->user();
        abort_unless($resultatsAttendu->canBeAccessedBy($user), 403);

        $resultatsAttendu->load([
            'objectifImmediats.actionPrioritaire.papa',
            'responsable',
            'activites' => fn ($query) => $query
                ->visibleTo($user)
                ->with('direction'),
            'indicateurs' => fn ($query) => $query
                ->visibleTo($user)
                ->orderBy('code'),
            'documents' => fn ($query) => $query
                ->visibleTo($user)
                ->with('categorie'),
        ]);

        return view('resultats_attendus.print', [
            'ra' => $resultatsAttendu,
            'scopeLabel' => $user->scopeLabel(),
            'printedAt' => now(),
        ]);
    }

    public function edit(ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.modifier');
        abort_unless($resultatsAttendu->canBeAccessedBy(request()->user()), 403);
        abort_if(! $resultatsAttendu->objectifImmediats?->actionPrioritaire?->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $users = app(UserScopeResolver::class)
            ->applyToQuery(User::actif()->orderBy('name'), request()->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get(['id', 'name', 'prenom']);
        $scopeLabel = request()->user()->scopeLabel();

        return view('resultats_attendus.edit', ['ra' => $resultatsAttendu, 'users' => $users, 'scopeLabel' => $scopeLabel]);
    }

    public function update(Request $request, ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.modifier');
        abort_unless($resultatsAttendu->canBeAccessedBy($request->user()), 403);
        abort_if(! $resultatsAttendu->objectifImmediats?->actionPrioritaire?->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $data = $request->validate([
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string',
            'type_resultat' => 'required|in:output,outcome,impact',
            'annee_reference' => 'nullable|integer|min:2020|max:2040',
            'statut' => 'required|in:planifie,en_cours,atteint,partiellement_atteint,non_atteint',
            'ordre' => 'nullable|integer|min:1',
            'responsable_id' => 'nullable|exists:users,id',
            'preuve_requise' => 'boolean',
            'type_preuve_attendue' => 'nullable|string|max:200',
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

        if (($data['statut'] === 'atteint' || $data['statut'] === 'partiellement_atteint')
            && ($resultatsAttendu->preuve_requise || ($data['preuve_requise'] ?? false))
        ) {
            $aDocumentValide = $resultatsAttendu->documents()
                ->where('statut', 'valide')
                ->exists();

            if (! $aDocumentValide) {
                return back()
                    ->withInput()
                    ->withErrors(['statut' => 'Ce resultat exige une preuve documentaire validee avant de pouvoir etre marque comme atteint.']);
            }
        }

        $resultatsAttendu->update($data);

        return redirect()
            ->route('resultats-attendus.show', $resultatsAttendu)
            ->with('success', 'Resultat attendu mis a jour.');
    }

    public function destroy(ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.supprimer');
        abort_unless($resultatsAttendu->canBeAccessedBy(request()->user()), 403);
        abort_if(! $resultatsAttendu->objectifImmediats?->actionPrioritaire?->estEditable(), 403, 'Le PAPA associe est verrouille.');

        $resultatsAttendu->delete();

        return redirect()
            ->route('objectifs-immediats.show', $resultatsAttendu->objectif_immediat_id)
            ->with('success', 'Resultat attendu supprime.');
    }
}
