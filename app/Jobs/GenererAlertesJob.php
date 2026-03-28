<?php

namespace App\Jobs;

use App\Models\Papa;
use App\Services\AlerteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenererAlertesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public readonly Papa $papa) {}

    public function handle(AlerteService $service): void
    {
        Log::info("GenererAlertes: début PAPA {$this->papa->code}");

        $alertes = $service->genererAlertesPapa($this->papa);

        Log::info("GenererAlertes: {$alertes->count()} alerte(s) générée(s) pour PAPA {$this->papa->code}");
    }

    public function failed(\Throwable $e): void
    {
        Log::error("GenererAlertes: échec PAPA {$this->papa->code} — {$e->getMessage()}");
    }
}
