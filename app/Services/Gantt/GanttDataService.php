<?php

namespace App\Services\Gantt;

use App\Models\Activite;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class GanttDataService
{
    /** Correspondance type_dependance BDD → code dhtmlx */
    private const LINK_TYPE_MAP = [
        'fin_debut'    => '0',
        'debut_debut'  => '1',
        'fin_fin'      => '2',
        'debut_fin'    => '3',
    ];

    /** TTL du cache en secondes (5 minutes) */
    private const CACHE_TTL = 300;

    public function __construct(
        private GanttTreeBuilder $treeBuilder,
        private GanttCriticalPathCalculator $cpmCalculator,
    ) {}

    /**
     * Construit le payload complet pour dhtmlx Gantt.
     * Résultat mis en cache par utilisateur + filtres + version globale.
     */
    public function build(User $user, array $filters): array
    {
        $version  = Cache::get('gantt.version', 0);
        $cacheKey = 'gantt.' . $user->id . '.' . $version . '.' . md5(serialize($filters));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $filters) {
            return $this->buildFresh($user, $filters);
        });
    }

    /**
     * Construit les données sans passer par le cache.
     * Utilisé par les exports (qui doivent toujours avoir des données fraîches).
     */
    public function buildFresh(User $user, array $filters): array
    {
        $activites = $this->query($user, $filters)->get();

        $tasks = $this->treeBuilder->build($activites, fn($a) => $this->toTask($a));
        $links = $this->buildLinks($activites);

        // Calcul du chemin critique et annotation des tâches
        $criticalIds = $this->cpmCalculator->compute($tasks, $links);
        $criticalSet = array_flip($criticalIds);

        foreach ($tasks as &$task) {
            if (!empty($task['is_group'])) {
                continue;
            }
            $task['est_chemin_critique'] = isset($criticalSet[$task['id']]);
        }
        unset($task);

        return [
            'data'        => $tasks,
            'links'       => $links,
            'scope_label' => $user->scopeLabel(),
            'total'       => $activites->count(),
        ];
    }

    // ─── Requête ────────────────────────────────────────────────

    private function query(User $user, array $filters): Builder
    {
        $defaultFrom = now()->subMonths(3)->toDateString();
        $defaultTo   = now()->addMonths(6)->toDateString();

        $dateFrom = $filters['date_from'] ?? $defaultFrom;
        $dateTo   = $filters['date_to']   ?? $defaultTo;

        return Activite::with([
                'resultatAttendu:id,code,libelle,objectif_immediat_id',
                'predecesseurs',
                'responsable:id,name',
                'alertes' => fn($q) => $q->where('statut', 'nouvelle'),
                'documents:id,documentable_id,documentable_type',
            ])
            ->visibleTo($user)
            ->whereNotNull('date_debut_prevue')
            ->whereNotNull('date_fin_prevue')
            ->where('date_debut_prevue', '<=', $dateTo)
            ->where('date_fin_prevue',   '>=', $dateFrom)
            ->when(
                !empty($filters['statut']),
                fn($q) => $q->whereIn('statut', (array) $filters['statut'])
            )
            ->when(
                !empty($filters['direction_id']) && $user->can('activite.voir_toutes_directions'),
                fn($q) => $q->where('direction_id', (int) $filters['direction_id'])
            )
            ->when(
                !empty($filters['priorite']),
                fn($q) => $q->where('priorite', $filters['priorite'])
            )
            ->orderBy('resultat_attendu_id')
            ->orderBy('date_debut_prevue');
    }

    // ─── DTO activité → tâche dhtmlx ────────────────────────────

    public function toTask(Activite $a): array
    {
        return [
            'id'         => $a->id,
            'text'       => "[{$a->code}] {$a->libelle}",
            'start_date' => $a->date_debut_prevue?->format('d-m-Y'),
            'end_date'   => $a->date_fin_prevue?->format('d-m-Y'),
            'progress'   => (float) $a->taux_realisation / 100,
            'type'       => $a->est_jalon ? 'milestone' : 'task',
            'color'      => $this->resolveColor($a),
            'open'       => true,

            'code'         => $a->code,
            'statut'       => $a->statut,
            'priorite'     => $a->priorite,
            'est_retard'   => $a->estEnRetard(),
            'est_critique' => $a->priorite === 'critique',
            'est_jalon'    => $a->est_jalon,

            'date_debut_reelle' => $a->date_debut_reelle?->format('d-m-Y'),
            'date_fin_reelle'   => $a->date_fin_reelle?->format('d-m-Y'),
            'has_baseline'      => $a->date_debut_reelle !== null && $a->date_fin_reelle !== null,

            'budget_prevu'    => (float) $a->budget_prevu,
            'budget_engage'   => (float) $a->budget_engage,
            'budget_consomme' => (float) $a->budget_consomme,
            'devise'          => $a->devise,

            'responsable'  => $a->responsable?->name,
            'direction_id' => $a->direction_id,

            'has_alerte'    => $a->alertes->isNotEmpty(),
            'nb_alertes'    => $a->alertes->count(),
            'has_documents' => $a->documents->isNotEmpty(),
            'nb_documents'  => $a->documents->count(),

            'url_detail' => route('activites.show', $a->id),
        ];
    }

    // ─── Couleur de barre ────────────────────────────────────────

    private function resolveColor(Activite $a): string
    {
        return match(true) {
            $a->statut === 'abandonnee'                      => '#9ca3af',
            $a->statut === 'terminee'                        => '#22c55e',
            $a->statut === 'suspendue'                       => '#f59e0b',
            $a->estEnRetard() && $a->priorite === 'critique' => '#7f1d1d',
            $a->estEnRetard()                                => '#ef4444',
            $a->priorite === 'critique'                      => '#dc2626',
            $a->priorite === 'haute'                         => '#8b5cf6',
            $a->statut === 'en_cours'                        => '#3b82f6',
            default                                          => '#6366f1',
        };
    }

    // ─── Liens de dépendances ────────────────────────────────────

    private function buildLinks(iterable $activites): array
    {
        $links = [];
        foreach ($activites as $activite) {
            foreach ($activite->predecesseurs as $dep) {
                $links[] = [
                    'id'     => $dep->id,
                    'source' => $dep->activite_predecesseur_id,
                    'target' => $dep->activite_id,
                    'type'   => self::LINK_TYPE_MAP[$dep->type_dependance] ?? '0',
                    'lag'    => (int) $dep->delai_jours,
                ];
            }
        }
        return $links;
    }
}
