<?php

namespace App\Services\Security;

use App\Models\User;
use App\Support\Security\UserVisibilityScope;
use Illuminate\Database\Eloquent\Builder;

class UserScopeResolver
{
    public function resolve(User $user): UserVisibilityScope
    {
        $isUnscopedAdmin = $user->can('admin.utilisateurs')
            && ! $user->departement_id
            && ! $user->direction_id
            && ! $user->service_id
            && ! $user->scope_level
            && ! $user->is_transversal;

        $isUnscopedAuditViewer = $user->can('admin.audit_log')
            && ! $user->departement_id
            && ! $user->direction_id
            && ! $user->service_id
            && ! $user->scope_level
            && ! $user->is_transversal;

        if ($user->hasRole('super_admin') || $isUnscopedAdmin || $isUnscopedAuditViewer) {
            return new UserVisibilityScope(level: 'commission', isGlobal: true, isTransversal: true);
        }

        $departementIds = [];
        $directionIds = [];
        $serviceIds = [];

        if ($user->service_id) {
            $serviceIds[] = $user->service_id;
        }

        $directionId = $user->direction_id ?? $user->service?->direction_id;
        if ($directionId) {
            $directionIds[] = $directionId;
        }

        $departementId = $user->departement_id
            ?? $user->direction?->departement_id
            ?? $user->service?->direction?->departement_id;

        if ($departementId) {
            $departementIds[] = $departementId;
        }

        $activeTransversalScopes = $user->transversalScopes()
            ->where(function (Builder $query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->where('can_view', true)
            ->get();

        foreach ($activeTransversalScopes as $scope) {
            if ($scope->departement_id) {
                $departementIds[] = $scope->departement_id;
            }
            if ($scope->direction_id) {
                $directionIds[] = $scope->direction_id;
            }
            if ($scope->service_id) {
                $serviceIds[] = $scope->service_id;
            }
        }

        $level = $user->scope_level
            ?: ($user->service_id ? 'service' : ($directionId ? 'direction' : ($departementId ? 'departement' : 'personnel')));

        return new UserVisibilityScope(
            level: $level,
            departementIds: array_values(array_unique(array_filter($departementIds))),
            directionIds: array_values(array_unique(array_filter($directionIds))),
            serviceIds: array_values(array_unique(array_filter($serviceIds))),
            isGlobal: false,
            isTransversal: $user->is_transversal || $activeTransversalScopes->isNotEmpty(),
        );
    }

    public function applyToQuery(Builder $query, User $user, array $columns): Builder
    {
        $scope = $this->resolve($user);

        if ($scope->isGlobal) {
            return $query;
        }

        if ($scope->isTransversal) {
            $hasServiceRule = ($columns['service'] ?? null) && $scope->serviceIds !== [];
            $hasDirectionRule = ($columns['direction'] ?? null) && $scope->directionIds !== [];
            $hasDepartmentRule = ($columns['departement'] ?? null) && $scope->departementIds !== [];

            if (! $hasServiceRule && ! $hasDirectionRule && ! $hasDepartmentRule) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where(function (Builder $builder) use ($scope, $columns) {
                if (($columns['service'] ?? null) && $scope->serviceIds !== []) {
                    $builder->orWhereIn($columns['service'], $scope->serviceIds);
                }

                if (($columns['direction'] ?? null) && $scope->directionIds !== []) {
                    $builder->orWhereIn($columns['direction'], $scope->directionIds);
                }

                if (($columns['departement'] ?? null) && $scope->departementIds !== []) {
                    $builder->orWhereIn($columns['departement'], $scope->departementIds);
                }
            });
        }

        $rule = match ($scope->level) {
            'service' => $this->firstApplicableRule($scope, $columns, ['service', 'direction', 'departement']),
            'direction' => $this->firstApplicableRule($scope, $columns, ['direction', 'departement']),
            'departement' => $this->firstApplicableRule($scope, $columns, ['departement']),
            default => null,
        };

        if ($rule === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($rule['column'], $rule['ids']);
    }

    public function canAccessAttributes(User $user, ?int $departementId = null, ?int $directionId = null, ?int $serviceId = null): bool
    {
        $scope = $this->resolve($user);

        if ($scope->isGlobal) {
            return true;
        }

        if ($scope->isTransversal) {
            return ($serviceId && in_array($serviceId, $scope->serviceIds, true))
                || ($directionId && in_array($directionId, $scope->directionIds, true))
                || ($departementId && in_array($departementId, $scope->departementIds, true));
        }

        return match ($scope->level) {
            'service' => $serviceId && in_array($serviceId, $scope->serviceIds, true),
            'direction' => $directionId && in_array($directionId, $scope->directionIds, true),
            'departement' => $departementId && in_array($departementId, $scope->departementIds, true),
            default => false,
        };
    }

    private function firstApplicableRule(UserVisibilityScope $scope, array $columns, array $levels): ?array
    {
        foreach ($levels as $level) {
            $column = $columns[$level] ?? null;
            $ids = match ($level) {
                'service' => $scope->serviceIds,
                'direction' => $scope->directionIds,
                'departement' => $scope->departementIds,
                default => [],
            };

            if ($column && $ids !== []) {
                return ['column' => $column, 'ids' => $ids];
            }
        }

        return null;
    }
}
