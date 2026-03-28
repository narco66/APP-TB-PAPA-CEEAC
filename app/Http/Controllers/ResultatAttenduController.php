<?php

namespace App\Http\Controllers;

use App\Models\ObjectifImmediats;
use App\Models\ResultatAttendu;
use App\Models\User;
use Illuminate\Http\Request;

class ResultatAttenduController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('papa.modifier');

        $oi = ObjectifImmediats::with('actionPrioritaire.papa')
            ->findOrFail($request->get('objectif_immediat_id', 0));

        abort_if(!$oi->actionPrioritaire?->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $users = User::actif()->orderBy('name')->get(['id', 'name', 'prenom']);

        return view('resultats_attendus.create', compact('oi', 'users'));
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
            'statut'              => 'required|in:planifie,en_cours,atteint,partiellement_atteint,non_atteint',
            'ordre'               => 'nullable|integer|min:1',
            'responsable_id'      => 'nullable|exists:users,id',
            'preuve_requise'      => 'boolean',
            'type_preuve_attendue' => 'nullable|string|max:200',
            'notes'               => 'nullable|string|max:2000',
        ]);

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
