@extends('layouts.app')
@section('title', 'Vue Audit & Contrôle — ' . $papa->code)
@section('page-title', 'Vue Audit & Contrôle — ' . $papa->code)

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HERO                                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl shadow-lg"
         style="background: linear-gradient(135deg, #111827 0%, #1f2937 40%, #374151 80%, #4b5563 100%);">
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>
        <div class="relative px-6 py-6 lg:px-8">
            <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-widest bg-white/10 text-gray-300 border border-white/10">
                            <i class="fas fa-shield-halved text-[9px]"></i> Audit & Contrôle
                        </span>
                    </div>
                    <h1 class="text-xl lg:text-2xl font-bold text-white leading-tight mb-2">{{ $papa->libelle }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-300">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-gray-500 text-xs"></i>
                            {{ $papa->date_debut->format('d/m/Y') }} – {{ $papa->date_fin->format('d/m/Y') }}
                        </span>
                        <span class="text-gray-600">·</span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-eye text-gray-500 text-xs"></i>
                            {{ $scopeLabel }}
                        </span>
                    </div>
                </div>
                <div class="flex items-stretch gap-4">
                    <div class="bg-white/10 border border-white/10 rounded-2xl px-5 py-4 text-center backdrop-blur-sm">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-300 mb-1">Physique</p>
                        <p class="text-4xl font-black text-white leading-none">{{ number_format($kpis['taux_execution_physique'], 0) }}<span class="text-2xl font-bold text-gray-400">%</span></p>
                        <div class="mt-2 h-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gray-400" style="width:{{ min(100,$kpis['taux_execution_physique']) }}%"></div>
                        </div>
                    </div>
                    <div class="bg-white/10 border border-white/10 rounded-2xl px-5 py-4 text-center backdrop-blur-sm">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-300 mb-1">Financier</p>
                        <p class="text-4xl font-black text-white leading-none">{{ number_format($kpis['taux_execution_financiere'], 0) }}<span class="text-2xl font-bold text-emerald-300">%</span></p>
                        <div class="mt-2 h-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-400" style="width:{{ min(100,$kpis['taux_execution_financiere']) }}%"></div>
                        </div>
                    </div>
                    @if($kpis['alertes_critiques'] > 0)
                    <div class="bg-red-500/15 border border-red-400/25 rounded-2xl px-5 py-4 text-center backdrop-blur-sm">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-red-300 mb-1">Alertes crit.</p>
                        <p class="text-4xl font-black text-red-200 leading-none">{{ $kpis['alertes_critiques'] }}</p>
                        <p class="text-[10px] text-red-400 mt-1">à traiter</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- GRAPHIQUES                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Répartition activités --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="mb-5">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-gray-400"></i> Répartition des activités par statut
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Vue d'ensemble de l'état d'avancement</p>
            </div>
            <div id="chart-activites" style="min-height:260px;"></div>
        </div>

        {{-- Évolution --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-chart-line text-gray-400"></i> Tendance d'exécution
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Progression trimestrielle</p>
                </div>
            </div>
            <div id="chart-evolution" style="min-height:260px;"></div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ACCÈS RAPIDES                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <a href="{{ route('alertes.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-red-200 hover:shadow-md transition flex items-center gap-4">
            <div class="relative w-12 h-12 rounded-xl bg-red-50 group-hover:bg-red-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-bell text-red-500 text-lg"></i>
                @if($kpis['alertes_critiques'] > 0)
                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 rounded-full text-white text-[10px] flex items-center justify-center font-bold">
                    {{ min(9, $kpis['alertes_critiques']) }}{{ $kpis['alertes_critiques'] > 9 ? '+' : '' }}
                </span>
                @endif
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Alertes critiques</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $kpis['alertes_critiques'] }} alerte(s) à traiter</p>
            </div>
        </a>

        <a href="{{ route('documents.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-folder-open text-blue-500 text-lg"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">GED — Documents</p>
                <p class="text-xs text-gray-400 mt-0.5">Gestion électronique</p>
            </div>
        </a>

        <a href="{{ route('admin.audit-log') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-gray-300 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 group-hover:bg-gray-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-history text-gray-500 text-lg"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Journal d'audit</p>
                <p class="text-xs text-gray-400 mt-0.5">Traçabilité & historique</p>
            </div>
        </a>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sharedFont = 'ui-sans-serif, system-ui, sans-serif';

    // Donut activités
    const acts    = @json($graphes['activites'] ?? {});
    const actKeys = Object.keys(acts);
    const actVals = Object.values(acts);
    new ApexCharts(document.querySelector('#chart-activites'), {
        chart: { type: 'donut', height: 260, toolbar: { show: false }, fontFamily: sharedFont },
        series: actVals,
        labels: actKeys.map(l => l.replace(/_/g, ' ')),
        colors: ['#94a3b8', '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#64748b'],
        legend: { position: 'bottom', fontSize: '11px', markers: { width: 8, height: 8, radius: 8 } },
        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#374151' } } } } },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => v + ' activité(s)' } },
    }).render();

    // Évolution
    const evolution = @json($graphes['evolution'] ?? { labels:[], physique:[], financier:[] });
    new ApexCharts(document.querySelector('#chart-evolution'), {
        chart: { type: 'line', height: 260, toolbar: { show: false }, fontFamily: sharedFont },
        series: [
            { name: 'Physique (%)',  data: evolution.physique  },
            { name: 'Financier (%)', data: evolution.financier },
        ],
        xaxis: { categories: evolution.labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
        yaxis: { max: 100, min: 0, tickAmount: 5, labels: { formatter: v => v.toFixed(0) + '%', style: { fontSize: '11px', colors: '#9ca3af' } } },
        colors: ['#6366f1', '#10b981'],
        stroke: { curve: 'smooth', width: 2.5, dashArray: [0, 4] },
        markers: { size: 5, strokeWidth: 2.5, strokeColors: '#fff' },
        legend: { position: 'top', fontSize: '12px', markers: { width: 8, height: 8 } },
        tooltip: { shared: true, intersect: false, y: { formatter: v => Number(v).toFixed(1) + '%' } },
        grid: { strokeDashArray: 4, borderColor: '#f1f5f9' },
    }).render();
});
</script>
@endpush
