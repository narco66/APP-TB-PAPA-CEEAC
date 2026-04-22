<?php

namespace App\Services\Import\Importers;

use App\Models\Activite;
use App\Models\DependanceActivite;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class DependancesImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Dependances_Activites'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $codeSrc  = $this->required($row, 'code_activite_source', $lineNum, $result);
        $codePred = $this->required($row, 'code_activite_predecesseur', $lineNum, $result);

        if ($codeSrc === null || $codePred === null) return;

        $actId  = $this->resolveCodeRequired(Activite::class, $codeSrc, 'Activité source', $lineNum, $result);
        $predId = $this->resolveCodeRequired(Activite::class, $codePred, 'Activité prédécesseur', $lineNum, $result);

        if ($actId === null || $predId === null) return;

        if ($actId === $predId) {
            $result->error($this->sheetName(), $lineNum, "Une activité ne peut pas être son propre prédécesseur.");
            return;
        }

        $typeVal = $this->inList(
            $this->val($row, 'type_dependance'),
            ['fin_debut', 'debut_debut', 'fin_fin', 'debut_fin'],
            'fin_debut'
        );

        DependanceActivite::updateOrCreate(
            ['activite_id' => $actId, 'activite_predecesseur_id' => $predId],
            [
                'type_dependance' => $typeVal,
                'delai_jours'     => $this->parseInt($this->val($row, 'lag_jours')) ?? 0,
            ]
        );

        $result->imported($this->sheetName());
    }
}
