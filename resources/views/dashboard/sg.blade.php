@extends('layouts.app')
@section('title', 'Dashboard SG')
@section('page-title', 'Coordination Générale — ' . $papa->code)
@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-2xl p-6 text-white">
        <h2 class="text-xl font-bold">{{ $papa->libelle }}</h2>
        <div class="flex space-x-8 mt-3">
            <div>
                <p class="text-slate-300 text-xs">Physique</p>
                <p class="text-2xl font-bold">{{ $kpis['taux_execution_physique'] }}%</p>
            </div>
            <div>
                <p class="text-slate-300 text-xs">Financier</p>
                <p class="text-2xl font-bold">{{ $kpis['taux_execution_financiere'] }}%</p>
            </div>
            <div>
                <p class="text-slate-300 text-xs">Alertes critiques</p>
                <p class="text-2xl font-bold text-red-400">{{ $kpis['alertes_critiques'] }}</p>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 text-center">
            <p class="text-3xl font-bold text-indigo-700">{{ $kpis['total_actions_prioritaires'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Actions prioritaires</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $kpis['actions_en_cours'] }}</p>
            <p class="text-xs text-gray-500 mt-1">En cours</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $kpis['activites_en_retard'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Activités en retard</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 text-center">
            <p class="text-3xl font-bold text-green-600">{{ number_format($kpis['taux_engagement'], 0) }}%</p>
            <p class="text-xs text-gray-500 mt-1">Taux d'engagement</p>
        </div>
    </div>
    <div id="chart-dep" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100"></div>
</div>
@endsection
@push('scripts')
<script>
const d = @json($graphes['departements'] ?? []);
const depEntries = Object.entries(d || {});
const labels = depEntries.map(([label]) => label);
const taux = depEntries.map(([, item]) => Number(item?.taux_moyen ?? 0));
const chartDep = document.querySelector('#chart-dep');

if (labels.length > 0) {
    new ApexCharts(chartDep, {
        chart: { type: 'radar', height: 300, toolbar: { show: false } },
        series: [{ name: 'Taux (%)', data: taux }],
        labels: labels,
        yaxis: { max: 100, min: 0 },
    }).render();
} else if (chartDep) {
    chartDep.innerHTML = '<p class="text-sm text-gray-400">Aucune donnée disponible pour les départements.</p>';
}
</script>
@endpush
