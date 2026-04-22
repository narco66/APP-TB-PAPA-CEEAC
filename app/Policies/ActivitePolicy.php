<?php

namespace App\Policies;

use App\Models\Activite;
use App\Models\User;
use App\Services\Security\UserScopeResolver;

class ActivitePolicy
{
    public function voir(User $user, Activite $activite): bool
    {
        if (! $user->can('activite.voir')) {
            return false;
        }

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            departementId: $activite->direction?->departement_id,
            directionId: $activite->direction_id,
            serviceId: $activite->service_id,
        );
    }

    public function creer(User $user): bool
    {
        return $user->can('activite.creer');
    }

    public function modifier(User $user, Activite $activite): bool
    {
        if (! $user->can('activite.modifier')) {
            return false;
        }

        if ($activite->resultatAttendu?->objectifImmediats?->actionPrioritaire?->papa?->estVerrouille()) {
            return false;
        }

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            departementId: $activite->direction?->departement_id,
            directionId: $activite->direction_id,
            serviceId: $activite->service_id,
        );
    }

    public function mettreAJourAvancement(User $user, Activite $activite): bool
    {
        if (! $user->can('activite.mettre_a_jour_avancement')) {
            return false;
        }

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            departementId: $activite->direction?->departement_id,
            directionId: $activite->direction_id,
            serviceId: $activite->service_id,
        );
    }

    public function supprimer(User $user, Activite $activite): bool
    {
        if (! $user->can('activite.supprimer')) {
            return false;
        }

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            departementId: $activite->direction?->departement_id,
            directionId: $activite->direction_id,
            serviceId: $activite->service_id,
        );
    }
}