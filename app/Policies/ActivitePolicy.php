<?php

namespace App\Policies;

use App\Models\Activite;
use App\Models\User;

class ActivitePolicy
{
    /**
     * Voir une activité.
     * Une Direction ne peut voir que ses propres activités,
     * sauf si l'utilisateur a le droit de voir toutes les directions.
     */
    public function voir(User $user, Activite $activite): bool
    {
        if (!$user->can('activite.voir')) return false;

        // Vision transversale (Président, VP, SG, Auditeur, Commissaire, etc.)
        if ($user->can('activite.voir_toutes_directions')) return true;

        // Restriction périmètre direction
        return $user->direction_id === $activite->direction_id;
    }

    public function creer(User $user): bool
    {
        return $user->can('activite.creer');
    }

    public function modifier(User $user, Activite $activite): bool
    {
        if (!$user->can('activite.modifier')) return false;

        // Vérification périmètre direction si pas vision transversale
        if (!$user->can('activite.voir_toutes_directions')) {
            return $user->direction_id === $activite->direction_id;
        }

        // L'activité d'un PAPA verrouillé n'est pas modifiable
        if ($activite->resultatAttendu?->objectifImmediats?->actionPrioritaire?->papa?->estVerrouille()) {
            return false;
        }

        return true;
    }

    public function mettreAJourAvancement(User $user, Activite $activite): bool
    {
        if (!$user->can('activite.mettre_a_jour_avancement')) return false;

        if (!$user->can('activite.voir_toutes_directions')) {
            return $user->direction_id === $activite->direction_id;
        }

        return true;
    }

    public function supprimer(User $user, Activite $activite): bool
    {
        if (!$user->can('activite.supprimer')) return false;
        if (!$user->can('activite.voir_toutes_directions')) {
            return $user->direction_id === $activite->direction_id;
        }
        return true;
    }
}
