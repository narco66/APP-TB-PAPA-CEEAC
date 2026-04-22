<?php

namespace App\Services\Import;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RbmTemplateGenerator
{
    private Spreadsheet $wb;

    /** Définition complète : [nom_feuille => [[col, largeur, obligatoire, exemple], ...]] */
    private const SHEETS = [
        'LISEZ-MOI'              => null, // Feuille d'instructions
        'Departements'           => [
            ['code',            18, true,  'DAP'],
            ['libelle',         40, true,  'Direction des Affaires Politiques'],
            ['libelle_court',   18, false, 'DAP'],
            ['type',            18, true,  'technique'],
            ['description',     35, false, 'Description du département'],
            ['ordre_affichage', 18, false, '1'],
            ['actif',           12, false, 'OUI'],
        ],
        'Directions'             => [
            ['code',             18, true,  'DRIP'],
            ['libelle',          40, true,  'Direction des Relations Internationales et du Protocole'],
            ['libelle_court',    18, false, 'DRIP'],
            ['code_departement', 18, true,  'DAP'],
            ['type_direction',   18, true,  'technique'],
            ['description',      35, false, ''],
            ['ordre_affichage',  18, false, '1'],
            ['actif',            12, false, 'OUI'],
        ],
        'Services'               => [
            ['code',            18, true,  'SVPM'],
            ['libelle',         40, true,  'Service de la Veille Politique et Médiation'],
            ['libelle_court',   18, false, 'SVPM'],
            ['code_direction',  18, true,  'DRIP'],
            ['description',     35, false, ''],
            ['ordre_affichage', 18, false, '1'],
            ['actif',           12, false, 'OUI'],
        ],
        'Utilisateurs'           => [
            ['matricule',        18, false, 'CEE-001'],
            ['prenom',           20, true,  'Jean'],
            ['name',             20, true,  'Dupont'],
            ['email',            30, true,  'jean.dupont@ceeac.int'],
            ['telephone',        18, false, '+241 01 234 567'],
            ['titre',            12, false, 'M.'],
            ['fonction',         30, false, 'Chef de Service'],
            ['code_direction',   18, false, 'DRIP'],
            ['code_departement', 18, false, 'DAP'],
            ['code_service',     18, false, 'SVPM'],
            ['scope_level',      18, false, 'direction'],
            ['roles',            30, false, 'chef_service'],
            ['actif',            12, false, 'OUI'],
        ],
        'PAPA'                   => [
            ['code',                18, true,  'PAPA-2026'],
            ['libelle',             45, true,  "Plan d'Actions Prioritaires Annuel 2026"],
            ['annee',               10, true,  '2026'],
            ['date_debut',          14, true,  '01/01/2026'],
            ['date_fin',            14, true,  '31/12/2026'],
            ['budget_total_prevu',  20, false, '500000000'],
            ['devise',              10, false, 'XAF'],
            ['description',         40, false, ''],
            ['statut',              18, false, 'brouillon'],
            ['notes',               30, false, ''],
            ['email_cree_par',      30, false, 'admin@ceeac.int'],
        ],
        'Actions_Prioritaires'   => [
            ['code',             20, true,  'AP-2026-01'],
            ['libelle',          45, true,  'Renforcement de la paix et de la sécurité régionale'],
            ['description',      40, false, ''],
            ['code_papa',        18, true,  'PAPA-2026'],
            ['code_departement', 18, true,  'DAP'],
            ['qualification',    18, true,  'technique'],
            ['priorite',         14, true,  'haute'],
            ['statut',           18, false, 'planifie'],
            ['ordre',            10, false, '1'],
            ['notes',            30, false, ''],
        ],
        'Objectifs_Immediats'    => [
            ['code',                      20, true,  'OI-2026-01-01'],
            ['libelle',                   45, true,  'Renforcer les capacités institutionnelles'],
            ['description',               40, false, ''],
            ['code_action_prioritaire',   22, true,  'AP-2026-01'],
            ['statut',                    18, false, 'planifie'],
            ['taux_atteinte',             14, false, '0'],
            ['ordre',                     10, false, '1'],
            ['email_responsable',         30, false, 'jean.dupont@ceeac.int'],
            ['notes',                     30, false, ''],
        ],
        'Resultats_Attendus'     => [
            ['code',                    22, true,  'RA-2026-01-01-01'],
            ['libelle',                 45, true,  'Formation de 50 officiers MARAC'],
            ['description',             40, false, ''],
            ['code_objectif_immediat',  22, true,  'OI-2026-01-01'],
            ['type_resultat',           16, true,  'output'],
            ['statut',                  18, false, 'planifie'],
            ['taux_atteinte',           14, false, '0'],
            ['annee_reference',         14, false, '2026'],
            ['preuve_requise',          14, false, 'OUI'],
            ['type_preuve_attendue',    30, false, 'Rapport de formation signé'],
            ['ordre',                   10, false, '1'],
            ['email_responsable',       30, false, 'jean.dupont@ceeac.int'],
            ['notes',                   30, false, ''],
        ],
        'Activites'              => [
            ['code',                   22, true,  'ACT-2026-01-01-01-01'],
            ['libelle',                45, true,  'Organisation séminaire de formation MARAC'],
            ['description',            40, false, ''],
            ['code_resultat_attendu',  22, true,  'RA-2026-01-01-01'],
            ['code_direction',         18, true,  'DRIP'],
            ['code_service',           18, false, 'SVPM'],
            ['date_debut_prevue',      14, false, '15/03/2026'],
            ['date_fin_prevue',        14, false, '30/03/2026'],
            ['date_debut_reelle',      14, false, ''],
            ['date_fin_reelle',        14, false, ''],
            ['statut',                 18, false, 'planifiee'],
            ['priorite',               14, true,  'haute'],
            ['taux_realisation',       16, false, '0'],
            ['budget_prevu',           18, false, '15000000'],
            ['budget_engage',          18, false, '0'],
            ['budget_consomme',        18, false, '0'],
            ['devise',                 10, false, 'XAF'],
            ['est_jalon',              12, false, 'NON'],
            ['email_responsable',      30, false, 'jean.dupont@ceeac.int'],
            ['email_point_focal',      30, false, ''],
            ['ordre',                  10, false, '1'],
            ['notes',                  30, false, ''],
        ],
        'Taches'                 => [
            ['code',               20, true,  'TCH-2026-01-01-01'],
            ['libelle',            40, true,  'Élaboration du programme de formation'],
            ['description',        35, false, ''],
            ['code_activite',      22, true,  'ACT-2026-01-01-01-01'],
            ['code_tache_parente', 22, false, ''],
            ['date_debut_prevue',  14, false, '15/03/2026'],
            ['date_fin_prevue',    14, false, '20/03/2026'],
            ['statut',             18, false, 'a_faire'],
            ['taux_realisation',   16, false, '0'],
            ['email_assignee',     30, false, 'jean.dupont@ceeac.int'],
            ['ordre',              10, false, '1'],
            ['notes',              30, false, ''],
        ],
        'Jalons'                 => [
            ['code',          18, true,  'JAL-2026-01-01-01'],
            ['libelle',       40, true,  'Rapport de formation déposé'],
            ['description',   35, false, ''],
            ['code_activite', 22, true,  'ACT-2026-01-01-01-01'],
            ['date_prevue',   14, true,  '30/03/2026'],
            ['date_reelle',   14, false, ''],
            ['statut',        14, false, 'planifie'],
            ['est_critique',  14, false, 'OUI'],
            ['notes',         30, false, ''],
        ],
        'Indicateurs'            => [
            ['code',                      20, true,  'IND-2026-01-01'],
            ['libelle',                   45, true,  'Taux de formation des officiers MARAC'],
            ['definition',                40, false, 'Mesure le % d\'officiers formés'],
            ['niveau_rattachement',       22, true,  'resultat_attendu'],
            ['code_entite_rattachement',  22, true,  'RA-2026-01-01-01'],
            ['unite_mesure',              14, false, '%'],
            ['type_indicateur',           18, true,  'quantitatif'],
            ['frequence_collecte',        18, true,  'trimestrielle'],
            ['valeur_baseline',           16, false, '0'],
            ['valeur_cible_annuelle',     18, false, '100'],
            ['methode_calcul',            35, false, '(formés / prévus) × 100'],
            ['source_donnees',            30, false, 'Rapport de mission'],
            ['outil_collecte',            25, false, 'Fiche de présence'],
            ['seuil_alerte_rouge',        18, false, '30'],
            ['seuil_alerte_orange',       18, false, '60'],
            ['seuil_alerte_vert',         18, false, '80'],
            ['code_direction',            18, false, 'DRIP'],
            ['email_responsable',         30, false, 'jean.dupont@ceeac.int'],
            ['actif',                     12, false, 'OUI'],
            ['notes',                     30, false, ''],
        ],
        'Valeurs_Indicateurs'    => [
            ['code_indicateur',      20, true,  'IND-2026-01-01'],
            ['periode_type',         18, true,  'trimestrielle'],
            ['periode_libelle',      18, true,  'T1-2026'],
            ['annee',                10, true,  '2026'],
            ['mois',                 10, false, ''],
            ['trimestre',            12, false, '1'],
            ['semestre',             12, false, ''],
            ['valeur_realisee',      16, true,  '25'],
            ['valeur_cible_periode', 18, false, '25'],
            ['commentaire',          35, false, ''],
            ['analyse_ecart',        35, false, ''],
            ['statut_validation',    18, false, 'brouillon'],
            ['email_saisi_par',      30, false, 'jean.dupont@ceeac.int'],
        ],
        'Budgets'                => [
            ['code_papa',           18, true,  'PAPA-2026'],
            ['niveau_rattachement', 22, false, 'action_prioritaire'],
            ['code_entite',         22, false, 'AP-2026-01'],
            ['libelle_ligne',       35, true,  'Fonctionnement — séminaires et ateliers'],
            ['source_financement',  28, true,  'budget_ceeac'],
            ['annee_budgetaire',    16, true,  '2026'],
            ['devise',              10, false, 'XAF'],
            ['montant_prevu',       18, true,  '15000000'],
            ['montant_engage',      18, false, '0'],
            ['montant_decaisse',    18, false, '0'],
            ['notes',               30, false, ''],
        ],
        'Risques'                => [
            ['code',                     20, true,  'RSQ-2026-01'],
            ['libelle',                  40, true,  'Risque de manque de financement'],
            ['description',              35, false, ''],
            ['code_papa',                18, true,  'PAPA-2026'],
            ['categorie',                18, true,  'financier'],
            ['probabilite',              18, true,  'moyenne'],
            ['impact',                   18, true,  'majeur'],
            ['statut',                   18, false, 'identifie'],
            ['mesures_mitigation',       35, false, 'Diversification des sources'],
            ['plan_contingence',         35, false, ''],
            ['date_echeance_traitement', 18, false, '30/06/2026'],
            ['email_responsable',        30, false, 'jean.dupont@ceeac.int'],
        ],
        'Dependances_Activites'  => [
            ['code_activite_source',       25, true,  'ACT-2026-01-01-01-02'],
            ['code_activite_predecesseur', 25, true,  'ACT-2026-01-01-01-01'],
            ['type_dependance',            18, false, 'fin_debut'],
            ['lag_jours',                  14, false, '0'],
        ],
    ];

