@extends('layouts.app')

@section('title', 'Budget — ' . $papa->code)

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
            <a href="{{ route('papas.show', $papa) }}" class="hover:text-indigo-600">{{ $papa->code }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 font-medium">Budget</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Situation budgétaire</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $papa->libelle }}</p>
    </div>
    @can('papa.modifier')
    @if($papa->estEditable())
    <a href="{{ route('budgets.create', $papa) }}"
       class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
        <i class="fas fa-plus"></i> Nouvelle ligne budgétaire
    </a>
    @endif
    @endcan
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<!-- KPIs budgétaires -->
<div class="grid grid-cols-4 gap-4 mb-6">
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
        <div class="text-2xl font-bold text-indigo-700">
            {{ $totaux['prevu'] > 0 ? number_format($totaux['engage'] / $totaux['prevu'] * 100, 1) : 0 }}%
        </div>
        <div class="text-xs text-gray-500 mt-1">Taux engagement</div>
    </div>
</div>

<!-- Tableau des lignes budgétaires -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($papa->budgets->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <i class="fas fa-wallet text-4xl mb-3"></i>
        <p class="font-medium">Aucune ligne budgétaire</p>
        @can('papa.modifier')
        @if($papa->estEditable())
        <a href="{{ route('budgets.create', $papa) }}" class="inline-block mt-3 text-sm text-indigo-600 hover:underline">
            Ajouter la première ligne
        </a>
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
                <th class="text-right px-4 py-3">Prévu (XAF)</th>
                <th class="text-right px-4 py-3">Engagé</th>
                <th class="text-right px-4 py-3">Décaissé</th>
                <th class="text-right px-4 py-3">Taux eng.</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($papa->budgets->sortBy('source_financement') as $budget)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $budget->libelleSource() }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ $budget->libelle_ligne ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($budget->actionPrioritaire)
                    <span class="font-mono text-xs text-indigo-600">{{ $budget->actionPrioritaire->code }}</span>
                    @else
                    <span class="text-gray-400 text-xs">PAPA global</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right font-medium">{{ number_format($budget->montant_prevu, 0, ',', ' ') }}</td>
                <td class="px-4 py-3 text-right text-blue-700">{{ number_format($budget->montant_engage, 0, ',', ' ') }}</td>
                <td class="px-4 py-3 text-right text-green-600">{{ number_format($budget->montant_decaisse, 0, ',', ' ') }}</td>
                <td class="px-4 py-3 text-right">
                    <span class="font-semibold {{ $budget->tauxEngagement() >= 80 ? 'text-green-600' : ($budget->tauxEngagement() >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                        {{ number_format($budget->tauxEngagement(), 1) }}%
                    </span>
                </td>
                <td class="px-4 py-3">
                    @can('papa.modifier')
                    @if($papa->estEditable())
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('budgets.edit', [$papa, $budget]) }}" class="text-gray-400 hover:text-indigo-600">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('budgets.destroy', [$papa, $budget]) }}"
                              onsubmit="return confirm('Supprimer cette ligne ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-blue-50 border-t-2 border-blue-200 font-bold text-sm">
                <td class="px-4 py-3" colspan="3">TOTAL</td>
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
@endsection
