<?php

namespace Database\Seeders;

use App\Models\ActionCorrective;
use App\Models\ActionPrioritaire;
use App\Models\Activite;
use App\Models\Alerte;
use App\Models\BudgetPapa;
use App\Models\CategorieDocument;
use App\Models\DependanceActivite;
use App\Models\Departement;
use App\Models\Direction;
use App\Models\Document;
use App\Models\EngagementFinancier;
use App\Models\Indicateur;
use App\Models\Jalon;
use App\Models\NotificationApp;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\Partenaire;
use App\Models\Rapport;
use App\Models\ResultatAttendu;
use App\Models\Risque;
use App\Models\Tache;
use App\Models\User;
use App\Models\ValidationWorkflow;
use App\Models\ValeurIndicateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoScenarioSeeder extends Seeder
{
    protected User $admin;
    protected Collection $users;
    protected Collection $departements;
    protected Collection $directions;
    protected Collection $partenaires;
    protected Collection $categories;
    protected int $counter = 0;

    public function run(): void
    {
        $this->users = User::query()->with('roles')->get();
        $this->admin = $this->users->firstWhere('email', 'admin@ceeac-eccas.org');
        $this->departements = Departement::query()->where('actif', true)->with('directions.services')->orderBy('ordre_affichage')->get();
        $this->directions = Direction::query()->where('actif', true)->with('services')->get()->keyBy('code');
        $this->partenaires = Partenaire::query()->where('actif', true)->get();
        $this->categories = CategorieDocument::query()->where('actif', true)->get()->keyBy('code');

        $papas = collect([
            $this->makePapa('PAPA-2024-V1', 2024, 'archive', 4640000000, 91.4, 88.2, 'Exercice historique clôturé et archivé.'),
            $this->makePapa('PAPA-2025-V1', 2025, 'en_execution', 5125000000, 62.3, 55.1, 'Version validée servant de socle à la démo d’exécution.'),
            $this->makePapa('PAPA-2025-V2', 2025, 'valide', 5380000000, 48.7, 43.9, 'Révision de mi-parcours pour illustrer le recalage budgétaire.'),
            $this->makePapa('PAPA-2026-V1', 2026, 'en_validation', 5560000000, 21.8, 18.4, 'Projet soumis dans le circuit de validation.'),
            $this->makePapa('PAPA-2027-V0', 2027, 'brouillon', 5900000000, 5.0, 2.5, 'Version préparatoire utilisée pour la planification future.'),
        ]);

        foreach ($papas as $papaIndex => $papa) {
            $this->seedPapa($papa, $papaIndex);
            $this->createReports($papa, $papaIndex);
            $this->createPapaWorkflow($papa);
        }

        $this->seedNotifications();
        $this->seedAuditLogs();
        $this->recalculatePapas();
    }

    protected function makePapa(string $code, int $annee, string $statut, float $budget, float $physique, float $financier, string $notes): Papa
    {
        return Papa::updateOrCreate(
            ['code' => $code],
            [
                'libelle' => "Plan d'Action Prioritaire Annuel {$annee} ({$code})",
                'annee' => $annee,
                'date_debut' => "{$annee}-01-01",
                'date_fin' => "{$annee}-12-31",
                'description' => "Jeu de données réaliste pour la démonstration de {$code}.",
                'statut' => $statut,
                'budget_total_prevu' => $budget,
                'devise' => 'XAF',
                'taux_execution_physique' => $physique,
                'taux_execution_financiere' => $financier,
                'created_by' => $this->admin->id,
                'validated_by' => in_array($statut, ['valide', 'en_execution', 'archive'], true) ? optional($this->users->firstWhere('email', 'president@ceeac-eccas.org'))->id : null,
                'validated_at' => in_array($statut, ['valide', 'en_execution', 'archive'], true) ? Carbon::create($annee, 1, 8) : null,
                'archived_by' => $statut === 'archive' ? $this->admin->id : null,
                'archived_at' => $statut === 'archive' ? Carbon::create($annee + 1, 2, 15) : null,
                'motif_archivage' => $statut === 'archive' ? 'Exercice consolidé et versé aux archives.' : null,
                'notes' => $notes,
                'est_verrouille' => $statut === 'archive',
            ]
        );
    }

    protected function seedPapa(Papa $papa, int $papaIndex): void
    {
        foreach ($this->departements as $deptIndex => $departement) {
            $theme = $this->themeFor($departement->code, $papaIndex);
            $profile = ['excellent', 'steady', 'watch', 'critical', 'recovery'][($papaIndex + $deptIndex) % 5];
            $action = $this->createAction($papa, $departement, $theme, $profile);

            for ($o = 1; $o <= 2; $o++) {
                $objectif = $this->createObjectif($action, $theme, $profile, $o);
                for ($r = 1; $r <= 2; $r++) {
                    $resultat = $this->createResultat($objectif, $theme, $profile, $r);
                    for ($i = 1; $i <= 2; $i++) {
                        $indicateur = $this->createIndicateur($papa, $action, $objectif, $resultat, $theme, $profile, $i);
                        $this->seedValeurs($papa, $indicateur, $profile);
                    }

                    $activities = collect();
                    for ($a = 1; $a <= 2; $a++) {
                        $activite = $this->createActivite($papa, $resultat, $theme, $profile, $a);
                        $activities->push($activite);
                        $budget = $this->createBudget($papa, $action, $activite, $profile);
                        $this->createEngagements($budget, $activite, $profile);
                        $this->createTasks($activite, $profile);
                        $this->createJalon($activite, $profile);
                        $this->createDocuments($resultat, $activite, $theme, $profile);
                        $this->createActivityAlerts($papa, $activite, $profile);
                    }

                    if ($activities->count() === 2) {
                        DependanceActivite::updateOrCreate(
                            ['activite_id' => $activities[1]->id, 'activite_predecesseur_id' => $activities[0]->id],
                            ['type_dependance' => 'fin_debut', 'delai_jours' => $profile === 'critical' ? 5 : 2]
                        );
                    }
                }
            }

            $this->createActionBudget($papa, $action, $theme, $profile);
            $this->createRiskAndCorrective($papa, $action, $theme, $profile);
        }
    }

    protected function themeFor(string $deptCode, int $index): array
    {
        $themes = [
            'SG' => [
                ['libelle' => 'Moderniser la chaîne budgétaire et comptable', 'focus' => 'la chaîne budgétaire', 'directions' => ['DAF', 'DAG'], 'priorite' => 'haute', 'prefixe' => 'SGF'],
                ['libelle' => 'Structurer la transformation numérique et la qualité des données', 'focus' => 'la transformation numérique', 'directions' => ['DIN', 'DAF'], 'priorite' => 'critique', 'prefixe' => 'NUM'],
                ['libelle' => 'Renforcer la communication institutionnelle et le protocole', 'focus' => 'la communication institutionnelle', 'directions' => ['DCP', 'DAJ'], 'priorite' => 'normale', 'prefixe' => 'COM'],
            ],
            'DPS' => [
                ['libelle' => 'Consolider le dispositif régional d’alerte précoce', 'focus' => 'l’alerte précoce régionale', 'directions' => ['DCPA'], 'priorite' => 'critique', 'prefixe' => 'MAR'],
                ['libelle' => 'Améliorer la préparation opérationnelle des mécanismes de paix', 'focus' => 'la préparation opérationnelle', 'directions' => ['DOSS'], 'priorite' => 'haute', 'prefixe' => 'FOM'],
                ['libelle' => 'Renforcer la coordination sécuritaire régionale', 'focus' => 'la coordination sécuritaire', 'directions' => ['DCPA', 'DOSS'], 'priorite' => 'haute', 'prefixe' => 'SEC'],
            ],
            'DIE' => [
                ['libelle' => 'Accélérer la facilitation du commerce intra-CEEAC', 'focus' => 'la facilitation du commerce', 'directions' => ['DCE'], 'priorite' => 'critique', 'prefixe' => 'COM'],
                ['libelle' => 'Développer l’attractivité des investissements régionaux', 'focus' => 'la promotion des investissements', 'directions' => ['DIM'], 'priorite' => 'normale', 'prefixe' => 'INV'],
                ['libelle' => 'Mettre à niveau le suivi statistique et commercial', 'focus' => 'le suivi statistique', 'directions' => ['DCE', 'DIM'], 'priorite' => 'haute', 'prefixe' => 'STA'],
            ],
            'DER' => [
                ['libelle' => 'Accélérer l’intégration économique régionale et la compétitivité', 'focus' => 'l’intégration économique régionale', 'directions' => ['DCE', 'DIM'], 'priorite' => 'critique', 'prefixe' => 'INT'],
                ['libelle' => 'Renforcer le climat des affaires et la mobilisation des investissements', 'focus' => 'le climat des affaires régional', 'directions' => ['DIM'], 'priorite' => 'haute', 'prefixe' => 'AFF'],
                ['libelle' => 'Améliorer la coordination des politiques commerciales régionales', 'focus' => 'les politiques commerciales régionales', 'directions' => ['DCE'], 'priorite' => 'normale', 'prefixe' => 'POL'],
            ],
            'DID' => [
                ['libelle' => 'Sécuriser la mise en œuvre des corridors multimodaux', 'focus' => 'les corridors multimodaux', 'directions' => ['DTI'], 'priorite' => 'critique', 'prefixe' => 'COR'],
                ['libelle' => 'Renforcer l’intégration énergétique et climatique', 'focus' => 'l’intégration énergétique', 'directions' => ['DEN'], 'priorite' => 'haute', 'prefixe' => 'ENE'],
                ['libelle' => 'Améliorer la préparation des projets d’infrastructure', 'focus' => 'la préparation de projets d’infrastructure', 'directions' => ['DTI', 'DEN'], 'priorite' => 'normale', 'prefixe' => 'INF'],
            ],
            'DDH' => [
                ['libelle' => 'Renforcer les programmes régionaux de développement social', 'focus' => 'les programmes sociaux', 'directions' => ['DDS'], 'priorite' => 'haute', 'prefixe' => 'SOC'],
                ['libelle' => 'Accroître l’intégration du genre dans les interventions', 'focus' => 'l’intégration du genre', 'directions' => ['DGF'], 'priorite' => 'normale', 'prefixe' => 'GEN'],
                ['libelle' => 'Structurer les dispositifs de formation et d’inclusion des jeunes', 'focus' => 'les compétences et l’inclusion des jeunes', 'directions' => ['DDS', 'DGF'], 'priorite' => 'haute', 'prefixe' => 'JEU'],
            ],
        ];

        if (! isset($themes[$deptCode])) {
            $departement = $this->departements->firstWhere('code', $deptCode);
            $fallbackCode = $departement?->type === 'appui' ? 'SG' : 'DIE';

            return $themes[$fallbackCode][$index % count($themes[$fallbackCode])];
        }

        return $themes[$deptCode][$index % count($themes[$deptCode])];
    }

    protected function createAction(Papa $papa, Departement $departement, array $theme, string $profile): ActionPrioritaire
    {
        $progress = $this->progressFor($papa->statut, $profile);

        return ActionPrioritaire::updateOrCreate(
            ['code' => sprintf('AP-%d-%03d', $papa->annee, ++$this->counter)],
            [
                'papa_id' => $papa->id,
                'departement_id' => $departement->id,
                'libelle' => $theme['libelle'],
                'description' => "Action prioritaire dédiée à {$theme['focus']} dans le cadre du {$papa->code}.",
                'qualification' => $departement->code === 'SG' ? 'appui' : (count($theme['directions']) > 1 ? 'transversal' : 'technique'),
                'ordre' => $this->counter,
                'priorite' => $theme['priorite'],
                'statut' => $progress >= 95 ? 'termine' : ($profile === 'critical' ? 'suspendu' : 'en_cours'),
                'taux_realisation' => $progress,
                'created_by' => $this->admin->id,
                'notes' => 'Action utilisée pour alimenter les tableaux de bord et les filtres directionnels.',
            ]
        );
    }

    protected function createObjectif(ActionPrioritaire $action, array $theme, string $profile, int $ordre): ObjectifImmediats
    {
        $direction = $this->directions->get($theme['directions'][($ordre - 1) % count($theme['directions'])]);
        $progress = max(0, min(100, (float) $action->taux_realisation + ($ordre === 1 ? 6 : -4)));

        return ObjectifImmediats::updateOrCreate(
            ['code' => sprintf('OI-%d-%04d', $action->papa->annee, ++$this->counter)],
            [
                'action_prioritaire_id' => $action->id,
                'libelle' => $ordre === 1 ? "Structurer {$theme['focus']} dans les directions concernées" : "Institutionnaliser le suivi et la qualité des données liées à {$theme['focus']}",
                'description' => 'Objectif immédiat formulé pour les besoins de la démonstration institutionnelle.',
                'ordre' => $ordre,
                'statut' => $progress >= 95 ? 'atteint' : ($progress >= 45 ? 'en_cours' : 'partiellement_atteint'),
                'taux_atteinte' => $progress,
                'responsable_id' => $this->userForDirection($direction?->code, ['directeur_technique', 'directeur_appui', 'point_focal'])?->id,
                'notes' => $profile === 'critical' ? 'Objectif sous tension à suivre de près.' : 'Objectif suivi lors des revues de performance.',
            ]
        );
    }

    protected function createResultat(ObjectifImmediats $objectif, array $theme, string $profile, int $ordre): ResultatAttendu
    {
        $progress = max(0, min(100, (float) $objectif->taux_atteinte + ($ordre === 1 ? 8 : -6)));

        return ResultatAttendu::updateOrCreate(
            ['code' => sprintf('RA-%d-%04d', $objectif->actionPrioritaire->papa->annee, ++$this->counter)],
            [
                'objectif_immediat_id' => $objectif->id,
                'libelle' => $ordre === 1 ? "Les livrables de {$theme['focus']} sont produits et validés" : "Les parties prenantes disposent d’un dispositif opérationnel pour {$theme['focus']}",
                'description' => 'Résultat attendu crédible généré pour nourrir la chaîne RBM et la GED.',
                'type_resultat' => $ordre === 1 ? 'output' : 'outcome',
                'ordre' => $ordre,
                'statut' => $progress >= 95 ? 'atteint' : ($progress >= 45 ? 'en_cours' : 'partiellement_atteint'),
                'taux_atteinte' => $progress,
                'preuve_requise' => true,
                'type_preuve_attendue' => $ordre === 1 ? 'PV, note technique, rapport de validation' : 'Tableau de bord, fiche de suivi, rapport consolidé',
                'responsable_id' => $objectif->responsable_id,
                'notes' => 'Résultat attendu mobilisé dans la démonstration de la GED et des alertes.',
            ]
        );
    }

    protected function createIndicateur(Papa $papa, ActionPrioritaire $action, ObjectifImmediats $objectif, ResultatAttendu $resultat, array $theme, string $profile, int $ordre): Indicateur
    {
        $direction = $this->directions->get($theme['directions'][($ordre - 1) % count($theme['directions'])]);
        $target = $ordre === 1 ? rand(4, 20) : rand(60, 95);
        $rate = max(10, min(108, $this->progressFor($papa->statut, $profile) + ($ordre * 5) + rand(-8, 8)));

        return Indicateur::updateOrCreate(
            ['code' => sprintf('IND-%d-%04d', $papa->annee, ++$this->counter)],
            [
                'resultat_attendu_id' => $resultat->id,
                'objectif_immediat_id' => $objectif->id,
                'action_prioritaire_id' => $action->id,
                'libelle' => $ordre === 1 ? "Nombre de livrables validés pour {$theme['focus']}" : "Taux de mise en oeuvre conforme pour {$theme['focus']}",
                'definition' => 'Indicateur généré pour une démonstration réaliste des tableaux de bord.',
                'unite_mesure' => $ordre === 1 ? 'nombre' : '%',
                'type_indicateur' => 'quantitatif',
                'valeur_baseline' => $ordre === 1 ? rand(0, 3) : rand(10, 30),
                'valeur_cible_annuelle' => $target,
                'methode_calcul' => $ordre === 1 ? 'Comptage des livrables validés.' : '(Valeur réalisée / cible) x 100.',
                'frequence_collecte' => 'trimestrielle',
                'source_donnees' => 'Rapports directionnels, fiches de suivi, procès-verbaux',
                'outil_collecte' => 'Tableau RBM trimestriel',
                'responsable_id' => $this->userForDirection($direction?->code, ['point_focal', 'chef_service', 'directeur_technique', 'directeur_appui'])?->id,
                'direction_id' => $direction?->id,
                'seuil_alerte_rouge' => 45,
                'seuil_alerte_orange' => 70,
                'seuil_alerte_vert' => 90,
                'taux_realisation_courant' => $rate,
                'tendance' => $rate >= 85 ? 'hausse' : ($rate >= 60 ? 'stable' : 'baisse'),
                'actif' => true,
                'notes' => $profile === 'critical' ? 'Indicateur à risque avec collecte fragile.' : 'Indicateur consolidé dans les dashboards.',
            ]
        );
    }

    protected function seedValeurs(Papa $papa, Indicateur $indicateur, string $profile): void
    {
        $quarters = $papa->annee < 2026 ? 4 : 2;
        for ($t = 1; $t <= $quarters; $t++) {
            $target = round((float) $indicateur->valeur_cible_annuelle / $quarters, 4);
            $rate = max(8, min(108, (float) $indicateur->taux_realisation_courant + rand(-8, 6)));
            $status = $papa->statut === 'brouillon' ? 'brouillon' : ($profile === 'critical' && $t === 1 ? 'rejete' : (in_array($papa->statut, ['archive', 'en_execution', 'valide'], true) ? 'valide' : 'soumis'));

            ValeurIndicateur::updateOrCreate(
                ['indicateur_id' => $indicateur->id, 'periode_type' => 'trimestrielle', 'annee' => $papa->annee, 'trimestre' => $t, 'mois' => null, 'semestre' => null],
                [
                    'periode_libelle' => "T{$t}-{$papa->annee}",
                    'valeur_realisee' => round($target * ($rate / 100), 4),
                    'valeur_cible_periode' => $target,
                    'taux_realisation' => $rate,
                    'tendance' => $t === 1 ? 'na' : ($rate >= 85 ? 'hausse' : ($rate >= 60 ? 'stable' : 'baisse')),
                    'commentaire' => $profile === 'critical' ? 'Les données du trimestre appellent des mesures correctives.' : 'La collecte du trimestre est exploitable.',
                    'analyse_ecart' => $rate >= 85 ? 'Écart maîtrisé.' : ($rate >= 60 ? 'Écart modéré.' : 'Écart significatif nécessitant un suivi.'),
                    'statut_validation' => $status,
                    'saisi_par' => $indicateur->responsable_id ?: $this->admin->id,
                    'valide_par' => $status === 'valide' ? ($indicateur->responsable_id ?: $this->admin->id) : null,
                    'valide_le' => $status === 'valide' ? Carbon::create($papa->annee, $t * 3, 20, 10, 0, 0) : null,
                    'motif_rejet' => $status === 'rejete' ? 'Justificatifs incomplets et commentaire insuffisant.' : null,
                ]
            );
        }
    }

    protected function createActivite(Papa $papa, ResultatAttendu $resultat, array $theme, string $profile, int $ordre): Activite
    {
        $direction = $this->directions->get($theme['directions'][($ordre - 1) % count($theme['directions'])]);
        $service = $direction?->services->values()->get(($ordre - 1) % max(1, $direction?->services->count() ?: 1));
        $progress = max(0, min(100, $this->progressFor($papa->statut, $profile) + ($ordre === 1 ? 7 : -10)));
        $start = Carbon::create($papa->annee, min(12, 1 + (($this->counter + $ordre) % 8)), rand(3, 18));
        $end = (clone $start)->addDays($profile === 'critical' ? rand(80, 130) : rand(45, 95));

        return Activite::updateOrCreate(
            ['code' => sprintf('ACT-%d-%04d', $papa->annee, ++$this->counter)],
            [
                'resultat_attendu_id' => $resultat->id,
                'direction_id' => $direction?->id,
                'service_id' => $service?->id,
                'libelle' => $ordre === 1 ? "Conduire la séquence opérationnelle de {$theme['focus']}" : "Assurer le suivi et la remontée des preuves de {$theme['focus']}",
                'description' => 'Activité démonstrative utilisée pour le Gantt, les budgets, la GED et les alertes.',
                'ordre' => $ordre,
                'date_debut_prevue' => $start,
                'date_fin_prevue' => $end,
                'date_debut_reelle' => in_array($papa->statut, ['brouillon', 'en_validation'], true) ? null : (clone $start)->addDays(rand(0, 7)),
                'date_fin_reelle' => $progress >= 98 ? (clone $end)->addDays(rand(-2, 8)) : null,
                'statut' => $progress >= 98 ? 'terminee' : ($profile === 'critical' ? 'suspendue' : ($papa->statut === 'brouillon' ? 'planifiee' : 'en_cours')),
                'taux_realisation' => $progress,
                'responsable_id' => $this->userForDirection($direction?->code, ['directeur_technique', 'directeur_appui', 'chef_service'])?->id,
                'point_focal_id' => $this->userForDirection($direction?->code, ['point_focal', 'chef_service'])?->id,
                'budget_prevu' => rand(18000000, 95000000),
                'budget_engage' => rand(9000000, 85000000),
                'budget_consomme' => rand(5000000, 75000000),
                'devise' => 'XAF',
                'priorite' => $theme['priorite'],
                'est_jalon' => false,
                'notes' => $profile === 'critical' ? 'Activité en tension, suivi rapproché recommandé.' : 'Activité suivie dans les revues trimestrielles.',
                'created_by' => $this->admin->id,
            ]
        );
    }

    protected function createBudget(Papa $papa, ActionPrioritaire $action, Activite $activite, string $profile): BudgetPapa
    {
        return BudgetPapa::updateOrCreate(
            ['papa_id' => $papa->id, 'action_prioritaire_id' => $action->id, 'activite_id' => $activite->id, 'source_financement' => 'budget_ceeac', 'libelle_ligne' => "Ligne opérationnelle - {$activite->libelle}"],
            [
                'partenaire_id' => null,
                'annee_budgetaire' => $papa->annee,
                'devise' => 'XAF',
                'montant_prevu' => $activite->budget_prevu,
                'montant_engage' => $profile === 'critical' ? $activite->budget_prevu + rand(500000, 3000000) : $activite->budget_engage,
                'montant_decaisse' => $activite->budget_consomme,
                'montant_solde' => max(0, (float) $activite->budget_prevu - (float) $activite->budget_engage),
                'notes' => 'Ligne budgétaire directement rattachée à l’activité.',
                'created_by' => $this->admin->id,
            ]
        );
    }

    protected function createActionBudget(Papa $papa, ActionPrioritaire $action, array $theme, string $profile): void
    {
        $amount = rand(160000000, 520000000);
        BudgetPapa::updateOrCreate(
            ['papa_id' => $papa->id, 'action_prioritaire_id' => $action->id, 'activite_id' => null, 'source_financement' => 'budget_ceeac', 'libelle_ligne' => "Enveloppe CEEAC - {$action->libelle}"],
            ['partenaire_id' => null, 'annee_budgetaire' => $papa->annee, 'devise' => 'XAF', 'montant_prevu' => $amount, 'montant_engage' => round($amount * rand(45, 90) / 100, 2), 'montant_decaisse' => round($amount * rand(25, 75) / 100, 2), 'montant_solde' => round($amount * rand(10, 40) / 100, 2), 'notes' => 'Enveloppe consolidée pour le suivi exécutif.', 'created_by' => $this->admin->id]
        );

        $partner = $this->partenaires->values()->get(($this->counter + $papa->id) % max(1, $this->partenaires->count()));
        if ($partner) {
            $partnerAmount = rand(70000000, 220000000);
            BudgetPapa::updateOrCreate(
                ['papa_id' => $papa->id, 'action_prioritaire_id' => $action->id, 'activite_id' => null, 'source_financement' => 'partenaire_technique_financier', 'libelle_ligne' => "Co-financement {$partner->libelleAffichage()} - {$theme['focus']}"],
                ['partenaire_id' => $partner->id, 'annee_budgetaire' => $papa->annee, 'devise' => 'XAF', 'montant_prevu' => $partnerAmount, 'montant_engage' => round($partnerAmount * rand(35, 80) / 100, 2), 'montant_decaisse' => round($partnerAmount * rand(20, 60) / 100, 2), 'montant_solde' => round($partnerAmount * rand(20, 50) / 100, 2), 'notes' => 'Contribution PTF mobilisée pour la démonstration.', 'created_by' => $this->admin->id]
            );
        }
    }

    protected function createEngagements(BudgetPapa $budget, Activite $activite, string $profile): void
    {
        $count = $profile === 'critical' ? 2 : 1;
        for ($i = 1; $i <= $count; $i++) {
            EngagementFinancier::updateOrCreate(
                ['numero_engagement' => sprintf('ENG-%d-%04d', $budget->annee_budgetaire, ($budget->id * 10) + $i)],
                [
                    'budget_papa_id' => $budget->id,
                    'activite_id' => $activite->id,
                    'libelle' => $i === 1 ? 'Engagement principal de mise en oeuvre' : 'Engagement complémentaire d’ajustement',
                    'date_engagement' => Carbon::parse($activite->date_debut_prevue)->addDays(5 * $i),
                    'montant_engage' => round((float) $budget->montant_engage / $count, 2),
                    'montant_decaisse' => round((float) $budget->montant_decaisse / $count, 2),
                    'fournisseur_beneficiaire' => $i === 1 ? 'Cabinet régional de conseil' : 'Prestataire logistique communautaire',
                    'statut' => (float) $budget->montant_decaisse >= (float) $budget->montant_engage ? 'totalement_decaisse' : 'partiellement_decaisse',
                    'notes' => 'Engagement généré pour alimenter les écrans financiers.',
                    'created_by' => $this->admin->id,
                ]
            );
        }
    }

    protected function createTasks(Activite $activite, string $profile): void
    {
        $labels = ['Préparer le dossier technique et le chronogramme détaillé', 'Coordonner les contributions, ateliers et validations internes'];
        foreach ($labels as $index => $label) {
            $parent = Tache::updateOrCreate(
                ['code' => sprintf('TCH-%04d', ++$this->counter)],
                ['activite_id' => $activite->id, 'parent_tache_id' => null, 'libelle' => $label, 'description' => 'Tâche structurante de démonstration.', 'ordre' => $index + 1, 'date_debut_prevue' => Carbon::parse($activite->date_debut_prevue)->addDays($index * 10), 'date_fin_prevue' => Carbon::parse($activite->date_debut_prevue)->addDays(18 + ($index * 9)), 'date_debut_reelle' => Carbon::parse($activite->date_debut_prevue)->addDays($index * 10 + 1), 'date_fin_reelle' => (float) $activite->taux_realisation > 90 ? Carbon::parse($activite->date_debut_prevue)->addDays(20 + ($index * 9)) : null, 'statut' => (float) $activite->taux_realisation > 90 ? 'terminee' : ($profile === 'critical' ? 'bloquee' : 'en_cours'), 'taux_realisation' => max(5, min(100, (float) $activite->taux_realisation + ($index === 0 ? 8 : -5))), 'assignee_id' => $activite->point_focal_id ?: $activite->responsable_id, 'notes' => 'Suivi hebdomadaire renseigné.']
            );

            Tache::updateOrCreate(
                ['code' => sprintf('TCH-%04d', ++$this->counter)],
                ['activite_id' => $activite->id, 'parent_tache_id' => $parent->id, 'libelle' => 'Documenter les preuves et consolider les écarts', 'description' => 'Sous-tâche de traçabilité et capitalisation.', 'ordre' => 1, 'date_debut_prevue' => Carbon::parse($activite->date_debut_prevue)->addDays(12), 'date_fin_prevue' => Carbon::parse($activite->date_debut_prevue)->addDays(26), 'date_debut_reelle' => Carbon::parse($activite->date_debut_prevue)->addDays(13), 'date_fin_reelle' => (float) $activite->taux_realisation > 92 ? Carbon::parse($activite->date_debut_prevue)->addDays(27) : null, 'statut' => (float) $activite->taux_realisation > 92 ? 'terminee' : ($profile === 'critical' ? 'bloquee' : 'en_revue'), 'taux_realisation' => max(5, min(100, (float) $activite->taux_realisation - 7)), 'assignee_id' => $activite->point_focal_id ?: $activite->responsable_id, 'notes' => 'Sous-tâche indispensable pour les exports et l’audit.']
            );
        }
    }

    protected function createJalon(Activite $activite, string $profile): void
    {
        $planned = Carbon::parse($activite->date_fin_prevue)->subDays(rand(5, 12));
        Jalon::updateOrCreate(
            ['code' => sprintf('JAL-%04d', ++$this->counter)],
            ['activite_id' => $activite->id, 'libelle' => 'Validation du livrable intermédiaire', 'description' => 'Jalon utilisé pour le Gantt et les alertes.', 'date_prevue' => $planned, 'date_reelle' => (float) $activite->taux_realisation >= 95 ? (clone $planned)->addDays(rand(-2, 7)) : null, 'statut' => (float) $activite->taux_realisation >= 95 ? 'atteint' : ($profile === 'critical' ? 'reporte' : 'planifie'), 'est_critique' => true, 'notes' => $profile === 'critical' ? 'Reporté faute de justificatifs complets.' : 'Jalon structurant.']
        );
    }

    protected function createDocuments(ResultatAttendu $resultat, Activite $activite, array $theme, string $profile): void
    {
        $docs = [['code' => 'NOTE', 'titre' => "Note de cadrage - {$theme['focus']}", 'ext' => 'pdf'], ['code' => 'RAPP', 'titre' => "Rapport d'avancement - {$theme['focus']}", 'ext' => 'docx']];
        foreach ($docs as $index => $doc) {
            if ($profile === 'critical' && $index === 1) {
                continue;
            }
            $path = "ged/demo/{$activite->id}/" . Str::slug($doc['titre']) . ".{$doc['ext']}";
            Document::updateOrCreate(
                ['documentable_type' => Activite::class, 'documentable_id' => $activite->id, 'titre' => $doc['titre']],
                ['categorie_id' => $this->categories->get($doc['code'])?->id ?? $this->categories->first()?->id, 'description' => 'Document démonstratif de GED.', 'reference' => sprintf('DOC/%s/%03d', $theme['prefixe'], $activite->id), 'date_document' => $activite->date_debut_prevue, 'chemin_fichier' => $path, 'nom_fichier_original' => basename($path), 'extension' => $doc['ext'], 'mime_type' => $doc['ext'] === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'taille_octets' => rand(180000, 1300000), 'version' => '1.0', 'confidentialite' => $doc['code'] === 'NOTE' ? 'interne' : 'confidentiel', 'statut' => $profile === 'critical' && $index === 0 ? 'soumis' : 'valide', 'depose_par' => $activite->point_focal_id ?: $this->admin->id, 'valide_par' => $profile === 'critical' && $index === 0 ? null : ($activite->responsable_id ?: $this->admin->id), 'valide_le' => $profile === 'critical' && $index === 0 ? null : now()->subDays(rand(4, 40)), 'est_archive' => false, 'archive_le' => null, 'hash_sha256' => hash('sha256', $path)]
            );
        }

        Document::updateOrCreate(
            ['documentable_type' => ResultatAttendu::class, 'documentable_id' => $resultat->id, 'titre' => "Fiche de preuve - {$theme['focus']}"],
            ['categorie_id' => $this->categories->get('FDS')?->id ?? $this->categories->first()?->id, 'description' => 'Fiche de preuve consolidée au niveau du résultat.', 'reference' => sprintf('PRV/%s/%03d', $theme['prefixe'], $resultat->id), 'date_document' => $activite->date_fin_prevue, 'chemin_fichier' => "ged/demo/resultats/{$resultat->id}/preuve-resultat.pdf", 'nom_fichier_original' => 'preuve-resultat.pdf', 'extension' => 'pdf', 'mime_type' => 'application/pdf', 'taille_octets' => rand(120000, 750000), 'version' => '1.0', 'confidentialite' => 'interne', 'statut' => $profile === 'critical' ? 'soumis' : 'valide', 'depose_par' => $activite->point_focal_id ?: $this->admin->id, 'valide_par' => $profile === 'critical' ? null : ($activite->responsable_id ?: $this->admin->id), 'valide_le' => $profile === 'critical' ? null : now()->subDays(rand(4, 40)), 'est_archive' => false, 'archive_le' => null, 'hash_sha256' => hash('sha256', "preuve-{$resultat->id}")]
        );
    }

    protected function createActivityAlerts(Papa $papa, Activite $activite, string $profile): void
    {
        if ($activite->date_fin_prevue && !$activite->date_fin_reelle && $profile === 'critical') {
            Alerte::updateOrCreate(
                ['papa_id' => $papa->id, 'alertable_type' => Activite::class, 'alertable_id' => $activite->id, 'type_alerte' => 'retard_activite'],
                ['niveau' => 'attention', 'titre' => "Retard sur l'activité {$activite->code}", 'message' => "L'activité {$activite->libelle} dépasse son calendrier prévu.", 'statut' => 'nouvelle', 'destinataire_id' => $activite->responsable_id, 'direction_id' => $activite->direction_id, 'auto_generee' => true]
            );
        }
    }

    protected function createRiskAndCorrective(Papa $papa, ActionPrioritaire $action, array $theme, string $profile): void
    {
        $code = sprintf('RSQ-%d-%04d', $papa->annee, ++$this->counter);
        $riskDraft = new Risque([
            'probabilite' => $profile === 'critical' ? 'elevee' : 'moyenne',
            'impact' => $profile === 'critical' ? 'majeur' : 'modere',
        ]);
        $score = $riskDraft->calculerScore();
        $niveau = $score >= 15 ? 'rouge' : ($score >= 8 ? 'orange' : ($score >= 3 ? 'jaune' : 'vert'));

        $risk = Risque::updateOrCreate(
            ['code' => $code],
            [
                'entite_type' => ActionPrioritaire::class,
                'entite_id' => $action->id,
                'papa_id' => $papa->id,
                'libelle' => "Risque opérationnel sur {$theme['focus']}",
                'description' => 'Risque généré pour illustrer les matrices, alertes et actions correctives.',
                'categorie' => $profile === 'critical' ? 'operationnel' : 'financier',
                'probabilite' => $profile === 'critical' ? 'elevee' : 'moyenne',
                'impact' => $profile === 'critical' ? 'majeur' : 'modere',
                'statut' => $profile === 'excellent' ? 'clos' : 'en_traitement',
                'mesures_mitigation' => 'Revues rapprochées, plan de rattrapage et arbitrage hiérarchique.',
                'plan_contingence' => 'Réallocation ciblée des ressources et ajustement du calendrier.',
                'responsable_id' => $this->userForDirection($theme['directions'][0], ['directeur_technique', 'directeur_appui', 'point_focal'])?->id,
                'date_echeance_traitement' => now()->addDays($profile === 'critical' ? 21 : 45),
                'date_derniere_revue' => now()->subDays(rand(3, 20)),
                'created_by' => $this->admin->id,
                'score_risque' => $score,
                'niveau_risque' => $niveau,
            ]
        );

        $alert = Alerte::updateOrCreate(
            ['papa_id' => $papa->id, 'alertable_type' => Risque::class, 'alertable_id' => $risk->id, 'type_alerte' => 'risque_eleve'],
            ['niveau' => $risk->niveau_risque === 'rouge' ? 'critique' : 'attention', 'titre' => "Risque à traiter : {$risk->libelle}", 'message' => "Le risque {$risk->code} nécessite un traitement prioritaire.", 'statut' => $profile === 'excellent' ? 'resolue' : 'en_traitement', 'destinataire_id' => $risk->responsable_id, 'direction_id' => optional($this->userForDirection($theme['directions'][0], ['directeur_technique', 'directeur_appui']))->direction_id, 'escaladee' => $risk->niveau_risque === 'rouge', 'escaladee_vers_id' => $risk->niveau_risque === 'rouge' ? optional($this->users->firstWhere('email', 'sg@ceeac-eccas.org'))->id : null, 'escaladee_le' => $risk->niveau_risque === 'rouge' ? now()->subDay() : null, 'auto_generee' => true]
        );

        ActionCorrective::updateOrCreate(
            ['code' => sprintf('ACR-%d-%04d', $papa->annee, ++$this->counter)],
            ['alerte_id' => $alert->id, 'risque_id' => $risk->id, 'papa_id' => $papa->id, 'libelle' => "Mettre en oeuvre le plan de mitigation du risque {$risk->code}", 'description' => 'Action corrective structurée pour la démonstration.', 'date_echeance' => now()->addDays($profile === 'critical' ? 18 : 35), 'priorite' => $risk->niveau_risque === 'rouge' ? 'critique' : 'haute', 'statut' => $profile === 'excellent' ? 'terminee' : 'en_cours', 'responsable_id' => $risk->responsable_id, 'date_realisation_effective' => $profile === 'excellent' ? now()->subDays(4) : null, 'resultat_obtenu' => $profile === 'excellent' ? 'Risque réduit à un niveau résiduel acceptable.' : null, 'created_by' => $this->admin->id]
        );
    }

    protected function createReports(Papa $papa, int $seed): void
    {
        foreach ([['type' => 'trimestriel', 'libelle' => "T1-{$papa->annee}", 'num' => 1], ['type' => 'trimestriel', 'libelle' => "T2-{$papa->annee}", 'num' => 2], ['type' => 'flash', 'libelle' => "FLASH-{$papa->annee}-1", 'num' => 1]] as $period) {
            $direction = $this->directions->values()->get(($seed + $period['num']) % max(1, $this->directions->count()));
            $rapport = Rapport::updateOrCreate(
                ['papa_id' => $papa->id, 'titre' => "Rapport {$period['type']} - {$direction?->libelleAffichage()} - {$period['libelle']}"],
                ['direction_id' => $direction?->id, 'departement_id' => $direction?->departement_id, 'type_rapport' => $period['type'], 'periode_couverte' => $period['libelle'], 'annee' => $papa->annee, 'numero_periode' => $period['num'], 'taux_execution_physique' => max(0, min(100, (float) $papa->taux_execution_physique + rand(-8, 5))), 'taux_execution_financiere' => max(0, min(100, (float) $papa->taux_execution_financiere + rand(-10, 6))), 'faits_saillants' => 'Progression des activités, arbitrages obtenus, mobilisation partenariale.', 'difficultes_rencontrees' => 'Délais de remontée, passation, coordination et disponibilité des preuves.', 'recommandations' => 'Renforcer la discipline de reporting et accélérer les arbitrages.', 'perspectives' => 'Poursuivre les activités structurantes et sécuriser les résultats sensibles.', 'statut' => in_array($papa->statut, ['archive', 'en_execution', 'valide'], true) ? 'publie' : 'soumis', 'redige_par' => $this->userForDirection($direction?->code, ['directeur_technique', 'directeur_appui', 'chef_service'])?->id, 'valide_par' => in_array($papa->statut, ['archive', 'en_execution', 'valide'], true) ? optional($this->users->firstWhere('email', 'sg@ceeac-eccas.org'))->id : null, 'valide_le' => in_array($papa->statut, ['archive', 'en_execution', 'valide'], true) ? now()->subDays(rand(15, 90)) : null, 'publie_le' => in_array($papa->statut, ['archive', 'en_execution', 'valide'], true) ? now()->subDays(rand(10, 60)) : null]
            );

            Document::updateOrCreate(
                ['documentable_type' => Rapport::class, 'documentable_id' => $rapport->id, 'titre' => $rapport->titre],
                ['categorie_id' => $this->categories->get('RAPP')?->id ?? $this->categories->first()?->id, 'description' => 'Version GED du rapport.', 'reference' => sprintf('RPT/%d/%03d', $rapport->annee, $rapport->id), 'date_document' => now()->subDays(rand(5, 60)), 'chemin_fichier' => "ged/demo/rapports/rapport-{$rapport->id}.pdf", 'nom_fichier_original' => "rapport-{$rapport->id}.pdf", 'extension' => 'pdf', 'mime_type' => 'application/pdf', 'taille_octets' => rand(250000, 1450000), 'version' => '1.0', 'confidentialite' => 'interne', 'statut' => $rapport->statut === 'publie' ? 'valide' : 'soumis', 'depose_par' => $rapport->redige_par ?: $this->admin->id, 'valide_par' => $rapport->valide_par, 'valide_le' => $rapport->valide_le, 'est_archive' => false, 'archive_le' => null, 'hash_sha256' => hash('sha256', "rapport-{$rapport->id}")]
            );
        }
    }

    protected function createPapaWorkflow(Papa $papa): void
    {
        $entries = [['etape' => 'soumission', 'action' => 'soumis', 'acteur' => $this->admin, 'avant' => 'brouillon', 'apres' => 'soumis', 'commentaire' => 'Version transmise pour validation.']];
        $sg = $this->users->firstWhere('email', 'sg@ceeac-eccas.org');
        $vp = $this->users->firstWhere('email', 'vpresident@ceeac-eccas.org');
        $president = $this->users->firstWhere('email', 'president@ceeac-eccas.org');

        if (in_array($papa->statut, ['en_validation', 'valide', 'en_execution', 'archive'], true)) {
            $entries[] = ['etape' => 'validation_sg', 'action' => 'approuve', 'acteur' => $sg, 'avant' => 'soumis', 'apres' => 'en_validation', 'commentaire' => 'Conformité administrative jugée satisfaisante.'];
        }
        if (in_array($papa->statut, ['valide', 'en_execution', 'archive'], true)) {
            $entries[] = ['etape' => 'validation_vp', 'action' => 'approuve', 'acteur' => $vp, 'avant' => 'en_validation', 'apres' => 'valide', 'commentaire' => 'Arbitrages stratégiques validés.'];
            $entries[] = ['etape' => 'validation_president', 'action' => 'approuve', 'acteur' => $president, 'avant' => 'valide', 'apres' => $papa->statut, 'commentaire' => 'Validation finale enregistrée.'];
        }
        if ($papa->statut === 'archive') {
            $entries[] = ['etape' => 'cloture', 'action' => 'information', 'acteur' => $this->admin, 'avant' => 'cloture', 'apres' => 'archive', 'commentaire' => 'Bascule en consultation historique.'];
        }

        foreach ($entries as $index => $entry) {
            ValidationWorkflow::updateOrCreate(
                ['validable_type' => Papa::class, 'validable_id' => $papa->id, 'etape' => $entry['etape'], 'action' => $entry['action'], 'acteur_id' => $entry['acteur']->id],
                ['papa_id' => $papa->id, 'commentaire' => $entry['commentaire'], 'motif_rejet' => null, 'statut_avant' => $entry['avant'], 'statut_apres' => $entry['apres'], 'created_at' => Carbon::create($papa->annee, 1, 1)->subDays(30 - ($index * 5)), 'updated_at' => Carbon::create($papa->annee, 1, 1)->subDays(30 - ($index * 5))]
            );
        }
    }

    protected function seedNotifications(): void
    {
        Alerte::query()->whereNotNull('destinataire_id')->get()->each(function (Alerte $alerte): void {
            NotificationApp::updateOrCreate(
                ['user_id' => $alerte->destinataire_id, 'type' => 'alerte', 'titre' => $alerte->titre],
                ['message' => $alerte->message, 'lien' => '/alertes/' . $alerte->id, 'icone' => 'fa-bell', 'niveau' => $alerte->niveau === 'critique' ? 'erreur' : 'attention', 'notifiable_type' => Alerte::class, 'notifiable_id' => $alerte->id, 'lue_le' => $alerte->lue_le]
            );
        });

        ValidationWorkflow::query()->take(80)->get()->each(function (ValidationWorkflow $workflow): void {
            NotificationApp::updateOrCreate(
                ['user_id' => $workflow->acteur_id, 'type' => 'workflow', 'titre' => "Workflow {$workflow->etape}"],
                ['message' => $workflow->commentaire ?: 'Étape de workflow enregistrée.', 'lien' => '/dashboard', 'icone' => 'fa-route', 'niveau' => $workflow->action === 'rejete' ? 'erreur' : 'info', 'notifiable_type' => ValidationWorkflow::class, 'notifiable_id' => $workflow->id, 'lue_le' => $workflow->action === 'approuve' ? now()->subDays(rand(1, 30)) : null]
            );
        });
    }

    protected function seedAuditLogs(): void
    {
        $rows = [];
        $subjects = collect()->merge(Papa::take(5)->get())->merge(ActionPrioritaire::take(30)->get())->merge(Activite::take(60)->get())->merge(Document::take(40)->get())->shuffle()->take(160);
        foreach ($subjects as $index => $subject) {
            $causer = $this->users->values()->get($index % max(1, $this->users->count()));
            $rows[] = ['log_name' => 'demo', 'description' => 'Trace de démonstration ' . class_basename($subject), 'subject_type' => get_class($subject), 'subject_id' => $subject->id, 'event' => $index % 4 === 0 ? 'created' : 'updated', 'causer_type' => User::class, 'causer_id' => $causer?->id, 'properties' => json_encode(['attributes' => ['id' => $subject->id], 'old' => ['status' => 'previous']], JSON_UNESCAPED_UNICODE), 'batch_uuid' => (string) Str::uuid(), 'created_at' => now()->subDays(rand(1, 360)), 'updated_at' => now()->subDays(rand(1, 360))];
        }
        if ($rows) {
            DB::table(config('activitylog.table_name', 'activity_log'))->insert($rows);
        }
    }

    protected function recalculatePapas(): void
    {
        Papa::query()->each(function (Papa $papa): void {
            $activities = Activite::query()->whereHas('resultatAttendu.objectifImmediats.actionPrioritaire', fn($q) => $q->where('papa_id', $papa->id))->get();
            $budgets = BudgetPapa::query()->where('papa_id', $papa->id)->get();
            $papa->update([
                'taux_execution_physique' => round((float) $activities->avg('taux_realisation'), 2),
                'taux_execution_financiere' => round($budgets->sum('montant_prevu') > 0 ? ($budgets->sum('montant_decaisse') / $budgets->sum('montant_prevu')) * 100 : 0, 2),
                'budget_total_prevu' => round((float) $budgets->sum('montant_prevu'), 2),
            ]);
        });
    }

    protected function userForDirection(?string $directionCode, array $roles): ?User
    {
        $directionId = $directionCode ? optional($this->directions->get($directionCode))->id : null;
        foreach ($roles as $role) {
            $user = $this->users->first(fn(User $u) => $u->hasRole($role) && ($directionId === null || $u->direction_id === $directionId));
            if ($user) {
                return $user;
            }
        }
        return $this->admin;
    }

    protected function progressFor(string $status, string $profile): float
    {
        return match ($status) {
            'archive' => match ($profile) {'excellent' => 100, 'steady' => 94, 'watch' => 88, 'critical' => 76, default => 91},
            'en_execution' => match ($profile) {'excellent' => 83, 'steady' => 68, 'watch' => 54, 'critical' => 37, default => 61},
            'valide' => match ($profile) {'excellent' => 58, 'steady' => 45, 'watch' => 38, 'critical' => 26, default => 41},
            'en_validation' => match ($profile) {'excellent' => 29, 'steady' => 23, 'watch' => 18, 'critical' => 12, default => 20},
            default => match ($profile) {'excellent' => 12, 'steady' => 8, 'watch' => 5, 'critical' => 2, default => 6},
        };
    }
}
