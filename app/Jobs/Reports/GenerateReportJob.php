<?php

namespace App\Jobs\Reports;

use App\Models\GeneratedReport;
use App\Services\Reports\ReportGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(public int $generatedReportId) {}

    public function handle(ReportGenerationService $reportGenerationService): void
    {
        $generatedReport = GeneratedReport::with(['definition', 'user', 'papa'])->findOrFail($this->generatedReportId);

        $reportGenerationService->processQueuedReport($generatedReport);
    }
}
