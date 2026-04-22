<?php

namespace App\Services\Import\Importers;

use App\Models\ActionPrioritaire;
use App\Models\ObjectifImmediats;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class ObjectifsImmediatsImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Objectifs_Immediats'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code    = $this->required($row, 'code', $lineNum, $result);
        $lib     = $this->required($row, 'libelle', $lineNum, $result);
        $codeAP  = $this->required($row, 'code_action_prioritaire', $lineNum, $result);

        if ($code === null || $lib === null || $codeAP === null) return;

        $apId = $this->resolveCodeRequired(ActionPrioritaire::class, $codeAP, 'Action prioritaire', $lineNum, $result);
        if ($apId === null) return;

        $respId = $this->resolveUserByEmail($this->val($row, 'email_responsable'), 'Responsable', $lineNum, $result);

        ObjectifImmediats::updateOrCreate(['code' => $code], [
            'libelle'               => $lib,
            'description'           => $this->val($row, 'description') ?: null,
            'action_prioritaire_id' => $apId,
            'statut'                => $this->inList($this->val($row, 'statut'), ['planifie', 'en_cours', 'atteint', 'partiellement_atteint', 'non_atteint'], 'planifie'),
            'taux_atteinte'         => $this->parseDecimal($this->val($row, 'taux_atteinte')) ?? 0,
            'ordre'                 => $this->parseInt($this->val($row, 'ordre')) ?? 0,
            'responsable_id'        => $respId,
            'notes'                 => $this->val($row, 'notes') ?: null,
        ]);

        $result->imported($this->sheetName());
    }
}
