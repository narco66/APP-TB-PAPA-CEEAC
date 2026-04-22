<?php

namespace App\Services\Import\Importers;

use App\Models\Papa;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class PapaImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'PAPA'; }

    private const STATUTS = ['brouillon', 'soumis', 'en_validation', 'valide', 'en_execution', 'cloture', 'archive'];

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code = $this->required($row, 'code', $lineNum, $result);
        $lib  = $this->required($row, 'libelle', $lineNum, $result);
        $annee = $this->required($row, 'annee', $lineNum, $result);
        $debut = $this->required($row, 'date_debut', $lineNum, $result);
        $fin   = $this->required($row, 'date_fin', $lineNum, $result);

        if ($code === null || $lib === null || $annee === null || $debut === null || $fin === null) return;

        $anneeInt = (int) $annee;
        if ($anneeInt < 2020 || $anneeInt > 2050) {
            $result->error($this->sheetName(), $lineNum, "Année '{$annee}' invalide (2020–2050).");
            return;
        }

        $dateDebut = $this->parseDate($debut);
        $dateFin   = $this->parseDate($fin);

        if (!$dateDebut || !$dateFin) {
            $result->error($this->sheetName(), $lineNum, "Dates invalides (format attendu : JJ/MM/AAAA).");
            return;
        }

        if ($dateFin <= $dateDebut) {
            $result->error($this->sheetName(), $lineNum, "Date de fin doit être postérieure à la date de début.");
            return;
        }

        $statut = $this->inList($this->val($row, 'statut'), self::STATUTS, 'brouillon');

        $createdBy = $this->resolveUserByEmail($this->val($row, 'email_cree_par'), 'Créé par', $lineNum, $result);

        Papa::updateOrCreate(['code' => $code], [
            'libelle'            => $lib,
            'annee'              => $anneeInt,
            'date_debut'         => $dateDebut,
            'date_fin'           => $dateFin,
            'budget_total_prevu' => $this->parseDecimal($this->val($row, 'budget_total_prevu')),
            'devise'             => $this->val($row, 'devise') ?: 'XAF',
            'description'        => $this->val($row, 'description') ?: null,
            'statut'             => $statut,
            'notes'              => $this->val($row, 'notes') ?: null,
            'created_by'         => $createdBy,
        ]);

        $result->imported($this->sheetName());
    }
}
