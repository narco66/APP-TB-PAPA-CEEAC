<?php

namespace App\Services\Import\Importers;

use App\Models\Activite;
use App\Models\Direction;
use App\Models\ResultatAttendu;
use App\Models\Service;
use App\Services\Import\AbstractRowImporter;
use App\Services\Import\ImportResult;

class ActivitesImporter extends AbstractRowImporter
{
    protected function sheetName(): string { return 'Activites'; }

    protected function processRow(array $row, int $lineNum, ImportResult $result): void
    {
        $code    = $this->required($row, 'code', $lineNum, $result);
        $lib     = $this->required($row, 'libelle', $lineNum, $result);
        $codeRA  = $this->required($row, 'code_resultat_attendu', $lineNum, $result);
        $codeDir = $this->required($row, 'code_direction', $lineNum, $result);
        $priorite = $this->required($row, 'priorite', $lineNum, $result);

        if ($code === null || $lib === null || $codeRA === null || $codeDir === null || $priorite === null) return;

        $raId  = $this->resolveCodeRequired(ResultatAttendu::class, $codeRA, 'Résultat attendu', $lineNum, $result);
        $dirId = $this->resolveCodeRequired(Direction::class, $codeDir, 'Direction', $lineNum, $result);

        if ($raId === null || $dirId === null) return;

        $prioriteVal = $this->inList($priorite, ['critique', 'haute', 'normale', 'basse']);
        if ($prioriteVal === '') {
            $result->error($this->sheetName(), $lineNum, "Priorité '{$priorite}' invalide.");
            return;
        }

        // Service optionnel
        $serviceId = null;
        $codeSvc = $this->val($row, 'code_service');
        if ($codeSvc !== '') {
            $serviceId = Service::where('code', $codeSvc)->where('direction_id', $dirId)->value('id');
            if ($serviceId === null) {
                $result->error($this->sheetName(), $lineNum, "Service '{$codeSvc}' introuvable dans la direction '{$codeDir}'.");
                return;
            }
        }

        // Dates
        $dateDebutP = $this->parseDate($this->val($row, 'date_debut_prevue'));
        $dateFinP   = $this->parseDate($this->val($row, 'date_fin_prevue'));
        $dateDebutR = $this->parseDate($this->val($row, 'date_debut_reelle'));
        $dateFinR   = $this->parseDate($this->val($row, 'date_fin_reelle'));

        if ($dateDebutP && $dateFinP && $dateFinP < $dateDebutP) {
            $result->error($this->sheetName(), $lineNum, "Date de fin prévue antérieure à la date de début prévue.");
            return;
        }

        $respId = $this->resolveUserByEmail($this->val($row, 'email_responsable'), 'Responsable', $lineNum, $result);
        $pfId   = $this->resolveUserByEmail($this->val($row, 'email_point_focal'), 'Point focal', $lineNum, $result);

        $budgetPrevu    = $this->parseDecimal($this->val($row, 'budget_prevu'));
        $budgetEngage   = $this->parseDecimal($this->val($row, 'budget_engage'));
        $budgetConsomme = $this->parseDecimal($this->val($row, 'budget_consomme'));

        if ($budgetEngage !== null && $budgetPrevu !== null && $budgetEngage > $budgetPrevu) {
            $result->error($this->sheetName(), $lineNum, "Budget engagé supérieur au budget prévu.");
            return;
        }

        Activite::updateOrCreate(['code' => $code], [
            'libelle'              => $lib,
            'description'          => $this->val($row, 'description') ?: null,
            'resultat_attendu_id'  => $raId,
            'direction_id'         => $dirId,
            'service_id'           => $serviceId,
            'date_debut_prevue'    => $dateDebutP,
            'date_fin_prevue'      => $dateFinP,
            'date_debut_reelle'    => $dateDebutR,
            'date_fin_reelle'      => $dateFinR,
            'statut'               => $this->inList($this->val($row, 'statut'), ['non_demarree', 'planifiee', 'en_cours', 'suspendue', 'terminee', 'abandonnee'], 'non_demarree'),
            'priorite'             => $prioriteVal,
            'taux_realisation'     => $this->parseDecimal($this->val($row, 'taux_realisation')) ?? 0,
            'budget_prevu'         => $budgetPrevu ?? 0,
            'budget_engage'        => $budgetEngage ?? 0,
            'budget_consomme'      => $budgetConsomme ?? 0,
            'devise'               => $this->val($row, 'devise') ?: 'XAF',
            'est_jalon'            => $this->parseBool($this->val($row, 'est_jalon')),
            'responsable_id'       => $respId,
            'point_focal_id'       => $pfId,
            'ordre'                => $this->parseInt($this->val($row, 'ordre')) ?? 0,
            'notes'                => $this->val($row, 'notes') ?: null,
        ]);

        $result->imported($this->sheetName());
    }
}
