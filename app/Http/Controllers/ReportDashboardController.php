<?php

namespace App\Http\Controllers;

use App\Models\GeneratedReport;
use App\Models\Papa;
use App\Models\Rapport;
use App\Models\ReportDefinition;
use App\Services\Reports\ReportGenerationService;
use Illuminate\Http\Request;

class ReportDashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            $request->user()->can('rapport.dashboard.voir') || $request->user()->can('rapport.voir'),
            403
        );

        $user = $request->user();

        $definitions = ReportDefinition::query()
            ->where('actif', true)
            ->orderBy('categorie')
            ->orderBy('libelle')
            ->get()
            ->groupBy('categorie');

        $recentReports = GeneratedReport::query()
            ->with(['definition', 'user', 'papa'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        $recentNarrativeReports = Rapport::query()
            ->with(['papa', 'redigePar'])
            ->visibleTo($user)
            ->latest()
            ->take(5)
            ->get();

        $papas = Papa::query()
            ->orderByDesc('annee')
            ->get(['id', 'code', 'libelle']);

        $stats = [
            'definitions' => ReportDefinition::query()->where('actif', true)->count(),
            'generated' => GeneratedReport::query()->where('user_id', $user->id)->where('statut', 'generated')->count(),
            'queued' => GeneratedReport::query()->where('user_id', $user->id)->whereIn('statut', ['queued', 'processing'])->count(),
            'failed' => GeneratedReport::query()->where('user_id', $user->id)->where('statut', 'failed')->count(),
        ];

        return view('reports.dashboard', compact('definitions', 'recentReports', 'recentNarrativeReports', 'stats', 'papas'));
    }

    public function generate(Request $request, ReportDefinition $definition, ReportGenerationService $reportGenerationService)
    {
        abort_unless(
            $request->user()->can('rapport.exporter') || $request->user()->can('rapport.voir'),
            403
        );

        $filters = $request->validate([
            'papa_id' => 'nullable|exists:papas,id',
            'format' => 'required|string|in:pdf,xlsx,csv',
        ]);

        $generatedReport = $definition->is_async_recommended
            ? $reportGenerationService->queue($definition, $request->user(), $filters)
            : $reportGenerationService->generate($definition, $request->user(), $filters);

        return redirect()
            ->route('reports.library.show', $generatedReport)
            ->with('success', $definition->is_async_recommended
                ? 'Rapport mis en file de génération et ajouté à la bibliothèque.'
                : 'Rapport généré et ajouté à la bibliothèque.');
    }
}
