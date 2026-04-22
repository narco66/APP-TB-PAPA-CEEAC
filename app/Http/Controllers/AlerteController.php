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
            ->visibleTo($user)
            ->orderBy('niveau', 'desc')
            ->orderByDesc('created_at');

        if ($request->filled('niveau')) {
            $query->where('niveau', $request->niveau);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $alertes = $query->paginate(20)->withQueryString();
        $scopeLabel = $user->scopeLabel();

        return view('alertes.index', compact('alertes', 'scopeLabel'));
    }

    public function show(Alerte $alerte)
    {
        $this->authorize('view', $alerte);
        $alerte->load(['papa', 'alertable', 'actionsCorrectives.responsable']);

        if ($alerte->statut === 'nouvelle') {
            $this->alerteService->marquerVue($alerte);
        }

        return view('alertes.show', compact('alerte'));
    }

    public function traiter(Request $request, Alerte $alerte)
    {
        $this->authorize('traiter', $alerte);

        $request->validate(['resolution' => 'required|string|max:2000']);

        $this->alerteService->resoudre($alerte, $request->user()->id, $request->resolution);

        return back()->with('success', 'Alerte resolue.');
    }

    public function escalader(Request $request, Alerte $alerte)
    {
        $this->authorize('escalader', $alerte);

        $request->validate(['destinataire_id' => 'required|exists:users,id']);

        $this->alerteService->escalader($alerte, $request->destinataire_id);

        return back()->with('success', 'Alerte escaladee.');
    }

    public function generer(Papa $papa)
    {
        $this->authorize('alerte.configurer');
        $alertes = $this->alerteService->genererAlertesPapa($papa);

        return redirect()
            ->route('alertes.index')
            ->with('success', "{$alertes->count()} alerte(s) generee(s) pour le PAPA {$papa->code}.");
    }
}