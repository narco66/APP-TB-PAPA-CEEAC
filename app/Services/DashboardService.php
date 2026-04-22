<?php

namespace App\Services;

use App\Models\Activite;
use App\Models\Alerte;
use App\Models\BudgetPapa;
use App\Models\Direction;
use App\Models\Indicateur;
use App\Models\Papa;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function kpisExecutif(Papa $papa, ?User $user = null): array
    {
        return $this->rememberScoped("kpis_executif_{$papa->id}_{$this->cacheScopeKey($user)}", function () use ($papa, $user) {
            return $this->computeKpisExecutif($papa, $user);
        });
    }

    public function kpisDirection(Papa $papa, Direction $direction, ?User $user = null): array
    {
        return $this->rememberScoped("kpis_direction_{$papa->id}_{$direction->id}_{$this->cacheScopeKey($user)}", function () use ($papa, $direction, $user) {
            $activites = $this->scopedActivitesQuery($papa, $user)
                ->where('direction_id', $direction->id)
                ->get();

            $tauxMoyen = $activites->avg('taux_realisation') ?? 0;
            $enRetard = $activites->filter(fn($activite) => $activite->estEnRetard())->count();
            $terminees = $activites->where('statut', 'terminee')->count();
            $enCours = $activites->where('statut', 'en_cours')->count();

            $budgetPrevu = $activites->sum('budget_prevu');
            $budgetEngage = $activites->sum('budget_engage');
            $budgetConsomme = $activites->sum('budget_consomme');

            $indicateurs = $user
                ? Indicateur::query()->visibleTo($user)->where('direction_id', $direction->id)->actif()->get()
                : Indicateur::where('direction_id', $direction->id)->actif()->get();

            $indicateursEnAlerte = $indicateurs
                ->filter(fn($indicateur) => in_array($indicateur->niveauAlerte(), ['rouge', 'orange']))
                ->count();

            return [
                'direction' => $direction,
                'taux_moyen_activites' => round((float) $tauxMoyen, 2),
                'total_activites' => $activites->count(),
                'activites_en_cours' => $enCours,
                'activites_terminees' => $terminees,
                'activites_en_retard' => $enRetard,
                'budget_prevu' => $budgetPrevu,
                'budget_engage' => $budgetEngage,
                'budget_consomme' => $budgetConsomme,
                'indicateurs_en_alerte' => $indicateursEnAlerte,
            ];
        });
    }

    public function evolutionTrimestrielle(Papa $papa, ?User $user = null): array
    {
        $kpis = $this->kpisExecutif($papa, $user);

        return [
            'labels' => ['T1', 'T2', 'T3', 'T4'],
            'physique' => [
                $kpis['taux_execution_physique'] * 0.25,
                $kpis['taux_execution_physique'] * 0.5,
                $kpis['taux_execution_physique'] * 0.75,
                $kpis['taux_execution_physique'],
            ],
            'financier' => [
                $kpis['taux_execution_financiere'] * 0.2,
                $kpis['taux_execution_financiere'] * 0.45,
                $kpis['taux_execution_financiere'] * 0.7,
                $kpis['taux_execution_financiere'],
            ],
        ];
    }

    public function repartitionActivitesStatut(Papa $papa, ?User $user = null): array
    {
        return $this->rememberScoped("repartition_activites_{$papa->id}_{$this->cacheScopeKey($user)}", fn() =>
            $this->scopedActivitesQuery($papa, $user)
                ->selectRaw('statut, count(*) as total')
                ->groupBy('statut')
                ->pluck('total', 'statut')
                ->toArray()
        );
    }

    public function comparatifDepartements(Papa $papa, ?User $user = null): array
    {
        return $this->rememberScoped("comparatif_departements_{$papa->id}_{$this->cacheScopeKey($user)}", fn() =>
            $this->scopedActionsQuery($papa, $user)
                ->with('departement')
                ->get()
                ->groupBy(fn($action) => $action->departement?->libelle_court ?? 'N/A')
                ->map(fn($actions) => [
                    'taux_moyen' => round((float) $actions->avg('taux_realisation'), 2),
                    'total' => $actions->count(),
                    'en_cours' => $actions->where('statut', 'en_cours')->count(),
                    'terminees' => $actions->where('statut', 'termine')->count(),
                ])
                ->toArray()
        );
    }

    private function computeKpisExecutif(Papa $papa, ?User $user = null): array
    {
        $actionsQuery = $this->scopedActionsQuery($papa, $user);
        $activitesQuery = $this->scopedActivitesQuery($papa, $user);
        $budgetsQuery = $this->scopedBudgetsQuery($papa, $user);
        $alertesQuery = $this->scopedAlertesQuery($papa, $user);

        $totalActions = (clone $actionsQuery)->count();
        $actionsEnCours = (clone $actionsQuery)->where('statut', 'en_cours')->count();
        $actionsTerminees = (clone $actionsQuery)->where('statut', 'termine')->count();

        $totalActivites = (clone $activitesQuery)->count();
        $activitesEnRetard = (clone $activitesQuery)
            ->where('statut', '!=', 'terminee')
            ->where('statut', '!=', 'abandonnee')
            ->where('date_fin_prevue', '<', now()->toDateString())
            ->count();

        $budgetTotal = (clone $budgetsQuery)->sum('montant_prevu');
        $budgetEngage = (clone $budgetsQuery)->sum('montant_engage');
        $budgetDecaisse = (clone $budgetsQuery)->sum('montant_decaisse');

        $alertesCritiques = (clone $alertesQuery)
            ->where('niveau', 'critique')
            ->whereIn('statut', ['nouvelle', 'vue'])
            ->count();

        $alertesAttention = (clone $alertesQuery)
            ->where('niveau', 'attention')
            ->whereIn('statut', ['nouvelle', 'vue'])
            ->count();

        $tauxExecutionPhysique = $user
            ? round((float) ((clone $actionsQuery)->avg('taux_realisation') ?? 0), 2)
            : (float) $papa->taux_execution_physique;

        $tauxExecutionFinanciere = $user
            ? ($budgetTotal > 0 ? round((float) $budgetDecaisse / (float) $budgetTotal * 100, 2) : 0)
            : (float) $papa->taux_execution_financiere;

        return [
            'papa' => $papa,
            'taux_execution_physique' => $tauxExecutionPhysique,
            'taux_execution_financiere' => $tauxExecutionFinanciere,
            'total_actions_prioritaires' => $totalActions,
            'actions_en_cours' => $actionsEnCours,
            'actions_terminees' => $actionsTerminees,
            'total_activites' => $totalActivites,
            'activites_en_retard' => $activitesEnRetard,
            'budget_total' => $budgetTotal,
            'budget_engage' => $budgetEngage,
            'budget_decaisse' => $budgetDecaisse,
            'taux_engagement' => $budgetTotal > 0 ? round((float) $budgetEngage / (float) $budgetTotal * 100, 2) : 0,
            'taux_decaissement' => $budgetTotal > 0 ? round((float) $budgetDecaisse / (float) $budgetTotal * 100, 2) : 0,
            'alertes_critiques' => $alertesCritiques,
            'alertes_attention' => $alertesAttention,
        ];
    }

    private function scopedActionsQuery(Papa $papa, ?User $user = null)
    {
        $query = $papa->actionsPrioritaires();

        if ($user) {
            $query->visibleTo($user);
        }

        return $query;
    }

    private function scopedActivitesQuery(Papa $papa, ?User $user = null)
    {
        $query = Activite::query()->whereHas(
            'resultatAttendu.objectifImmediats.actionPrioritaire',
            fn($relationQuery) => $relationQuery->where('papa_id', $papa->id)
        );

        if ($user) {
            $query->visibleTo($user);
        }

        return $query;
    }

    private function scopedBudgetsQuery(Papa $papa, ?User $user = null)
    {
        $query = BudgetPapa::query()->where('papa_id', $papa->id);

        if (! $user) {
            return $query;
        }

        return $query->where(function ($budgetQuery) use ($user) {
            $budgetQuery
                ->whereHas('actionPrioritaire', fn($actionQuery) => $actionQuery->visibleTo($user))
                ->orWhereHas('activite', fn($activiteQuery) => $activiteQuery->visibleTo($user));
        });
    }

    private function scopedAlertesQuery(Papa $papa, ?User $user = null)
    {
        $query = Alerte::query()->where('papa_id', $papa->id);

        if ($user) {
            $query->visibleTo($user);
        }

        return $query;
    }

    private function cacheScopeKey(?User $user = null): string
    {
        return $user ? 'user_' . $user->id : 'global';
    }

    private function rememberScoped(string $key, callable $callback): array
    {
        if (app()->runningUnitTests()) {
            return $callback();
        }

        return Cache::remember($key, 900, $callback);
    }
}
