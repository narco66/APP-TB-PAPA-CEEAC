<?php

namespace App\Console\Commands;

use App\Jobs\GenererAlertesJob;
use App\Jobs\RecalculerTauxPapaJob;
use App\Models\Papa;
use App\Services\AlerteService;
use App\Services\PapaService;
use Illuminate\Console\Command;

class RecalculerPapaCommand extends Command
{
    protected $signature = 'app:recalculer-papa
                            {--papa= : ID du PAPA (tous si absent)}
                            {--alertes : Générer les alertes après recalcul}
                            {--queue : Envoyer en file d\'attente plutôt qu\'en synchrone}';

    protected $description = 'Recalcule les taux de réalisation des PAPA (et génère les alertes si demandé)';

    public function handle(PapaService $papaService, AlerteService $alerteService): int
    {
        $papaId = $this->option('papa');
        $avecAlertes = $this->option('alertes');
        $viaQueue = $this->option('queue');

        $query = Papa::whereIn('statut', ['en_execution', 'en_cours', 'valide']);

        if ($papaId) {
            $query->where('id', $papaId);
        }

        $papas = $query->get();

        if ($papas->isEmpty()) {
            $this->warn('Aucun PAPA en exécution trouvé.');
            return Command::SUCCESS;
        }

        $this->info("Traitement de {$papas->count()} PAPA(s)...");
        $bar = $this->output->createProgressBar($papas->count());
        $bar->start();

        foreach ($papas as $papa) {
            if ($viaQueue) {
                RecalculerTauxPapaJob::dispatch($papa);
                if ($avecAlertes) {
                    GenererAlertesJob::dispatch($papa)->delay(now()->addSeconds(10));
                }
            } else {
                $papaService->recalculerTaux($papa);
                if ($avecAlertes) {
                    $nb = $alerteService->genererAlertesPapa($papa)->count();
                    $this->line(" → {$papa->code} : {$nb} alerte(s) générée(s)");
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info($viaQueue ? 'Jobs envoyés en file d\'attente.' : 'Recalcul terminé.');

        return Command::SUCCESS;
    }
}
