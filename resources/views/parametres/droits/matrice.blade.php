@extends('layouts.app')
@section('title', 'Matrice des droits')

@section('content')
<div class="max-w-full px-4 sm:px-6 lg:px-8 py-8">

    <nav class="text-xs text-gray-500 mb-6 flex items-center gap-1.5">
        <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <a href="{{ route('parametres.hub') }}" class="hover:underline">Paramètres</a>
        <span>/</span>
        <a href="{{ route('parametres.droits.index') }}" class="hover:underline">Droits & Rôles</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Matrice des droits</span>
    </nav>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Matrice des droits</h1>
            <p class="text-sm text-gray-500 mt-1">Vue consolidée rôles × permissions — lecture seule</p>
        </div>
        <a href="{{ route('parametres.droits.index') }}"
           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
            <i class="fas fa-arrow-left mr-1.5"></i>Retour
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="text-xs border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="sticky left-0 z-10 bg-gray-50 text-left px-4 py-3 font-semibold text-gray-600 border-r border-gray-200 min-w-[220px]">
                        Permission
                    </th>
                    @foreach($roles as $role)
                    <th class="px-2 py-3 text-center font-medium text-gray-600 min-w-[90px] border-r border-gray-100 last:border-0">
                        <div class="truncate max-w-[80px] mx-auto" title="{{ $role->name }}">
                            {{ $role->name }}
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($groupes as $groupe => $perms)
                {{-- Section header --}}
                <tr class="bg-indigo-50 border-y border-indigo-100">
                    <td colspan="{{ $roles->count() + 1 }}"
                        class="sticky left-0 px-4 py-2 font-bold text-indigo-700 uppercase tracking-wider text-xs bg-indigo-50">
                        <i class="fas fa-folder text-indigo-400 mr-1.5"></i>{{ $groupe }}
                    </td>
                </tr>
                @foreach($perms as $perm)
                <tr class="hover:bg-gray-50 border-b border-gray-50 transition">
                    <td class="sticky left-0 z-10 bg-white hover:bg-gray-50 px-4 py-2 font-mono text-gray-700 border-r border-gray-100 whitespace-nowrap">
                        {{ str_replace($groupe . '.', '', $perm->name) }}
                    </td>
                    @foreach($roles as $role)
                    @php
                        $hasPerm = $role->name === 'super_admin'
                            || $role->permissions->contains('name', $perm->name);
                    @endphp
                    <td class="px-2 py-2 text-center border-r border-gray-50 last:border-0">
                        @if($hasPerm)
                        <span class="{{ $role->name === 'super_admin' ? 'text-red-500' : 'text-green-500' }}">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        @else
                        <span class="text-gray-200">
                            <i class="fas fa-times"></i>
                        </span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center gap-6 text-xs text-gray-500">
        <span><i class="fas fa-check-circle text-red-500 mr-1"></i>super_admin (toutes permissions)</span>
        <span><i class="fas fa-check-circle text-green-500 mr-1"></i>Permission assignée</span>
        <span><i class="fas fa-times text-gray-300 mr-1"></i>Non assignée</span>
    </div>
</div>
@endsection
