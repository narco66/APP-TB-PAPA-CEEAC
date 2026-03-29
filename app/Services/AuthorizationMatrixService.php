<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkflowStep;
use Illuminate\Database\Eloquent\Collection;

class AuthorizationMatrixService
{
    public function userCanHandleStep(User $user, WorkflowStep $step): bool
    {
        if ($step->role_requis && ! $user->hasRole($step->role_requis)) {
            return false;
        }

        if ($step->permission_requise && ! $user->checkPermissionTo($step->permission_requise, 'web')) {
            return false;
        }

        return true;
    }

    public function eligibleUsersForStep(WorkflowStep $step): Collection
    {
        $query = User::query()->actif();

        if ($step->role_requis) {
            $query->role($step->role_requis);
        }

        $users = $query->get();

        if (! $step->permission_requise) {
            return $users;
        }

        return $users->filter(
            fn (User $user) => $user->checkPermissionTo($step->permission_requise, 'web')
        )->values();
    }
}
