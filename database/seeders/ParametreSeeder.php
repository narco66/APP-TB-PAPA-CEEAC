<?php

namespace Database\Seeders;

use App\Models\LibelleMetier;
use App\Models\Parametre;
use App\Models\Referentiel;
use Illuminate\Database\Seeder;

class ParametreSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedParametresGeneraux();
        $this->seedLibellesMetier();
        $this->seedReferentiels();
    }

    private function seedParametresGeneraux(): void
    {
        $items = [
            ['cle' => 'app_nom',                   'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => 'TB-PAPA-CEEAC',                          'libelle' => 'Nom de l\'application'],
            ['cle' => 'app_sigle',                 'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => 'TB-PAPA',                                'libelle' => 'Sigle'],
            ['cle' => 'app_organisation',          'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => 'Commission de la CEEAC',                  'libelle' => 'Organisation'],
            ['cle' => 'app_langue_defaut',         'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => 'fr',                                     'libelle' => 'Langue par défaut'],
            ['cle' => 'app_fuseau_horaire',        'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => 'Africa/Libreville',                      'libelle' => 'Fuseau horaire'],
            ['cle' => 'app_devise',                'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => 'FCFA',                                   'libelle' => 'Devise par défaut'],
            ['cle' => 'app_format_date',           'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => 'd/m/Y',                                  'libelle' => 'Format de date'],
            ['cle' => 'app_format_monetaire',      'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => 'fr',                                     'libelle' => 'Format monétaire'],
            ['cle' => 'app_annee_reference',       'groupe' => 'general',   'type' => 'integer', 'valeur_defaut' => (string) now()->year,                     'libelle' => 'Année de référence'],
            ['cle' => 'app_pied_page',             'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => '© Commission de la CEEAC — Tous droits réservés', 'libelle' => 'Texte pied de page'],
            ['cle' => 'app_couleur_primaire',      'groupe' => 'general',   'type' => 'string',  'valeur_defaut' => '#4338ca',                                'libelle' => 'Couleur primaire'],
            ['cle' => 'app_maintenance',           'groupe' => 'technique', 'type' => 'boolean', 'valeur_defaut' => 'false',  'libelle' => 'Mode maintenance',     'est_systeme' => true],
            ['cle' => 'session_duree_minutes',     'groupe' => 'technique', 'type' => 'integer', 'valeur_defaut' => '120',    'libelle' => 'Durée session (min)',   'est_systeme' => true],
            ['cle' => 'upload_taille_max_mo',      'groupe' => 'ged',       'type' => 'integer', 'valeur_defaut' => '20',     'libelle' => 'Taille max upload (Mo)'],
            ['cle' => 'upload_formats_autorises',  'groupe' => 'ged',       'type' => 'string',  'valeur_defaut' => 'pdf,docx,xlsx,pptx,jpg,png',             'libelle' => 'Formats autorisés'],
            ['cle' => 'alerte_seuil_retard_jours', 'groupe' => 'alertes',   'type' => 'integer', 'valeur_defaut' => '7',      'libelle' => 'Seuil retard (jours)'],
            ['cle' => 'alerte_seuil_budget_pct',   'groupe' => 'alertes',   'type' => 'integer', 'valeur_defaut' => '80',     'libelle' => 'Seuil alerte budgétaire (%)'],
            ['cle' => 'rbm_seuil_atteint',         'groupe' => 'rbm',       'type' => 'integer', 'valeur_defaut' => '80',     'libelle' => 'Seuil résultat atteint (%)'],
            ['cle' => 'rbm_seuil_risque',          'groupe' => 'rbm',       'type' => 'integer', 'valeur_defaut' => '50',     'libelle' => 'Seuil résultat en risque (%)'],
            ['cle' => 'rbm_seuil_non_atteint',     'groupe' => 'rbm',       'type' => 'integer', 'valeur_defaut' => '30',     'libelle' => 'Seuil résultat non atteint (%)'],
            ['cle' => 'rbm_prefixe_ap',            'groupe' => 'rbm',       'type' => 'string',  'valeur_defaut' => 'AP',     'libelle' => 'Préfixe Actions Prioritaires'],
            ['cle' => 'rbm_prefixe_oi',            'groupe' => 'rbm',       'type' => 'string',  'valeur_defaut' => 'OI',     'libelle' => 'Préfixe Objectifs Immédiats'],
            ['cle' => 'rbm_prefixe_ra',            'groupe' => 'rbm',       'type' => 'string',  'valeur_defaut' => 'RA',     'libelle' => 'Préfixe Résultats Attendus'],
            ['cle' => 'pagination_items',          'groupe' => 'affichage', 'type' => 'integer', 'valeur_defaut' => '20',     'libelle' => 'Éléments par page'],
            ['cle' => 'export_format_defaut',      'groupe' => 'affichage', 'type' => 'string',  'valeur_defaut' => 'xlsx',   'libelle' => 'Format d\'export par défaut'],
        ];

        foreach ($items as $item) {
            Parametre::updateOrCreate(
                ['cle' => $item['cle']],
                array_merge(['est_systeme' => false, 'est_sensible' => false], $item)
            );
        }
    }

    private function seedLibellesMetier(): void
    {
        $items = [
            ['cle' => 'papa.label',               'module' => 'papa',    'valeur_defaut' => 'Plan d\'Action Prioritaire Annuel'],
            ['cle' => 'papa.sigle',               'module' => 'papa',    'valeur_defaut' => 'PAPA'],
            ['cle' => 'papa.action_prioritaire',  'module' => 'papa',    'valeur_defaut' => 'Action prioritaire'],
            ['cle' => 'papa.objectif_immediat',   'module' => 'papa',    'valeur_defaut' => 'Objectif immédiat'],
            ['cle' => 'papa.resultat_attendu',    'module' => 'papa',    'valeur_defaut' => 'Résultat attendu'],
            ['cle' => 'papa.point_focal',         'module' => 'papa',    'valeur_defaut' => 'Point focal'],
            ['cle' => 'papa.direction_appui',     'module' => 'papa',    'valeur_defaut' => 'Direction d\'appui'],
            ['cle' => 'admin.commissaire',        'module' => 'admin',   'valeur_defaut' => 'Commissaire',         'est_systeme' => true],
            ['cle' => 'admin.secretaire_general', 'module' => 'admin',   'valeur_defaut' => 'Secrétaire Général',  'est_systeme' => true],
            ['cle' => 'admin.president',          'module' => 'admin',   'valeur_defaut' => 'Président',           'est_systeme' => true],
            ['cle' => 'budget.label',             'module' => 'budget',  'valeur_defaut' => 'Budget'],
            ['cle' => 'budget.partenaire',        'module' => 'budget',  'valeur_defaut' => 'Budget partenaire'],
            ['cle' => 'ged.piece_justificative',  'module' => 'ged',     'valeur_defaut' => 'Pièce justificative'],
            ['cle' => 'alerte.critique',          'module' => 'alertes', 'valeur_defaut' => 'Alerte critique'],
            ['cle' => 'alerte.retard',            'module' => 'alertes', 'valeur_defaut' => 'Alerte de retard'],
        ];

        foreach ($items as $item) {
            LibelleMetier::updateOrCreate(
                ['cle' => $item['cle']],
                array_merge(['est_systeme' => false, 'traductible' => false, 'locale' => 'fr'], $item)
            );
        }
    }

    private function seedReferentiels(): void
    {
        $items = [
            // Catégories d'actions
            ['type' => 'categorie_action', 'code' => 'INST',  'libelle' => 'Renforcement institutionnel', 'ordre' => 1, 'est_systeme' => true],
            ['type' => 'categorie_action', 'code' => 'TECH',  'libelle' => 'Appui technique',            'ordre' => 2, 'est_systeme' => true],
            ['type' => 'categorie_action', 'code' => 'FORM',  'libelle' => 'Formation et capacités',     'ordre' => 3],
            ['type' => 'categorie_action', 'code' => 'INFRA', 'libelle' => 'Infrastructure',             'ordre' => 4],
            ['type' => 'categorie_action', 'code' => 'COMM',  'libelle' => 'Communication',              'ordre' => 5],
            ['type' => 'categorie_action', 'code' => 'RECH',  'libelle' => 'Recherche et études',        'ordre' => 6],
            // Types d'indicateurs
            ['type' => 'type_indicateur', 'code' => 'IMPACT',    'libelle' => 'Impact',     'est_systeme' => true, 'ordre' => 1],
            ['type' => 'type_indicateur', 'code' => 'EFFET',     'libelle' => 'Effet',      'est_systeme' => true, 'ordre' => 2],
            ['type' => 'type_indicateur', 'code' => 'PRODUIT',   'libelle' => 'Produit',    'est_systeme' => true, 'ordre' => 3],
            ['type' => 'type_indicateur', 'code' => 'PROCESSUS', 'libelle' => 'Processus',  'ordre' => 4],
            ['type' => 'type_indicateur', 'code' => 'INTRANT',   'libelle' => 'Intrant',    'ordre' => 5],
            // Unités de mesure
            ['type' => 'unite_mesure', 'code' => 'PCT',    'libelle' => 'Pourcentage (%)',   'libelle_court' => '%',      'ordre' => 1],
            ['type' => 'unite_mesure', 'code' => 'NB',     'libelle' => 'Nombre',            'libelle_court' => 'nb',     'ordre' => 2],
            ['type' => 'unite_mesure', 'code' => 'JOURS',  'libelle' => 'Jours',             'libelle_court' => 'j',      'ordre' => 3],
            ['type' => 'unite_mesure', 'code' => 'MOIS',   'libelle' => 'Mois',              'libelle_court' => 'mois',   'ordre' => 4],
            ['type' => 'unite_mesure', 'code' => 'FCFA_M', 'libelle' => 'Millions FCFA',     'libelle_court' => 'MFCFA',  'ordre' => 5],
            ['type' => 'unite_mesure', 'code' => 'USD_M',  'libelle' => 'Millions USD',      'libelle_court' => 'MUSD',   'ordre' => 6],
            ['type' => 'unite_mesure', 'code' => 'KM',     'libelle' => 'Kilomètres',        'libelle_court' => 'km',     'ordre' => 7],
            // Sources de financement
            ['type' => 'source_financement', 'code' => 'BUDGET_COMM', 'libelle' => 'Budget de la Commission',         'est_systeme' => true, 'ordre' => 1],
            ['type' => 'source_financement', 'code' => 'ETATS_MEM',   'libelle' => 'Contributions États membres',     'est_systeme' => true, 'ordre' => 2],
            ['type' => 'source_financement', 'code' => 'PARTENAIRES', 'libelle' => 'Partenaires au développement',    'ordre' => 3],
            ['type' => 'source_financement', 'code' => 'UE',          'libelle' => 'Union Européenne',                'ordre' => 4],
            ['type' => 'source_financement', 'code' => 'BM',          'libelle' => 'Banque Mondiale',                 'ordre' => 5],
            ['type' => 'source_financement', 'code' => 'BAD',         'libelle' => 'Banque Africaine de Développement','ordre' => 6],
            ['type' => 'source_financement', 'code' => 'FED',         'libelle' => 'Fonds Européen de Développement', 'ordre' => 7],
            // Fréquences de collecte
            ['type' => 'frequence_collecte', 'code' => 'MENSUEL',     'libelle' => 'Mensuelle',     'ordre' => 1],
            ['type' => 'frequence_collecte', 'code' => 'TRIMESTRIEL', 'libelle' => 'Trimestrielle', 'ordre' => 2],
            ['type' => 'frequence_collecte', 'code' => 'SEMESTRIEL',  'libelle' => 'Semestrielle',  'ordre' => 3],
            ['type' => 'frequence_collecte', 'code' => 'ANNUEL',      'libelle' => 'Annuelle',      'ordre' => 4],
            // Périodes de reporting
            ['type' => 'periode_reporting', 'code' => 'T1', 'libelle' => 'Trimestre 1 (Jan–Mar)', 'ordre' => 1],
            ['type' => 'periode_reporting', 'code' => 'T2', 'libelle' => 'Trimestre 2 (Avr–Jun)', 'ordre' => 2],
            ['type' => 'periode_reporting', 'code' => 'T3', 'libelle' => 'Trimestre 3 (Jul–Sep)', 'ordre' => 3],
            ['type' => 'periode_reporting', 'code' => 'T4', 'libelle' => 'Trimestre 4 (Oct–Déc)', 'ordre' => 4],
            ['type' => 'periode_reporting', 'code' => 'S1', 'libelle' => 'Semestre 1',             'ordre' => 5],
            ['type' => 'periode_reporting', 'code' => 'S2', 'libelle' => 'Semestre 2',             'ordre' => 6],
            ['type' => 'periode_reporting', 'code' => 'AN', 'libelle' => 'Annuel',                 'ordre' => 7],
            // Types de documents
            ['type' => 'type_document', 'code' => 'RAPPORT',      'libelle' => 'Rapport',            'ordre' => 1],
            ['type' => 'type_document', 'code' => 'DECISION',     'libelle' => 'Décision',           'ordre' => 2],
            ['type' => 'type_document', 'code' => 'CONTRAT',      'libelle' => 'Contrat',            'ordre' => 3],
            ['type' => 'type_document', 'code' => 'FACTURE',      'libelle' => 'Facture',            'ordre' => 4],
            ['type' => 'type_document', 'code' => 'JUSTIFICATIF', 'libelle' => 'Pièce justificative','ordre' => 5],
            ['type' => 'type_document', 'code' => 'NOTE_INFO',    'libelle' => 'Note d\'information', 'ordre' => 6],
            ['type' => 'type_document', 'code' => 'AUTRE',        'libelle' => 'Autre',               'ordre' => 99],
            // Niveaux de priorité
            ['type' => 'niveau_priorite', 'code' => 'CRITIQUE', 'libelle' => 'Critique', 'couleur' => 'red',    'ordre' => 1, 'est_systeme' => true],
            ['type' => 'niveau_priorite', 'code' => 'HAUTE',    'libelle' => 'Haute',    'couleur' => 'orange', 'ordre' => 2, 'est_systeme' => true],
            ['type' => 'niveau_priorite', 'code' => 'MOYENNE',  'libelle' => 'Moyenne',  'couleur' => 'yellow', 'ordre' => 3, 'est_systeme' => true],
            ['type' => 'niveau_priorite', 'code' => 'FAIBLE',   'libelle' => 'Faible',   'couleur' => 'green',  'ordre' => 4, 'est_systeme' => true],
        ];

        foreach ($items as $item) {
            Referentiel::updateOrCreate(
                ['type' => $item['type'], 'code' => $item['code']],
                array_merge(['actif' => true, 'est_systeme' => false, 'ordre' => 99], $item)
            );
        }
    }
}
