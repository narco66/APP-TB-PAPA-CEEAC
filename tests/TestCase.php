<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    /**
     * Crée un rôle avec les permissions données et l'assigne à l'utilisateur.
     */
    protected function creerRoleAvecPermissions(string $role, array $permissions): Role
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $r = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $r->syncPermissions($permissions);

        return $r;
    }
}
