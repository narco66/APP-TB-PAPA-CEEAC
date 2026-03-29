<?php

namespace App\Policies;

use App\Models\Rapport;
use App\Models\User;

class RapportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('rapport.voir') || $user->can('papa.voir');
    }

    public function view(User $user, Rapport $rapport): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        return $rapport->canBeAccessedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->can('rapport.creer');
    }

    public function export(User $user, Rapport $rapport): bool
    {
        return $user->can('rapport.exporter') && $this->view($user, $rapport);
    }

    public function valider(User $user, Rapport $rapport): bool
    {
        return $user->can('rapport.valider') && $rapport->statut === 'brouillon';
    }

    public function publier(User $user, Rapport $rapport): bool
    {
        return $user->can('rapport.publier') && $rapport->statut === 'valide';
    }
}
