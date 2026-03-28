@extends('layouts.app')
@section('title', 'Indicateurs de performance')
@section('page-title', 'Indicateurs de performance')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Indicateurs</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $indicateurs->total() }} indicateur(s)</p>
        @can('indicateur.creer')
        <a href="{{ route('indicateurs.create') }}"
           class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fas fa-plus"></i>
            <span>Nouvel indicateur</span>
        </a>
        @endcan
    </div>

    <!-- Filtres -->
    <form method="GET" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
            <select name="type" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous les types</option>
                <option value="quantitatif" {{ request('type') === 'quantitatif' ? 'selected' : '' }}>Quantitatif</option>
                <option value="qualitatif"  {{ request('type') === 'qualitatif'  ? 'selected' : '' }}>Qualitatif</option>
                <option value="binaire"     {{ request('type') === 'binaire'     ? 'selected' : '' }}>Binaire (Oui/Non)</option>
                <option value="taux"        {{ request('type') === 'taux'        ? 'selected' : '' }}>Taux (%)</option>
            </select>
        </div>
        <button type="submit"
                class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>
        @if(request()->anyFilled(['type']))
        <a href="{{ route('indicateurs.index') }}" class="px-4 py-1.5 text-gray-500 text-sm hover:text-gray-700">
            <i class="fas fa-times mr-1"></i>Réinitialiser
        </a>
        @endif
    </form>

    <!-- Liste -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($indicateurs as $ind)
        @php
            $niveau = $ind->niveauAlerte();
            $alertColors = ['rouge' => 'red', 'orange' => 'orange', 'vert' => 'green', 'neutre' => 'gray'];
            $alertColor = $alertColors[$niveau] ?? 'gray';
        @endphp
        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="font-mono text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $ind->code }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            @if($niveau === 'rouge') bg-red-100 text-red-700
                            @elseif($niveau === 'orange') bg-orange-100 text-orange-700
                            @elseif($niveau === 'vert') bg-green-100 text-green-700
                            @else bg-gray-100 text-gray-600 @endif">
                            @if($niveau !== 'neutre') <i class="fas fa-circle text-xs mr-0.5"></i> @endif
                            {{ ucfirst($niveau) }}
                        </span>
                        <span class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $ind->type_indicateur) }}</span>
                    </div>
                    <p class="font-medium text-sm text-gray-800 truncate">{{ $ind->libelle }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <i class="fas fa-building mr-1"></i>{{ $ind->direction?->sigle ?? '—' }} •
                        Resp. : {{ $ind->responsable?->nomComplet() ?? '—' }} •
                        Fréquence : {{ $ind->frequence_collecte ?? '—' }}
                    </p>
                </div>
                <div class="flex items-center space-x-6">
                    <!-- Taux courant -->
                    <div class="text-center w-20">
                        <p class="text-2xl font-bold
                            @if($niveau === 'rouge') text-red-600
                            @elseif($niveau === 'orange') text-orange-500
                            @elseif($niveau === 'vert') text-green-600
                            @else text-indigo-700 @endif">
                            {{ number_format($ind->taux_realisation_courant, 0) }}%
                        </p>
                        <p class="text-xs text-gray-400">Réalisation</p>
                    </div>
                    <!-- Tendance -->
                    <div class="text-center w-10">
                        <span class="text-xl font-bold text-{{ $ind->couleurTendance() }}-600">
                            {{ $ind->iconesTendance() }}
                        </span>
                        <p class="text-xs text-gray-400">Tendance</p>
                    </div>
                    <!-- Cible -->
                    <div class="text-center w-24 hidden md:block">
                        <p class="font-semibold text-sm text-gray-700">
                            {{ number_format($ind->valeur_cible_annuelle, 0) }}
                            @if($ind->unite_mesure) <span class="text-xs font-normal text-gray-400">{{ $ind->unite_mesure }}</span> @endif
                        </p>
                        <p class="text-xs text-gray-400">Cible annuelle</p>
                    </div>
                    <!-- Barre -->
                    <div class="w-24 hidden lg:block">
                        <div class="h-2 bg-gray-100 rounded-full">
                            <div class="h-full rounded-full transition-all"
                                 style="width: {{ min(100, $ind->taux_realisation_courant) }}%;
                                        background: {{ $niveau === 'rouge' ? '#ef4444' : ($niveau === 'orange' ? '#f97316' : ($niveau === 'vert' ? '#22c55e' : '#6366f1')) }}">
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('indicateurs.show', $ind) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        Détail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-chart-line text-gray-200 text-5xl mb-4"></i>
            <p class="text-gray-400">Aucun indicateur trouvé.</p>
            @can('indicateur.creer')
            <a href="{{ route('indicateurs.create') }}" class="mt-4 inline-block text-indigo-600 hover:underline text-sm">
                Créer le premier indicateur
            </a>
            @endcan
        </div>
        @endforelse
    </div>

    {{ $indicateurs->links() }}
</div>
@endsection
