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
                <form action="{{ route('papas.soumettre', $papa) }}" method="POST" class="inline">
                    @csrf
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-paper-plane mr-1"></i>Soumettre
                    </button>
                </form>
                @endcan

                @can('valider', $papa)
                <form action="{{ route('papas.valider', $papa) }}" method="POST" class="inline">
                    @csrf
                    <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-check mr-1"></i>Valider
                    </button>
                </form>
                @endcan

                @can('cloner', $papa)
                <button onclick="document.getElementById('modal-clone').classList.remove('hidden')"
                        class="px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-copy mr-1"></i>Cloner N+1
                </button>
                @endcan

                @can('archiver', $papa)
                <form action="{{ route('papas.archiver', $papa) }}" method="POST" class="inline"
                      onsubmit="return confirm('Archiver ce PAPA ? Cette action le rend définitivement en lecture seule.')">
                    @csrf
                    <button class="px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-archive mr-1"></i>Archiver
                    </button>
                </form>
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

    <!-- Onglets -->
    <div x-data="{ onglet: 'actions' }">
        <div class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm border border-gray-100">
            @foreach(['actions' => 'Actions prioritaires', 'budget' => 'Budget', 'workflow' => 'Historique validation'] as $key => $label)
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
        <div x-show="onglet === 'budget'" class="mt-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500">Source</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500">Prévu</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500">Engagé</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500">Décaissé</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500">Taux eng.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($papa->budgets as $budget)
                        <tr>
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-700">{{ $budget->libelleSource() }}</p>
                                @if($budget->partenaire)
                                <p class="text-xs text-gray-400">{{ $budget->partenaire->sigle }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-medium">{{ number_format($budget->montant_prevu / 1000000, 2) }} M</td>
                            <td class="px-5 py-3 text-right text-blue-600">{{ number_format($budget->montant_engage / 1000000, 2) }} M</td>
                            <td class="px-5 py-3 text-right text-green-600">{{ number_format($budget->montant_decaisse / 1000000, 2) }} M</td>
                            <td class="px-5 py-3 text-right font-semibold">{{ number_format($budget->tauxEngagement(), 1) }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Aucune ligne budgétaire définie</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Workflow -->
        <div x-show="onglet === 'workflow'" class="mt-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="space-y-4">
                    @forelse($papa->validationsWorkflow->sortByDesc('created_at') as $vw)
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                            bg-{{ $vw->couleurAction() }}-100">
                            <i class="fas fa-circle text-{{ $vw->couleurAction() }}-500 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-sm text-gray-800">{{ $vw->acteur?->nomComplet() }}</span>
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    bg-{{ $vw->couleurAction() }}-100 text-{{ $vw->couleurAction() }}-700">
                                    {{ $vw->libelleAction() }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $vw->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($vw->commentaire)
                            <p class="text-sm text-gray-500 mt-1">{{ $vw->commentaire }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-sm">Aucune action de validation enregistrée.</p>
                    @endforelse
                </div>
            </div>
        </div>
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
