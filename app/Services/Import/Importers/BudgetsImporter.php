<?php

namespace App\Services\Import\Importers;

use App\Models\Activite;
use App\Models\ActionPrioritaire;
use App\Models\BudgetPapa;
use App\Models\Papa;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class BudgetsImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Budgets'; }

    private const SOURCES = ['budget_ceeac', 'contribution_etat_membre', 'partenaire_technique_financier', 'fonds_propres', 'autre'];

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $codePapa = $this->required($row, 'code_papa', $lineNum, $result);
        $libLigne = $this->required($row, 'libelle_ligne', $lineNum, $result);
        $source   = $this->required($row, 'source_financement', $lineNum, $result);
        $annee    = $this->required($row, 'annee_budgetaire', $lineNum, $result);
        $montant  = $this->required($row, 'montant_prevu', $lineNum, $result);

        if ($codePapa === null || $libLigne === null || $source === null || $annee === null || $montant === null) return;

        $papaId = $this->resolveCodeRequired(Papa::class, $codePapa, 'PAPA', $lineNum, $result);
        if ($papaId === null) return;

        $sourceVal = $this->inList($source, self::SOURCES);
        if ($sourceVal === '') {
            $result->error($this->sheetName(), $lineNum, "source_financement '{$source}' invalide.");
            return;
        }

        $montantPrevu = $this->parseDecimal($montant);
        if ($montantPrevu === null || $montantPrevu < 0) {
            $result->error($this->sheetName(), $lineNum, "montant_prevu invalide.");
            return;
        }

        $montantEngage   = $this->parseDecimal($this->val($row, 'montant_engage')) ?? 0;
        $montantDecaisse = $this->parseDecimal($this->val($row, 'montant_decaisse')) ?? 0;

        // Rattachement optionnel (AP ou Activité)
        $apId = null;
        $actId = null;
        $niveau = $this->val($row, 'niveau_rattachement');
        $codeEnt = $this->val($row, 'code_entite');

        if ($niveau === 'action_prioritaire' && $codeEnt !== '') {
            $apId = ActionPrioritaire::where('code', $codeEnt)->value('id');
        } elseif ($niveau === 'activite' && $codeEnt !== '') {
            $actId = Activite::where('code', $codeEnt)->value('id');
        }

        BudgetPapa::create([
            'papa_id'                => $papaId,
            'action_prioritaire_id'  => $apId,
            'activite_id'            => $actId,
            'libelle_ligne'          => $libLigne,
            'source_financement'     => $sourceVal,
            'annee_budgetaire'       => (int) $annee,
            'devise'                 => $this->val($row, 'devise') ?: 'XAF',
            'montant_prevu'          => $montantPrevu,
            'montant_engage'         => $montantEngage,
            'montant_decaisse'       => $montantDecaisse,
            'montant_solde'          => $montantPrevu - $montantEngage,
            'notes'                  => $this->val($row, 'notes') ?: null,
        ]);

        $result->imported($this->sheetName());
    }
}
