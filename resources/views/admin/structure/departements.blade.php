@extends('layouts.app')

@section('title', 'Départements')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Départements</h1>
            <p class="text-sm text-gray-500 mt-1">Structure organisationnelle de la Commission CEEAC</p>
        </div>
        <a href="{{ route('admin.structure.departements.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            <i class="fas fa-plus"></i> Nouveau département
        </a>
    </div>

    {{-- Fil d'Ariane --}}
    <nav class="text-xs text-gray-500 mb-4 flex items-center gap-1">
        <a href="{{ route('dashboard') }}" class="hover:underline">Tableau de bord</a>
        <i class="fas fa-chevron-right text-gray-300"></i>
        <span>Structure organisationnelle</span>
        <i class="fas fa-chevron-right text-gray-300"></i>
        <span class="text-gray-700 font-medium">Départements</span>
    </nav>

    {{-- Sous-navigation structure --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200">
        <a href="{{ route('admin.structure.departements') }}"
           class="px-4 py-2 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600">
            <i class="fas fa-sitemap mr-1"></i> Départements
        </a>
        <a href="{{ route('admin.structure.directions') }}"
           class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
            <i class="fas fa-building mr-1"></i> Directions
        </a>
        <a href="{{ route('admin.structure.services') }}"
           class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
            <i class="fas fa-layer-group mr-1"></i> Services
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Département</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Directions</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">APs rattachés</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($departements as $dep)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold text-indigo-700 text-xs">{{ $dep->code }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $dep->libelle }}</div>
                        @if($dep->libelle_court && $dep->libelle_court !== $dep->libelle)
                            <div class="text-xs text-gray-400">{{ $dep->libelle_court }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($dep->type === 'technique')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Technique</span>
                        @elseif($dep->type === 'appui')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">Appui</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Transversal</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $dep->directions_count }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $dep->actions_prioritaires_count }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($dep->actif)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Actif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Inactif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.structure.departements.edit', $dep) }}"
                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            <i class="fas fa-pencil-alt mr-1"></i>Modifier
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Aucun département enregistré.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
