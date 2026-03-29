<?php

namespace App\Http\Controllers;

use App\Models\GeneratedReport;
use App\Models\ReportDefinition;
use App\Models\ReportDownloadLog;
use App\Services\AuditService;
use App\Services\Reports\ReportGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneratedReportController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            $request->user()->can('rapport.bibliotheque.voir') || $request->user()->can('rapport.voir'),
            403
        );

        $user = $request->user();

        $query = GeneratedReport::query()
            ->with(['definition', 'user', 'papa'])
            ->where('user_id', $user->id)
            ->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        if ($request->filled('format')) {
            $query->where('format', $request->string('format'));
        }

        if ($request->filled('definition_id')) {
            $query->where('report_definition_id', $request->integer('definition_id'));
        }

        $generatedReports = $query->paginate(20)->withQueryString();
        $definitions = ReportDefinition::query()->where('actif', true)->orderBy('libelle')->get();

        return view('reports.library.index', compact('generatedReports', 'definitions'));
    }

    public function show(Request $request, GeneratedReport $generatedReport)
    {
        abort_unless(
            $request->user()->can('rapport.bibliotheque.voir') || $request->user()->can('rapport.voir'),
            403
        );

        abort_unless($generatedReport->user_id === $request->user()->id, 403);

        $generatedReport->load(['definition', 'user', 'papa', 'downloadLogs.user']);

        return view('reports.library.show', compact('generatedReport'));
    }

    public function download(Request $request, GeneratedReport $generatedReport, AuditService $auditService)
    {
        abort_unless(
            $request->user()->can('rapport.bibliotheque.telecharger') || $request->user()->can('rapport.exporter'),
            403
        );

        abort_unless($generatedReport->user_id === $request->user()->id, 403);

        abort_unless($generatedReport->canBeDownloaded(), 404);

        $disk = Storage::disk($generatedReport->file_disk);
        abort_unless($disk->exists($generatedReport->file_path), 404);

        ReportDownloadLog::create([
            'generated_report_id' => $generatedReport->id,
            'user_id' => $request->user()->id,
            'downloaded_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $generatedReport->forceFill(['last_downloaded_at' => now()])->save();

        $auditService->enregistrer(
            module: 'reporting',
            eventType: 'report_downloaded',
            auditable: $generatedReport,
            acteur: $request->user(),
            action: 'telecharger',
            description: "Téléchargement du rapport généré {$generatedReport->titre}.",
            niveau: 'info',
            papa: $generatedReport->papa
        );

        return $disk->download($generatedReport->file_path, $generatedReport->file_name ?: basename($generatedReport->file_path));
    }

    public function retry(Request $request, GeneratedReport $generatedReport, ReportGenerationService $reportGenerationService)
    {
        abort_unless(
            $request->user()->can('rapport.exporter') || $request->user()->can('rapport.bibliotheque.telecharger'),
            403
        );

        abort_unless($generatedReport->user_id === $request->user()->id, 403);

        abort_unless($generatedReport->definition, 404);

        $freshFilters = array_merge($generatedReport->filters ?? [], [
            'format' => $generatedReport->format,
        ]);

        $queuedReport = $reportGenerationService->queue($generatedReport->definition, $request->user(), $freshFilters);

        return redirect()
            ->route('reports.library.show', $queuedReport)
            ->with('success', 'Rapport relancé en génération asynchrone.');
    }
}
