<?php

namespace App\Services\Import\Importers;

use App\Models\Activite;
use App\Models\Jalon;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class JalonsImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Jalons'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code    = $this->required($row, 'code', $lineNum, $result);
        $lib     = $this->required($row, 'libelle', $lineNum, $result);
        $codeAct = $this->required($row, 'code_activite', $lineNum, $result);
        $datePrev = $this->required($row, 'date_prevue', $lineNum, $result);

        if ($code === null || $lib === null || $codeAct === null || $datePrev === null) return;

        $activiteId = $this->resolveCodeRequired(Activite::class, $codeAct, 'Activité', $lineNum, $result);
        if ($activiteId === null) return;

        $datePrevueParsed = $this->parseDate($datePrev);
        if (!$datePrevueParsed) {
            $result->error($this->sheetName(), $lineNum, "Date prévue invalide (format : JJ/MM/AAAA).");
            return;
        }

        Jalon::updateOrCreate(['code' => $code], [
            'libelle'      => $lib,
            'description'  => $this->val($row, 'description') ?: null,
            'activite_id'  => $activiteId,
            'date_prevue'  => $datePrevueParsed,
            'date_reelle'  => $this->parseDate($this->val($row, 'date_reelle')),
            'statut'       => $this->inList($this->val($row, 'statut'), ['planifie', 'atteint', 'non_atteint', 'reporte'], 'planifie'),
            'est_critique' => $this->parseBool($this->val($row, 'est_critique')),
            'notes'        => $this->val($row, 'notes') ?: null,
        ]);

        $result->imported($this->sheetName());
    }
}
