<?php

namespace App\Services\Import\Importers;

use App\Models\ObjectifImmediats;
use App\Models\ResultatAttendu;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class ResultatsAttendusImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Resultats_Attendus'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code   = $this->required($row, 'code', $lineNum, $result);
        $lib    = $this->required($row, 'libelle', $lineNum, $result);
        $codeOI = $this->required($row, 'code_objectif_immediat', $lineNum, $result);
        $type   = $this->required($row, 'type_resultat', $lineNum, $result);

        if ($code === null || $lib === null || $codeOI === null || $type === null) return;

        $oiId = $this->resolveCodeRequired(ObjectifImmediats::class, $codeOI, 'Objectif immédiat', $lineNum, $result);
        if ($oiId === null) return;

        $typeVal = $this->inList($type, ['output', 'outcome', 'impact']);
        if ($typeVal === '') {
            $result->error($this->sheetName(), $lineNum, "Type de résultat '{$type}' invalide. Valeurs : output, outcome, impact.");
            return;
        }

        $respId = $this->resolveUserByEmail($this->val($row, 'email_responsable'), 'Responsable', $lineNum, $result);

        $anneeRef = $this->parseInt($this->val($row, 'annee_reference'));

        ResultatAttendu::updateOrCreate(['code' => $code], [
            'libelle'               => $lib,
            'description'           => $this->val($row, 'description') ?: null,
            'objectif_immediat_id'  => $oiId,
            'type_resultat'         => $typeVal,
            'statut'                => $this->inList($this->val($row, 'statut'), ['planifie', 'en_cours', 'atteint', 'partiellement_atteint', 'non_atteint'], 'planifie'),
            'taux_atteinte'         => $this->parseDecimal($this->val($row, 'taux_atteinte')) ?? 0,
            'annee_reference'       => $anneeRef,
            'preuve_requise'        => $this->parseBool($this->val($row, 'preuve_requise')),
            'type_preuve_attendue'  => $this->val($row, 'type_preuve_attendue') ?: null,
            'ordre'                 => $this->parseInt($this->val($row, 'ordre')) ?? 0,
            'responsable_id'        => $respId,
            'notes'                 => $this->val($row, 'notes') ?: null,
        ]);

        $result->imported($this->sheetName());
    }
}
