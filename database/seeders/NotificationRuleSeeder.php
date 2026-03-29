<?php

namespace Database\Seeders;

use App\Models\NotificationRule;
use Illuminate\Database\Seeder;

class NotificationRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'code' => 'WF_SOUMISSION_SG',
                'libelle' => 'Notifier le SG à la soumission d’un PAPA',
                'event_type' => 'workflow_demarre',
                'canal' => 'in_app',
                'role_cible' => 'secretaire_general',
                'template_sujet' => 'Nouvelle soumission à examiner',
                'template_message' => 'Un nouveau dossier a été soumis dans le workflow et nécessite un examen du Secrétariat Général.',
            ],
            [
                'code' => 'WF_REJET_INITIATEUR',
                'libelle' => 'Notifier l’initiateur d’un rejet de workflow',
                'event_type' => 'workflow_rejete',
                'canal' => 'email',
                'permission_cible' => 'workflow.demarrer',
                'template_sujet' => 'Dossier rejeté',
                'template_message' => 'Le dossier a été rejeté. Consultez le motif et préparez les corrections demandées.',
            ],
            [
                'code' => 'DECISION_VALIDEE_PILOTES',
                'libelle' => 'Informer les pilotes de décision validée',
                'event_type' => 'decision_validee',
                'canal' => 'in_app',
                'permission_cible' => 'decision.executer',
                'template_sujet' => 'Décision validée',
                'template_message' => 'Une décision validée est désormais exécutoire et doit être prise en compte dans le pilotage.',
            ],
            [
                'code' => 'AUDIT_CRITIQUE_ADMIN',
                'libelle' => 'Escalader les événements critiques',
                'event_type' => 'audit_critique',
                'canal' => 'email',
                'role_cible' => 'super_admin',
                'delai_minutes' => 0,
                'escalade' => true,
                'template_sujet' => 'Alerte audit critique',
                'template_message' => 'Un événement critique a été détecté dans le journal d’audit et doit être examiné immédiatement.',
            ],
        ];

        foreach ($rules as $rule) {
            NotificationRule::updateOrCreate(
                ['code' => $rule['code']],
                array_merge([
                    'role_cible' => null,
                    'permission_cible' => null,
                    'delai_minutes' => null,
                    'escalade' => false,
                    'actif' => true,
                ], $rule)
            );
        }
    }
}
