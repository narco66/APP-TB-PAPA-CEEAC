<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use App\Services\Security\UserScopeResolver;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ParametreDroitsController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $this->authorize('parametres.droits.voir');

        $scopeResolver = app(UserScopeResolver::class);

        $roles = Role::withCount('permissions')
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) use ($request, $scopeResolver) {
                $role->users_count = $scopeResolver
                    ->applyToQuery(User::role($role->name), $request->user(), [
                        'departement' => 'departement_id',
                        'direction' => 'direction_id',
                        'service' => 'service_id',
                    ])
                    ->count();

                return $role;
            });

        $totalUsers = $scopeResolver
            ->applyToQuery(User::query(), $request->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->count();
        $totalPermissions = Permission::count();
        $scopeLabel = $request->user()->scopeLabel();

        return view('parametres.droits.index', compact('roles', 'totalUsers', 'totalPermissions', 'scopeLabel'));
    }

    public function matrice(Request $request)
    {
        $this->authorize('parametres.droits.voir');

        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $groupes = $permissions->groupBy(fn ($permission) => explode('.', $permission->name)[0]);
        $scopeLabel = $request->user()->scopeLabel();

        return view('parametres.droits.matrice', compact('roles', 'permissions', 'groupes', 'scopeLabel'));
    }

    public function show(Request $request, Role $role)
    {
        $this->authorize('parametres.droits.voir');

        $role->load('permissions');
        $allPermissions = Permission::orderBy('name')->get();
        $groupes = $allPermissions->groupBy(fn ($permission) => explode('.', $permission->name)[0]);
        $users = app(UserScopeResolver::class)
            ->applyToQuery(User::role($role->name)->with('direction')->orderBy('name'), $request->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->get();
        $scopeLabel = $request->user()->scopeLabel();

        return view('parametres.droits.show', compact('role', 'allPermissions', 'groupes', 'users', 'scopeLabel'));
    }

    public function updateRole(Request $request, Role $role)
    {
        $this->authorize('parametres.droits.modifier');

        $systemRoles = ['super_admin'];
        abort_if(in_array($role->name, $systemRoles, true), 403, 'Ce role systeme ne peut pas etre modifie.');

        $request->validate([
            'permissions' => 'array',
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
            description: "Permissions du role {$role->name} modifiees",
            donneesAvant: ['permissions' => $avant],
            donneesApres: ['permissions' => $apres],
        );

        return back()->with('success', "Permissions du role \"{$role->name}\" mises a jour.");
    }

    public function toggleUser(Request $request, User $user)
    {
        $this->authorize('parametres.droits.modifier');
        abort_if($user->id === $request->user()->id, 403, 'Vous ne pouvez pas vous desactiver vous-meme.');
        abort_if($user->hasRole('super_admin') && ! $request->user()->hasRole('super_admin'), 403, 'Impossible de modifier un super administrateur.');

        $userVisible = app(UserScopeResolver::class)
            ->applyToQuery(User::query()->whereKey($user->id), $request->user(), [
                'departement' => 'departement_id',
                'direction' => 'direction_id',
                'service' => 'service_id',
            ])
            ->exists();

        abort_unless($userVisible, 403);

        $user->update(['actif' => ! $user->actif]);
        $etat = $user->actif ? 'active' : 'desactive';

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
