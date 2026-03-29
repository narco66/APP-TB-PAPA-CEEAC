<?php

namespace App\Http\Controllers;

use App\Models\ActionPrioritaire;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\User;
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
            ->orderBy('code');

        if ($request->filled('papa_id')) {
            $query->whereHas(
                'objectifImmediats.actionPrioritaire',
                fn($q) => $q->where('papa_id', $request->papa_id)
            );
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

        $resultats   = $query->paginate(20)->withQueryString();
        $papas       = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $objectifs   = $request->filled('papa_id')
            ? ObjectifImmediats::whereHas(
                'actionPrioritaire',
                fn($q) => $q->where('papa_id', $request->papa_id)
              )->orderBy('code')->get(['id', 'code', 'libelle'])
            : collect();

        return view('resultats_attendus.index', compact('resultats', 'papas', 'objectifs'));
    }

    public function create(Request $request)
    {
        $this->authorize('papa.modifier');

        $oi = $request->filled('objectif_immediat_id')
            ? ObjectifImmediats::with('actionPrioritaire.papa')->findOrFail($request->objectif_immediat_id)
            : null;

        if ($oi) {
            abort_if(!$oi->actionPrioritaire?->estEditable(), 403, 'Le PAPA associé est verrouillé.');
        }

        $users = User::actif()->orderBy('name')->get(['id', 'name', 'prenom']);
        $papas = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $objectifsImmediats = $oi
            ? ObjectifImmediats::whereHas('actionPrioritaire', fn($q) => $q->where('papa_id', $oi->actionPrioritaire->papa_id))
                ->orderBy('code')->get(['id', 'code', 'libelle'])
            : collect();

        return view('resultats_attendus.create', compact('oi', 'users', 'papas', 'objectifsImmediats'));
    }

    public function store(Request $request)
    {
        $this->authorize('papa.modifier');

        $data = $request->validate([
            'objectif_immediat_id'  => 'required|exists:objectifs_immediats,id',
            'code'                  => 'required|string|max:50|unique:resultats_attendus,code',
            'libelle'               => 'required|string|max:500',
            'description'           => 'nullable|string',
            'type_resultat'         => 'required|in:output,outcome,impact',
            'annee_reference'       => 'nullable|integer|min:2020|max:2040',
            'ordre'                 => 'nullable|integer|min:1',
            'responsable_id'        => 'nullable|exists:users,id',
            'preuve_requise'        => 'boolean',
            'type_preuve_attendue'  => 'nullable|string|max:200',
            'notes'                 => 'nullable|string|max:2000',
        ]);

        $data['statut'] = 'planifie';

        $ra = ResultatAttendu::create($data);

        return redirect()
            ->route('resultats-attendus.show', $ra)
            ->with('success', "Résultat attendu {$ra->code} créé.");
    }

    public function show(ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.voir');

        $resultatsAttendu->load([
            'objectifImmediats.actionPrioritaire.papa',
            'responsable',
            'activites.direction',
            'indicateurs',
            'documents.categorie',
        ]);

        return view('resultats_attendus.show', ['ra' => $resultatsAttendu]);
    }

    public function edit(ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.modifier');

        abort_if(!$resultatsAttendu->objectifImmediats?->actionPrioritaire?->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $users = User::actif()->orderBy('name')->get(['id', 'name', 'prenom']);

        return view('resultats_attendus.edit', ['ra' => $resultatsAttendu, 'users' => $users]);
    }

    public function update(Request $request, ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.modifier');
        abort_if(!$resultatsAttendu->objectifImmediats?->actionPrioritaire?->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $data = $request->validate([
            'libelle'              => 'required|string|max:500',
            'description'         => 'nullable|string',
            'type_resultat'       => 'required|in:output,outcome,impact',
            'annee_reference'     => 'nullable|integer|min:2020|max:2040',
            'statut'              => 'required|in:planifie,en_cours,atteint,partiellement_atteint,non_atteint',
            'ordre'               => 'nullable|integer|min:1',
            'responsable_id'      => 'nullable|exists:users,id',
            'preuve_requise'      => 'boolean',
            'type_preuve_attendue' => 'nullable|string|max:200',
            'notes'               => 'nullable|string|max:2000',
        ]);

        // M11-F04 : si preuve requise et statut = atteint, vérifier qu'un document validé existe
        if (($data['statut'] === 'atteint' || $data['statut'] === 'partiellement_atteint')
            && ($resultatsAttendu->preuve_requise || ($data['preuve_requise'] ?? false))
        ) {
            $aDocumentValide = $resultatsAttendu->documents()
                ->where('statut', 'valide')
                ->exists();

            if (!$aDocumentValide) {
                return back()
                    ->withInput()
                    ->withErrors(['statut' => 'Ce résultat exige une preuve documentaire validée avant de pouvoir être marqué comme atteint. Veuillez déposer et faire valider un document justificatif.']);
            }
        }

        $resultatsAttendu->update($data);

        return redirect()
            ->route('resultats-attendus.show', $resultatsAttendu)
            ->with('success', 'Résultat attendu mis à jour.');
    }

    public function destroy(ResultatAttendu $resultatsAttendu)
    {
        $this->authorize('papa.supprimer');
        abort_if(!$resultatsAttendu->objectifImmediats?->actionPrioritaire?->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $resultatsAttendu->delete();

        return redirect()
            ->route('objectifs-immediats.show', $resultatsAttendu->objectif_immediat_id)
            ->with('success', 'Résultat attendu supprimé.');
    }
}
