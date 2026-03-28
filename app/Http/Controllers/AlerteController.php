<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Papa;
use App\Services\AlerteService;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function __construct(private AlerteService $alerteService) {}

    public function index(Request $request)
    {
        $this->authorize('alerte.voir');
        $user = $request->user();

        $query = Alerte::with(['papa', 'destinataire', 'alertable', 'direction'])
            ->orderBy('niveau', 'desc')
            ->orderByDesc('created_at');

        // Filtrage par périmètre
        if (!$user->can('activite.voir_toutes_directions')) {
            $query->where(function ($q) use ($user) {
                $q->where('destinataire_id', $user->id)
                  ->orWhere('direction_id', $user->direction_id);
            });
        }

        if ($request->filled('niveau')) {
            $query->where('niveau', $request->niveau);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $alertes = $query->paginate(20)->withQueryString();

        return view('alertes.index', compact('alertes'));
    }

    public function show(Alerte $alerte)
    {
        $this->authorize('alerte.voir');
        $alerte->load(['papa', 'alertable', 'actionsCorrectives.responsable']);

        if ($alerte->statut === 'nouvelle') {
            $this->alerteService->marquerVue($alerte);
        }

        return view('alertes.show', compact('alerte'));
    }

    public function traiter(Request $request, Alerte $alerte)
    {
        $this->authorize('alerte.traiter');

        $request->validate(['resolution' => 'required|string|max:2000']);

        $this->alerteService->resoudre($alerte, $request->user()->id, $request->resolution);

        return back()->with('success', 'Alerte résolue.');
    }

    public function escalader(Request $request, Alerte $alerte)
    {
        $this->authorize('alerte.escalader');

        $request->validate(['destinataire_id' => 'required|exists:users,id']);

        $this->alerteService->escalader($alerte, $request->destinataire_id);

        return back()->with('success', 'Alerte escaladée.');
    }

    public function generer(Papa $papa)
    {
        $this->authorize('alerte.configurer');
        $alertes = $this->alerteService->genererAlertesPapa($papa);

        return redirect()
            ->route('alertes.index')
            ->with('success', "{$alertes->count()} alerte(s) générée(s) pour le PAPA {$papa->code}.");
    }
}
