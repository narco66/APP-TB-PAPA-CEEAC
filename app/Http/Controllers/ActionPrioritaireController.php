<?php

namespace App\Http\Controllers;

use App\Models\ActionPrioritaire;
use App\Models\Departement;
use App\Models\Papa;
use Illuminate\Http\Request;

class ActionPrioritaireController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('papa.voir');

        $query = ActionPrioritaire::with(['papa', 'departement'])
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

        $actions      = $query->paginate(20)->withQueryString();
        $papas        = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $papaCourante = $request->filled('papa_id') ? Papa::find($request->papa_id) : null;

        return view('actions_prioritaires.index', compact('actions', 'papas', 'papaCourante'));
    }

    public function create(Request $request)
    {
        $this->authorize('papa.modifier');

        $papas       = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);
        $departements = Departement::actif()->orderBy('libelle')->get();
        $papaId      = $request->get('papa_id');

        return view('actions_prioritaires.create', compact('papas', 'departements', 'papaId'));
    }

    public function store(Request $request)
    {
        $this->authorize('papa.modifier');

        $data = $request->validate([
            'papa_id'       => 'required|exists:papas,id',
            'departement_id' => 'nullable|exists:departements,id',
            'code'          => 'required|string|max:50|unique:actions_prioritaires,code',
            'libelle'       => 'required|string|max:500',
            'description'   => 'nullable|string',
            'qualification' => 'required|in:technique,appui,transversal',
            'priorite'      => 'required|in:critique,haute,normale,basse',
            'ordre'         => 'nullable|integer|min:1',
            'notes'         => 'nullable|string|max:2000',
        ]);

        $data['created_by'] = $request->user()->id;
        $data['statut']     = 'planifie';

        $ap = ActionPrioritaire::create($data);

        return redirect()
            ->route('actions-prioritaires.show', $ap)
            ->with('success', "Action prioritaire {$ap->code} créée.");
    }

    public function show(ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.voir');

        $actionsPrioritaire->load([
            'papa',
            'departement',
            'creePar',
            'objectifsImmediat.resultatsAttendus.activites',
            'indicateurs',
        ]);

        return view('actions_prioritaires.show', ['ap' => $actionsPrioritaire]);
    }

    public function edit(ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.modifier');

        abort_if(!$actionsPrioritaire->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $departements = Departement::actif()->orderBy('libelle')->get();

        return view('actions_prioritaires.edit', [
            'ap'          => $actionsPrioritaire,
            'departements' => $departements,
        ]);
    }

    public function update(Request $request, ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.modifier');
        abort_if(!$actionsPrioritaire->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $data = $request->validate([
            'departement_id' => 'nullable|exists:departements,id',
            'libelle'       => 'required|string|max:500',
            'description'   => 'nullable|string',
            'qualification' => 'required|in:technique,appui,transversal',
            'priorite'      => 'required|in:critique,haute,normale,basse',
            'statut'        => 'required|in:planifie,en_cours,suspendu,termine,abandonne',
            'ordre'         => 'nullable|integer|min:1',
            'notes'         => 'nullable|string|max:2000',
        ]);

        $actionsPrioritaire->update($data);

        return redirect()
            ->route('actions-prioritaires.show', $actionsPrioritaire)
            ->with('success', 'Action prioritaire mise à jour.');
    }

    public function destroy(ActionPrioritaire $actionsPrioritaire)
    {
        $this->authorize('papa.supprimer');
        abort_if(!$actionsPrioritaire->estEditable(), 403, 'Le PAPA associé est verrouillé.');

        $actionsPrioritaire->delete();

        return redirect()
            ->route('actions-prioritaires.index')
            ->with('success', 'Action prioritaire supprimée.');
    }
}
