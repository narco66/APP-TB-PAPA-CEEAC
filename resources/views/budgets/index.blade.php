@extends('layouts.app')

@section('title', 'Budget - ' . $papa->code)
@section('page-title', 'Situation budgétaire')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('papas.index') }}" class="hover:text-indigo-600">Plans d'action</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('papas.show', $papa) }}" class="hover:text-indigo-600">{{ $papa->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Budget</li>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Périmètre de données :</span> {{ $scopeLabel }}
    </div>

    <!-- En-tête -->
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <p class="text-sm text-gray-500 mt-1">{{ $papa->libelle }}</p>
            @if(!$papa->estEditable())
            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full">
                <i class="fas fa-lock"></i> PAPA verrouillé - modifications désactivées
            </span>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('budgets.print', [$papa] + request()->only('source_financement', 'action_prioritaire_id', 'annee_budgetaire')) }}"
               target="_blank"
               class="inline-flex items-center gap-2 bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-medium border border-gray-200 hover:bg-gray-50 transition">
                <i class="fas fa-print"></i> Version imprimable
            </a>
            @can('budget.creer')
            @if($papa->estEditable())
            <a href="{{ route('budgets.create', $papa) }}"
               class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-plus"></i> Nouvelle ligne budgétaire
            </a>
            @else
            <span title="Le PAPA est verrouillé"
                  class="inline-flex items-center gap-2 bg-gray-200 text-gray-400 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                <i class="fas fa-lock"></i> Nouvelle ligne budgétaire
            </span>
            @endif
            @endcan
        </div>
    </div>

    <!-- Filtres -->
    <form method="GET" action="{{ route('budgets.index', $papa) }}"
          class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">PAPA</label>
            <select name="_papa" onchange="window.location=this.value"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                @foreach($papas as $p)
                <option value="{{ route('budgets.index', $p) }}{{ request()->has('source_financement') || request()->has('action_prioritaire_id') || request()->has('annee_budgetaire') ? '?' . http_build_query(request()->only('source_financement', 'action_prioritaire_id', 'annee_budgetaire')) : '' }}"
                        {{ $p->id === $papa->id ? 'selected' : '' }}>
                    {{ $p->code }} - {{ $p->annee }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Source</label>
            <select name="source_financement"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Toutes sources</option>
                @foreach([
                    'budget_ceeac' => 'Budget CEEAC',
                    'contribution_etat_membre' => 'Contribution État membre',
                    'partenaire_technique_financier' => 'PTF',
                    'fonds_propres' => 'Fonds propres',
                    'autre' => 'Autre'
                ] as $v => $l)
                <option value="{{ $v }}" {{ request('source_financement') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        @if($actionsPrioritaires->isNotEmpty())
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Action prioritaire</label>
            <select name="action_prioritaire_id"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Toutes les AP</option>
                @foreach($actionsPrioritaires as $ap)
                <option value="{{ $ap->id }}" {{ request('action_prioritaire_id') == $ap->id ? 'selected' : '' }}>
                    {{ $ap->code }} - {{ Str::limit($ap->libelle, 40) }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        @if($annees->isNotEmpty())
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Année</label>
            <select name="annee_budgetaire"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Toutes années</option>
                @foreach($annees as $annee)
                <option value="{{ $annee }}" {{ request('annee_budgetaire') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <button type="submit"
                class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>

        @if(request()->anyFilled(['source_financement', 'action_prioritaire_id', 'annee_budgetaire']))
        <a href="{{ route('budgets.index', $papa) }}"
           class="px-4 py-1.5 text-gray-500 text-sm hover:text-gray-700">
            <i class="fas fa-times mr-1"></i>Réinitialiser
        </a>
        @endif
    </form>

    <!-- KPIs budgétaires -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($totaux['prevu'] / 1000000, 2) }} M</div>
            <div class="text-xs text-gray-500 mt-1">Budget prévu (XAF)</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-700">{{ number_format($totaux['engage'] / 1000000, 2) }} M</div>
            <div class="text-xs text-gray-500 mt-1">Engagé (XAF)</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ number_format($totaux['decaisse'] / 1000000, 2) }} M</div>
            <div class="text-xs text-gray-500 mt-1">Décaissé (XAF)</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            @php $tauxEng = $totaux['prevu'] > 0 ? $totaux['engage'] / $totaux['prevu'] * 100 : 0; @endphp
            <div class="text-2xl font-bold {{ $tauxEng >= 80 ? 'text-green-600' : ($tauxEng >= 50 ? 'text-amber-600' : 'text-indigo-700') }}">
                {{ number_format($tauxEng, 1) }}%
            </div>
            <div class="text-xs text-gray-500 mt-1">Taux engagement</div>
        </div>
    </div>

    <!-- Tableau des lignes budgétaires -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($budgets->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-wallet text-4xl mb-3"></i>
            <p class="font-medium">Aucune ligne budgétaire
                @if(request()->anyFilled(['source_financement', 'action_prioritaire_id', 'annee_budgetaire']))
                    pour ces filtres
                @endif
            </p>
            @can('budget.creer')
            @if(!request()->anyFilled(['source_financement', 'action_prioritaire_id', 'annee_budgetaire']))
            @if($papa->estEditable())
            <a href="{{ route('budgets.create', $papa) }}" class="inline-block mt-3 text-sm text-indigo-600 hover:underline">
                Ajouter la première ligne
            </a>
            @else
            <span class="inline-block mt-3 text-sm text-gray-400">
                <i class="fas fa-lock mr-1"></i>PAPA verrouillé
            </span>
            @endif
            @endif
            @endcan
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <th class="text-left px-4 py-3">Source</th>
                    <th class="text-left px-4 py-3">Libellé ligne</th>
                    <th class="text-left px-4 py-3">AP rattachée</th>
                    <th class="text-center px-4 py-3">Année</th>
                    <th class="text-right px-4 py-3">Prévu (XAF)</th>
                    <th class="text-right px-4 py-3">Engagé</th>
                    <th class="text-right px-4 py-3">Décaissé</th>
                    <th class="text-right px-4 py-3">Taux eng.</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($budgets as $budget)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $budget->libelleSource() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $budget->libelle_ligne ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($budget->actionPrioritaire)
                        <span class="font-mono text-xs text-indigo-600">{{ $budget->actionPrioritaire->code }}</span>
                        @else
                        <span class="text-gray-400 text-xs">PAPA global</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $budget->annee_budgetaire }}</td>
                    <td class="px-4 py-3 text-right font-medium">{{ number_format($budget->montant_prevu, 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right text-blue-700">{{ number_format($budget->montant_engage, 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right text-green-600">{{ number_format($budget->montant_decaisse, 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-semibold {{ $budget->tauxEngagement() >= 80 ? 'text-green-600' : ($budget->tauxEngagement() >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ number_format($budget->tauxEngagement(), 1) }}%
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3 justify-end">
                            @can('budget.modifier')
                            @if($papa->estEditable())
                            <a href="{{ route('budgets.edit', [$papa, $budget]) }}"
                               class="text-gray-400 hover:text-indigo-600" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </a>
                            @else
                            <span class="text-gray-200 cursor-not-allowed" title="PAPA verrouillé">
                                <i class="fas fa-pen"></i>
                            </span>
                            @endif
                            @endcan

                            @can('budget.modifier')
                            @if($papa->estEditable())
                            <form method="POST" action="{{ route('budgets.destroy', [$papa, $budget]) }}"
                                  onsubmit="return confirm('Supprimer cette ligne ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-gray-200 cursor-not-allowed" title="PAPA verrouillé">
                                <i class="fas fa-trash"></i>
                            </span>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-blue-50 border-t-2 border-blue-200 font-bold text-sm">
                    <td class="px-4 py-3" colspan="4">
                        TOTAL
                        @if(request()->anyFilled(['source_financement', 'action_prioritaire_id', 'annee_budgetaire']))
                        <span class="text-xs font-normal text-blue-500">(filtré - {{ $budgets->count() }} ligne(s))</span>
                        @else
                        <span class="text-xs font-normal text-blue-500">{{ $budgets->count() }} ligne(s)</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">{{ number_format($totaux['prevu'], 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right text-blue-700">{{ number_format($totaux['engage'], 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right text-green-600">{{ number_format($totaux['decaisse'], 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right">
                        {{ $totaux['prevu'] > 0 ? number_format($totaux['engage'] / $totaux['prevu'] * 100, 1) : 0 }}%
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        @endif
    </div>

</div>
@endsection
