<?php

namespace App\Policies;

use App\Models\Decision;
use App\Models\User;

class DecisionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('decision.voir');
    }

    public function view(User $user, Decision $decision): bool
    {
        return $user->can('decision.voir');
    }

    public function create(User $user): bool
    {
        return $user->can('decision.creer');
    }

    public function valider(User $user, Decision $decision): bool
    {
        if (! $user->can('decision.valider')) {
            return false;
        }

        return in_array($decision->statut, ['brouillon', 'soumise'], true);
    }

    public function executer(User $user, Decision $decision): bool
    {
        if (! $user->can('decision.executer')) {
            return false;
        }

        return $decision->statut === 'validee';
    }

    public function rattacherDocument(User $user, Decision $decision): bool
    {
        return $user->can('decision.creer') && in_array($decision->statut, ['brouillon', 'soumise', 'validee'], true);
    }
}
