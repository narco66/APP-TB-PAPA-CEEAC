<?php

namespace App\Services\Gantt;

use Carbon\Carbon;

/**
 * Calcule le chemin critique d'un graphe d'activités via la méthode CPM
 * (Critical Path Method) — Algorithme en deux passes (forward / backward).
 *
 * Entrées : tableau plat de tâches dhtmlx + tableau de liens.
 * Sortie  : tableau des IDs d'activités sur le chemin critique (float == 0).
 */
class GanttCriticalPathCalculator
{
    /**
     * @param  array  $tasks  Tâches dhtmlx (champs : id, start_date, end_date, is_group, type)
     * @param  array  $links  Liens dhtmlx (champs : source, target, lag)
     * @return int[]          IDs des tâches sur le chemin critique
     */
    public function compute(array $tasks, array $links): array
    {
        // ── 1. Construire la carte des activités ──────────────────────────
        $acts = [];
        foreach ($tasks as $task) {
            // Exclure les nœuds de groupe et les jalons (sans durée)
            if (!empty($task['is_group']) || ($task['type'] ?? '') === 'milestone') {
                continue;
            }

            $start = $this->parseDate($task['start_date'] ?? '');
            $end   = $this->parseDate($task['end_date']   ?? '');

            if (!$start || !$end || $end->lessThanOrEqualTo($start)) {
                continue;
            }

            $acts[$task['id']] = [
                'dur'   => (int) $start->diffInDays($end),   // durée en jours
                'preds' => [],   // [{id, lag}]
                'succs' => [],   // [id]
                'es'    => 0,
                'ef'    => 0,
                'ls'    => PHP_INT_MAX,
                'lf'    => PHP_INT_MAX,
            ];
        }

        if (empty($acts)) {
            return [];
        }

        // ── 2. Enregistrer les dépendances ────────────────────────────────
        foreach ($links as $link) {
            $src = $link['source'];
            $tgt = $link['target'];
            $lag = (int) ($link['lag'] ?? 0);

            if (!isset($acts[$src]) || !isset($acts[$tgt])) {
                continue;
            }

            $acts[$tgt]['preds'][] = ['id' => $src, 'lag' => $lag];
            $acts[$src]['succs'][] = $tgt;
        }

        // ── 3. Tri topologique (algorithme de Kahn) ───────────────────────
        $inDegree = [];
        foreach ($acts as $id => $data) {
            $inDegree[$id] = count($data['preds']);
        }

        $queue = array_keys(array_filter($inDegree, fn($d) => $d === 0));
        $order = [];

        while ($queue) {
            $id = array_shift($queue);
            $order[] = $id;
            foreach ($acts[$id]['succs'] as $succId) {
                if (--$inDegree[$succId] === 0) {
                    $queue[] = $succId;
                }
            }
        }

        // Cycle détecté → graphe invalide, pas de chemin critique calculable
        if (count($order) !== count($acts)) {
            return [];
        }

        // ── 4. Passe avant (Early Start / Early Finish) ───────────────────
        foreach ($order as $id) {
            $maxPredEF = 0;
            foreach ($acts[$id]['preds'] as $pred) {
                $maxPredEF = max($maxPredEF, $acts[$pred['id']]['ef'] + $pred['lag']);
            }
            $acts[$id]['es'] = $maxPredEF;
            $acts[$id]['ef'] = $maxPredEF + $acts[$id]['dur'];
        }

        // Durée totale du projet
        $projectFinish = max(array_column($acts, 'ef'));

        // ── 5. Passe arrière (Late Start / Late Finish) ───────────────────
        foreach (array_reverse($order) as $id) {
            if (empty($acts[$id]['succs'])) {
                $acts[$id]['lf'] = $projectFinish;
            } else {
                $minSuccLS = PHP_INT_MAX;
                foreach ($acts[$id]['succs'] as $succId) {
                    $minSuccLS = min($minSuccLS, $acts[$succId]['ls']);
                }
                $acts[$id]['lf'] = $minSuccLS;
            }
            $acts[$id]['ls'] = $acts[$id]['lf'] - $acts[$id]['dur'];
        }

        // ── 6. Chemin critique = float nul ────────────────────────────────
        return array_values(
            array_keys(array_filter($acts, fn($a) => ($a['ls'] - $a['es']) === 0))
        );
    }

    private function parseDate(string $date): ?Carbon
    {
        if (!$date) {
            return null;
        }
        try {
            return Carbon::createFromFormat('d-m-Y', $date);
        } catch (\Throwable) {
            return null;
        }
    }
}
