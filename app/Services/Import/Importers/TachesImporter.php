<?php

namespace App\Services\Import\Importers;

use App\Models\Activite;
use App\Models\Tache;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class TachesImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Taches'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code       = $this->required($row, 'code', $lineNum, $result);
        $lib        = $this->required($row, 'libelle', $lineNum, $result);
        $codeAct    = $this->required($row, 'code_activite', $lineNum, $result);

        if ($code === null || $lib === null || $codeAct === null) return;

        $activiteId = $this->resolveCodeRequired(Activite::class, $codeAct, 'Activité', $lineNum, $result);
        if ($activiteId === null) return;

        // Tâche parente optionnelle
        $parentId = null;
        $codeParent = $this->val($row, 'code_tache_parente');
        if ($codeParent !== '') {
            $parentId = Tache::where('code', $codeParent)->value('id');
            if ($parentId === null) {
                $result->error($this->sheetName(), $lineNum, "Tâche parente '{$codeParent}' introuvable. Vérifiez l'ordre des lignes.");
                return;
            }
        }

        $assigneeId = $this->resolveUserByEmail($this->val($row, 'email_assignee'), 'Assigné', $lineNum, $result);

        Tache::updateOrCreate(['code' => $code], [
            'libelle'          => $lib,
            'description'      => $this->val($row, 'description') ?: null,
            'activite_id'      => $activiteId,
            'parent_tache_id'  => $parentId,
            'date_debut_prevue' => $this->parseDate($this->val($row, 'date_debut_prevue')),
            'date_fin_prevue'   => $this->parseDate($this->val($row, 'date_fin_prevue')),
            'statut'           => $this->inList($this->val($row, 'statut'), ['a_faire', 'en_cours', 'en_revue', 'terminee', 'bloquee', 'abandonnee'], 'a_faire'),
            'taux_realisation' => $this->parseDecimal($this->val($row, 'taux_realisation')) ?? 0,
            'assignee_id'      => $assigneeId,
            'ordre'            => $this->parseInt($this->val($row, 'ordre')) ?? 0,
            'notes'            => $this->val($row, 'notes') ?: null,
        ]);

        $result->imported($this->sheetName());
    }
}
