@extends('layouts.app')

@section('title', $papa->code)
@section('page-title', $papa->code . ' — ' . $papa->libelle)

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('papas.index') }}" class="hover:text-indigo-600">PAPA</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">{{ $papa->code }}</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- En-tête & Actions -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <h1 class="text-xl font-bold text-gray-800">{{ $papa->libelle }}</h1>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        bg-{{ $papa->couleurStatut() }}-100 text-{{ $papa->couleurStatut() }}-700">
                        {{ $papa->libelleStatut() }}
                    </span>
                    @if($papa->est_verrouille)
                        <span class="text-xs text-gray-500"><i class="fas fa-lock mr-1"></i>Verrouillé</span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                    <span><i class="fas fa-calendar mr-1"></i>{{ $papa->date_debut->format('d/m/Y') }} – {{ $papa->date_fin->format('d/m/Y') }}</span>
                    <span><i class="fas fa-money-bill-wave mr-1"></i>{{ number_format($papa->budget_total_prevu, 0, ',', ' ') }} {{ $papa->devise }}</span>
                    <span><i class="fas fa-user mr-1"></i>Créé par {{ $papa->creePar?->nomComplet() }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-2">
                @can('modifier', $papa)
                <a href="{{ route('papas.edit', $papa) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-edit mr-1"></i>Modifier
                </a>
                @endcan

                @can('soumettre', $papa)
                <button onclick="document.getElementById('modal-soumettre').classList.remove('hidden')"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-paper-plane mr-1"></i>Soumettre
                </button>
                @endcan

                @can('valider', $papa)
                <button onclick="document.getElementById('modal-valider').classList.remove('hidden')"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-check mr-1"></i>Valider
                </button>
                @endcan

                @can('rejeter', $papa)
                <button onclick="document.getElementById('modal-rejeter').classList.remove('hidden')"
                        class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-times mr-1"></i>Rejeter
                </button>
                @endcan

                @can('cloner', $papa)
                <button onclick="document.getElementById('modal-clone').classList.remove('hidden')"
                        class="px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-copy mr-1"></i>Cloner N+1
                </button>
                @endcan

                @can('archiver', $papa)
                <button onclick="document.getElementById('modal-archiver').classList.remove('hidden')"
                        class="px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-archive mr-1"></i>Archiver
                </button>
                @endcan
            </div>
        </div>

        <!-- Jauges globales -->
        <div class="mt-6 grid grid-cols-2 gap-6">
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-medium text-gray-600">Exécution physique</span>
                    <span class="font-bold text-indigo-700">{{ $papa->taux_execution_physique }}%</span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all"
                         style="width: {{ min(100, $papa->taux_execution_physique) }}%;
                                background: {{ $papa->taux_execution_physique >= 75 ? '#22c55e' : ($papa->taux_execution_physique >= 50 ? '#f59e0b' : '#ef4444') }}">
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-medium text-gray-600">Exécution financière</span>
                    <span class="font-bold text-green-600">{{ $papa->taux_execution_financiere }}%</span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full"
                         style="width: {{ min(100, $papa->taux_execution_financiere) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accès rapide modules -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @can('papa.voir')
        <a href="{{ route('actions-prioritaires.index', ['papa_id' => $papa->id]) }}"
           class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 shadow-sm border border-gray-100 hover:border-indigo-300 hover:shadow transition group">
            <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200">
                <i class="fas fa-list-ol text-blue-600 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-700">Actions prioritaires</p>
                <p class="text-xs text-gray-400">{{ $papa->actionsPrioritaires->count() }} AP</p>
            </div>
        </a>
        <a href="{{ route('activites.index', ['papa_id' => $papa->id]) }}"
           class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 shadow-sm border border-gray-100 hover:border-indigo-300 hover:shadow transition group">
            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200">
                <i class="fas fa-tasks text-indigo-600 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-700">Activités</p>
                <p class="text-xs text-gray-400">Suivi & Gantt</p>
            </div>
        </a>
        @can('workflow.voir')
        <a href="{{ route('workflows.index', ['papa_id' => $papa->id]) }}"
           class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 shadow-sm border border-gray-100 hover:border-amber-300 hover:shadow transition group">
            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center group-hover:bg-amber-200">
                <i class="fas fa-diagram-project text-amber-600 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-700">Workflow</p>
                <p class="text-xs text-gray-400">{{ $papa->workflowInstances->count() }} circuit(s)</p>
            </div>
        </a>
        @endcan
        @can('decision.voir')
        <a href="{{ route('decisions.index', ['papa_id' => $papa->id]) }}"
           class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 shadow-sm border border-gray-100 hover:border-rose-300 hover:shadow transition group">
            <div class="w-9 h-9 rounded-lg bg-rose-100 flex items-center justify-center group-hover:bg-rose-200">
                <i class="fas fa-gavel text-rose-600 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-700">Décisions</p>
                <p class="text-xs text-gray-400">{{ $papa->decisions->count() }} arbitrage(s)</p>
            </div>
        </a>
        @endcan
        @endcan
        @can('budget.voir')
        <a href="{{ route('budgets.index', $papa) }}"
           class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 shadow-sm border border-gray-100 hover:border-green-300 hover:shadow transition group">
            <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center group-hover:bg-green-200">
                <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-700">Budget</p>
                <p class="text-xs text-gray-400">{{ number_format($papa->budget_total_prevu / 1000000, 1) }} M {{ $papa->devise }}</p>
            </div>
        </a>
        @endcan
        @can('risque.voir')
        <a href="{{ route('risques.index', $papa) }}"
           class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 shadow-sm border border-gray-100 hover:border-orange-300 hover:shadow transition group">
            <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center group-hover:bg-orange-200">
                <i class="fas fa-exclamation-triangle text-orange-600 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-700">Risques</p>
                <p class="text-xs text-gray-400">Matrice & suivi</p>
            </div>
        </a>
        @endcan
    </div>

    <!-- Onglets -->
    <div x-data="{ onglet: 'actions' }">
        <div class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm border border-gray-100">
            @foreach(['actions' => 'Actions prioritaires', 'budget' => 'Budget', 'risques' => 'Risques', 'workflow' => 'Historique validation'] as $key => $label)
            <button @click="onglet = '{{ $key }}'"
                    :class="onglet === '{{ $key }}' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                    class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <!-- Actions prioritaires -->
        <div x-show="onglet === 'actions'" class="mt-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">{{ $papa->actionsPrioritaires->count() }} action(s) prioritaire(s)</h3>
            </div>
            <div class="space-y-3">
                @foreach($papa->actionsPrioritaires->sortBy('ordre') as $ap)
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-mono text-xs text-gray-400">{{ $ap->code }}</span>
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    bg-{{ $ap->couleurPriorite() }}-100 text-{{ $ap->couleurPriorite() }}-700">
                                    {{ ucfirst($ap->priorite) }}
                                </span>
                                <span class="text-xs text-gray-400">{{ ucfirst($ap->qualification) }}</span>
                            </div>
                            <p class="font-medium text-gray-800 text-sm">{{ $ap->libelle }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $ap->departement?->libelleAffichage() }}</p>
                        </div>
                        <div class="ml-4 text-right">
                            <p class="text-xl font-bold text-indigo-700">{{ number_format($ap->taux_realisation, 0) }}%</p>
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                bg-{{ $ap->couleurStatut() }}-100 text-{{ $ap->couleurStatut() }}-700">
                                {{ ucfirst(str_replace('_', ' ', $ap->statut)) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 h-1.5 bg-gray-100 rounded-full">
                        <div class="h-full rounded-full"
                             style="width: {{ min(100, $ap->taux_realisation) }}%;
                                    background: {{ $ap->taux_realisation >= 75 ? '#22c55e' : ($ap->taux_realisation >= 50 ? '#f59e0b' : '#ef4444') }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Budget -->
        <div x-show="onglet === 'budget'" class="mt-4 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-700">Suivi budgétaire</h3>
                <div class="flex gap-2">
                    @can('budget.creer')
                    @if($papa->estEditable())
                    <a href="{{ route('budgets.create', $papa) }}"
                       class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg">
                        <i class="fas fa-plus"></i> Ajouter une ligne
                    </a>
                    @endif
                    @endcan
                    @can('budget.voir')
                    <a href="{{ route('budgets.index', $papa) }}"
                       class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg">
                        <i class="fas fa-external-link-alt"></i> Gérer le budget complet
                    </a>
                    @endcan
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Source</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500">Prévu (M)</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500">Engagé (M)</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500">Décaissé (M)</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500">Taux eng.</th>
                            @can('budget.modifier')
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500"></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($papa->budgets as $budget)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-700">{{ $budget->libelleSource() }}</p>
                                @if($budget->partenaire)
                                <p class="text-xs text-gray-400">{{ $budget->partenaire->sigle }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-medium">{{ number_format($budget->montant_prevu / 1000000, 2) }}</td>
                            <td class="px-5 py-3 text-right text-blue-600">{{ number_format($budget->montant_engage / 1000000, 2) }}</td>
                            <td class="px-5 py-3 text-right text-green-600">{{ number_format($budget->montant_decaisse / 1000000, 2) }}</td>
                            <td class="px-5 py-3 text-right font-semibold">{{ number_format($budget->tauxEngagement(), 1) }}%</td>
                            @can('budget.modifier')
                            <td class="px-5 py-3 text-right">
                                @if($papa->estEditable())
                                <a href="{{ route('budgets.edit', [$papa, $budget]) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-xs">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endif
                            </td>
                            @endcan
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucune ligne budgétaire — <a href="{{ route('budgets.create', $papa) }}" class="text-indigo-600 hover:underline">Ajouter</a></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Risques -->
        <div x-show="onglet === 'risques'" class="mt-4 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-700">Risques identifiés</h3>
                <div class="flex gap-2">
                    @can('risque.creer')
                    @if($papa->estEditable())
                    <a href="{{ route('risques.create', $papa) }}"
                       class="inline-flex items-center gap-1 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg">
                        <i class="fas fa-plus"></i> Nouveau risque
                    </a>
                    @endif
                    @endcan
                    @can('risque.voir')
                    <a href="{{ route('risques.index', $papa) }}"
                       class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg">
                        <i class="fas fa-external-link-alt"></i> Matrice des risques complète
                    </a>
                    @endcan
                </div>
            </div>
            @php
                $risques = $papa->risques ?? collect();
            @endphp
            @if($risques->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400 text-sm">
                Aucun risque enregistré pour ce PAPA.
                @can('risque.creer')
                @if($papa->estEditable())
                <a href="{{ route('risques.create', $papa) }}" class="text-indigo-600 hover:underline ml-1">Identifier un risque</a>
                @endif
                @endcan
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Risque</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Probabilité</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Impact</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Criticité</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Statut</th>
                            @can('risque.modifier')
                            <th class="px-4 py-3"></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($risques->take(5) as $risque)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800 text-xs">{{ Str::limit($risque->libelle, 60) }}</p>
                                <p class="text-xs text-gray-400">{{ $risque->categorie }}</p>
                            </td>
                            <td class="px-4 py-3 text-center text-xs">{{ ucfirst($risque->probabilite) }}</td>
                            <td class="px-4 py-3 text-center text-xs">{{ ucfirst($risque->impact) }}</td>
                            <td class="px-4 py-3 text-center">
                                @php $c = $risque->criticite ?? 'faible'; @endphp
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    {{ $c === 'critique' ? 'bg-red-100 text-red-700' : ($c === 'eleve' ? 'bg-orange-100 text-orange-700' : ($c === 'moyen' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')) }}">
                                    {{ ucfirst($c) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $risque->statut)) }}</td>
                            @can('risque.modifier')
                            <td class="px-4 py-3 text-right">
                                @if($papa->estEditable())
                                <a href="{{ route('risques.edit', [$papa, $risque]) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-xs">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endif
                            </td>
                            @endcan
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($risques->count() > 5)
                <div class="px-4 py-2 bg-gray-50 text-center">
                    <a href="{{ route('risques.index', $papa) }}" class="text-xs text-indigo-600 hover:underline">
                        Voir les {{ $risques->count() - 5 }} autres risques →
                    </a>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Workflow -->
        <div x-show="onglet === 'workflow'" class="mt-4 space-y-4">

            {{-- Pipeline visuel --}}
            @php
                $etapes = [
                    'brouillon'    => ['label' => 'Brouillon',    'icon' => 'fa-file-alt',    'color' => 'gray'],
                    'soumis'       => ['label' => 'Soumis',       'icon' => 'fa-paper-plane', 'color' => 'blue'],
                    'valide'       => ['label' => 'Validé',       'icon' => 'fa-check-circle','color' => 'green'],
                    'en_execution' => ['label' => 'En exécution', 'icon' => 'fa-play-circle', 'color' => 'indigo'],
                    'cloture'      => ['label' => 'Clôturé',      'icon' => 'fa-flag-checkered','color' => 'purple'],
                ];
                $statutOrdre = array_keys($etapes);
                $indexActuel = array_search($papa->statut, $statutOrdre);
                // Cas spéciaux : archive = après clôture, en_validation = entre soumis et validé
                if ($papa->statut === 'archive')     $indexActuel = count($statutOrdre);
                if ($papa->statut === 'en_validation') $indexActuel = 2; // entre soumis et valide
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-5">Circuit de validation</h3>
                <div class="flex items-center">
                    @foreach($etapes as $key => $etape)
                    @php
                        $idx = array_search($key, $statutOrdre);
                        $isDone   = $indexActuel !== false && $idx < $indexActuel;
                        $isCurrent = $papa->statut === $key || ($key === 'valide' && $papa->statut === 'en_validation');
                        $isPending = $indexActuel !== false && $idx > $indexActuel;
                    @endphp
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            @if($isCurrent) bg-{{ $etape['color'] }}-600 text-white ring-4 ring-{{ $etape['color'] }}-100
                            @elseif($isDone) bg-{{ $etape['color'] }}-100 text-{{ $etape['color'] }}-600
                            @else bg-gray-100 text-gray-400
                            @endif">
                            <i class="fas {{ $etape['icon'] }} text-sm"></i>
                        </div>
                        <p class="text-xs font-medium mt-2 text-center
                            @if($isCurrent) text-{{ $etape['color'] }}-700
                            @elseif($isDone) text-gray-600
                            @else text-gray-400
                            @endif">
                            {{ $etape['label'] }}
                            @if($isCurrent)<br><span class="text-xs font-bold">← Actuel</span>@endif
                        </p>
                    </div>
                    @if(!$loop->last)
                    <div class="h-0.5 flex-1 {{ $isDone ? 'bg-green-400' : 'bg-gray-200' }} -mt-5"></div>
                    @endif
                    @endforeach

                    @if($papa->statut === 'archive')
                    <div class="flex-1 h-0.5 bg-gray-300 -mt-5"></div>
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-orange-600 text-white ring-4 ring-orange-100">
                            <i class="fas fa-archive text-sm"></i>
                        </div>
                        <p class="text-xs font-medium mt-2 text-center text-orange-700">Archivé<br><span class="font-bold">← Actuel</span></p>
                    </div>
                    @endif
                </div>

                @if($papa->est_verrouille && !$papa->estArchive())
                <p class="text-xs text-amber-600 mt-4 text-center"><i class="fas fa-lock mr-1"></i>PAPA verrouillé — aucune modification possible</p>
                @endif
            </div>

            {{-- Panel d'actions contextuelles --}}
            @php
                $peutSoumettre = auth()->user()->can('soumettre', $papa);
                $peutValider   = auth()->user()->can('valider', $papa);
                $peutRejeter   = auth()->user()->can('rejeter', $papa);
                $peutArchiver  = auth()->user()->can('archiver', $papa);
                $aDesActions   = $peutSoumettre || $peutValider || $peutRejeter || $peutArchiver;
            @endphp
            @if($aDesActions)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Actions disponibles</h3>
                <div class="flex flex-wrap gap-3">
                    @if($peutSoumettre)
                    <button onclick="document.getElementById('modal-soumettre').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-paper-plane"></i>Soumettre pour validation
                    </button>
                    @endif
                    @if($peutValider)
                    <button onclick="document.getElementById('modal-valider').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-check"></i>Valider le PAPA
                    </button>
                    @endif
                    @if($peutRejeter)
                    <button onclick="document.getElementById('modal-rejeter').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-times"></i>Rejeter (renvoi en brouillon)
                    </button>
                    @endif
                    @if($peutArchiver)
                    <button onclick="document.getElementById('modal-archiver').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-archive"></i>Archiver ce PAPA
                    </button>
                    @endif
                    @can('workflow.demarrer')
                    <form action="{{ route('workflows.demarrer-papa', $papa) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium transition">
                            <i class="fas fa-diagram-project"></i>Démarrer workflow
                        </button>
                    </form>
                    @endcan
                    @can('decision.creer')
                    <a href="{{ route('decisions.create', ['papa_id' => $papa->id]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-gavel"></i>Nouvelle décision
                    </a>
                    @endcan
                </div>
                <p class="text-xs text-gray-400 mt-3">
                    @if($papa->statut === 'brouillon') En attente de soumission pour démarrer la chaîne de validation.
                    @elseif($papa->statut === 'soumis') PAPA soumis — en attente de décision du validateur.
                    @elseif($papa->statut === 'valide') PAPA validé — peut être mis en exécution ou archivé.
                    @elseif($papa->statut === 'en_execution') PAPA en cours d'exécution.
                    @endif
                </p>
            </div>
            @endif

            @if($papa->workflowInstances->isNotEmpty() || $papa->decisions->isNotEmpty())
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Workflows liés</h3>
                        @can('workflow.voir')
                        <a href="{{ route('workflows.index', ['papa_id' => $papa->id]) }}" class="text-xs text-indigo-600 hover:underline">Tout voir</a>
                        @endcan
                    </div>
                    <div class="space-y-3">
                        @forelse($papa->workflowInstances->sortByDesc('created_at')->take(3) as $instance)
                        <a href="{{ route('workflows.show', $instance) }}" class="block rounded-lg border border-gray-200 p-3 hover:border-amber-300 hover:bg-amber-50 transition">
                            <p class="text-sm font-medium text-gray-800">{{ $instance->definition?->libelle ?? 'Workflow' }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Statut: {{ str($instance->statut)->replace('_', ' ')->title() }}
                                @if($instance->etapeCourante)
                                    • Étape: {{ $instance->etapeCourante->libelle }}
                                @endif
                            </p>
                        </a>
                        @empty
                        <p class="text-sm text-gray-400">Aucun workflow démarré sur ce PAPA.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Décisions liées</h3>
                        @can('decision.voir')
                        <a href="{{ route('decisions.index', ['papa_id' => $papa->id]) }}" class="text-xs text-indigo-600 hover:underline">Tout voir</a>
                        @endcan
                    </div>
                    <div class="space-y-3">
                        @forelse($papa->decisions->sortByDesc('created_at')->take(3) as $decision)
                        <a href="{{ route('decisions.show', $decision) }}" class="block rounded-lg border border-gray-200 p-3 hover:border-rose-300 hover:bg-rose-50 transition">
                            <p class="text-sm font-medium text-gray-800">{{ $decision->reference }} - {{ $decision->titre }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ str($decision->niveau_decision)->replace('_', ' ')->title() }}
                                • {{ str($decision->statut)->replace('_', ' ')->title() }}
                            </p>
                        </a>
                        @empty
                        <p class="text-sm text-gray-400">Aucune décision rattachée à ce PAPA.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            {{-- Historique --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Historique des actions</h3>
                @if($papa->validationsWorkflow->isEmpty())
                <p class="text-gray-400 text-sm">Aucune action de validation enregistrée.</p>
                @else
                <div class="relative pl-6 space-y-6">
                    <div class="absolute left-2 top-2 bottom-2 w-0.5 bg-gray-100"></div>
                    @foreach($papa->validationsWorkflow->sortByDesc('created_at') as $vw)
                    @php
                        $icons = ['soumis' => 'fa-paper-plane', 'approuve' => 'fa-check', 'rejete' => 'fa-times', 'demande_correction' => 'fa-edit', 'information' => 'fa-info'];
                        $icon = $icons[$vw->action] ?? 'fa-circle';
                    @endphp
                    <div class="relative flex items-start gap-4">
                        <div class="absolute -left-6 w-5 h-5 rounded-full flex items-center justify-center
                            bg-{{ $vw->couleurAction() }}-100 border-2 border-{{ $vw->couleurAction() }}-300">
                            <i class="fas {{ $icon }} text-{{ $vw->couleurAction() }}-600" style="font-size:0.5rem"></i>
                        </div>
                        <div class="flex-1 min-w-0 bg-gray-50 rounded-lg p-3">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-semibold text-sm text-gray-800">{{ $vw->acteur?->nomComplet() ?? 'Système' }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    bg-{{ $vw->couleurAction() }}-100 text-{{ $vw->couleurAction() }}-700">
                                    {{ $vw->libelleAction() }}
                                </span>
                                @if($vw->etape)
                                <span class="text-xs text-gray-400 italic">{{ str_replace('_', ' ', $vw->etape) }}</span>
                                @endif
                                <span class="ml-auto text-xs text-gray-400">{{ $vw->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            @if($vw->statut_avant && $vw->statut_apres)
                            <p class="text-xs text-gray-500">
                                Statut : <span class="font-medium">{{ $vw->statut_avant }}</span>
                                <i class="fas fa-arrow-right mx-1"></i>
                                <span class="font-medium text-indigo-600">{{ $vw->statut_apres }}</span>
                            </p>
                            @endif
                            @if($vw->commentaire)
                            <p class="text-sm text-gray-600 mt-1 italic">"{{ $vw->commentaire }}"</p>
                            @endif
                            @if($vw->motif_rejet)
                            <p class="text-sm text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>Motif : {{ $vw->motif_rejet }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Soumettre -->
<div id="modal-soumettre" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Soumettre pour validation</h3>
        <p class="text-sm text-gray-500 mb-4">Le PAPA passera au statut <strong>Soumis</strong> et sera transmis aux validateurs.</p>
        <form action="{{ route('papas.soumettre', $papa) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire (optionnel)</label>
                <textarea name="commentaire" rows="3" placeholder="Observations ou remarques de soumission…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-soumettre').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-paper-plane mr-1"></i>Soumettre
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Valider -->
<div id="modal-valider" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Valider le PAPA</h3>
        <p class="text-sm text-gray-500 mb-4">Le PAPA passera au statut <strong>Validé</strong>.</p>
        <form action="{{ route('papas.valider', $papa) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire (optionnel)</label>
                <textarea name="commentaire" rows="3" placeholder="Observations ou approbation…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-valider').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                    <i class="fas fa-check mr-1"></i>Confirmer la validation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Rejeter -->
<div id="modal-rejeter" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Rejeter le PAPA</h3>
        <p class="text-sm text-gray-500 mb-4">Le PAPA sera renvoyé en <strong>Brouillon</strong> pour corrections.</p>
        <form action="{{ route('papas.rejeter', $papa) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Motif du rejet <span class="text-red-500">*</span>
                </label>
                <textarea name="motif" rows="4" required placeholder="Expliquez les raisons du rejet et les corrections attendues…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-rejeter').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                    <i class="fas fa-times mr-1"></i>Confirmer le rejet
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Archiver -->
<div id="modal-archiver" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Archiver le PAPA</h3>
        <p class="text-sm text-amber-600 bg-amber-50 rounded-lg px-3 py-2 mb-4 text-sm">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Cette action est irréversible. Le PAPA sera définitivement verrouillé en lecture seule.
        </p>
        <form action="{{ route('papas.archiver', $papa) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motif d'archivage (optionnel)</label>
                <textarea name="motif_archivage" rows="3" placeholder="Raison de l'archivage…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-archiver').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700">
                    <i class="fas fa-archive mr-1"></i>Confirmer l'archivage
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Clone -->
<div id="modal-clone" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Cloner vers N+1</h3>
        <form action="{{ route('papas.cloner', $papa) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle année</label>
                <input type="number" name="annee_nouvelle" value="{{ $papa->annee + 1 }}"
                       min="2024" max="2050"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modal-clone').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                    Cloner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
