<?php

namespace App\Services\Gantt;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GanttTreeBuilder
{
    /**
     * Offset appliqué aux IDs des nœuds de groupe (ResultatAttendu)
     * pour éviter toute collision avec les IDs d'activités.
     */
    private const GROUP_OFFSET = 1_000_000;

    /**
     * Construit la liste plate de tâches dhtmlx avec hiérarchie RBM :
     *   [Groupe ResultatAttendu] > [Activités enfants]
     *
     * @param  Collection  $activites  Collection<Activite> avec resultatAttendu chargé
     * @param  callable    $toTask     Callable(Activite): array — transforme une activité en tâche dhtmlx
     * @return array                   Tableau plat de nœuds prêt pour gantt.parse()
     */
    public function build(Collection $activites, callable $toTask): array
    {
        if ($activites->isEmpty()) {
            return [];
        }

        $nodes = [];

        // Regroupe par resultat_attendu_id, conserve l'ordre (date_debut_prevue)
        $byRa = $activites->groupBy('resultat_attendu_id');

        foreach ($byRa as $raId => $raActivites) {
            $ra      = $raActivites->first()->resultatAttendu;
            $groupId = self::GROUP_OFFSET + (int) $raId;

            // Plage temporelle du groupe = union des plages enfants
            $starts = $raActivites->pluck('date_debut_prevue')->filter();
            $ends   = $raActivites->pluck('date_fin_prevue')->filter();

            // Avancement moyen pondéré (simple : moyenne des taux_realisation)
            $avgProgress = $raActivites->avg('taux_realisation') / 100;

            $groupLabel = $ra
                ? "[{$ra->code}] " . Str::limit($ra->libelle, 90)
                : "Résultat attendu #{$raId}";

            // ── Nœud groupe ─────────────────────────────────────────
            $nodes[] = [
                'id'         => $groupId,
                'text'       => $groupLabel,
                'type'       => 'project',
                'start_date' => $starts->min()?->format('d-m-Y'),
                'end_date'   => $ends->max()?->format('d-m-Y'),
                'progress'   => round($avgProgress, 2),
                'open'       => true,
                'is_group'   => true,
                'readonly'   => true,
                'color'      => '#e0e7ff',    // indigo très clair pour distinguer les groupes
            ];

            // ── Activités enfants ────────────────────────────────────
            foreach ($raActivites as $activite) {
                $task           = ($toTask)($activite);
                $task['parent'] = $groupId;
                $nodes[]        = $task;
            }
        }

        return $nodes;
    }
}
