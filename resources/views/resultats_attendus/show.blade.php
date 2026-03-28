@extends('layouts.app')
@section('title', $ra->code)
@section('page-title', $ra->code . ' — ' . Str::limit($ra->libelle, 60))

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('objectifs-immediats.show', $ra->objectifImmediats) }}" class="hover:text-indigo-600">{{ $ra->objectifImmediats?->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">{{ $ra->code }}</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ onglet: 'activites' }">

    <!-- En-tête -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex-1">
                <!-- Fil d'Ariane métier -->
                <div class="text-xs text-gray-400 mb-2 flex flex-wrap gap-1 items-center">
                    <span>{{ $ra->objectifImmediats?->actionPrioritaire?->papa?->code }}</span>
                    <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    <span>{{ $ra->objectifImmediats?->actionPrioritaire?->code }}</span>
                    <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    <span>{{ $ra->objectifImmediats?->code }}</span>
                    <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    <span class="font-medium text-gray-600">{{ $ra->code }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $ra->code }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        bg-{{ $ra->couleurStatut() }}-100 text-{{ $ra->couleurStatut() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $ra->statut)) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                        {{ $ra->libelleTypeResultat() }}
                    </span>
                    @if($ra->preuveManquante())
                    <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-600 animate-pulse">
                        <i class="fas fa-exclamation-triangle mr-0.5"></i>Preuve requise
                    </span>
                    @endif
                </div>
                <h1 class="text-lg font-bold text-gray-800">{{ $ra->libelle }}</h1>
                <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2">
                    @if($ra->responsable)
                    <span><i class="fas fa-user mr-1"></i>{{ $ra->responsable->nomComplet() }}</span>
                    @endif
                    <span><i class="fas fa-tasks mr-1"></i>{{ $ra->activites->count() }} activité(s)</span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-indigo-700">{{ number_format($ra->taux_atteinte, 0) }}%</div>
                <p class="text-xs text-gray-400">d'atteinte</p>
            </div>
        </div>

        <div class="mt-4 h-3 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 style="width: {{ min(100, $ra->taux_atteinte) }}%;
                        background: {{ $ra->taux_atteinte >= 75 ? '#22c55e' : ($ra->taux_atteinte >= 50 ? '#f59e0b' : '#ef4444') }}">
            </div>
        </div>

        <!-- Preuve attendue -->
        @if($ra->preuve_requise && $ra->type_preuve_attendue)
        <div class="mt-3 p-3 bg-orange-50 border border-orange-100 rounded-lg text-xs text-orange-700">
            <i class="fas fa-paperclip mr-1"></i>
            <strong>Preuve attendue :</strong> {{ $ra->type_preuve_attendue }}
        </div>
        @endif

        @if($ra->objectifImmediats?->actionPrioritaire?->estEditable())
        <div class="mt-4 flex flex-wrap gap-2">
            @can('papa.modifier')
            <a href="{{ route('resultats-attendus.edit', $ra) }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                <i class="fas fa-edit mr-1"></i>Modifier
            </a>
            <a href="{{ route('activites.create') }}?resultat_attendu_id={{ $ra->id }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-1"></i>Ajouter une activité
            </a>
            @endcan
            @can('document.deposer')
            <a href="{{ route('documents.create') }}?documentable_type={{ urlencode(App\Models\ResultatAttendu::class) }}&documentable_id={{ $ra->id }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                <i class="fas fa-paperclip mr-1"></i>Joindre une preuve
            </a>
            @endcan
        </div>
        @endif
    </div>

    <!-- Onglets -->
    <div class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm border border-gray-100">
        @foreach(['activites' => 'Activités (' . $ra->activites->count() . ')', 'documents' => 'Preuves & Documents (' . $ra->documents->count() . ')', 'indicateurs' => 'Indicateurs (' . $ra->indicateurs->count() . ')'] as $key => $label)
        <button @click="onglet = '{{ $key }}'"
                :class="onglet === '{{ $key }}' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition">{{ $label }}</button>
        @endforeach
    </div>

    <!-- Activités -->
    <div x-show="onglet === 'activites'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($ra->activites as $act)
        <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between hover:bg-gray-50 transition">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="font-mono text-xs text-gray-400">{{ $act->code }}</span>
                    <span class="px-2 py-0.5 rounded text-xs
                        bg-{{ $act->couleurStatut() }}-100 text-{{ $act->couleurStatut() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $act->statut)) }}
                    </span>
                    @if($act->estEnRetard())
                    <span class="px-2 py-0.5 rounded text-xs bg-red-600 text-white">En retard</span>
                    @endif
                </div>
                <p class="font-medium text-sm text-gray-800 truncate">{{ $act->libelle }}</p>
                <p class="text-xs text-gray-400">
                    {{ $act->direction?->sigle }} •
                    Fin prévue : {{ $act->date_fin_prevue?->format('d/m/Y') ?? '—' }}
                </p>
            </div>
            <div class="flex items-center gap-4 ml-4">
                <div class="text-center w-16">
                    <p class="text-xl font-bold text-indigo-600">{{ number_format($act->taux_realisation, 0) }}%</p>
                </div>
                <a href="{{ route('activites.show', $act) }}"
                   class="text-indigo-600 hover:text-indigo-800 text-sm">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="p-10 text-center">
            <i class="fas fa-tasks text-gray-200 text-4xl mb-3"></i>
            <p class="text-gray-400 text-sm">Aucune activité définie.</p>
        </div>
        @endforelse
    </div>

    <!-- Documents -->
    <div x-show="onglet === 'documents'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($ra->documents as $doc)
        <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">{{ $doc->iconeExtension() }}</span>
                <div>
                    <p class="font-medium text-sm text-gray-800">{{ $doc->titre }}</p>
                    <p class="text-xs text-gray-400">{{ $doc->tailleLisible() }} • v{{ $doc->version }} • {{ $doc->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            @can('telecharger', $doc)
            <a href="{{ route('documents.download', $doc) }}" class="text-indigo-600 text-sm hover:underline">
                <i class="fas fa-download mr-1"></i>Télécharger
            </a>
            @endcan
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">Aucun document joint.</div>
        @endforelse
    </div>

    <!-- Indicateurs -->
    <div x-show="onglet === 'indicateurs'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($ra->indicateurs as $ind)
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
@endsection
