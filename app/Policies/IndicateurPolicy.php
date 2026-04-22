<?php

namespace App\Policies;

use App\Models\Indicateur;
use App\Models\User;
use App\Services\Security\UserScopeResolver;

class IndicateurPolicy
{
    public function voir(User $user, Indicateur $indicateur): bool
    {
        if (! $user->can('indicateur.voir')) {
            return false;
        }

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            departementId: $indicateur->direction?->departement_id,
            directionId: $indicateur->direction_id,
        );
    }

    public function creer(User $user): bool
    {
        return $user->can('indicateur.creer');
    }

    public function modifier(User $user, Indicateur $indicateur): bool
    {
        if (! $user->can('indicateur.modifier')) {
            return false;
        }

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            departementId: $indicateur->direction?->departement_id,
            directionId: $indicateur->direction_id,
        );
    }

    public function saisirValeur(User $user, Indicateur $indicateur): bool
    {
        if (! $user->can('indicateur.saisir_valeur')) {
            return false;
        }

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            departementId: $indicateur->direction?->departement_id,
            directionId: $indicateur->direction_id,
        );
    }

    public function validerValeur(User $user, Indicateur $indicateur): bool
    {
        if (! $user->can('indicateur.valider_valeur')) {
            return false;
        }

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            departementId: $indicateur->direction?->departement_id,
            directionId: $indicateur->direction_id,
        );
    }
}