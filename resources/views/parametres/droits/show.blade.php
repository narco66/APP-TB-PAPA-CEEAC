@extends('layouts.app')
@section('title', 'Rôle — ' . $role->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <nav class="text-xs text-gray-500 mb-6 flex items-center gap-1.5">
        <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <a href="{{ route('parametres.hub') }}" class="hover:underline">Paramètres</a>
        <span>/</span>
        <a href="{{ route('parametres.droits.index') }}" class="hover:underline">Droits & Rôles</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">{{ $role->name }}</span>
    </nav>

    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Rôle : <span class="text-indigo-600">{{ $role->name }}</span></h1>
            <p class="text-sm text-gray-500 mt-1">{{ $role->permissions->count() }} permissions assignées — {{ $users->count() }} utilisateur(s)</p>
        </div>
        <a href="{{ route('parametres.droits.index') }}"
           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
            <i class="fas fa-arrow-left mr-1.5"></i>Retour
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Permissions --}}
        <div class="lg:col-span-2">
            @can('parametres.droits.modifier')
            @unless($role->name === 'super_admin')
            <form action="{{ route('parametres.droits.roles.update', $role) }}" method="POST">
                @csrf @method('PUT')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-key text-amber-400 mr-1.5"></i>Permissions
                        </h2>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition">
                            <i class="fas fa-save mr-1.5"></i>Enregistrer
                        </button>
                    </div>

                    @php $rolePerms = $role->permissions->pluck('name')->toArray(); @endphp

                    @foreach($groupes as $groupe => $perms)
                    <div class="mb-5">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 pb-1 border-b border-gray-100">
                            {{ $groupe }}
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                            @foreach($perms as $perm)
                            <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                       {{ in_array($perm->name, $rolePerms) ? 'checked' : '' }}
                                       class="w-3.5 h-3.5 text-indigo-600 rounded border-gray-300">
                                <span class="text-xs text-gray-700 leading-tight">{{ str_replace($groupe . '.', '', $perm->name) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </form>
            @else
            <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-sm text-red-700">
                <i class="fas fa-shield-halved mr-2"></i>
                Le rôle <strong>super_admin</strong> est protégé. Il dispose automatiquement de <strong>toutes</strong> les permissions du système.
            </div>
            @endunless
            @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">
                    <i class="fas fa-key text-amber-400 mr-1.5"></i>Permissions assignées
                </h2>
                @php $rolePerms = $role->permissions->pluck('name')->toArray(); @endphp
                @foreach($groupes as $groupe => $perms)
                <div class="mb-4">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ $groupe }}</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($perms as $perm)
                        @if(in_array($perm->name, $rolePerms))
                        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-xs font-mono">{{ $perm->name }}</span>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endcan
        </div>

        {{-- Utilisateurs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">
                <i class="fas fa-users text-indigo-400 mr-1.5"></i>Utilisateurs
                <span class="text-gray-400 font-normal text-xs ml-1">({{ $users->count() }})</span>
            </h2>
            @forelse($users as $user)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">{{ $user->nomComplet() }}</p>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
                <span class="text-xs {{ $user->actif ? 'text-green-500' : 'text-gray-400' }}">
                    <i class="fas fa-circle text-xs"></i>
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Aucun utilisateur</p>
            @endforelse
        </div>

    </div>
</div>
@endsection
