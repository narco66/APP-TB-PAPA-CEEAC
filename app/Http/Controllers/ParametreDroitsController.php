<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ParametreDroitsController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index()
    {
        $this->authorize('parametres.droits.voir');

        $roles = Role::withCount('users', 'permissions')
            ->orderBy('name')
            ->get();

        $totalUsers       = User::count();
        $totalPermissions = Permission::count();

        return view('parametres.droits.index', compact('roles', 'totalUsers', 'totalPermissions'));
    }

    public function matrice()
    {
        $this->authorize('parametres.droits.voir');

        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        // Group permissions by prefix
        $groupes = $permissions->groupBy(fn($p) => explode('.', $p->name)[0]);

        return view('parametres.droits.matrice', compact('roles', 'permissions', 'groupes'));
    }

    public function show(Role $role)
    {
        $this->authorize('parametres.droits.voir');

        $role->load('permissions');
        $allPermissions = Permission::orderBy('name')->get();
        $groupes = $allPermissions->groupBy(fn($p) => explode('.', $p->name)[0]);
        $users = User::role($role->name)->with('direction')->orderBy('name')->get();

        return view('parametres.droits.show', compact('role', 'allPermissions', 'groupes', 'users'));
    }

    public function updateRole(Request $request, Role $role)
    {
        $this->authorize('parametres.droits.modifier');

        // Protect system roles
        $systemRoles = ['super_admin'];
        abort_if(in_array($role->name, $systemRoles), 403, 'Ce rôle système ne peut pas être modifié.');

        $request->validate([
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $avant = $role->permissions->pluck('name')->sort()->values()->toArray();
        $role->syncPermissions($request->input('permissions', []));
        $apres = $role->fresh()->permissions->pluck('name')->sort()->values()->toArray();

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'droits_role_modifies',
            auditable: null,
            acteur: $request->user(),
            action: 'modifier',
            description: "Permissions du rôle {$role->name} modifiées",
            donneesAvant: ['permissions' => $avant],
            donneesApres: ['permissions' => $apres],
        );

        return back()->with('success', "Permissions du rôle \"{$role->name}\" mises à jour.");
    }

    public function toggleUser(Request $request, User $user)
    {
        $this->authorize('parametres.droits.modifier');
        abort_if($user->id === $request->user()->id, 403, 'Vous ne pouvez pas vous désactiver vous-même.');
        abort_if($user->hasRole('super_admin') && !$request->user()->hasRole('super_admin'), 403, 'Impossible de modifier un super administrateur.');

        $user->update(['actif' => ! $user->actif]);
        $etat = $user->actif ? 'activé' : 'désactivé';

        $this->auditService->enregistrer(
            module: 'parametres',
            eventType: 'utilisateur_' . ($user->actif ? 'actif' : 'inactif'),
            auditable: $user,
            acteur: $request->user(),
            action: 'modifier',
            description: "Utilisateur {$user->name} {$etat}",
        );

        return back()->with('success', "Utilisateur {$user->nomComplet()} {$etat}.");
    }
}
