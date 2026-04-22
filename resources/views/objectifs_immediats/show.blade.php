@extends('layouts.app')
@section('title', $oi->code)
@section('page-title', $oi->code . ' - ' . Str::limit($oi->libelle, 60))

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('actions-prioritaires.show', $oi->actionPrioritaire) }}" class="hover:text-indigo-600">{{ $oi->actionPrioritaire?->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">{{ $oi->code }}</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ onglet: 'resultats' }">

    <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Périmètre de données :</span> {{ $scopeLabel }}
    </div>

    <!-- En-tête -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex-1">
                <!-- Fil d'Ariane métier -->
                <div class="text-xs text-gray-400 mb-2 flex flex-wrap gap-1 items-center">
                    <span>{{ $oi->actionPrioritaire?->papa?->code }}</span>
                    <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    <span>{{ $oi->actionPrioritaire?->code }}</span>
                    <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    <span class="font-medium text-gray-600">{{ $oi->code }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $oi->code }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        bg-{{ $oi->couleurStatut() }}-100 text-{{ $oi->couleurStatut() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $oi->statut)) }}
                    </span>
                </div>
                <h1 class="text-lg font-bold text-gray-800">{{ $oi->libelle }}</h1>
                <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2">
                    @if($oi->responsable)
                    <span><i class="fas fa-user mr-1"></i>{{ $oi->responsable->nomComplet() }}</span>
                    @endif
                    <span><i class="fas fa-layer-group mr-1"></i>{{ $oi->resultatsAttendus->count() }} résultat(s)</span>
                    <span><i class="fas fa-tasks mr-1"></i>{{ $oi->resultatsAttendus->sum(fn($ra) => $ra->activites->count()) }} activité(s)</span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-indigo-700">{{ number_format($oi->taux_atteinte, 0) }}%</div>
                <p class="text-xs text-gray-400">d'atteinte</p>
            </div>
        </div>

        <div class="mt-4 h-3 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 style="width: {{ min(100, $oi->taux_atteinte) }}%;
                        background: {{ $oi->taux_atteinte >= 75 ? '#22c55e' : ($oi->taux_atteinte >= 50 ? '#f59e0b' : '#ef4444') }}">
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('objectifs-immediats.print', $oi) }}"
               target="_blank"
               class="px-4 py-2 bg-white text-gray-700 rounded-lg text-sm border border-gray-200 hover:bg-gray-50 transition">
                <i class="fas fa-print mr-1"></i>Version imprimable
            </a>
        @if($oi->actionPrioritaire?->estEditable())
            @can('papa.modifier')
            <a href="{{ route('objectifs-immediats.edit', $oi) }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                <i class="fas fa-edit mr-1"></i>Modifier
            </a>
            <a href="{{ route('resultats-attendus.create') }}?objectif_immediat_id={{ $oi->id }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-1"></i>Ajouter un résultat attendu
            </a>
            @endcan
        @endif
        </div>
    </div>

    <!-- Onglets -->
    <div class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm border border-gray-100">
        @foreach(['resultats' => 'Résultats attendus (' . $oi->resultatsAttendus->count() . ')', 'indicateurs' => 'Indicateurs (' . $oi->indicateurs->count() . ')'] as $key => $label)
        <button @click="onglet = '{{ $key }}'"
                :class="onglet === '{{ $key }}' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition">{{ $label }}</button>
        @endforeach
    </div>

    <!-- Résultats attendus -->
    <div x-show="onglet === 'resultats'" class="space-y-3">
        @forelse($oi->resultatsAttendus->sortBy('ordre') as $ra)
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-mono text-xs text-gray-400">{{ $ra->code }}</span>
                        <span class="px-2 py-0.5 rounded text-xs
                            bg-{{ $ra->couleurStatut() }}-100 text-{{ $ra->couleurStatut() }}-700">
                            {{ ucfirst(str_replace('_', ' ', $ra->statut)) }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-500">
                            {{ $ra->libelleTypeResultat() }}
                        </span>
                        @if($ra->preuveManquante())
                        <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-600">
                            <i class="fas fa-exclamation-triangle mr-0.5"></i>Preuve manquante
                        </span>
                        @endif
                    </div>
                    <p class="font-medium text-gray-800 text-sm">{{ $ra->libelle }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $ra->activites->count() }} activité(s)
                        @if($ra->responsable) • {{ $ra->responsable->nomComplet() }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-3 ml-4">
                    <div class="text-center w-14">
                        <p class="text-lg font-bold text-indigo-600">{{ number_format($ra->taux_atteinte, 0) }}%</p>
                        <p class="text-xs text-gray-400">Atteinte</p>
                    </div>
                    <a href="{{ route('resultats-attendus.show', $ra) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl p-10 text-center shadow-sm border border-gray-100">
            <i class="fas fa-flag-checkered text-gray-200 text-4xl mb-3"></i>
            <p class="text-gray-400 text-sm">Aucun résultat attendu défini.</p>
            @can('papa.modifier')
            @if($oi->actionPrioritaire?->estEditable())
            <a href="{{ route('resultats-attendus.create') }}?objectif_immediat_id={{ $oi->id }}"
               class="mt-3 inline-block text-indigo-600 hover:underline text-sm">
                Ajouter le premier résultat attendu
            </a>
            @endif
            @endcan
        </div>
        @endforelse
    </div>

    <!-- Indicateurs -->
    <div x-show="onglet === 'indicateurs'">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @forelse($oi->indicateurs as $ind)
            <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <span class="font-mono text-xs text-gray-400 mr-2">{{ $ind->code }}</span>
                    <span class="text-sm text-gray-700">{{ $ind->libelle }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-sm text-indigo-700">{{ number_format($ind->taux_realisation_courant, 0) }}%</span>
                    <a href="{{ route('indicateurs.show', $ind) }}" class="text-indigo-600 text-xs hover:underline">Voir</a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 text-sm">Aucun indicateur rattaché.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
