<?php

namespace Database\Seeders;

use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use App\Models\User;
use Illuminate\Database\Seeder;

class WorkflowDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()->where('email', 'admin@ceeac-eccas.org')->value('id');

        $definitions = [
            [
                'code' => 'PAPA_VALIDATION_STANDARD',
                'libelle' => 'Validation hiérarchique standard du PAPA',
                'description' => 'Circuit standard de soumission, examen SG, arbitrage VP et validation finale.',
                'module_cible' => 'papa',
                'type_objet' => \App\Models\Papa::class,
                'steps' => [
                    ['code' => 'soumission', 'libelle' => 'Soumission', 'ordre' => 1, 'permission_requise' => 'papa.soumettre', 'est_etape_initiale' => true],
                    ['code' => 'validation_sg', 'libelle' => 'Validation SG', 'ordre' => 2, 'role_requis' => 'secretaire_general', 'permission_requise' => 'workflow.approuver'],
                    ['code' => 'validation_vp', 'libelle' => 'Validation Vice-Présidence', 'ordre' => 3, 'role_requis' => 'vice_president', 'permission_requise' => 'workflow.approuver'],
                    ['code' => 'validation_president', 'libelle' => 'Validation Présidence', 'ordre' => 4, 'role_requis' => 'president', 'permission_requise' => 'workflow.approuver', 'est_etape_finale' => true],
                ],
            ],
            [
                'code' => 'RAPPORT_VALIDATION_STANDARD',
                'libelle' => 'Validation institutionnelle des rapports',
                'description' => 'Workflow de revue, validation et publication des rapports périodiques.',
                'module_cible' => 'rapport',
                'type_objet' => \App\Models\Rapport::class,
                'steps' => [
                    ['code' => 'soumission', 'libelle' => 'Soumission du rapport', 'ordre' => 1, 'permission_requise' => 'rapport.creer', 'est_etape_initiale' => true],
                    ['code' => 'revue_direction', 'libelle' => 'Revue directionnelle', 'ordre' => 2, 'permission_requise' => 'workflow.approuver'],
                    ['code' => 'validation_sg', 'libelle' => 'Validation SG', 'ordre' => 3, 'role_requis' => 'secretaire_general', 'permission_requise' => 'workflow.approuver'],
                    ['code' => 'publication', 'libelle' => 'Publication', 'ordre' => 4, 'permission_requise' => 'rapport.publier', 'est_etape_finale' => true],
                ],
            ],
            [
                'code' => 'DECISION_ARBITRAGE',
                'libelle' => 'Arbitrage et validation des décisions',
                'description' => 'Workflow de formalisation, validation et mise en exécution des décisions institutionnelles.',
                'module_cible' => 'decision',
                'type_objet' => \App\Models\Decision::class,
                'steps' => [
                    ['code' => 'redaction', 'libelle' => 'Rédaction de la décision', 'ordre' => 1, 'permission_requise' => 'decision.creer', 'est_etape_initiale' => true],
                    ['code' => 'arbitrage', 'libelle' => 'Arbitrage institutionnel', 'ordre' => 2, 'permission_requise' => 'workflow.approuver'],
                    ['code' => 'validation', 'libelle' => 'Validation finale', 'ordre' => 3, 'permission_requise' => 'decision.valider'],
                    ['code' => 'execution', 'libelle' => 'Mise en oeuvre', 'ordre' => 4, 'permission_requise' => 'decision.executer', 'est_etape_finale' => true],
                ],
            ],
        ];

        foreach ($definitions as $definitionData) {
            $steps = $definitionData['steps'];
            unset($definitionData['steps']);

            $definition = WorkflowDefinition::updateOrCreate(
                ['code' => $definitionData['code']],
                array_merge($definitionData, [
                    'actif' => true,
                    'version' => 1,
                    'cree_par' => $adminId,
                    'maj_par' => $adminId,
                ])
            );

            foreach ($steps as $stepData) {
                WorkflowStep::updateOrCreate(
                    [
                        'workflow_definition_id' => $definition->id,
                        'code' => $stepData['code'],
                    ],
                    array_merge([
                        'description' => null,
                        'role_requis' => null,
                        'permission_requise' => null,
                        'validation_multiple' => false,
                        'nb_validateurs_min' => null,
                        'est_etape_initiale' => false,
                        'est_etape_finale' => false,
                        'delai_jours' => 7,
                        'escalade_apres_jours' => 3,
                    ], $stepData)
                );
            }
        }
    }
}
