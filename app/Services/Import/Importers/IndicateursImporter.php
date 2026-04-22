<?php

namespace App\Services\Import\Importers;

use App\Models\ActionPrioritaire;
use App\Models\Direction;
use App\Models\Indicateur;
use App\Models\ObjectifImmediats;
use App\Models\ResultatAttendu;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class IndicateursImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Indicateurs'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code    = $this->required($row, 'code', $lineNum, $result);
        $lib     = $this->required($row, 'libelle', $lineNum, $result);
        $niveau  = $this->required($row, 'niveau_rattachement', $lineNum, $result);
        $codeEnt = $this->required($row, 'code_entite_rattachement', $lineNum, $result);
        $type    = $this->required($row, 'type_indicateur', $lineNum, $result);
        $freq    = $this->required($row, 'frequence_collecte', $lineNum, $result);

        if ($code === null || $lib === null || $niveau === null || $codeEnt === null || $type === null || $freq === null) return;

        // Résolution du rattachement
        [$apId, $oiId, $raId] = [null, null, null];

        match ($niveau) {
            'action_prioritaire' => $apId = $this->resolveCodeRequired(ActionPrioritaire::class, $codeEnt, 'Action prioritaire', $lineNum, $result),
            'objectif_immediat'  => $oiId = $this->resolveCodeRequired(ObjectifImmediats::class, $codeEnt, 'Objectif immédiat', $lineNum, $result),
            'resultat_attendu'   => $raId = $this->resolveCodeRequired(ResultatAttendu::class, $codeEnt, 'Résultat attendu', $lineNum, $result),
            default => $result->error($this->sheetName(), $lineNum, "niveau_rattachement '{$niveau}' invalide. Valeurs : action_prioritaire, objectif_immediat, resultat_attendu."),
        };

        if (!in_array($niveau, ['action_prioritaire', 'objectif_immediat', 'resultat_attendu'])) return;
        if ($apId === null && $oiId === null && $raId === null) return;

        $typeVal = $this->inList($type, ['quantitatif', 'qualitatif', 'binaire']);
        if ($typeVal === '') {
            $result->error($this->sheetName(), $lineNum, "type_indicateur '{$type}' invalide.");
            return;
        }

        $freqVal = $this->inList($freq, ['mensuelle', 'trimestrielle', 'semestrielle', 'annuelle', 'ponctuelle']);
        if ($freqVal === '') {
            $result->error($this->sheetName(), $lineNum, "frequence_collecte '{$freq}' invalide.");
            return;
        }

        $dirId  = $this->resolveCode(Direction::class, $this->val($row, 'code_direction'), 'Direction', $lineNum, $result);
        $respId = $this->resolveUserByEmail($this->val($row, 'email_responsable'), 'Responsable', $lineNum, $result);

        Indicateur::updateOrCreate(['code' => $code], [
            'libelle'                  => $lib,
            'definition'               => $this->val($row, 'definition') ?: null,
            'action_prioritaire_id'    => $apId,
            'objectif_immediat_id'     => $oiId,
            'resultat_attendu_id'      => $raId,
            'unite_mesure'             => $this->val($row, 'unite_mesure') ?: null,
            'type_indicateur'          => $typeVal,
            'frequence_collecte'       => $freqVal,
            'valeur_baseline'          => $this->parseDecimal($this->val($row, 'valeur_baseline')),
            'valeur_cible_annuelle'    => $this->parseDecimal($this->val($row, 'valeur_cible_annuelle')),
            'methode_calcul'           => $this->val($row, 'methode_calcul') ?: null,
            'source_donnees'           => $this->val($row, 'source_donnees') ?: null,
            'outil_collecte'           => $this->val($row, 'outil_collecte') ?: null,
            'seuil_alerte_rouge'       => $this->parseDecimal($this->val($row, 'seuil_alerte_rouge')),
            'seuil_alerte_orange'      => $this->parseDecimal($this->val($row, 'seuil_alerte_orange')),
            'seuil_alerte_vert'        => $this->parseDecimal($this->val($row, 'seuil_alerte_vert')),
            'direction_id'             => $dirId,
            'responsable_id'           => $respId,
            'actif'                    => $this->parseBool($this->val($row, 'actif'), true),
            'notes'                    => $this->val($row, 'notes') ?: null,
        ]);

        $result->imported($this->sheetName());
    }
}
