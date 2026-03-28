<?php

namespace App\Policies;

use App\Models\Indicateur;
use App\Models\User;

class IndicateurPolicy
{
    public function voir(User $user): bool
    {
        return $user->can('indicateur.voir');
    }

    public function creer(User $user): bool
    {
        return $user->can('indicateur.creer');
    }

    public function modifier(User $user, Indicateur $indicateur): bool
    {
        if (!$user->can('indicateur.modifier')) return false;

        // Périmètre direction : un directeur ne modifie que ses indicateurs
        if ($user->hasRole(['directeur_technique', 'directeur_appui', 'chef_service', 'point_focal'])) {
            return $user->direction_id === $indicateur->direction_id;
        }

        return true;
    }

    public function saisirValeur(User $user, Indicateur $indicateur): bool
    {
        if (!$user->can('indicateur.saisir_valeur')) return false;
        if (!$user->can('activite.voir_toutes_directions')) {
            return $user->direction_id === $indicateur->direction_id;
        }
        return true;
    }

    public function validerValeur(User $user): bool
    {
        return $user->can('indicateur.valider_valeur');
    }
}
