<?php

namespace App\Services\Import\Importers;

use App\Models\ActionPrioritaire;
use App\Models\Departement;
use App\Models\Papa;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class ActionsPrioritairesImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Actions_Prioritaires'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code      = $this->required($row, 'code', $lineNum, $result);
        $lib       = $this->required($row, 'libelle', $lineNum, $result);
        $codePapa  = $this->required($row, 'code_papa', $lineNum, $result);
        $codeDep   = $this->required($row, 'code_departement', $lineNum, $result);
        $qualif    = $this->required($row, 'qualification', $lineNum, $result);
        $priorite  = $this->required($row, 'priorite', $lineNum, $result);

        if ($code === null || $lib === null || $codePapa === null || $codeDep === null || $qualif === null || $priorite === null) return;

        $papaId = $this->resolveCodeRequired(Papa::class, $codePapa, 'PAPA', $lineNum, $result);
        $depId  = $this->resolveCodeRequired(Departement::class, $codeDep, 'Département', $lineNum, $result);

        if ($papaId === null || $depId === null) return;

        $qualifVal = $this->inList($qualif, ['technique', 'appui', 'transversal']);
        if ($qualifVal === '') {
            $result->error($this->sheetName(), $lineNum, "Qualification '{$qualif}' invalide.");
            return;
        }

        $prioriteVal = $this->inList($priorite, ['critique', 'haute', 'normale', 'basse']);
        if ($prioriteVal === '') {
            $result->error($this->sheetName(), $lineNum, "Priorité '{$priorite}' invalide.");
            return;
        }

        ActionPrioritaire::updateOrCreate(['code' => $code], [
            'libelle'         => $lib,
            'description'     => $this->val($row, 'description') ?: null,
            'papa_id'         => $papaId,
            'departement_id'  => $depId,
            'qualification'   => $qualifVal,
            'priorite'        => $prioriteVal,
            'statut'          => $this->inList($this->val($row, 'statut'), ['planifie', 'en_cours', 'suspendu', 'termine', 'abandonne'], 'planifie'),
            'ordre'           => $this->parseInt($this->val($row, 'ordre')) ?? 0,
            'notes'           => $this->val($row, 'notes') ?: null,
        ]);

        $result->imported($this->sheetName());
    }
}
