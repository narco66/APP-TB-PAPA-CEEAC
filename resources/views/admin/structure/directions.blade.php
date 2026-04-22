@extends('layouts.app')

@section('title', 'Directions')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Directions</h1>
            <p class="text-sm text-gray-500 mt-1">Directions et unités de la Commission</p>
        </div>
        <a href="{{ route('admin.structure.directions.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            <i class="fas fa-plus"></i> Nouvelle direction
        </a>
    </div>

    <div class="mb-4 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <i class="fas fa-shield-halved mr-2"></i>{{ $scopeLabel }}
    </div>

    {{-- Sous-navigation --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200">
        <a href="{{ route('admin.structure.departements') }}"
           class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
            <i class="fas fa-sitemap mr-1"></i> Départements
        </a>
        <a href="{{ route('admin.structure.directions') }}"
           class="px-4 py-2 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600">
            <i class="fas fa-building mr-1"></i> Directions
        </a>
        <a href="{{ route('admin.structure.services') }}"
           class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
            <i class="fas fa-layer-group mr-1"></i> Services
        </a>
    </div>

    {{-- Filtre --}}
    <form method="GET" class="mb-4 flex items-center gap-3">
        <select name="departement_id" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
            <option value="">Tous les départements</option>
            @foreach($departements as $dep)
                <option value="{{ $dep->id }}" {{ request('departement_id') == $dep->id ? 'selected' : '' }}>
                    {{ $dep->libelle_court ?? $dep->code }} — {{ $dep->libelle }}
                </option>
            @endforeach
        </select>
        @if(request('departement_id'))
            <a href="{{ route('admin.structure.directions') }}" class="text-xs text-gray-400 hover:text-red-500">
                <i class="fas fa-times"></i> Réinitialiser
            </a>
        @endif
    </form>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Direction</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Département</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Services</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Activités</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Agents</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($directions as $dir)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold text-indigo-700 text-xs">{{ $dir->code }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $dir->libelle }}</div>
                        @if($dir->libelle_court && $dir->libelle_court !== $dir->libelle)
                            <div class="text-xs text-gray-400">{{ $dir->libelle_court }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs">
                        {{ $dir->departement?->libelle_court ?? $dir->departement?->code ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($dir->type_direction === 'technique')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Technique</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">Appui</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $dir->services_count }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $dir->activites_count }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $dir->users_count }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($dir->actif)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Actif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Inactif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.structure.directions.edit', $dir) }}"
                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            <i class="fas fa-pencil-alt mr-1"></i>Modifier
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-400 text-sm">Aucune direction.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
