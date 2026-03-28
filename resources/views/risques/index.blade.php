@extends('layouts.app')

@section('title', 'Registre des risques — ' . $papa->code)

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <nav class="text-sm text-gray-500 mb-1 flex items-center gap-2">
            <a href="{{ route('papas.show', $papa) }}" class="hover:text-indigo-600">{{ $papa->code }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 font-medium">Risques</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Registre des risques</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $papa->libelle }}</p>
    </div>
    @can('papa.modifier')
    @if($papa->estEditable())
    <a href="{{ route('risques.create', $papa) }}"
       class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
        <i class="fas fa-plus"></i> Nouveau risque
    </a>
    @endif
    @endcan
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<!-- Statistiques -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4 text-center">
        <div class="text-3xl font-bold text-red-600">{{ $stats['rouge'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Risques critiques</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-4 text-center">
        <div class="text-3xl font-bold text-orange-600">{{ $stats['orange'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Risques élevés</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-yellow-200 p-4 text-center">
        <div class="text-3xl font-bold text-yellow-600">{{ $stats['jaune'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Risques modérés</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4 text-center">
        <div class="text-3xl font-bold text-green-600">{{ $stats['vert'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Risques faibles</div>
    </div>
</div>

<!-- Matrice de risques 5×5 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-base font-semibold text-gray-900 mb-4">Matrice des risques (Probabilité × Impact)</h2>

    @php
    $labelsProbabilite = [
        'tres_faible' => 'Très faible',
        'faible'      => 'Faible',
        'moyenne'     => 'Moyenne',
        'elevee'      => 'Élevée',
        'tres_elevee' => 'Très élevée',
    ];
    $labelsImpact = [
        'negligeable'   => 'Négligeable',
        'mineur'        => 'Mineur',
        'modere'        => 'Modéré',
        'majeur'        => 'Majeur',
        'catastrophique' => 'Catastrophique',
    ];
    // Scores probabilité/impact (1-5)
    $scoresProb = ['tres_faible'=>1, 'faible'=>2, 'moyenne'=>3, 'elevee'=>4, 'tres_elevee'=>5];
    $scoresImp  = ['negligeable'=>1, 'mineur'=>2, 'modere'=>3, 'majeur'=>4, 'catastrophique'=>5];

    $couleurCellule = function($prob, $imp) use ($scoresProb, $scoresImp) {
        $score = $scoresProb[$prob] * $scoresImp[$imp];
        return match(true) {
            $score >= 15 => 'bg-red-100 border-red-200',
            $score >= 8  => 'bg-orange-100 border-orange-200',
            $score >= 3  => 'bg-yellow-50 border-yellow-200',
            default      => 'bg-green-50 border-green-200',
        };
    };
    @endphp

    <div class="overflow-x-auto">
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr>
                    <th class="p-2 text-left text-gray-500 border border-gray-200 w-28">Probabilité \ Impact</th>
                    @foreach($labelsImpact as $imp => $labImp)
                    <th class="p-2 text-center border border-gray-200 font-medium text-gray-700">{{ $labImp }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(array_reverse($probabilites) as $prob)
                <tr>
                    <td class="p-2 border border-gray-200 font-medium text-gray-700 text-xs">{{ $labelsProbabilite[$prob] }}</td>
                    @foreach($impacts as $imp)
                    @php $cellRisques = $matrice[$prob][$imp]; @endphp
                    <td class="p-2 border border-gray-200 text-center {{ $couleurCellule($prob, $imp) }} min-h-12">
                        @if($cellRisques->isEmpty())
                        <span class="text-gray-300">—</span>
                        @else
                        @foreach($cellRisques as $r)
                        <a href="{{ route('risques.edit', [$papa, $r]) }}"
                           class="inline-block bg-white border border-gray-300 rounded px-1 py-0.5 text-gray-700 hover:bg-gray-50 mb-0.5 font-mono text-xs"
                           title="{{ $r->libelle }}">
                            {{ $r->code }}
                        </a>
                        @endforeach
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex items-center gap-6 mt-3 text-xs text-gray-600">
        <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 bg-red-200 rounded"></span> Critique (≥15)</span>
        <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 bg-orange-200 rounded"></span> Élevé (8-14)</span>
        <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 bg-yellow-100 rounded"></span> Modéré (3-7)</span>
        <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 bg-green-100 rounded"></span> Faible (&lt;3)</span>
    </div>
</div>

<!-- Liste des risques -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($risques->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <i class="fas fa-shield-alt text-4xl mb-3"></i>
        <p class="font-medium">Aucun risque identifié</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="text-left px-4 py-3">Code</th>
                <th class="text-left px-4 py-3">Libellé</th>
                <th class="text-left px-4 py-3">Catégorie</th>
                <th class="text-center px-4 py-3">Niveau</th>
                <th class="text-center px-4 py-3">Score</th>
                <th class="text-left px-4 py-3">Statut</th>
                <th class="text-left px-4 py-3">Responsable</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($risques as $risque)
            @php
            $badgeNiveau = match($risque->niveau_risque) {
                'rouge'  => 'bg-red-100 text-red-800',
                'orange' => 'bg-orange-100 text-orange-800',
                'jaune'  => 'bg-yellow-100 text-yellow-800',
                'vert'   => 'bg-green-100 text-green-800',
                default  => 'bg-gray-100 text-gray-700',
            };
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $risque->code }}</td>
                <td class="px-4 py-3 font-medium text-gray-900">{{ Str::limit($risque->libelle, 60) }}</td>
                <td class="px-4 py-3 text-gray-600">{{ ucfirst($risque->categorie) }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeNiveau }}">
                        {{ ucfirst($risque->niveau_risque) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $risque->score_risque }}/25</td>
                <td class="px-4 py-3 text-gray-600">{{ ucfirst(str_replace('_', ' ', $risque->statut)) }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $risque->responsable?->nomComplet() ?? '—' }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        @can('papa.modifier')
                        @if($papa->estEditable())
                        <a href="{{ route('risques.edit', [$papa, $risque]) }}" class="text-gray-400 hover:text-indigo-600">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('risques.destroy', [$papa, $risque]) }}"
                              onsubmit="return confirm('Supprimer ce risque ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endcan
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
