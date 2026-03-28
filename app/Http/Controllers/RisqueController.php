<?php

namespace App\Http\Controllers;

use App\Models\Papa;
use App\Models\Risque;
use App\Models\User;
use Illuminate\Http\Request;

class RisqueController extends Controller
{
    public function index(Papa $papa)
    {
        $this->authorize('papa.voir');

        $risques = Risque::where('papa_id', $papa->id)
            ->with('responsable')
            ->orderByDesc('score_risque')
            ->get();

        // Matrice probabilité × impact : tableau 5×5
        $probabilites = ['tres_faible', 'faible', 'moyenne', 'elevee', 'tres_elevee'];
        $impacts      = ['negligeable', 'mineur', 'modere', 'majeur', 'catastrophique'];

        $matrice = [];
        foreach ($probabilites as $prob) {
            foreach ($impacts as $imp) {
                $matrice[$prob][$imp] = $risques->filter(
                    fn($r) => $r->probabilite === $prob && $r->impact === $imp
                );
            }
        }

        $stats = [
            'rouge'  => $risques->where('niveau_risque', 'rouge')->count(),
            'orange' => $risques->where('niveau_risque', 'orange')->count(),
            'jaune'  => $risques->where('niveau_risque', 'jaune')->count(),
            'vert'   => $risques->where('niveau_risque', 'vert')->count(),
        ];

        return view('risques.index', compact('papa', 'risques', 'matrice', 'probabilites', 'impacts', 'stats'));
    }

    public function create(Request $request, Papa $papa)
    {
        $this->authorize('papa.modifier');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');

        $responsables = User::actif()->orderBy('name')->get(['id', 'name', 'prenom']);

        return view('risques.create', compact('papa', 'responsables'));
    }

    public function store(Request $request, Papa $papa)
    {
        $this->authorize('papa.modifier');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');

        $data = $request->validate([
            'code'                       => 'required|string|max:40|unique:risques,code',
            'libelle'                    => 'required|string|max:400',
            'description'                => 'nullable|string',
            'categorie'                  => 'required|in:strategique,operationnel,financier,juridique,reputationnel,securitaire,naturel,autre',
            'probabilite'                => 'required|in:tres_faible,faible,moyenne,elevee,tres_elevee',
            'impact'                     => 'required|in:negligeable,mineur,modere,majeur,catastrophique',
            'mesures_mitigation'         => 'nullable|string',
            'plan_contingence'           => 'nullable|string',
            'responsable_id'             => 'nullable|exists:users,id',
            'date_echeance_traitement'   => 'nullable|date',
        ]);

        $data['papa_id']     = $papa->id;
        $data['entite_type'] = Papa::class;
        $data['entite_id']   = $papa->id;
        $data['created_by']  = $request->user()->id;
        $data['statut']      = 'identifie';

        // Calcul automatique score et niveau
        $risque = new Risque($data);
        $data['score_risque']  = $risque->calculerScore();
        $data['niveau_risque'] = $risque->calculerNiveau();

        Risque::create($data);

        return redirect()
            ->route('risques.index', $papa)
            ->with('success', 'Risque enregistré.');
    }

    public function edit(Papa $papa, Risque $risque)
    {
        $this->authorize('papa.modifier');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');
        abort_if($risque->papa_id !== $papa->id, 404);

        $responsables = User::actif()->orderBy('name')->get(['id', 'name', 'prenom']);

        return view('risques.edit', compact('papa', 'risque', 'responsables'));
    }

    public function update(Request $request, Papa $papa, Risque $risque)
    {
        $this->authorize('papa.modifier');
        abort_if(!$papa->estEditable(), 403, 'Ce PAPA est verrouillé.');
        abort_if($risque->papa_id !== $papa->id, 404);

        $data = $request->validate([
            'libelle'                    => 'required|string|max:400',
            'description'                => 'nullable|string',
            'categorie'                  => 'required|in:strategique,operationnel,financier,juridique,reputationnel,securitaire,naturel,autre',
            'probabilite'                => 'required|in:tres_faible,faible,moyenne,elevee,tres_elevee',
            'impact'                     => 'required|in:negligeable,mineur,modere,majeur,catastrophique',
            'statut'                     => 'required|in:identifie,en_traitement,residu,clos',
            'mesures_mitigation'         => 'nullable|string',
            'plan_contingence'           => 'nullable|string',
            'responsable_id'             => 'nullable|exists:users,id',
            'date_echeance_traitement'   => 'nullable|date',
            'date_derniere_revue'        => 'nullable|date',
        ]);

        $risque->fill($data);
        $data['score_risque']  = $risque->calculerScore();
        $data['niveau_risque'] = $risque->calculerNiveau();

        $risque->update($data);

        return redirect()
            ->route('risques.index', $papa)
            ->with('success', 'Risque mis à jour.');
    }

    public function destroy(Papa $papa, Risque $risque)
    {
        $this->authorize('papa.modifier');
        abort_if($risque->papa_id !== $papa->id, 404);

        $risque->delete();

        return back()->with('success', 'Risque supprimé.');
    }
}
