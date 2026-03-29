<?php

namespace App\Http\Controllers;

use App\Models\ActionPrioritaire;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\User;
use Illuminate\Http\Request;

class ObjectifImmediatsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('papa.voir');

        $query = ObjectifImmediats::with(['actionPrioritaire.papa', 'responsable'])
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
        $papas     = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $actions   = $request->filled('papa_id')
            ? ActionPrioritaire::where('papa_id', $request->papa_id)->orderBy('ordre')->get(['id', 'code', 'libelle'])
            : collect();

        return view('objectifs_immediats.index', compact('objectifs', 'papas', 'actions'));
    }

    public function create(Request $request)
    {
        $this->authorize('papa.modifier');

        $ap    = $request->filled('action_prioritaire_id')
                    ? ActionPrioritaire::findOrFail($request->action_prioritaire_id)
                    : null;

        if ($ap) {
            abort_if(!$ap->estEditable(), 403, 'Le PAPA associé est verrouillé.');
        }

        $users = User::actif()->orderBy('name')->get(['id', 'name', 'prenom']);
        $papas = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $actionsPrioritaires = $ap
            ? ActionPrioritaire::where('papa_id', $ap->papa_id)->orderBy('ordre')->get(['id', 'code', 'libelle'])
            : collect();

        return view('objectifs_immediats.create', compact('ap', 'users', 'papas', 'actionsPrioritaires'));
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
