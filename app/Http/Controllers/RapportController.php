<?php

namespace App\Http\Controllers;

use App\Exports\PapaExport;
use App\Jobs\RecalculerTauxPapaJob;
use App\Models\Papa;
use App\Models\Rapport;
use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RapportController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $this->authorize('viewAny', Rapport::class);

        $query = Rapport::with(['papa', 'direction', 'redigePar'])
            ->visibleTo($user)
            ->orderByDesc('created_at');

        if ($request->filled('papa_id')) {
            $query->where('papa_id', $request->papa_id);
        }
        if ($request->filled('type_rapport')) {
            $query->where('type_rapport', $request->type_rapport);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $rapports = $query->paginate(20);
        $papas = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);

        return view('rapports.index', compact('rapports', 'papas'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Rapport::class);

        $papa = Papa::findOrFail($request->get('papa_id', Papa::latest()->first()?->id));
        $papas = Papa::orderByDesc('annee')->get(['id', 'code', 'libelle']);

        return view('rapports.create', compact('papa', 'papas'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Rapport::class);

        $data = $request->validate([
            'papa_id' => 'required|exists:papas,id',
            'titre' => 'required|string|max:300',
            'type_rapport' => 'required|in:mensuel,trimestriel,semestriel,annuel,ponctuel',
            'periode_couverte' => 'required|string|max:100',
            'annee' => 'required|integer',
            'numero_periode' => 'nullable|integer',
            'faits_saillants' => 'nullable|string',
            'difficultes_rencontrees' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'perspectives' => 'nullable|string',
        ]);

        $papa = Papa::findOrFail($data['papa_id']);

        $data['taux_execution_physique'] = $papa->taux_execution_physique;
        $data['taux_execution_financiere'] = $papa->taux_execution_financiere;
        $data['statut'] = 'brouillon';
        $data['redige_par'] = $request->user()->id;

        $rapport = Rapport::create($data);

        return redirect()
            ->route('rapports.show', $rapport)
            ->with('success', 'Rapport cree.');
    }

    public function show(Rapport $rapport)
    {
        $this->authorize('view', $rapport);

        $rapport->load(['papa.actionsPrioritaires', 'direction', 'redigePar']);

        return view('rapports.show', compact('rapport'));
    }

    public function exportPdf(Rapport $rapport)
    {
        $this->authorize('export', $rapport);

        $rapport->load(['papa.actionsPrioritaires.objectifsImmediat.resultatsAttendus.activites', 'direction', 'redigePar']);

        $papa = $rapport->papa;
        $kpis = $this->dashboardService->kpisExecutif($papa, auth()->user());

        $pdf = Pdf::loadView('rapports.pdf', compact('rapport', 'papa', 'kpis'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isCssFloatEnabled' => true,
            ]);

        $filename = 'Rapport_' . $rapport->papa?->code . '_' . $rapport->periode_couverte . '.pdf';

        return $pdf->download(str_replace(' ', '_', $filename));
    }

    public function exportExcel(Papa $papa)
    {
        $this->authorize('rapport.exporter');
        $this->authorize('voir', $papa);

        RecalculerTauxPapaJob::dispatch($papa);

        $filename = 'PAPA_' . $papa->code . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PapaExport($papa->fresh()), $filename);
    }

    public function exportPapaPdf(Papa $papa)
    {
        $this->authorize('rapport.exporter');
        $this->authorize('voir', $papa);

        $papa->load(['actionsPrioritaires.objectifsImmediat.resultatsAttendus', 'budgets.partenaire']);
        $kpis = $this->dashboardService->kpisExecutif($papa, auth()->user());

        $pdf = Pdf::loadView('rapports.papa_pdf', compact('papa', 'kpis'))
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true]);

        $filename = 'Synthese_' . $papa->code . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function valider(Rapport $rapport)
    {
        $this->authorize('valider', $rapport);

        $rapport->update([
            'statut' => 'valide',
            'valide_par' => auth()->id(),
            'valide_le' => now(),
        ]);

        return back()->with('success', 'Rapport valide.');
    }

    public function publier(Rapport $rapport)
    {
        $this->authorize('publier', $rapport);

        $rapport->update(['statut' => 'publie', 'publie_le' => now()]);

        return back()->with('success', 'Rapport publie.');
    }
}