    public function generate(): string
    {
        $this->wb = new Spreadsheet();
        $this->wb->removeSheetByIndex(0);

        $this->addInstructionsSheet();

        foreach (self::SHEETS as $name => $cols) {
            if ($cols === null) continue;
            $this->addDataSheet($name, $cols);
        }

        $this->wb->setActiveSheetIndex(0);

        $path = sys_get_temp_dir() . '/modele-import-rbm-' . date('Ymd') . '.xlsx';
        (new Xlsx($this->wb))->save($path);

        return $path;
    }

    private function addInstructionsSheet(): void
    {
        $ws = $this->wb->createSheet();
        $ws->setTitle('LISEZ-MOI');

        $instructions = [
            ['TB-PAPA-CEEAC — Modèle d\'import RBM', '', '', ''],
            ['Version 1.0 — ' . date('d/m/Y'), '', '', ''],
            ['', '', '', ''],
            ['RÈGLES GÉNÉRALES', '', '', ''],
            ['• Ne pas modifier les en-têtes de colonnes (ligne 1 de chaque feuille)', '', '', ''],
            ['• Dates au format JJ/MM/AAAA (ex: 01/01/2026)', '', '', ''],
            ['• Booléens : OUI ou NON (insensible à la casse)', '', '', ''],
            ['• Les colonnes marquées * sont OBLIGATOIRES', '', '', ''],
            ['• Les codes sont des clés de jointure — ils doivent être UNIQUES et COHÉRENTS entre feuilles', '', '', ''],
            ['', '', '', ''],
            ['ORDRE D\'IMPORT', '', '', ''],
            ['1. Departements', '2. Directions',   '3. Services',          '4. Utilisateurs'],
            ['5. PAPA',         '6. Actions_Prioritaires', '7. Objectifs_Immediats', '8. Resultats_Attendus'],
            ['9. Activites',    '10. Taches',      '11. Jalons',           '12. Indicateurs'],
            ['13. Valeurs_Indicateurs', '14. Budgets', '15. Risques',       '16. Dependances_Activites'],
            ['', '', '', ''],
            ['VALEURS AUTORISÉES', '', '', ''],
            ['type (Départements)', 'technique | appui | transversal', '', ''],
            ['type_direction',      'technique | appui', '', ''],
            ['qualification (AP)',  'technique | appui | transversal', '', ''],
            ['priorite',            'critique | haute | normale | basse', '', ''],
            ['statut (Papa)',       'brouillon | soumis | valide | en_execution | cloture | archive', '', ''],
            ['statut (AP)',         'planifie | en_cours | suspendu | termine | abandonne', '', ''],
            ['statut (OI/RA)',      'planifie | en_cours | atteint | partiellement_atteint | non_atteint', '', ''],
            ['statut (Activite)',   'non_demarree | planifiee | en_cours | suspendue | terminee | abandonnee', '', ''],
            ['statut (Tache)',      'a_faire | en_cours | en_revue | terminee | bloquee | abandonnee', '', ''],
            ['statut (Jalon)',      'planifie | atteint | non_atteint | reporte', '', ''],
            ['type_resultat',       'output | outcome | impact', '', ''],
            ['type_indicateur',     'quantitatif | qualitatif | binaire', '', ''],
            ['frequence_collecte',  'mensuelle | trimestrielle | semestrielle | annuelle | ponctuelle', '', ''],
            ['niveau_rattachement', 'action_prioritaire | objectif_immediat | resultat_attendu', '', ''],
            ['source_financement',  'budget_ceeac | contribution_etat_membre | partenaire_technique_financier | fonds_propres | autre', '', ''],
            ['categorie (Risque)',   'strategique | operationnel | financier | juridique | reputationnel | securitaire | naturel | autre', '', ''],
            ['probabilite',         'tres_faible | faible | moyenne | elevee | tres_elevee', '', ''],
            ['impact',              'negligeable | mineur | modere | majeur | catastrophique', '', ''],
            ['type_dependance',     'fin_debut | debut_debut | fin_fin | debut_fin', '', ''],
            ['scope_level',         'global | departement | direction | service', '', ''],
            ['statut_validation',   'brouillon | soumis | valide | rejete', '', ''],
        ];

        foreach ($instructions as $r => $line) {
            foreach ($line as $c => $val) {
                $ws->setCellValueByColumnAndRow($c + 1, $r + 1, $val);
            }
        }

        // Titre principal
        $ws->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e3a5f']],
        ]);
        $ws->getStyle('A4')->applyFromArray(['font' => ['bold' => true]]);
        $ws->getStyle('A11')->applyFromArray(['font' => ['bold' => true]]);
        $ws->getStyle('A17')->applyFromArray(['font' => ['bold' => true]]);
        $ws->getColumnDimension('A')->setWidth(40);
        $ws->getColumnDimension('B')->setWidth(80);
        $ws->getColumnDimension('C')->setWidth(35);
        $ws->getColumnDimension('D')->setWidth(35);
    }

    private function addDataSheet(string $name, array $cols): void
    {
        $ws = $this->wb->createSheet();
        $ws->setTitle($name);

        $col = 1;
        foreach ($cols as [$header, $width, $required, $example]) {
            $label = $required ? "{$header} *" : $header;
            $ws->setCellValueByColumnAndRow($col, 1, $label);
            $ws->setCellValueByColumnAndRow($col, 2, $example);
            $ws->getColumnDimensionByColumn($col)->setWidth($width);
            $col++;
        }

        // Style en-tête (ligne 1)
        $lastCol = $ws->getHighestColumn();
        $headerRange = "A1:{$lastCol}1";

        $ws->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1e3a5f'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        // Style exemple (ligne 2)
        $ws->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6b7280']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f9fafb'],
            ],
        ]);

        $ws->getRowDimension(1)->setRowHeight(28);
        $ws->freezePane('A3');
    }
}
