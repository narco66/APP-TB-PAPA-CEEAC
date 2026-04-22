@extends('layouts.app')
@section('title', $ap->code)
@section('page-title', $ap->code . ' - ' . Str::limit($ap->libelle, 60))

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('papas.show', $ap->papa) }}" class="hover:text-indigo-600">{{ $ap->papa?->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('actions-prioritaires.index') }}?papa_id={{ $ap->papa_id }}" class="hover:text-indigo-600">Actions prioritaires</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">{{ $ap->code }}</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ onglet: 'objectifs' }">

    <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Périmètre de données :</span> {{ $scopeLabel }}
    </div>

    <!-- En-tête -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $ap->code }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        bg-{{ $ap->couleurStatut() }}-100 text-{{ $ap->couleurStatut() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $ap->statut)) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        @if($ap->qualification === 'technique') bg-blue-50 text-blue-700 border border-blue-200
                        @elseif($ap->qualification === 'appui') bg-purple-50 text-purple-700 border border-purple-200
                        @else bg-gray-50 text-gray-600 border border-gray-200 @endif">
                        <i class="fas fa-tag mr-1"></i>{{ ucfirst($ap->qualification) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs
                        bg-{{ $ap->couleurPriorite() }}-100 text-{{ $ap->couleurPriorite() }}-700">
                        Priorité {{ ucfirst($ap->priorite) }}
                    </span>
                </div>
                <h1 class="text-lg font-bold text-gray-800">{{ $ap->libelle }}</h1>
                <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2">
                    <span><i class="fas fa-bookmark mr-1"></i>PAPA : {{ $ap->papa?->code }}</span>
                    <span><i class="fas fa-sitemap mr-1"></i>{{ $ap->departement?->libelle ?? 'Tous départements' }}</span>
                    <span><i class="fas fa-sort-numeric-up mr-1"></i>Ordre : {{ $ap->ordre ?? '-' }}</span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-indigo-700">{{ number_format($ap->taux_realisation, 0) }}%</div>
                <p class="text-xs text-gray-400">de réalisation</p>
            </div>
        </div>

        <!-- Barre avancement -->
        <div class="mt-4 h-3 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 style="width: {{ min(100, $ap->taux_realisation) }}%;
                        background: {{ $ap->taux_realisation >= 75 ? '#22c55e' : ($ap->taux_realisation >= 50 ? '#f59e0b' : '#ef4444') }}">
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('actions-prioritaires.print', $ap) }}"
               target="_blank"
               class="px-4 py-2 bg-white text-gray-700 rounded-lg text-sm border border-gray-200 hover:bg-gray-50 transition">
                <i class="fas fa-print mr-1"></i>Version imprimable
            </a>
        @if($ap->estEditable())
            @can('papa.modifier')
            <a href="{{ route('actions-prioritaires.edit', $ap) }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                <i class="fas fa-edit mr-1"></i>Modifier
            </a>
            <a href="{{ route('objectifs-immediats.create') }}?action_prioritaire_id={{ $ap->id }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-1"></i>Ajouter un OI
            </a>
            @endcan
        @endif
        </div>
    </div>

    <!-- Description -->
    @if($ap->description)
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-700 mb-2">Description</h3>
        <p class="text-sm text-gray-600">{{ $ap->description }}</p>
    </div>
    @endif

    <!-- Onglets -->
    <div class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm border border-gray-100">
        @foreach(['objectifs' => 'Objectifs immédiats (' . $ap->objectifsImmediat->count() . ')', 'indicateurs' => 'Indicateurs (' . $ap->indicateurs->count() . ')'] as $key => $label)
        <button @click="onglet = '{{ $key }}'"
                :class="onglet === '{{ $key }}' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition">{{ $label }}</button>
        @endforeach
    </div>

    <!-- Objectifs immédiats -->
    <div x-show="onglet === 'objectifs'" class="space-y-3">
        @forelse($ap->objectifsImmediat->sortBy('ordre') as $oi)
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-mono text-xs text-gray-400">{{ $oi->code }}</span>
                        <span class="px-2 py-0.5 rounded text-xs
                            bg-{{ $oi->couleurStatut() }}-100 text-{{ $oi->couleurStatut() }}-700">
                            {{ ucfirst(str_replace('_', ' ', $oi->statut)) }}
                        </span>
                    </div>
                    <p class="font-medium text-gray-800 text-sm">{{ $oi->libelle }}</p>
                    <div class="flex gap-4 text-xs text-gray-400 mt-1">
                        <span>{{ $oi->resultatsAttendus->count() }} résultat(s) attendu(s)</span>
                        <span>{{ $oi->resultatsAttendus->sum(fn($ra) => $ra->activites->count()) }} activité(s)</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 ml-4">
                    <div class="text-center w-16">
                        <p class="text-xl font-bold text-indigo-600">{{ number_format($oi->taux_atteinte, 0) }}%</p>
                        <p class="text-xs text-gray-400">Atteinte</p>
                    </div>
                    <a href="{{ route('objectifs-immediats.show', $oi) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <!-- Mini barre -->
            <div class="mt-2 h-1.5 bg-gray-100 rounded-full">
                <div class="h-full rounded-full"
                     style="width: {{ min(100, $oi->taux_atteinte) }}%;
                            background: {{ $oi->taux_atteinte >= 75 ? '#22c55e' : ($oi->taux_atteinte >= 50 ? '#f59e0b' : '#ef4444') }}">
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl p-10 text-center shadow-sm border border-gray-100">
            <i class="fas fa-project-diagram text-gray-200 text-4xl mb-3"></i>
            <p class="text-gray-400 text-sm">Aucun objectif immédiat défini.</p>
            @can('papa.modifier')
            @if($ap->estEditable())
            <a href="{{ route('objectifs-immediats.create') }}?action_prioritaire_id={{ $ap->id }}"
               class="mt-3 inline-block text-indigo-600 hover:underline text-sm">
                Ajouter le premier OI
            </a>
            @endif
            @endcan
        </div>
        @endforelse
    </div>

    <!-- Indicateurs -->
    <div x-show="onglet === 'indicateurs'">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @forelse($ap->indicateurs as $ind)
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
