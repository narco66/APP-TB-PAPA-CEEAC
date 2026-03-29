<?php

namespace App\Services\Reports;

use App\Exports\BudgetExport;
use App\Exports\GenericArrayExport;
use App\Jobs\Reports\GenerateReportJob;
use App\Models\Activite;
use App\Models\Decision;
use App\Models\GeneratedReport;
use App\Models\NotificationApp;
use App\Models\Papa;
use App\Models\ReportDefinition;
use App\Models\ResultatAttendu;
use App\Models\User;
use App\Services\AuditService;
use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class ReportGenerationService
{
    public function __construct(
        private DashboardService $dashboardService,
        private AuditService $auditService,
    ) {}

    public function generate(ReportDefinition $definition, User $user, array $filters): GeneratedReport
    {
        $format = strtolower((string) ($filters['format'] ?? 'pdf'));

        if (! in_array($format, $definition->formats ?? [], true)) {
            throw ValidationException::withMessages([
                'format' => "Le format {$format} n'est pas autorise pour ce modele.",
            ]);
        }

        $papa = isset($filters['papa_id']) ? Papa::findOrFail($filters['papa_id']) : null;
        $timestamp = now();
        $disk = Storage::disk('local');
        $baseName = $this->buildBaseName($definition, $papa, $timestamp, $format);
        $relativePath = 'reports/generated/' . $timestamp->format('Y/m') . '/' . $baseName;

        [$content, $mimeType] = $this->buildContent($definition, $papa, $format);

        $disk->put($relativePath, $content);

        $generatedReport = GeneratedReport::create([
            'report_definition_id' => $definition->id,
            'user_id' => $user->id,
            'papa_id' => $papa?->id,
            'titre' => $definition->libelle,
            'format' => $format,
            'statut' => 'generated',
            'file_disk' => 'local',
            'file_path' => $relativePath,
            'file_name' => basename($relativePath),
            'mime_type' => $mimeType,
            'file_size' => $disk->size($relativePath),
            'filters' => $filters,
            'contexte' => [
                'definition_code' => $definition->code,
                'definition_label' => $definition->libelle,
                'papa_code' => $papa?->code,
            ],
            'generated_at' => $timestamp,
        ]);

        $this->auditService->enregistrer(
            module: 'reporting',
            eventType: 'report_generated',
            auditable: $generatedReport,
            acteur: $user,
            action: 'generer',
            description: "Generation du rapport {$definition->libelle} en format {$format}.",
            niveau: 'info',
            papa: $papa
        );

        $this->notifyUser(
            user: $user,
            type: 'report_generated',
            title: 'Rapport genere',
            message: "Le rapport {$definition->libelle} a ete genere au format {$format}.",
            report: $generatedReport,
            level: 'succes'
        );

        return $generatedReport;
    }

    public function queue(ReportDefinition $definition, User $user, array $filters): GeneratedReport
    {
        $format = strtolower((string) ($filters['format'] ?? 'pdf'));

        if (! in_array($format, $definition->formats ?? [], true)) {
            throw ValidationException::withMessages([
                'format' => "Le format {$format} n'est pas autorise pour ce modele.",
            ]);
        }

        $papa = isset($filters['papa_id']) ? Papa::findOrFail($filters['papa_id']) : null;

        $generatedReport = GeneratedReport::create([
            'report_definition_id' => $definition->id,
            'user_id' => $user->id,
            'papa_id' => $papa?->id,
            'titre' => $definition->libelle,
            'format' => $format,
            'statut' => 'queued',
            'file_disk' => 'local',
            'filters' => $filters,
            'contexte' => [
                'definition_code' => $definition->code,
                'definition_label' => $definition->libelle,
                'papa_code' => $papa?->code,
            ],
        ]);

        GenerateReportJob::dispatch($generatedReport->id);

        $this->auditService->enregistrer(
            module: 'reporting',
            eventType: 'report_queued',
            auditable: $generatedReport,
            acteur: $user,
            action: 'mettre_en_file',
            description: "Mise en file du rapport {$definition->libelle} en format {$format}.",
            niveau: 'info',
            papa: $papa
        );

        $this->notifyUser(
            user: $user,
            type: 'report_queued',
            title: 'Rapport mis en file',
            message: "La generation du rapport {$definition->libelle} a ete mise en file.",
            report: $generatedReport,
            level: 'info'
        );

        return $generatedReport;
    }

    public function processQueuedReport(GeneratedReport $generatedReport): GeneratedReport
    {
        $definition = $generatedReport->definition;

        if (! $definition) {
            $generatedReport->update([
                'statut' => 'failed',
                'failed_at' => now(),
                'error_message' => 'Definition de rapport introuvable.',
            ]);

            return $generatedReport;
        }

        $generatedReport->update([
            'statut' => 'processing',
            'failed_at' => null,
            'error_message' => null,
        ]);

        try {
            $papa = $generatedReport->papa_id ? Papa::find($generatedReport->papa_id) : null;
            $timestamp = now();
            $disk = Storage::disk('local');
            $baseName = $this->buildBaseName($definition, $papa, $timestamp, $generatedReport->format);
            $relativePath = 'reports/generated/' . $timestamp->format('Y/m') . '/' . $baseName;

            [$content, $mimeType] = $this->buildContent($definition, $papa, $generatedReport->format);

            $disk->put($relativePath, $content);

            $generatedReport->update([
                'statut' => 'generated',
                'file_path' => $relativePath,
                'file_name' => basename($relativePath),
                'mime_type' => $mimeType,
                'file_size' => $disk->size($relativePath),
                'generated_at' => $timestamp,
            ]);

            $this->auditService->enregistrer(
                module: 'reporting',
                eventType: 'report_generated',
                auditable: $generatedReport->fresh(),
                acteur: $generatedReport->user,
                action: 'generer_async',
                description: "Generation asynchrone du rapport {$definition->libelle} en format {$generatedReport->format}.",
                niveau: 'info',
                papa: $papa
            );

            if ($generatedReport->user) {
                $this->notifyUser(
                    user: $generatedReport->user,
                    type: 'report_generated',
                    title: 'Rapport pret',
                    message: "Le rapport {$definition->libelle} est disponible au telechargement.",
                    report: $generatedReport->fresh(),
                    level: 'succes'
                );
            }
        } catch (\Throwable $exception) {
            $generatedReport->update([
                'statut' => 'failed',
                'failed_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);

            $this->auditService->enregistrer(
                module: 'reporting',
                eventType: 'report_failed',
                auditable: $generatedReport->fresh(),
                acteur: $generatedReport->user,
                action: 'echec_generation',
                description: "Echec de generation du rapport {$definition->libelle}: {$exception->getMessage()}",
                niveau: 'warning',
                papa: $generatedReport->papa
            );

            if ($generatedReport->user) {
                $this->notifyUser(
                    user: $generatedReport->user,
                    type: 'report_failed',
                    title: 'Echec de generation',
                    message: "La generation du rapport {$definition->libelle} a echoue: {$exception->getMessage()}",
                    report: $generatedReport->fresh(),
                    level: 'erreur'
                );
            }

            throw $exception;
        }

        return $generatedReport->fresh();
    }

    private function generateExecutiveGlobal(?Papa $papa, string $format): array
    {
        if (! $papa) {
            throw ValidationException::withMessages(['papa_id' => 'Le PAPA est obligatoire pour ce rapport.']);
        }

        if ($format !== 'pdf') {
            throw ValidationException::withMessages(['format' => 'Le rapport executif global est disponible en PDF pour ce lot.']);
        }

        $papa->load(['actionsPrioritaires', 'budgets.partenaire']);
        $kpis = $this->dashboardService->kpisExecutif($papa);
        $content = Pdf::loadView('pdf.reports.executive_global', compact('papa', 'kpis'))
            ->setPaper('a4', 'portrait')
            ->output();

        return [$content, 'application/pdf'];
    }

    private function generateFinancialGlobal(?Papa $papa, string $format): array
    {
        if (! $papa) {
            throw ValidationException::withMessages(['papa_id' => 'Le PAPA est obligatoire pour ce rapport.']);
        }

        return match ($format) {
            'pdf' => $this->generateFinancialGlobalPdf($papa),
            'xlsx' => [Excel::raw(new BudgetExport($papa), ExcelFormat::XLSX), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'csv' => [Excel::raw(new BudgetExport($papa), ExcelFormat::CSV), 'text/csv'],
            default => throw ValidationException::withMessages(['format' => 'Format non pris en charge pour le rapport budgetaire global.']),
        };
    }

    private function generateFinancialGlobalPdf(Papa $papa): array
    {
        $papa->load(['budgets.partenaire']);
        $budgets = $papa->budgets;
        $totaux = [
            'prevu' => $budgets->sum('montant_prevu'),
            'engage' => $budgets->sum('montant_engage'),
            'decaisse' => $budgets->sum('montant_decaisse'),
        ];

        $content = Pdf::loadView('pdf.reports.financial_global', compact('papa', 'budgets', 'totaux'))
            ->setPaper('a4', 'portrait')
            ->output();

        return [$content, 'application/pdf'];
    }

    private function generateOverdueActivities(?Papa $papa, string $format): array
    {
        if (! $papa) {
            throw ValidationException::withMessages(['papa_id' => 'Le PAPA est obligatoire pour ce rapport.']);
        }

        $activities = Activite::query()
            ->with(['direction', 'service', 'responsable'])
            ->whereHas('resultatAttendu.objectifImmediats.actionPrioritaire', fn ($query) => $query->where('papa_id', $papa->id))
            ->enRetard()
            ->orderBy('date_fin_prevue')
            ->get();

        $headings = ['Code', 'Activite', 'Direction', 'Service', 'Responsable', 'Statut', 'Fin prevue', 'Taux (%)'];
        $rows = $activities->map(fn (Activite $activite) => [
            $activite->code,
            $activite->libelle,
            $activite->direction?->libelle_court ?? '-',
            $activite->service?->libelle_court ?? '-',
            $activite->responsable?->nomComplet() ?? '-',
            $activite->statut,
            optional($activite->date_fin_prevue)->format('d/m/Y'),
            (float) $activite->taux_realisation,
        ])->all();

        return $this->renderTabularReport(
            format: $format,
            title: "Rapport des activites en retard - {$papa->code}",
            subtitle: "PAPA {$papa->code} · Genere le " . now()->format('d/m/Y H:i'),
            headings: $headings,
            rows: $rows,
            exportTitle: 'ActivitesEnRetard'
        );
    }

    private function generateRbmChainConsolidated(?Papa $papa, string $format): array
    {
        if (! $papa) {
            throw ValidationException::withMessages(['papa_id' => 'Le PAPA est obligatoire pour ce rapport.']);
        }

        $actions = \App\Models\ActionPrioritaire::query()
            ->with([
                'departement',
                'objectifsImmediat.resultatsAttendus.activites',
                'objectifsImmediat.resultatsAttendus.indicateurs',
            ])
            ->where('papa_id', $papa->id)
            ->orderBy('ordre')
            ->get();

        $headings = [
            'Action',
            'Departement',
            'Objectif immediat',
            'Resultat attendu',
            'Indicateurs',
            'Activites',
            'Statut resultat',
            'Taux resultat (%)',
            'Preuve requise',
        ];

        $rows = [];

        foreach ($actions as $action) {
            foreach ($action->objectifsImmediat as $objectif) {
                foreach ($objectif->resultatsAttendus as $resultat) {
                    $rows[] = [
                        $action->code . ' - ' . $action->libelle,
                        $action->departement?->code ?? '-',
                        $objectif->code . ' - ' . $objectif->libelle,
                        $resultat->code . ' - ' . $resultat->libelle,
                        (string) $resultat->indicateurs->count(),
                        (string) $resultat->activites->count(),
                        $resultat->statut,
                        (float) $resultat->taux_atteinte,
                        $resultat->preuve_requise ? 'Oui' : 'Non',
                    ];
                }
            }
        }

        return $this->renderTabularReport(
            format: $format,
            title: "Chaine consolidee des resultats - {$papa->code}",
            subtitle: "PAPA {$papa->code} · Genere le " . now()->format('d/m/Y H:i'),
            headings: $headings,
            rows: $rows,
            exportTitle: 'ChaineConsolideeRBM'
        );
    }

    private function generateGovernanceDecisions(?Papa $papa, string $format): array
    {
        if (! $papa) {
            throw ValidationException::withMessages(['papa_id' => 'Le PAPA est obligatoire pour ce rapport.']);
        }

        $decisions = Decision::query()
            ->with(['prisePar', 'valideePar'])
            ->where('papa_id', $papa->id)
            ->orderByDesc('date_decision')
            ->get();

        $headings = ['Reference', 'Titre', 'Type', 'Niveau', 'Statut', 'Date', 'Prise par', 'Validee par', 'Impact budgetaire'];
        $rows = $decisions->map(fn (Decision $decision) => [
            $decision->reference,
            $decision->titre,
            $decision->type_decision,
            $decision->niveau_decision,
            $decision->statut,
            optional($decision->date_decision)->format('d/m/Y'),
            $decision->prisePar?->nomComplet() ?? '-',
            $decision->valideePar?->nomComplet() ?? '-',
            $decision->impact_budgetaire,
        ])->all();

        return $this->renderTabularReport(
            format: $format,
            title: "Decisions et arbitrages - {$papa->code}",
            subtitle: "PAPA {$papa->code} · Genere le " . now()->format('d/m/Y H:i'),
            headings: $headings,
            rows: $rows,
            exportTitle: 'Decisions'
        );
    }

    private function generateGedMissingEvidence(?Papa $papa, string $format): array
    {
        if (! $papa) {
            throw ValidationException::withMessages(['papa_id' => 'Le PAPA est obligatoire pour ce rapport.']);
        }

        $results = ResultatAttendu::query()
            ->with(['objectifImmediats.actionPrioritaire', 'responsable'])
            ->whereHas('objectifImmediats.actionPrioritaire', fn ($query) => $query->where('papa_id', $papa->id))
            ->where('preuve_requise', true)
            ->get()
            ->filter(fn (ResultatAttendu $resultat) => $resultat->preuveManquante())
            ->values();

        $headings = ['Code', 'Resultat attendu', 'Action prioritaire', 'Responsable', 'Statut', 'Type de preuve attendue'];
        $rows = $results->map(fn (ResultatAttendu $resultat) => [
            $resultat->code,
            $resultat->libelle,
            $resultat->objectifImmediats?->actionPrioritaire?->libelle ?? '-',
            $resultat->responsable?->nomComplet() ?? '-',
            $resultat->statut,
            $resultat->type_preuve_attendue,
        ])->all();

        return $this->renderTabularReport(
            format: $format,
            title: "Resultats non prouves - {$papa->code}",
            subtitle: "PAPA {$papa->code} · Genere le " . now()->format('d/m/Y H:i'),
            headings: $headings,
            rows: $rows,
            exportTitle: 'ResultatsNonProuves'
        );
    }

    private function buildContent(ReportDefinition $definition, ?Papa $papa, string $format): array
    {
        return match ($definition->code) {
            'executive_global_papa' => $this->generateExecutiveGlobal($papa, $format),
            'financial_global_papa' => $this->generateFinancialGlobal($papa, $format),
            'rbm_chain_consolidated' => $this->generateRbmChainConsolidated($papa, $format),
            'operational_overdue_activities' => $this->generateOverdueActivities($papa, $format),
            'governance_decisions' => $this->generateGovernanceDecisions($papa, $format),
            'ged_missing_evidence' => $this->generateGedMissingEvidence($papa, $format),
            default => throw ValidationException::withMessages([
                'definition' => "Le modele {$definition->libelle} n'est pas encore implemente.",
            ]),
        };
    }

    private function renderTabularReport(
        string $format,
        string $title,
        string $subtitle,
        array $headings,
        array $rows,
        string $exportTitle,
    ): array {
        return match ($format) {
            'pdf' => [
                Pdf::loadView('pdf.reports.tabular', compact('title', 'subtitle', 'headings', 'rows'))
                    ->setPaper('a4', 'landscape')
                    ->output(),
                'application/pdf',
            ],
            'xlsx' => [
                Excel::raw(new GenericArrayExport($headings, $rows, $exportTitle), ExcelFormat::XLSX),
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
            'csv' => [
                Excel::raw(new GenericArrayExport($headings, $rows, $exportTitle), ExcelFormat::CSV),
                'text/csv',
            ],
            default => throw ValidationException::withMessages(['format' => 'Format non pris en charge pour ce rapport.']),
        };
    }

    private function buildBaseName(ReportDefinition $definition, ?Papa $papa, $timestamp, string $format): string
    {
        $scope = $papa?->code ?? 'GLOBAL';

        return sprintf(
            'CEEAC_%s_%s_%s.%s',
            strtoupper($scope),
            strtoupper($definition->code),
            $timestamp->format('Ymd_His'),
            $format
        );
    }

    private function notifyUser(
        User $user,
        string $type,
        string $title,
        string $message,
        GeneratedReport $report,
        string $level,
    ): void {
        NotificationApp::create([
            'user_id' => $user->id,
            'type' => $type,
            'titre' => $title,
            'message' => $message,
            'lien' => route('reports.library.show', $report),
            'icone' => 'fa-file-lines',
            'niveau' => $level,
            'notifiable_type' => $report->getMorphClass() ?: $report::class,
            'notifiable_id' => $report->getKey(),
        ]);
    }
}
