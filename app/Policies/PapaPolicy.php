<?php

namespace App\Policies;

use App\Models\Papa;
use App\Models\User;

class PapaPolicy
{
    public function voir(User $user, Papa $papa): bool
    {
        if ($papa->statut === 'archive') {
            return $user->can('papa.voir_archive');
        }
        return $user->can('papa.voir');
    }

    public function creer(User $user): bool
    {
        return $user->can('papa.creer');
    }

    public function modifier(User $user, Papa $papa): bool
    {
        if ($papa->estVerrouille()) return false;
        return $user->can('papa.modifier');
    }

    public function supprimer(User $user, Papa $papa): bool
    {
        if ($papa->estVerrouille()) return false;
        return $user->can('papa.supprimer');
    }

    public function soumettre(User $user, Papa $papa): bool
    {
        if ($papa->statut !== 'brouillon') return false;
        return $user->can('papa.soumettre') || $user->can('papa.modifier');
    }

    public function valider(User $user, Papa $papa): bool
    {
        if (!$papa->peutEtreValide()) return false;
        return $user->can('papa.valider');
    }

    public function rejeter(User $user, Papa $papa): bool
    {
        if ($papa->statut !== 'soumis') return false;
        return $user->can('papa.rejeter') || $user->can('papa.valider');
    }

    public function archiver(User $user, Papa $papa): bool
    {
        if ($papa->estArchive()) return false;
        return $user->can('papa.archiver');
    }

    public function cloner(User $user, Papa $papa): bool
    {
        return $user->can('papa.cloner') || $user->can('papa.creer');
    }

    public function verrouiller(User $user, Papa $papa): bool
    {
        return $user->can('papa.verrouiller');
    }
}
