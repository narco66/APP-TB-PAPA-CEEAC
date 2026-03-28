<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePapaRequest;
use App\Http\Requests\UpdatePapaRequest;
use App\Models\Papa;
use App\Services\PapaService;
use Illuminate\Http\Request;

class PapaController extends Controller
{
    public function __construct(private PapaService $papaService) {}

    public function index(Request $request)
    {
        $this->authorize('papa.voir');

        $query = Papa::query()->orderByDesc('annee');

        if (!$request->user()->can('papa.voir_archive')) {
            $query->where('statut', '!=', 'archive');
        }

        $papas = $query->paginate(15);

        return view('papas.index', compact('papas'));
    }

    public function create()
    {
        $this->authorize('papa.creer');
        return view('papas.create');
    }

    public function store(StorePapaRequest $request)
    {
        $papa = Papa::create(array_merge($request->validated(), [
            'created_by' => $request->user()->id,
        ]));

        return redirect()
            ->route('papas.show', $papa)
            ->with('success', "PAPA {$papa->code} créé avec succès.");
    }

    public function show(Papa $papa)
    {
        $this->authorize('voir', $papa);

        $papa->load([
            'actionsPrioritaires.departement',
            'actionsPrioritaires.objectifsImmediat.resultatsAttendus',
            'budgets.partenaire',
            'creePar',
            'validePar',
        ]);

        return view('papas.show', compact('papa'));
    }

    public function edit(Papa $papa)
    {
        $this->authorize('modifier', $papa);
        return view('papas.edit', compact('papa'));
    }

    public function update(UpdatePapaRequest $request, Papa $papa)
    {
        $papa->update($request->validated());

        return redirect()
            ->route('papas.show', $papa)
            ->with('success', "PAPA {$papa->code} mis à jour.");
    }

    public function destroy(Papa $papa)
    {
        $this->authorize('supprimer', $papa);
        $papa->delete();

        return redirect()
            ->route('papas.index')
            ->with('success', "PAPA supprimé.");
    }

    public function soumettre(Request $request, Papa $papa)
    {
        $this->authorize('soumettre', $papa);

        $request->validate(['commentaire' => 'nullable|string|max:1000']);

        $this->papaService->soumettre($papa, $request->user(), $request->commentaire ?? '');

        return redirect()
            ->route('papas.show', $papa)
            ->with('success', "PAPA {$papa->code} soumis pour validation.");
    }

    public function valider(Request $request, Papa $papa)
    {
        $this->authorize('valider', $papa);

        $request->validate(['commentaire' => 'nullable|string|max:1000']);

        $this->papaService->valider($papa, $request->user(), $request->commentaire ?? '');

        return redirect()
            ->route('papas.show', $papa)
            ->with('success', "PAPA {$papa->code} validé.");
    }

    public function rejeter(Request $request, Papa $papa)
    {
        $this->authorize('valider', $papa);

        $request->validate(['motif' => 'required|string|max:1000']);

        $this->papaService->rejeter($papa, $request->user(), $request->motif);

        return redirect()
            ->route('papas.show', $papa)
            ->with('success', "PAPA {$papa->code} rejeté. Motif notifié.");
    }

    public function archiver(Request $request, Papa $papa)
    {
        $this->authorize('archiver', $papa);

        $request->validate(['motif_archivage' => 'nullable|string|max:1000']);

        $this->papaService->archiver($papa, $request->user(), $request->motif_archivage ?? '');

        return redirect()
            ->route('papas.show', $papa)
            ->with('success', "PAPA {$papa->code} archivé.");
    }

    public function cloner(Request $request, Papa $papa)
    {
        $this->authorize('cloner', $papa);

        $request->validate(['annee_nouvelle' => 'required|integer|min:2024|max:2050']);

        $nouveau = $this->papaService->cloner($papa, (int)$request->annee_nouvelle, $request->user());

        return redirect()
            ->route('papas.show', $nouveau)
            ->with('success', "PAPA {$nouveau->code} créé par clonage du PAPA {$papa->code}.");
    }

    public function recalculer(Papa $papa)
    {
        $this->authorize('modifier', $papa);
        $this->papaService->recalculerTaux($papa);

        return redirect()
            ->route('papas.show', $papa)
            ->with('success', 'Taux de réalisation recalculés.');
    }
}
