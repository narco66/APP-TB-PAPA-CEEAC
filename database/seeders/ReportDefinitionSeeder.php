<?php

namespace Database\Seeders;

use App\Models\ReportDefinition;
use Illuminate\Database\Seeder;

class ReportDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            ['code' => 'executive_global_papa', 'libelle' => 'Rapport executif global du PAPA', 'categorie' => 'Executif', 'description' => 'Synthese strategique globale du PAPA actif.', 'formats' => ['pdf', 'xlsx', 'csv'], 'is_async_recommended' => false],
            ['code' => 'rbm_chain_consolidated', 'libelle' => 'Chaine consolidee des resultats', 'categorie' => 'RBM', 'description' => 'Consolidation Action -> Objectif -> Resultat -> KPI -> Activite.', 'formats' => ['pdf', 'xlsx', 'csv'], 'is_async_recommended' => true],
            ['code' => 'operational_overdue_activities', 'libelle' => 'Activites en retard', 'categorie' => 'Operationnel', 'description' => 'Suivi des activites en depassement de delai.', 'formats' => ['pdf', 'xlsx', 'csv'], 'is_async_recommended' => false],
            ['code' => 'financial_global_papa', 'libelle' => 'Rapport budgetaire global du PAPA', 'categorie' => 'Financier', 'description' => 'Vue globale des previsions, engagements et consommations.', 'formats' => ['pdf', 'xlsx', 'csv'], 'is_async_recommended' => false],
            ['code' => 'governance_decisions', 'libelle' => 'Decisions et arbitrages', 'categorie' => 'Gouvernance', 'description' => 'Synthese des validations, arbitrages et decisions institutionnelles.', 'formats' => ['pdf', 'xlsx', 'csv'], 'is_async_recommended' => false],
            ['code' => 'ged_missing_evidence', 'libelle' => 'Resultats non prouves', 'categorie' => 'GED', 'description' => 'Resultats sans preuves GED suffisantes ou pieces manquantes.', 'formats' => ['pdf', 'xlsx', 'csv'], 'is_async_recommended' => true],
        ];

        foreach ($definitions as $definition) {
            ReportDefinition::updateOrCreate(
                ['code' => $definition['code']],
                $definition + ['actif' => true, 'is_system' => true]
            );
        }
    }
}