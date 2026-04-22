<?php

namespace App\Services\Import\Importers;

use App\Models\Departement;
use App\Models\Direction;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class DirectionsImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Directions'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code   = $this->required($row, 'code', $lineNum, $result);
        $lib    = $this->required($row, 'libelle', $lineNum, $result);
        $codeDep = $this->required($row, 'code_departement', $lineNum, $result);

        if ($code === null || $lib === null || $codeDep === null) return;

        $depId = $this->resolveCodeRequired(Departement::class, $codeDep, 'Département', $lineNum, $result);
        if ($depId === null) return;

        $typeDir = $this->inList($this->val($row, 'type_direction'), ['technique', 'appui'], 'technique');

        Direction::updateOrCreate(['code' => $code], [
            'libelle'          => $lib,
            'libelle_court'    => $this->val($row, 'libelle_court') ?: null,
            'departement_id'   => $depId,
            'type_direction'   => $typeDir,
            'description'      => $this->val($row, 'description') ?: null,
            'ordre_affichage'  => $this->parseInt($this->val($row, 'ordre_affichage')) ?? 0,
            'actif'            => $this->parseBool($this->val($row, 'actif'), true),
        ]);

        $result->imported($this->sheetName());
    }
}
