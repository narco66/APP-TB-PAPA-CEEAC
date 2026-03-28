<?php

namespace Database\Seeders;

use App\Models\ActionPrioritaire;
use App\Models\Activite;
use App\Models\BudgetPapa;
use App\Models\Departement;
use App\Models\Direction;
use App\Models\Indicateur;
use App\Models\Jalon;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemonstrationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@ceeac-eccas.org')->first();
        $dps   = Departement::where('code', 'DPS')->first();
        $die   = Departement::where('code', 'DIE')->first();
        $sg    = Departement::where('code', 'SG')->first();
        $dcpa  = Direction::where('code', 'DCPA')->first();
        $dce   = Direction::where('code', 'DCE')->first();
        $daf   = Direction::where('code', 'DAF')->first();
        $dirTech = User::where('email', 'dir.dcpa@ceeac-eccas.org')->first();
        $pf      = User::where('email', 'pf.dce@ceeac-eccas.org')->first();

        // ── PAPA 2025 ─────────────────────────────────────────────────────
        $papa = Papa::updateOrCreate(['code' => 'PAPA-2025'], [
            'libelle'         => 'Plan d\'Action Prioritaire Annuel 2025',
            'annee'           => 2025,
            'date_debut'      => '2025-01-01',
            'date_fin'        => '2025-12-31',
            'description'     => 'PAPA de la Commission de la CEEAC pour l\'exercice 2025',
            'statut'          => 'en_execution',
            'budget_total_prevu' => 45_000_000_00, // 4.5 Milliards XAF
            'devise'          => 'XAF',
            'taux_execution_physique' => 38.5,
            'taux_execution_financiere' => 31.2,
            'created_by'      => $admin?->id,
        ]);

        // ── Action Prioritaire 1 (Technique - DPS) ───────────────────────
        $ap1 = ActionPrioritaire::updateOrCreate(['code' => 'AP-2025-01'], [
            'papa_id'         => $papa->id,
            'departement_id'  => $dps?->id,
            'libelle'         => 'Renforcement du mécanisme de prévention des conflits en Afrique Centrale',
            'qualification'   => 'technique',
            'ordre'           => 1,
            'priorite'        => 'critique',
            'statut'          => 'en_cours',
            'taux_realisation' => 45.0,
            'created_by'      => $admin?->id,
        ]);

        $oi1 = ObjectifImmediats::updateOrCreate(['code' => 'OI-2025-01-01'], [
            'action_prioritaire_id' => $ap1->id,
            'libelle'    => 'Renforcer les capacités institutionnelles du mécanisme d\'alerte précoce',
            'statut'     => 'en_cours',
            'taux_atteinte' => 50.0,
            'responsable_id' => $dirTech?->id,
        ]);

        $ra1 = ResultatAttendu::updateOrCreate(['code' => 'RA-2025-01-01-01'], [
            'objectif_immediat_id' => $oi1->id,
            'libelle'         => 'Le système MARAC est opérationnel et dispose d\'un tableau de bord en temps réel',
            'type_resultat'   => 'output',
            'statut'          => 'en_cours',
            'taux_atteinte'   => 55.0,
            'preuve_requise'  => true,
            'type_preuve_attendue' => 'Rapport de mise en service + Capture d\'écran',
        ]);

        // Indicateur
        $ind1 = Indicateur::updateOrCreate(['code' => 'IND-2025-01-01-01'], [
            'resultat_attendu_id'   => $ra1->id,
            'libelle'               => 'Nombre d\'alertes précoces traitées par le MARAC',
            'unite_mesure'          => 'nombre',
            'type_indicateur'       => 'quantitatif',
            'valeur_baseline'       => 0,
            'valeur_cible_annuelle' => 50,
            'frequence_collecte'    => 'trimestrielle',
            'source_donnees'        => 'Base de données MARAC',
            'responsable_id'        => $dirTech?->id,
            'direction_id'          => $dcpa?->id,
            'seuil_alerte_rouge'    => 40,
            'seuil_alerte_orange'   => 60,
            'seuil_alerte_vert'     => 80,
            'taux_realisation_courant' => 52.0,
            'tendance'              => 'hausse',
        ]);

        // Activité
        $act1 = Activite::updateOrCreate(['code' => 'ACT-2025-01-01-01-01'], [
            'resultat_attendu_id' => $ra1->id,
            'direction_id'        => $dcpa?->id,
            'libelle'             => 'Déploiement du module de tableau de bord MARAC',
            'statut'              => 'en_cours',
            'taux_realisation'    => 65.0,
            'date_debut_prevue'   => '2025-02-01',
            'date_fin_prevue'     => '2025-06-30',
            'date_debut_reelle'   => '2025-02-15',
            'responsable_id'      => $dirTech?->id,
            'point_focal_id'      => $dirTech?->id,
            'budget_prevu'        => 85_000_000,  // 85M XAF
            'budget_engage'       => 60_000_000,
            'budget_consomme'     => 45_000_000,
            'devise'              => 'XAF',
            'priorite'            => 'haute',
            'created_by'          => $admin?->id,
        ]);

        // Jalon
        Jalon::updateOrCreate(['code' => 'JAL-2025-01-01-01-01-01'], [
            'activite_id'   => $act1->id,
            'libelle'       => 'Mise en production du tableau de bord MARAC',
            'date_prevue'   => '2025-04-30',
            'date_reelle'   => '2025-05-10',
            'statut'        => 'atteint',
            'est_critique'  => true,
        ]);

        // Budget
        BudgetPapa::updateOrCreate(
            ['papa_id' => $papa->id, 'action_prioritaire_id' => $ap1->id, 'source_financement' => 'budget_ceeac'],
            [
                'libelle_ligne'     => 'Mécanisme de prévention des conflits',
                'annee_budgetaire'  => 2025,
                'devise'            => 'XAF',
                'montant_prevu'     => 500_000_000,
                'montant_engage'    => 250_000_000,
                'montant_decaisse'  => 180_000_000,
                'montant_solde'     => 250_000_000,
                'created_by'        => $admin?->id,
            ]
        );

        // ── Action Prioritaire 2 (Appui - SG/DAF) ───────────────────────
        $ap2 = ActionPrioritaire::updateOrCreate(['code' => 'AP-2025-05'], [
            'papa_id'         => $papa->id,
            'departement_id'  => $sg?->id,
            'libelle'         => 'Modernisation du système de gestion financière et budgétaire de la Commission',
            'qualification'   => 'appui',
            'ordre'           => 5,
            'priorite'        => 'haute',
            'statut'          => 'en_cours',
            'taux_realisation' => 30.0,
            'created_by'      => $admin?->id,
        ]);

        $oi2 = ObjectifImmediats::updateOrCreate(['code' => 'OI-2025-05-01'], [
            'action_prioritaire_id' => $ap2->id,
            'libelle'    => 'Déployer un système intégré de gestion financière (SIGF)',
            'statut'     => 'en_cours',
            'taux_atteinte' => 30.0,
        ]);

        $ra2 = ResultatAttendu::updateOrCreate(['code' => 'RA-2025-05-01-01'], [
            'objectif_immediat_id' => $oi2->id,
            'libelle'         => 'Le SIGF est déployé et opérationnel dans toutes les directions',
            'type_resultat'   => 'output',
            'statut'          => 'en_cours',
            'taux_atteinte'   => 25.0,
            'preuve_requise'  => true,
        ]);

        $act2 = Activite::updateOrCreate(['code' => 'ACT-2025-05-01-01-01'], [
            'resultat_attendu_id' => $ra2->id,
            'direction_id'        => $daf?->id,
            'libelle'             => 'Acquisition et paramétrage du logiciel SIGF',
            'statut'              => 'en_cours',
            'taux_realisation'    => 40.0,
            'date_debut_prevue'   => '2025-01-15',
            'date_fin_prevue'     => '2025-08-31',
            'date_debut_reelle'   => '2025-01-20',
            'responsable_id'      => $admin?->id,
            'budget_prevu'        => 120_000_000,
            'budget_engage'       => 80_000_000,
            'budget_consomme'     => 35_000_000,
            'devise'              => 'XAF',
            'priorite'            => 'haute',
            'created_by'          => $admin?->id,
        ]);

        $this->command->info('Données de démonstration créées avec succès.');
    }
}
