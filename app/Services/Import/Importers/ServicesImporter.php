<?php

namespace App\Services\Import\Importers;

use App\Models\Direction;
use App\Models\Service;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class ServicesImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Services'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code   = $this->required($row, 'code', $lineNum, $result);
        $lib    = $this->required($row, 'libelle', $lineNum, $result);
        $codeDir = $this->required($row, 'code_direction', $lineNum, $result);

        if ($code === null || $lib === null || $codeDir === null) return;

        $dirId = $this->resolveCodeRequired(Direction::class, $codeDir, 'Direction', $lineNum, $result);
        if ($dirId === null) return;

        Service::updateOrCreate(['code' => $code], [
            'libelle'         => $lib,
            'libelle_court'   => $this->val($row, 'libelle_court') ?: null,
            'direction_id'    => $dirId,
            'description'     => $this->val($row, 'description') ?: null,
            'ordre_affichage' => $this->parseInt($this->val($row, 'ordre_affichage')) ?? 0,
            'actif'           => $this->parseBool($this->val($row, 'actif'), true),
        ]);

        $result->imported($this->sheetName());
    }
}
