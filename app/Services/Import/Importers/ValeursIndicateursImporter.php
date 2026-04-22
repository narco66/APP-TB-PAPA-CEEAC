<?php

namespace App\Services\Import\Importers;

use App\Models\Indicateur;
use App\Models\ValeurIndicateur;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class ValeursIndicateursImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Valeurs_Indicateurs'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $codeInd    = $this->required($row, 'code_indicateur', $lineNum, $result);
        $periodeType = $this->required($row, 'periode_type', $lineNum, $result);
        $periodeLbl  = $this->required($row, 'periode_libelle', $lineNum, $result);
        $annee      = $this->required($row, 'annee', $lineNum, $result);
        $valeurReal  = $this->required($row, 'valeur_realisee', $lineNum, $result);

        if ($codeInd === null || $periodeType === null || $periodeLbl === null || $annee === null || $valeurReal === null) return;

        $indicateurId = $this->resolveCodeRequired(Indicateur::class, $codeInd, 'Indicateur', $lineNum, $result);
        if ($indicateurId === null) return;

        $periodeTypeVal = $this->inList($periodeType, ['mensuelle', 'trimestrielle', 'semestrielle', 'annuelle']);
        if ($periodeTypeVal === '') {
            $result->error($this->sheetName(), $lineNum, "periode_type '{$periodeType}' invalide.");
            return;
        }

        $valRealisee = $this->parseDecimal($valeurReal);
        if ($valRealisee === null) {
            $result->error($this->sheetName(), $lineNum, "valeur_realisee '{$valeurReal}' n'est pas un nombre valide.");
            return;
        }

        $anneeInt   = (int) $annee;
        $mois       = $this->parseInt($this->val($row, 'mois'));
        $trimestre  = $this->parseInt($this->val($row, 'trimestre'));
        $semestre   = $this->parseInt($this->val($row, 'semestre'));

        $saisiParId = $this->resolveUserByEmail($this->val($row, 'email_saisi_par'), 'Saisi par', $lineNum, $result);

        $statutVal = $this->inList($this->val($row, 'statut_validation'), ['brouillon', 'soumis', 'valide', 'rejete'], 'brouillon');

        ValeurIndicateur::updateOrCreate(
            [
                'indicateur_id' => $indicateurId,
                'periode_type'  => $periodeTypeVal,
                'annee'         => $anneeInt,
                'mois'          => $mois,
                'trimestre'     => $trimestre,
                'semestre'      => $semestre,
            ],
            [
                'periode_libelle'     => $periodeLbl,
                'valeur_realisee'     => $valRealisee,
                'valeur_cible_periode' => $this->parseDecimal($this->val($row, 'valeur_cible_periode')),
                'commentaire'         => $this->val($row, 'commentaire') ?: null,
                'analyse_ecart'       => $this->val($row, 'analyse_ecart') ?: null,
                'statut_validation'   => $statutVal,
                'saisi_par'           => $saisiParId,
            ]
        );

        $result->imported($this->sheetName());
    }
}
