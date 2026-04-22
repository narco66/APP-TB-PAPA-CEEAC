<?php

namespace App\Services\Import\Importers;

use App\Models\Papa;
use App\Models\Risque;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class RisquesImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Risques'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code       = $this->required($row, 'code', $lineNum, $result);
        $lib        = $this->required($row, 'libelle', $lineNum, $result);
        $codePapa   = $this->required($row, 'code_papa', $lineNum, $result);
        $categorie  = $this->required($row, 'categorie', $lineNum, $result);
        $prob       = $this->required($row, 'probabilite', $lineNum, $result);
        $impact     = $this->required($row, 'impact', $lineNum, $result);

        if ($code === null || $lib === null || $codePapa === null || $categorie === null || $prob === null || $impact === null) return;

        $papaId = $this->resolveCodeRequired(Papa::class, $codePapa, 'PAPA', $lineNum, $result);
        if ($papaId === null) return;

        $cats = ['strategique', 'operationnel', 'financier', 'juridique', 'reputationnel', 'securitaire', 'naturel', 'autre'];
        $categorieVal = $this->inList($categorie, $cats);
        if ($categorieVal === '') {
            $result->error($this->sheetName(), $lineNum, "Catégorie '{$categorie}' invalide.");
            return;
        }

        $probs = ['tres_faible', 'faible', 'moyenne', 'elevee', 'tres_elevee'];
        $probVal = $this->inList($prob, $probs);
        if ($probVal === '') {
            $result->error($this->sheetName(), $lineNum, "Probabilité '{$prob}' invalide.");
            return;
        }

        $impacts = ['negligeable', 'mineur', 'modere', 'majeur', 'catastrophique'];
        $impactVal = $this->inList($impact, $impacts);
        if ($impactVal === '') {
            $result->error($this->sheetName(), $lineNum, "Impact '{$impact}' invalide.");
            return;
        }

        $respId = $this->resolveUserByEmail($this->val($row, 'email_responsable'), 'Responsable', $lineNum, $result);

        $risque = Risque::updateOrCreate(['code' => $code], [
            'libelle'                    => $lib,
            'description'                => $this->val($row, 'description') ?: null,
            'papa_id'                    => $papaId,
            'categorie'                  => $categorieVal,
            'probabilite'                => $probVal,
            'impact'                     => $impactVal,
            'statut'                     => $this->inList($this->val($row, 'statut'), ['identifie', 'en_traitement', 'residu', 'clos'], 'identifie'),
            'mesures_mitigation'         => $this->val($row, 'mesures_mitigation') ?: null,
            'plan_contingence'           => $this->val($row, 'plan_contingence') ?: null,
            'date_echeance_traitement'   => $this->parseDate($this->val($row, 'date_echeance_traitement')),
            'responsable_id'             => $respId,
        ]);

        // Calcul automatique du score et du niveau
        $risque->update([
            'score_risque'  => $risque->calculerScore(),
            'niveau_risque' => $risque->calculerNiveau(),
        ]);

        $result->imported($this->sheetName());
    }
}
