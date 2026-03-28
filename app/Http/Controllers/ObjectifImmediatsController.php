<?php

namespace App\Http\Controllers;

use App\Models\ActionPrioritaire;
use App\Models\ObjectifImmediats;
use App\Models\User;
use Illuminate\Http\Request;

class ObjectifImmediatsController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('papa.modifier');

        $ap = ActionPrioritaire::findOrFail($request->get('action_prioritaire_id', 0));
        abort_if(!$ap->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $users = User::actif()->orderBy('name')->get(['id', 'name', 'prenom']);

        return view('objectifs_immediats.create', compact('ap', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('papa.modifier');

        $data = $request->validate([
            'action_prioritaire_id' => 'required|exists:actions_prioritaires,id',
            'code'                  => 'required|string|max:50|unique:objectifs_immediats,code',
            'libelle'               => 'required|string|max:500',
            'description'           => 'nullable|string',
            'ordre'                 => 'nullable|integer|min:1',
            'responsable_id'        => 'nullable|exists:users,id',
            'notes'                 => 'nullable|string|max:2000',
        ]);

        $data['statut'] = 'planifie';

        $oi = ObjectifImmediats::create($data);

        return redirect()
            ->route('objectifs-immediats.show', $oi)
            ->with('success', "Objectif immédiat {$oi->code} créé.");
    }

    public function show(ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.voir');

        $objectifsImmediat->load([
            'actionPrioritaire.papa',
            'responsable',
            'resultatsAttendus.activites',
            'indicateurs',
        ]);

        return view('objectifs_immediats.show', ['oi' => $objectifsImmediat]);
    }

    public function edit(ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.modifier');

        abort_if(!$objectifsImmediat->actionPrioritaire?->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $users = User::actif()->orderBy('name')->get(['id', 'name', 'prenom']);

        return view('objectifs_immediats.edit', ['oi' => $objectifsImmediat, 'users' => $users]);
    }

    public function update(Request $request, ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.modifier');
        abort_if(!$objectifsImmediat->actionPrioritaire?->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $data = $request->validate([
            'libelle'        => 'required|string|max:500',
            'description'    => 'nullable|string',
            'statut'         => 'required|in:planifie,en_cours,atteint,partiellement_atteint,non_atteint',
            'ordre'          => 'nullable|integer|min:1',
            'responsable_id' => 'nullable|exists:users,id',
            'notes'          => 'nullable|string|max:2000',
        ]);

        $objectifsImmediat->update($data);

        return redirect()
            ->route('objectifs-immediats.show', $objectifsImmediat)
            ->with('success', 'Objectif immédiat mis à jour.');
    }

    public function destroy(ObjectifImmediats $objectifsImmediat)
    {
        $this->authorize('papa.supprimer');
        abort_if(!$objectifsImmediat->actionPrioritaire?->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $objectifsImmediat->delete();

        return redirect()
            ->route('actions-prioritaires.show', $objectifsImmediat->action_prioritaire_id)
            ->with('success', 'Objectif immédiat supprimé.');
    }
}
