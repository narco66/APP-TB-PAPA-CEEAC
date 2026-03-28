<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActiviteRequest;
use App\Http\Requests\UpdateAvancementActiviteRequest;
use App\Models\Activite;
use App\Models\Direction;
use App\Models\ResultatAttendu;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('activite.voir');
        $user = $request->user();

        $query = Activite::with(['direction', 'resultatAttendu', 'responsable'])
            ->orderBy('date_fin_prevue');

        // Filtrage périmètre direction si pas vision transversale
        if (!$user->can('activite.voir_toutes_directions')) {
            $query->where('direction_id', $user->direction_id);
        }

        // Filtres optionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('direction_id') && $user->can('activite.voir_toutes_directions')) {
            $query->where('direction_id', $request->direction_id);
        }
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        if ($request->boolean('en_retard')) {
            $query->enRetard();
        }

        $activites  = $query->paginate(20)->withQueryString();
        $directions = Direction::actif()->orderBy('libelle')->get();

        return view('activites.index', compact('activites', 'directions'));
    }

    public function create(Request $request)
    {
        $this->authorize('activite.creer');

        $resultatsAttendus = ResultatAttendu::with('objectifImmediats.actionPrioritaire.papa')
            ->orderBy('code')
            ->get();
        $directions = Direction::actif()->orderBy('libelle')->get();
        $users      = User::actif()->orderBy('name')->get();

        return view('activites.create', compact('resultatsAttendus', 'directions', 'users'));
    }

    public function store(StoreActiviteRequest $request)
    {
        $activite = Activite::create(array_merge($request->validated(), [
            'created_by' => $request->user()->id,
        ]));

        return redirect()
            ->route('activites.show', $activite)
            ->with('success', "Activité {$activite->code} créée.");
    }

    public function show(Activite $activite)
    {
        $this->authorize('voir', $activite);

        $activite->load([
            'direction.departement',
            'service',
            'resultatAttendu.objectifImmediats.actionPrioritaire.papa',
            'responsable',
            'pointFocal',
            'taches',
            'jalons',
            'budgets.partenaire',
            'engagements',
            'documents.categorie',
            'alertes',
        ]);

        return view('activites.show', compact('activite'));
    }

    public function edit(Activite $activite)
    {
        $this->authorize('modifier', $activite);

        $resultatsAttendus = ResultatAttendu::orderBy('code')->get();
        $directions = Direction::actif()->orderBy('libelle')->get();
        $services   = Service::actif()->orderBy('libelle')->get();
        $users      = User::actif()->orderBy('name')->get();

        return view('activites.edit', compact('activite', 'resultatsAttendus', 'directions', 'services', 'users'));
    }

    public function update(Request $request, Activite $activite)
    {
        $this->authorize('modifier', $activite);

        $data = $request->validate([
            'libelle'           => 'required|string|max:500',
            'description'       => 'nullable|string',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue'   => 'nullable|date|after_or_equal:date_debut_prevue',
            'responsable_id'    => 'nullable|exists:users,id',
            'point_focal_id'    => 'nullable|exists:users,id',
            'priorite'          => 'required|in:critique,haute,normale,basse',
            'budget_prevu'      => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
        ]);

        $activite->update($data);

        return redirect()
            ->route('activites.show', $activite)
            ->with('success', 'Activité mise à jour.');
    }

    public function mettreAJourAvancement(UpdateAvancementActiviteRequest $request, Activite $activite)
    {
        $activite->update($request->validated());

        return redirect()
            ->route('activites.show', $activite)
            ->with('success', 'Avancement mis à jour.');
    }

    public function destroy(Activite $activite)
    {
        $this->authorize('supprimer', $activite);
        $activite->delete();

        return redirect()
            ->route('activites.index')
            ->with('success', 'Activité supprimée.');
    }

    public function gantt(Request $request)
    {
        $this->authorize('activite.voir');
        $user = $request->user();

        $query = Activite::with(['direction', 'predecesseurs.predecesseur'])
            ->whereNotNull('date_debut_prevue')
            ->orderBy('date_debut_prevue');

        if (!$user->can('activite.voir_toutes_directions')) {
            $query->where('direction_id', $user->direction_id);
        }

        $activites = $query->get();

        // Préparer les données pour DHTMLX Gantt
        $taches = $activites->map(fn($a) => $a->toGanttTask())->values()->toArray();

        $liens = $activites->flatMap(function ($activite) {
            return $activite->predecesseurs->map(fn($dep) => [
                'id'     => $dep->id,
                'source' => $dep->activite_predecesseur_id,
                'target' => $dep->activite_id,
                'type'   => '0',
            ]);
        })->values()->toArray();

        $ganttData = ['data' => $taches, 'links' => $liens];

        return view('activites.gantt', compact('ganttData'));
    }
}
