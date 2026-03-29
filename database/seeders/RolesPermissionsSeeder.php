<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ═══════════════════════════════════════════════════════════════
        // PERMISSIONS
        // ═══════════════════════════════════════════════════════════════

        $permissions = [

            // ── PAPA ────────────────────────────────────────────────────────
            'papa.voir',
            'papa.creer',
            'papa.modifier',
            'papa.supprimer',
            'papa.soumettre',
            'papa.valider',
            'papa.rejeter',
            'papa.archiver',
            'papa.cloner',
            'papa.verrouiller',
            'papa.voir_archive',

            // ── Actions Prioritaires ─────────────────────────────────────────
            'action_prioritaire.voir',
            'action_prioritaire.creer',
            'action_prioritaire.modifier',
            'action_prioritaire.supprimer',

            // ── Objectifs Immédiats ─────────────────────────────────────────
            'objectif_immediat.voir',
            'objectif_immediat.creer',
            'objectif_immediat.modifier',
            'objectif_immediat.supprimer',

            // ── Résultats Attendus ─────────────────────────────────────────
            'resultat_attendu.voir',
            'resultat_attendu.creer',
            'resultat_attendu.modifier',
            'resultat_attendu.supprimer',

            // ── Indicateurs ────────────────────────────────────────────────
            'indicateur.voir',
            'indicateur.creer',
            'indicateur.modifier',
            'indicateur.supprimer',
            'indicateur.saisir_valeur',
            'indicateur.valider_valeur',

            // ── Activités ─────────────────────────────────────────────────
            'activite.voir',
            'activite.creer',
            'activite.modifier',
            'activite.supprimer',
            'activite.mettre_a_jour_avancement',
            'activite.voir_toutes_directions',

            // ── Tâches ────────────────────────────────────────────────────
            'tache.voir',
            'tache.creer',
            'tache.modifier',
            'tache.supprimer',

            // ── Budget ────────────────────────────────────────────────────
            'budget.voir',
            'budget.creer',
            'budget.modifier',
            'budget.supprimer',
            'budget.valider',
            'budget.voir_consolidation',

            // ── Alertes ───────────────────────────────────────────────────
            'alerte.voir',
            'alerte.creer',
            'alerte.traiter',
            'alerte.escalader',
            'alerte.configurer',

            // ── Risques ───────────────────────────────────────────────────
            'risque.voir',
            'risque.creer',
            'risque.modifier',
            'risque.supprimer',

            // ── GED ───────────────────────────────────────────────────────
            'document.voir',
            'document.deposer',
            'document.modifier',
            'document.supprimer',
            'document.valider',
            'document.archiver',
            'document.telecharger',
            'document.voir_confidentiel',

            // ── Rapports ──────────────────────────────────────────────────
            'rapport.voir',
            'rapport.creer',
            'rapport.modifier',
            'rapport.valider',
            'rapport.publier',
            'rapport.exporter',

            // ── Workflow / Décisions / Audit ──────────────────────────────────
            'workflow.voir',
            'workflow.demarrer',
            'workflow.approuver',
            'workflow.rejeter',
            'workflow.commenter',
            'decision.voir',
            'decision.creer',
            'decision.valider',
            'decision.executer',
            'audit_event.voir',
            'notification_rule.gerer',

            // ── Dashboard ─────────────────────────────────────────────────
            'dashboard.president',
            'dashboard.vice_president',
            'dashboard.commissaire',
            'dashboard.sg',
            'dashboard.direction',
            'dashboard.service',
            'dashboard.audit',

            // ── Administration ────────────────────────────────────────────
            'admin.utilisateurs',
            'admin.roles',
            'admin.structure',
            'admin.partenaires',
            'admin.parametres',
            'admin.audit_log',
            'admin.purger',

            // ── Paramètres ────────────────────────────────────────────────
            'parametres.generaux.voir',
            'parametres.generaux.modifier',
            'parametres.papa.voir',
            'parametres.papa.modifier',
            'parametres.papa.archiver',
            'parametres.referentiels.voir',
            'parametres.referentiels.gerer',
            'parametres.libelles.voir',
            'parametres.libelles.modifier',
            'parametres.rbm.voir',
            'parametres.rbm.modifier',
            'parametres.budget.voir',
            'parametres.budget.modifier',
            'parametres.alertes.voir',
            'parametres.alertes.modifier',
            'parametres.ged.voir',
            'parametres.ged.modifier',
            'parametres.workflows.voir',
            'parametres.workflows.modifier',
            'parametres.droits.voir',
            'parametres.droits.modifier',
            'parametres.affichage.voir',
            'parametres.affichage.modifier',
            'parametres.technique.voir',
            'parametres.technique.modifier',
            'parametres.journal.voir',
            'parametres.sauvegardes.voir',
            'parametres.sauvegardes.exporter',
            'parametres.sauvegardes.importer',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ═══════════════════════════════════════════════════════════════
        // RÔLES
        // ═══════════════════════════════════════════════════════════════

        // Super Admin — accès total
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Président — vision stratégique consolidée
        $president = Role::firstOrCreate(['name' => 'president', 'guard_name' => 'web']);
        $president->syncPermissions([
            'papa.voir', 'papa.valider', 'papa.rejeter', 'papa.archiver', 'papa.verrouiller', 'papa.voir_archive',
            'action_prioritaire.voir', 'objectif_immediat.voir', 'resultat_attendu.voir',
            'indicateur.voir', 'activite.voir', 'activite.voir_toutes_directions',
            'budget.voir', 'budget.voir_consolidation',
            'alerte.voir', 'alerte.traiter', 'alerte.escalader',
            'risque.voir',
            'document.voir', 'document.telecharger', 'document.voir_confidentiel',
            'rapport.voir', 'rapport.exporter',
            'workflow.voir', 'workflow.approuver',
            'decision.voir', 'decision.valider', 'decision.executer',
            'audit_event.voir',
            'dashboard.president',
        ]);

        // Vice-Président — arbitrage stratégique
        $vp = Role::firstOrCreate(['name' => 'vice_president', 'guard_name' => 'web']);
        $vp->syncPermissions([
            'papa.voir', 'papa.valider', 'papa.rejeter', 'papa.voir_archive',
            'action_prioritaire.voir', 'objectif_immediat.voir', 'resultat_attendu.voir',
            'indicateur.voir', 'activite.voir', 'activite.voir_toutes_directions',
            'budget.voir', 'budget.voir_consolidation',
            'alerte.voir', 'alerte.traiter', 'alerte.escalader',
            'risque.voir',
            'document.voir', 'document.telecharger', 'document.voir_confidentiel',
            'rapport.voir', 'rapport.exporter',
            'workflow.voir', 'workflow.approuver',
            'decision.voir', 'decision.valider',
            'audit_event.voir',
            'dashboard.vice_president',
        ]);

        // Commissaire — pilotage sectoriel
        $commissaire = Role::firstOrCreate(['name' => 'commissaire', 'guard_name' => 'web']);
        $commissaire->syncPermissions([
            'papa.voir', 'papa.soumettre',
            'action_prioritaire.voir', 'action_prioritaire.creer', 'action_prioritaire.modifier',
            'objectif_immediat.voir', 'objectif_immediat.creer', 'objectif_immediat.modifier',
            'resultat_attendu.voir', 'resultat_attendu.creer', 'resultat_attendu.modifier',
            'indicateur.voir', 'indicateur.valider_valeur',
            'activite.voir', 'activite.voir_toutes_directions', 'activite.modifier',
            'budget.voir', 'budget.voir_consolidation', 'budget.valider',
            'alerte.voir', 'alerte.traiter', 'alerte.escalader',
            'risque.voir', 'risque.creer', 'risque.modifier',
            'document.voir', 'document.telecharger', 'document.voir_confidentiel',
            'rapport.voir', 'rapport.valider', 'rapport.exporter',
            'workflow.voir', 'workflow.approuver', 'workflow.commenter',
            'decision.voir', 'decision.creer', 'decision.valider',
            'audit_event.voir',
            'dashboard.commissaire',
        ]);

        // Secrétaire Général — coordination administrative
        $sg = Role::firstOrCreate(['name' => 'secretaire_general', 'guard_name' => 'web']);
        $sg->syncPermissions([
            'papa.voir', 'papa.soumettre', 'papa.valider', 'papa.cloner',
            'action_prioritaire.voir', 'action_prioritaire.creer', 'action_prioritaire.modifier',
            'objectif_immediat.voir', 'objectif_immediat.creer', 'objectif_immediat.modifier',
            'resultat_attendu.voir', 'resultat_attendu.creer', 'resultat_attendu.modifier',
            'indicateur.voir', 'indicateur.valider_valeur',
            'activite.voir', 'activite.voir_toutes_directions', 'activite.modifier',
            'budget.voir', 'budget.voir_consolidation', 'budget.valider',
            'alerte.voir', 'alerte.traiter', 'alerte.escalader', 'alerte.configurer',
            'risque.voir', 'risque.creer', 'risque.modifier',
            'document.voir', 'document.telecharger', 'document.voir_confidentiel', 'document.valider',
            'rapport.voir', 'rapport.creer', 'rapport.valider', 'rapport.publier', 'rapport.exporter',
            'workflow.voir', 'workflow.demarrer', 'workflow.approuver', 'workflow.rejeter', 'workflow.commenter',
            'decision.voir', 'decision.creer', 'decision.valider', 'decision.executer',
            'audit_event.voir', 'notification_rule.gerer',
            'dashboard.sg',
            'admin.utilisateurs', 'admin.structure', 'admin.partenaires',
        ]);

        // Directeur Technique — responsable opérationnel sectoriel
        $dirTech = Role::firstOrCreate(['name' => 'directeur_technique', 'guard_name' => 'web']);
        $dirTech->syncPermissions([
            'papa.voir',
            'action_prioritaire.voir',
            'objectif_immediat.voir', 'objectif_immediat.creer', 'objectif_immediat.modifier',
            'resultat_attendu.voir', 'resultat_attendu.creer', 'resultat_attendu.modifier',
            'indicateur.voir', 'indicateur.creer', 'indicateur.modifier', 'indicateur.saisir_valeur', 'indicateur.valider_valeur',
            'activite.voir', 'activite.creer', 'activite.modifier', 'activite.mettre_a_jour_avancement',
            'tache.voir', 'tache.creer', 'tache.modifier',
            'budget.voir', 'budget.creer', 'budget.modifier', 'budget.supprimer',
            'alerte.voir', 'alerte.traiter',
            'risque.voir', 'risque.creer', 'risque.modifier',
            'document.voir', 'document.deposer', 'document.modifier', 'document.telecharger', 'document.valider',
            'rapport.voir', 'rapport.creer', 'rapport.modifier', 'rapport.valider', 'rapport.exporter',
            'workflow.voir', 'workflow.demarrer', 'workflow.commenter',
            'decision.voir', 'decision.creer',
            'dashboard.direction',
        ]);

        // Directeur Appui — responsable d'appui et de soutien (identiques droits dans périmètre)
        $dirAppui = Role::firstOrCreate(['name' => 'directeur_appui', 'guard_name' => 'web']);
        $dirAppui->syncPermissions([
            'papa.voir',
            'action_prioritaire.voir',
            'objectif_immediat.voir', 'objectif_immediat.creer', 'objectif_immediat.modifier',
            'resultat_attendu.voir', 'resultat_attendu.creer', 'resultat_attendu.modifier',
            'indicateur.voir', 'indicateur.creer', 'indicateur.modifier', 'indicateur.saisir_valeur', 'indicateur.valider_valeur',
            'activite.voir', 'activite.creer', 'activite.modifier', 'activite.mettre_a_jour_avancement',
            'tache.voir', 'tache.creer', 'tache.modifier',
            'budget.voir', 'budget.creer', 'budget.modifier', 'budget.supprimer',
            'alerte.voir', 'alerte.traiter',
            'risque.voir', 'risque.creer', 'risque.modifier',
            'document.voir', 'document.deposer', 'document.modifier', 'document.telecharger', 'document.valider',
            'rapport.voir', 'rapport.creer', 'rapport.modifier', 'rapport.valider', 'rapport.exporter',
            'workflow.voir', 'workflow.demarrer', 'workflow.commenter',
            'decision.voir', 'decision.creer',
            'dashboard.direction',
        ]);

        // Chef de Service
        $chefService = Role::firstOrCreate(['name' => 'chef_service', 'guard_name' => 'web']);
        $chefService->syncPermissions([
            'papa.voir',
            'action_prioritaire.voir', 'objectif_immediat.voir', 'resultat_attendu.voir',
            'indicateur.voir', 'indicateur.saisir_valeur',
            'activite.voir', 'activite.modifier', 'activite.mettre_a_jour_avancement',
            'tache.voir', 'tache.creer', 'tache.modifier',
            'budget.voir',
            'alerte.voir',
            'risque.voir',
            'document.voir', 'document.deposer', 'document.telecharger',
            'rapport.voir', 'rapport.creer', 'rapport.modifier',
            'workflow.voir', 'workflow.commenter',
            'decision.voir',
            'dashboard.service',
        ]);

        // Point Focal — exécution et mise à jour
        $pointFocal = Role::firstOrCreate(['name' => 'point_focal', 'guard_name' => 'web']);
        $pointFocal->syncPermissions([
            'papa.voir',
            'action_prioritaire.voir', 'objectif_immediat.voir', 'resultat_attendu.voir',
            'indicateur.voir', 'indicateur.saisir_valeur',
            'activite.voir', 'activite.mettre_a_jour_avancement',
            'tache.voir', 'tache.modifier',
            'budget.voir',
            'alerte.voir',
            'document.voir', 'document.deposer', 'document.telecharger',
            'rapport.voir',
            'workflow.voir',
            'decision.voir',
            'dashboard.service',
        ]);

        // Auditeur Interne — lecture seule + audit
        $auditeur = Role::firstOrCreate(['name' => 'auditeur_interne', 'guard_name' => 'web']);
        $auditeur->syncPermissions([
            'papa.voir', 'papa.voir_archive',
            'action_prioritaire.voir', 'objectif_immediat.voir', 'resultat_attendu.voir',
            'indicateur.voir', 'activite.voir', 'activite.voir_toutes_directions',
            'budget.voir', 'budget.voir_consolidation',
            'alerte.voir', 'risque.voir',
            'document.voir', 'document.telecharger', 'document.voir_confidentiel',
            'rapport.voir', 'rapport.exporter',
            'workflow.voir',
            'decision.voir',
            'audit_event.voir',
            'dashboard.audit',
            'admin.audit_log',
            'parametres.journal.voir',
            'parametres.generaux.voir',
            'parametres.papa.voir',
            'parametres.referentiels.voir',
        ]);

        // Contrôle Financier Central
        $controlFinancier = Role::firstOrCreate(['name' => 'controle_financier', 'guard_name' => 'web']);
        $controlFinancier->syncPermissions([
            'papa.voir',
            'budget.voir', 'budget.voir_consolidation', 'budget.valider',
            'activite.voir', 'activite.voir_toutes_directions',
            'document.voir', 'document.telecharger', 'document.voir_confidentiel',
            'rapport.voir', 'rapport.exporter',
            'workflow.voir',
            'decision.voir',
            'audit_event.voir',
            'dashboard.audit',
            'admin.audit_log',
        ]);

        $agenceComptable = Role::firstOrCreate(['name' => 'agence_comptable', 'guard_name' => 'web']);
        $agenceComptable->syncPermissions([
            'papa.voir',
            'budget.voir', 'budget.modifier', 'budget.voir_consolidation',
            'activite.voir',
            'document.voir', 'document.deposer', 'document.telecharger', 'document.voir_confidentiel',
            'rapport.voir', 'rapport.exporter',
            'workflow.voir',
            'decision.voir',
            'audit_event.voir',
            'admin.audit_log',
        ]);

        $adminFonctionnel = Role::firstOrCreate(['name' => 'administrateur_fonctionnel', 'guard_name' => 'web']);
        $adminFonctionnel->syncPermissions([
            'papa.voir', 'papa.creer', 'papa.modifier', 'papa.cloner',
            'action_prioritaire.voir', 'action_prioritaire.creer', 'action_prioritaire.modifier',
            'objectif_immediat.voir', 'objectif_immediat.creer', 'objectif_immediat.modifier',
            'resultat_attendu.voir', 'resultat_attendu.creer', 'resultat_attendu.modifier',
            'indicateur.voir', 'indicateur.creer', 'indicateur.modifier', 'indicateur.saisir_valeur',
            'activite.voir', 'activite.creer', 'activite.modifier', 'activite.mettre_a_jour_avancement',
            'tache.voir', 'tache.creer', 'tache.modifier',
            'budget.voir', 'budget.creer', 'budget.modifier', 'budget.supprimer',
            'alerte.voir', 'alerte.traiter',
            'risque.voir', 'risque.creer', 'risque.modifier',
            'document.voir', 'document.deposer', 'document.modifier', 'document.telecharger',
            'rapport.voir', 'rapport.creer', 'rapport.modifier', 'rapport.exporter',
            'workflow.voir', 'workflow.demarrer', 'workflow.commenter',
            'decision.voir', 'decision.creer',
            'notification_rule.gerer',
            'admin.parametres',
            // Paramètres
            'parametres.generaux.voir', 'parametres.generaux.modifier',
            'parametres.papa.voir', 'parametres.papa.modifier', 'parametres.papa.archiver',
            'parametres.referentiels.voir', 'parametres.referentiels.gerer',
            'parametres.libelles.voir', 'parametres.libelles.modifier',
            'parametres.rbm.voir', 'parametres.rbm.modifier',
            'parametres.budget.voir', 'parametres.budget.modifier',
            'parametres.alertes.voir', 'parametres.alertes.modifier',
            'parametres.ged.voir', 'parametres.ged.modifier',
            'parametres.workflows.voir', 'parametres.workflows.modifier',
            'parametres.droits.voir',
            'parametres.affichage.voir', 'parametres.affichage.modifier',
            'parametres.journal.voir',
            'parametres.sauvegardes.voir', 'parametres.sauvegardes.exporter',
        ]);

        // Conseiller Juridique
        $cj = Role::firstOrCreate(['name' => 'conseiller_juridique', 'guard_name' => 'web']);
        $cj->syncPermissions([
            'papa.voir',
            'action_prioritaire.voir', 'objectif_immediat.voir', 'resultat_attendu.voir',
            'activite.voir',
            'document.voir', 'document.telecharger', 'document.voir_confidentiel',
            'rapport.voir',
            'decision.voir',
            'risque.voir',
        ]);

        // Lecteur — accès lecture seule minimal
        $lecteur = Role::firstOrCreate(['name' => 'lecteur', 'guard_name' => 'web']);
        $lecteur->syncPermissions([
            'papa.voir',
            'action_prioritaire.voir', 'objectif_immediat.voir', 'resultat_attendu.voir',
            'indicateur.voir', 'activite.voir',
            'document.voir', 'document.telecharger',
            'rapport.voir',
            'decision.voir',
        ]);

        $this->command->info('Rôles et permissions créés avec succès.');
    }
}
