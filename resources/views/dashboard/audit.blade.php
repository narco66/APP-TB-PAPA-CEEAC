@extends('layouts.app')
@section('title', 'Dashboard Audit')
@section('page-title', 'Vue Audit & Contrôle — ' . $papa->code)
@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-2xl p-6 text-white">
        <h2 class="text-xl font-bold">{{ $papa->libelle }} — Vue Auditeur</h2>
        <div class="flex space-x-8 mt-3">
            <div><p class="text-gray-300 text-xs">Physique</p><p class="text-2xl font-bold">{{ $kpis['taux_execution_physique'] }}%</p></div>
            <div><p class="text-gray-300 text-xs">Financier</p><p class="text-2xl font-bold">{{ $kpis['taux_execution_financiere'] }}%</p></div>
            <div><p class="text-gray-300 text-xs">Alertes critiques</p><p class="text-2xl font-bold text-red-400">{{ $kpis['alertes_critiques'] }}</p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-700 mb-4">Répartition des activités par statut</h3>
        <div id="chart-activites" style="min-height: 250px;"></div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <a href="{{ route('alertes.index') }}" class="bg-red-50 rounded-xl p-4 text-center hover:bg-red-100 transition">
            <p class="text-2xl font-bold text-red-600">{{ $kpis['alertes_critiques'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Alertes critiques</p>
        </a>
        <a href="{{ route('documents.index') }}" class="bg-blue-50 rounded-xl p-4 text-center hover:bg-blue-100 transition">
            <i class="fas fa-folder-open text-2xl text-blue-600"></i>
            <p class="text-xs text-gray-500 mt-1">GED</p>
        </a>
        <a href="#" class="bg-gray-50 rounded-xl p-4 text-center hover:bg-gray-100 transition">
            <i class="fas fa-history text-2xl text-gray-600"></i>
            <p class="text-xs text-gray-500 mt-1">Journal d'audit</p>
        </a>
    </div>
</div>
@endsection
@push('scripts')
<script>
const acts = @json($graphes['activites']);
const actLabels = Object.keys(acts);
const actVals = Object.values(acts);
new ApexCharts(document.querySelector('#chart-activites'), {
    chart: { type: 'donut', height: 250 },
    series: actVals,
    labels: actLabels.map(l => l.replace(/_/g, ' ')),
    colors: ['#94a3b8', '#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#64748b'],
}).render();
</script>
@endpush
