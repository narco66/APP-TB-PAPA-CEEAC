@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Utilisateurs</h1>
        <p class="text-sm text-gray-500 mt-1">Gestion des comptes et des rôles</p>
    </div>
    <a href="{{ route('admin.utilisateurs.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
        <i class="fas fa-plus"></i> Nouvel utilisateur
    </a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<!-- Filtres -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom, prénom, email, matricule..."
                   class="border border-gray-300 rounded-lg text-sm px-3 py-2 w-64 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Rôle</label>
            <select name="role" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tous les rôles</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Statut</label>
            <select name="actif" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tous</option>
                <option value="1" {{ request('actif') === '1' ? 'selected' : '' }}>Actifs</option>
                <option value="0" {{ request('actif') === '0' ? 'selected' : '' }}>Désactivés</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-filter mr-1"></i> Filtrer
        </button>
        @if(request()->hasAny(['search','role','actif']))
        <a href="{{ route('admin.utilisateurs.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
            <i class="fas fa-times mr-1"></i> Réinitialiser
        </a>
        @endif
    </form>
</div>

<!-- Tableau -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($users->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <i class="fas fa-users text-4xl mb-3"></i>
        <p>Aucun utilisateur trouvé.</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="text-left px-4 py-3">Utilisateur</th>
                <th class="text-left px-4 py-3">Email</th>
                <th class="text-left px-4 py-3">Matricule</th>
                <th class="text-left px-4 py-3">Direction</th>
                <th class="text-left px-4 py-3">Rôle</th>
                <th class="text-center px-4 py-3">Statut</th>
                <th class="text-left px-4 py-3">Connexion</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($users as $user)
            <tr class="{{ $user->trashed() ? 'opacity-50 bg-gray-50' : 'hover:bg-gray-50' }}">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $user->nomComplet() }}</div>
                            @if($user->titre || $user->fonction)
                            <div class="text-xs text-gray-500">{{ $user->titre }} {{ $user->fonction }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $user->matricule ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $user->direction?->code ?? '—' }}</td>
                <td class="px-4 py-3">
                    @foreach($user->roles as $role)
                    <span class="inline-block bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded font-medium">
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </span>
                    @endforeach
                </td>
                <td class="px-4 py-3 text-center">
                    @if($user->trashed())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">Archivé</span>
                    @elseif($user->actif)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Actif</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Désactivé</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-xs text-gray-500">
                    {{ $user->derniere_connexion?->diffForHumans() ?? 'Jamais' }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        @if($user->trashed())
                        <form method="POST" action="{{ route('admin.utilisateurs.restore', $user->id) }}">
                            @csrf
                            <button type="submit" class="text-xs text-green-600 hover:underline">Restaurer</button>
                        </form>
                        @else
                        <a href="{{ route('admin.utilisateurs.edit', $user) }}" class="text-gray-400 hover:text-indigo-600">
                            <i class="fas fa-pen"></i>
                        </a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.utilisateurs.toggle-actif', $user) }}">
                            @csrf
                            <button type="submit" class="text-gray-400 {{ $user->actif ? 'hover:text-amber-600' : 'hover:text-green-600' }}"
                                    title="{{ $user->actif ? 'Désactiver' : 'Activer' }}">
                                <i class="fas {{ $user->actif ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.utilisateurs.destroy', $user) }}"
                              onsubmit="return confirm('Archiver cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
