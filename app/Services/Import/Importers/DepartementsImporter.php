<?php

namespace App\Services\Import\Importers;

use App\Models\Departement;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class DepartementsImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Departements'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code = $this->required($row, 'code', $lineNum, $result);
        $lib  = $this->required($row, 'libelle', $lineNum, $result);
        $type = $this->required($row, 'type', $lineNum, $result);

        if ($code === null || $lib === null || $type === null) return;

        $typeVal = $this->inList($type, ['technique', 'appui', 'transversal']);
        if ($typeVal === '') {
            $result->error($this->sheetName(), $lineNum, "Type '{$type}' invalide. Valeurs : technique, appui, transversal.");
            return;
        }

        Departement::updateOrCreate(['code' => $code], [
            'libelle'         => $lib,
            'libelle_court'   => $this->val($row, 'libelle_court') ?: null,
            'type'            => $typeVal,
            'description'     => $this->val($row, 'description') ?: null,
            'ordre_affichage' => $this->parseInt($this->val($row, 'ordre_affichage')) ?? 0,
            'actif'           => $this->parseBool($this->val($row, 'actif'), true),
        ]);

        $result->imported($this->sheetName());
    }
}
