<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $query = User::with(['direction', 'roles'])
            ->withTrashed()
            ->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('prenom', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('matricule', 'like', "%{$s}%")
            );
        }
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        if ($request->filled('actif')) {
            $query->where('actif', (bool)$request->actif);
        }

        $users      = $query->paginate(25);
        $roles      = Role::orderBy('name')->get();
        $directions = Direction::actif()->orderBy('libelle')->get(['id', 'code', 'libelle']);

        return view('admin.utilisateurs.index', compact('users', 'roles', 'directions'));
    }

    public function create()
    {
        $this->authorize('admin.utilisateurs');

        $roles      = Role::orderBy('name')->get();
        $directions = Direction::actif()->orderBy('libelle')->get(['id', 'code', 'libelle']);

        return view('admin.utilisateurs.create', compact('roles', 'directions'));
    }

    public function store(Request $request)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'prenom'       => 'nullable|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'matricule'    => 'nullable|string|max:50|unique:users,matricule',
            'titre'        => 'nullable|string|max:100',
            'fonction'     => 'nullable|string|max:200',
            'telephone'    => 'nullable|string|max:30',
            'direction_id' => 'nullable|exists:directions,id',
            'actif'        => 'boolean',
            'role'         => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'         => $data['name'],
            'prenom'       => $data['prenom'] ?? null,
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'matricule'    => $data['matricule'] ?? null,
            'titre'        => $data['titre'] ?? null,
            'fonction'     => $data['fonction'] ?? null,
            'telephone'    => $data['telephone'] ?? null,
            'direction_id' => $data['direction_id'] ?? null,
            'actif'        => $data['actif'] ?? true,
        ]);

        $user->assignRole($data['role']);

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', "Utilisateur {$user->nomComplet()} créé.");
    }

    public function edit(User $user)
    {
        $this->authorize('admin.utilisateurs');

        $roles      = Role::orderBy('name')->get();
        $directions = Direction::actif()->orderBy('libelle')->get(['id', 'code', 'libelle']);

        return view('admin.utilisateurs.edit', compact('user', 'roles', 'directions'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('admin.utilisateurs');

        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'prenom'       => 'nullable|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'password'     => 'nullable|string|min:8|confirmed',
            'matricule'    => 'nullable|string|max:50|unique:users,matricule,' . $user->id,
            'titre'        => 'nullable|string|max:100',
            'fonction'     => 'nullable|string|max:200',
            'telephone'    => 'nullable|string|max:30',
            'direction_id' => 'nullable|exists:directions,id',
            'actif'        => 'boolean',
            'role'         => 'required|exists:roles,name',
        ]);

        $updateData = [
            'name'         => $data['name'],
            'prenom'       => $data['prenom'] ?? null,
            'email'        => $data['email'],
            'matricule'    => $data['matricule'] ?? null,
            'titre'        => $data['titre'] ?? null,
            'fonction'     => $data['fonction'] ?? null,
            'telephone'    => $data['telephone'] ?? null,
            'direction_id' => $data['direction_id'] ?? null,
            'actif'        => $data['actif'] ?? $user->actif,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', "Utilisateur {$user->nomComplet()} mis à jour.");
    }

    public function toggleActif(User $user)
    {
        $this->authorize('admin.utilisateurs');
        abort_if($user->id === auth()->id(), 403, 'Vous ne pouvez pas désactiver votre propre compte.');

        $user->update(['actif' => !$user->actif]);

        $etat = $user->actif ? 'activé' : 'désactivé';
        return back()->with('success', "Compte {$etat}.");
    }

    public function destroy(User $user)
    {
        $this->authorize('admin.utilisateurs');
        abort_if($user->id === auth()->id(), 403, 'Vous ne pouvez pas supprimer votre propre compte.');

        $user->delete();

        return back()->with('success', 'Utilisateur archivé.');
    }

    public function restore(User $user)
    {
        $this->authorize('admin.utilisateurs');

        $user->restore();

        return back()->with('success', 'Utilisateur restauré.');
    }
}
