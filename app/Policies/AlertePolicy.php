<?php

namespace App\Policies;

use App\Models\Alerte;
use App\Models\User;

class AlertePolicy
{
    public function view(User $user, Alerte $alerte): bool
    {
        return $user->can('alerte.voir') && $alerte->canBeAccessedBy($user);
    }

    public function traiter(User $user, Alerte $alerte): bool
    {
        return $user->can('alerte.traiter') && $alerte->canBeAccessedBy($user);
    }

    public function escalader(User $user, Alerte $alerte): bool
    {
        return $user->can('alerte.escalader') && $alerte->canBeAccessedBy($user);
    }
}
