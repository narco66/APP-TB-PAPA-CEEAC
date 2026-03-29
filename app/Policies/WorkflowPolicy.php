<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkflowInstance;

class WorkflowPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('workflow.voir');
    }

    public function view(User $user, WorkflowInstance $instance): bool
    {
        return $user->can('workflow.voir');
    }

    public function approuver(User $user, WorkflowInstance $instance): bool
    {
        if (! $user->can('workflow.approuver')) {
            return false;
        }

        return in_array($instance->statut, ['en_cours'], true) && $instance->etapeCourante !== null;
    }

    public function rejeter(User $user, WorkflowInstance $instance): bool
    {
        if (! $user->can('workflow.rejeter')) {
            return false;
        }

        return in_array($instance->statut, ['en_cours'], true) && $instance->etapeCourante !== null;
    }

    public function commenter(User $user, WorkflowInstance $instance): bool
    {
        return $user->can('workflow.commenter');
    }

    public function demarrer(User $user): bool
    {
        return $user->can('workflow.demarrer');
    }
}
