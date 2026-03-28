<?php

namespace App\Services;

use App\Models\Activite;
use App\Models\ActionPrioritaire;
use App\Models\Alerte;
use App\Models\BudgetPapa;
use App\Models\Direction;
use App\Models\Indicateur;
use App\Models\Papa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * KPIs consolidés pour le Président / VP
     */
    public function kpisExecutif(Papa $papa): array
    {
        $totalAP       = $papa->actionsPrioritaires()->count();
        $apEnCours     = $papa->actionsPrioritaires()->where('statut', 'en_cours')->count();
        $apTerminees   = $papa->actionsPrioritaires()->where('statut', 'termine')->count();

        $totalActivites    = Activite::whereHas('resultatAttendu.objectifImmediats.actionPrioritaire', fn($q) => $q->where('papa_id', $papa->id))->count();
        $activitesEnRetard = Activite::whereHas('resultatAttendu.objectifImmediats.actionPrioritaire', fn($q) => $q->where('papa_id', $papa->id))
            ->where('statut', '!=', 'terminee')
            ->where('statut', '!=', 'abandonnee')
            ->where('date_fin_prevue', '<', now()->toDateString())
            ->count();

        $budgetTotal   = $papa->budgets()->sum('montant_prevu');
        $budgetEngage  = $papa->budgets()->sum('montant_engage');
        $budgetDecaisse = $papa->budgets()->sum('montant_decaisse');

        $alertesCritiques = Alerte::where('papa_id', $papa->id)
            ->where('niveau', 'critique')
            ->whereIn('statut', ['nouvelle', 'vue'])
            ->count();

        $alertesAttention = Alerte::where('papa_id', $papa->id)
            ->where('niveau', 'attention')
            ->whereIn('statut', ['nouvelle', 'vue'])
            ->count();

        return [
            'papa'                         => $papa,
            'taux_execution_physique'      => $papa->taux_execution_physique,
            'taux_execution_financiere'    => $papa->taux_execution_financiere,
            'total_actions_prioritaires'   => $totalAP,
            'actions_en_cours'             => $apEnCours,
            'actions_terminees'            => $apTerminees,
            'total_activites'              => $totalActivites,
            'activites_en_retard'          => $activitesEnRetard,
            'budget_total'                 => $budgetTotal,
            'budget_engage'                => $budgetEngage,
            'budget_decaisse'              => $budgetDecaisse,
            'taux_engagement'              => $budgetTotal > 0 ? round($budgetEngage / $budgetTotal * 100, 2) : 0,
            'taux_decaissement'            => $budgetTotal > 0 ? round($budgetDecaisse / $budgetTotal * 100, 2) : 0,
            'alertes_critiques'            => $alertesCritiques,
            'alertes_attention'            => $alertesAttention,
        ];
    }

    /**
     * KPIs pour une Direction
     */
    public function kpisDirection(Papa $papa, Direction $direction): array
    {
        $activites = Activite::where('direction_id', $direction->id)
            ->whereHas('resultatAttendu.objectifImmediats.actionPrioritaire', fn($q) => $q->where('papa_id', $papa->id))
            ->get();

        $tauxMoyen     = $activites->avg('taux_realisation') ?? 0;
        $enRetard      = $activites->filter(fn($a) => $a->estEnRetard())->count();
        $terminees     = $activites->where('statut', 'terminee')->count();
        $enCours       = $activites->where('statut', 'en_cours')->count();

        $budgetPrevu   = $activites->sum('budget_prevu');
        $budgetEngage  = $activites->sum('budget_engage');
        $budgetConso   = $activites->sum('budget_consomme');

        $indicateurs   = Indicateur::where('direction_id', $direction->id)->actif()->get();
        $indEnAlerte   = $indicateurs->filter(fn($i) => in_array($i->niveauAlerte(), ['rouge', 'orange']))->count();

        return [
            'direction'            => $direction,
            'taux_moyen_activites' => round($tauxMoyen, 2),
            'total_activites'      => $activites->count(),
            'activites_en_cours'   => $enCours,
            'activites_terminees'  => $terminees,
            'activites_en_retard'  => $enRetard,
            'budget_prevu'         => $budgetPrevu,
            'budget_engage'        => $budgetEngage,
            'budget_consomme'      => $budgetConso,
            'indicateurs_en_alerte' => $indEnAlerte,
        ];
    }

    /**
     * Données pour graphique évolution trimestrielle
     */
    public function evolutionTrimestrielle(Papa $papa): array
    {
        // Simulé depuis les valeurs indicateurs agrégées
        // En production : requête réelle sur valeurs_indicateurs
        return [
            'labels' => ['T1', 'T2', 'T3', 'T4'],
            'physique' => [
                $papa->taux_execution_physique * 0.25,
                $papa->taux_execution_physique * 0.5,
                $papa->taux_execution_physique * 0.75,
                $papa->taux_execution_physique,
            ],
            'financier' => [
                $papa->taux_execution_financiere * 0.2,
                $papa->taux_execution_financiere * 0.45,
                $papa->taux_execution_financiere * 0.7,
                $papa->taux_execution_financiere,
            ],
        ];
    }

    /**
     * Répartition des activités par statut (pour camembert)
     */
    public function repartitionActivitesStatut(Papa $papa): array
    {
        return Activite::whereHas(
            'resultatAttendu.objectifImmediats.actionPrioritaire',
            fn($q) => $q->where('papa_id', $papa->id)
        )
        ->selectRaw('statut, count(*) as total')
        ->groupBy('statut')
        ->pluck('total', 'statut')
        ->toArray();
    }

    /**
     * Comparatif AP par département
     */
    public function comparatifDepartements(Papa $papa): array
    {
        return $papa->actionsPrioritaires()
            ->with('departement')
            ->get()
            ->groupBy('departement.libelle_court')
            ->map(fn($aps) => [
                'taux_moyen'  => round($aps->avg('taux_realisation'), 2),
                'total'       => $aps->count(),
                'en_cours'    => $aps->where('statut', 'en_cours')->count(),
                'terminees'   => $aps->where('statut', 'termine')->count(),
            ])
            ->toArray();
    }
}
