<?php

namespace App\Models\Concerns;

use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Database\Eloquent\Builder;

trait AppliesVisibilityScope
{
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return app(UserScopeResolver::class)->applyToQuery($query, $user, $this->visibilityScopeColumns());
    }

    public function isVisibleTo(User $user): bool
    {
        $columns = $this->visibilityScopeColumns();

        return app(UserScopeResolver::class)->canAccessAttributes(
            $user,
            $columns['departement'] ? $this->{$columns['departement']} : null,
            $columns['direction'] ? $this->{$columns['direction']} : null,
            $columns['service'] ? $this->{$columns['service']} : null,
        );
    }

    abstract protected function visibilityScopeColumns(): array;
}
