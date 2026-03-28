@extends('layouts.app')

@section('title', 'Dashboard Président')
@section('page-title', 'Vision Stratégique — ' . $papa->code)

@section('content')
<div class="space-y-6">

    <!-- En-tête PAPA -->
    <div class="bg-gradient-to-r from-indigo-700 to-indigo-900 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold">{{ $papa->libelle }}</h2>
                <p class="text-indigo-200 text-sm mt-1">Période : {{ $papa->date_debut->format('d/m/Y') }} – {{ $papa->date_fin->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-indigo-500 text-white">
                    {{ $papa->libelleStatut() }}
                </span>
                @if($alertes['critique'] ?? 0 > 0)
                <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-red-500 text-white animate-pulse">
                    ⚠ {{ $alertes['critique'] }} Alerte(s) critique(s)
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- KPIs Principaux -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Taux physique -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Exécution physique</span>
                <span class="text-indigo-500"><i class="fas fa-chart-bar text-xl"></i></span>
            </div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($kpis['taux_execution_physique'], 1) }}%</div>
            <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full progress-bar transition-all duration-1000"
                     style="width: {{ min(100, $kpis['taux_execution_physique']) }}%;
                            background: {{ $kpis['taux_execution_physique'] >= 75 ? '#22c55e' : ($kpis['taux_execution_physique'] >= 50 ? '#f59e0b' : '#ef4444') }}">
                </div>
            </div>
        </div>

        <!-- Taux financier -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Exécution financière</span>
                <span class="text-green-500"><i class="fas fa-money-bill-wave text-xl"></i></span>
            </div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($kpis['taux_execution_financiere'], 1) }}%</div>
            <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full progress-bar"
                     style="width: {{ min(100, $kpis['taux_execution_financiere']) }}%"></div>
            </div>
        </div>

        <!-- Activités en retard -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Activités en retard</span>
                <span class="text-red-500"><i class="fas fa-exclamation-triangle text-xl"></i></span>
            </div>
            <div class="text-3xl font-bold {{ $kpis['activites_en_retard'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $kpis['activites_en_retard'] }}
            </div>
            <p class="text-xs text-gray-400 mt-1">sur {{ $kpis['total_activites'] }} activités</p>
        </div>

        <!-- Budget -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Budget décaissé</span>
                <span class="text-yellow-500"><i class="fas fa-wallet text-xl"></i></span>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($kpis['taux_decaissement'], 1) }}%</div>
            <p class="text-xs text-gray-400 mt-1">
                {{ number_format($kpis['budget_decaisse'] / 1000000, 1) }} M XAF /
                {{ number_format($kpis['budget_total'] / 1000000, 1) }} M XAF
            </p>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Évolution trimestrielle -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Évolution de l'exécution</h3>
            <div id="chart-evolution" style="min-height: 250px;"></div>
        </div>

        <!-- Répartition par département -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Taux par département</h3>
            <div id="chart-departements" style="min-height: 250px;"></div>
        </div>
    </div>

    <!-- Actions prioritaires -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Actions prioritaires</h3>
            <a href="{{ route('papas.show', $papa) }}" class="text-xs text-indigo-600 hover:underline">Voir tout</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Code</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Action</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Département</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Taux</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($papa->actionsPrioritaires->sortBy('ordre')->take(10) as $ap)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $ap->code }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800 line-clamp-1">{{ $ap->libelle }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst($ap->qualification) }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $ap->departement?->libelleAffichage() }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 h-1.5 bg-gray-100 rounded-full w-20">
                                    <div class="h-full rounded-full"
                                         style="width: {{ min(100, $ap->taux_realisation) }}%;
                                                background: {{ $ap->taux_realisation >= 75 ? '#22c55e' : ($ap->taux_realisation >= 50 ? '#f59e0b' : '#ef4444') }}">
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">{{ number_format($ap->taux_realisation, 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                bg-{{ $ap->couleurStatut() }}-100 text-{{ $ap->couleurStatut() }}-700">
                                {{ ucfirst(str_replace('_', ' ', $ap->statut)) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Graphique évolution ─────────────────────────────────────────────────
const evolutionData = @json($graphes['evolution']);
new ApexCharts(document.querySelector('#chart-evolution'), {
    chart: { type: 'line', height: 250, toolbar: { show: false } },
    series: [
        { name: 'Physique (%)', data: evolutionData.physique },
        { name: 'Financier (%)', data: evolutionData.financier },
    ],
    xaxis: { categories: evolutionData.labels },
    yaxis: { max: 100, min: 0, labels: { formatter: v => v.toFixed(0) + '%' } },
    colors: ['#6366f1', '#22c55e'],
    stroke: { curve: 'smooth', width: 3 },
    markers: { size: 5 },
    legend: { position: 'top' },
    tooltip: { y: { formatter: v => v.toFixed(1) + '%' } },
}).render();

// ── Graphique départements ───────────────────────────────────────────────
const depData = @json($graphes['departements'] ?? []);
const depEntries = Object.entries(depData || {});
const depLabels = depEntries.map(([label]) => label);
const depTaux = depEntries.map(([, item]) => Number(item?.taux_moyen ?? 0));
const depChartEl = document.querySelector('#chart-departements');

if (depLabels.length > 0) {
    new ApexCharts(depChartEl, {
        chart: { type: 'bar', height: 250, toolbar: { show: false } },
        series: [{ name: 'Taux moyen (%)', data: depTaux }],
        xaxis: { categories: depLabels, labels: { style: { fontSize: '11px' } } },
        yaxis: { max: 100, min: 0, labels: { formatter: v => Number(v).toFixed(0) + '%' } },
        colors: ['#6366f1'],
        plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: false } },
        dataLabels: { enabled: true, formatter: v => Number(v).toFixed(0) + '%' },
        tooltip: { y: { formatter: v => Number(v).toFixed(1) + '%' } },
    }).render();
} else if (depChartEl) {
    depChartEl.innerHTML = '<p class="text-sm text-gray-400">Aucune donnée disponible pour les départements.</p>';
}
</script>
@endpush
