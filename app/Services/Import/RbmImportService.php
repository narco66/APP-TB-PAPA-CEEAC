<?php

namespace App\Services\Import;

use App\Services\Import\Importers\ActivitesImporter;
use App\Services\Import\Importers\ActionsPrioritairesImporter;
use App\Services\Import\Importers\BudgetsImporter;
use App\Services\Import\Importers\DependancesImporter;
use App\Services\Import\Importers\DepartementsImporter;
use App\Services\Import\Importers\DirectionsImporter;
use App\Services\Import\Importers\IndicateursImporter;
use App\Services\Import\Importers\JalonsImporter;
use App\Services\Import\Importers\ObjectifsImmediatsImporter;
use App\Services\Import\Importers\PapaImporter;
use App\Services\Import\Importers\ResultatsAttendusImporter;
use App\Services\Import\Importers\RisquesImporter;
use App\Services\Import\Importers\ServicesImporter;
use App\Services\Import\Importers\TachesImporter;
use App\Services\Import\Importers\UtilisateursImporter;
use App\Services\Import\Importers\ValeursIndicateursImporter;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RbmImportService
{
    /** Ordre impératif d'import — respecte les dépendances FK */
    private const PIPELINE = [
        DepartementsImporter::class,
        DirectionsImporter::class,
        ServicesImporter::class,
        UtilisateursImporter::class,
        PapaImporter::class,
        ActionsPrioritairesImporter::class,
        ObjectifsImmediatsImporter::class,
        ResultatsAttendusImporter::class,
        ActivitesImporter::class,
        TachesImporter::class,
        JalonsImporter::class,
        IndicateursImporter::class,
        ValeursIndicateursImporter::class,
        BudgetsImporter::class,
        RisquesImporter::class,
        DependancesImporter::class,
    ];

    /**
     * Importe un fichier Excel RBM complet.
     *
     * Modes :
     *  - 'strict' : rollback complet à la première erreur
     *  - 'souple' : continue et rapporte les erreurs ligne par ligne
     */
    public function import(string $filePath, string $mode = 'strict'): ImportResult
    {
        $spreadsheet = IOFactory::load($filePath);
        $result      = new ImportResult();

        DB::transaction(function () use ($spreadsheet, $result, $mode) {
            foreach (self::PIPELINE as $importerClass) {
                /** @var AbstractRowImporter $importer */
                $importer = new $importerClass();
                $importer->import($spreadsheet, $result);

                if ($mode === 'strict' && $result->hasErrors()) {
                    // Lève une exception pour déclencher le rollback
                    throw new \RuntimeException('Import annulé (mode strict) — ' . count($result->getErrors()) . ' erreur(s).');
                }
            }
        });

        return $result;
    }

    /**
     * Valide le fichier sans persister (dry-run).
     * Toujours en mode souple pour collecter toutes les erreurs.
     */
    public function valider(string $filePath): ImportResult
    {
        $spreadsheet = IOFactory::load($filePath);
        $result      = new ImportResult();

        // Dry-run dans une transaction qu'on rollback immédiatement
        DB::transaction(function () use ($spreadsheet, $result) {
            foreach (self::PIPELINE as $importerClass) {
                $importer = new $importerClass();
                $importer->import($spreadsheet, $result);
            }
            // Rollback systématique
            throw new DryRunException();
        });

        return $result;
    }
}
