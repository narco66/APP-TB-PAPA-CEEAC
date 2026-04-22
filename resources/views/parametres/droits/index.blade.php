@extends('layouts.app')
@section('title', 'Droits & Rôles')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <nav class="text-xs text-gray-500 mb-6 flex items-center gap-1.5">
        <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <a href="{{ route('parametres.hub') }}" class="hover:underline">Paramètres</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Droits & Rôles</span>
    </nav>

    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="mb-5 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <i class="fas fa-shield-halved mr-2"></i>{{ $scopeLabel }}
    </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Droits & Rôles</h1>
            <p class="text-sm text-gray-500 mt-1">Gestion des rôles et permissions — {{ $roles->count() }} rôles, {{ $totalPermissions }} permissions, {{ $totalUsers }} utilisateurs</p>
        </div>
        <a href="{{ route('parametres.droits.matrice') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg text-sm font-medium transition">
            <i class="fas fa-table"></i> Matrice complète
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ $roles->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Rôles définis</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-amber-600">{{ $totalPermissions }}</div>
            <div class="text-xs text-gray-500 mt-1">Permissions totales</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $totalUsers }}</div>
            <div class="text-xs text-gray-500 mt-1">Utilisateurs actifs</div>
        </div>
    </div>

    {{-- Tableau des rôles --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Rôle</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilisateurs</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Permissions</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            @foreach($roles as $role)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2.5">
                        @php
                            $colors = [
                                'super_admin'             => 'bg-red-100 text-red-700',
                                'president'               => 'bg-purple-100 text-purple-700',
                                'vice_president'          => 'bg-indigo-100 text-indigo-700',
                                'secretaire_general'      => 'bg-blue-100 text-blue-700',
                                'administrateur_fonctionnel' => 'bg-amber-100 text-amber-700',
                                'auditeur_interne'        => 'bg-teal-100 text-teal-700',
                            ];
                            $colorClass = $colors[$role->name] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                            {{ $role->name }}
                        </span>
                        @if($role->name === 'super_admin')
                        <span class="text-xs text-red-400 italic">système protégé</span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="font-semibold text-gray-700">{{ $role->users_count }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="font-semibold text-indigo-600">{{ $role->permissions_count }}</span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('parametres.droits.roles.show', $role) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium transition">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        @can('parametres.droits.modifier')
                        @unless($role->name === 'super_admin')
                        <a href="{{ route('parametres.droits.roles.show', $role) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        @endunless
                        @endcan
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
        <i class="fas fa-info-circle mr-1.5"></i>
        Le rôle <strong>super_admin</strong> est protégé et dispose automatiquement de toutes les permissions.
        La modification de ses droits n'est pas possible via cette interface.
    </div>
</div>
@endsection
