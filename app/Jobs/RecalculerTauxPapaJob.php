<?php

namespace App\Jobs;

use App\Models\Papa;
use App\Services\PapaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecalculerTauxPapaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public readonly Papa $papa) {}

    public function handle(PapaService $service): void
    {
        Log::info("RecalculerTaux: début PAPA {$this->papa->code}");

        $service->recalculerTaux($this->papa);

        Log::info("RecalculerTaux: fin PAPA {$this->papa->code} — taux physique = {$this->papa->fresh()->taux_execution_physique}%");
    }

    public function failed(\Throwable $e): void
    {
        Log::error("RecalculerTaux: échec PAPA {$this->papa->code} — {$e->getMessage()}");
    }
}
