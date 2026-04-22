@extends('layouts.app')
@section('title', 'Coordination Générale — ' . $papa->code)
@section('page-title', 'Coordination Générale — ' . $papa->code)

@section('content')
<div class="space-y-6 max-w-screen-2xl mx-auto">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HERO                                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl shadow-lg"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #334155 80%, #475569 100%);">
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>
        <div class="relative px-6 py-6 lg:px-8">
            <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-widest bg-white/10 text-slate-300 border border-white/10">
                            <i class="fas fa-sitemap text-[9px]"></i> Secrétaire Général
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold
                            {{ $papa->statut === 'en_cours' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-white/10 text-slate-300 border border-white/10' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $papa->statut === 'en_cours' ? 'bg-emerald-400 animate-pulse' : 'bg-slate-400' }}"></span>
                            {{ $papa->libelleStatut() }}
                        </span>
                    </div>
                    <h1 class="text-xl lg:text-2xl font-bold text-white leading-tight mb-2">{{ $papa->libelle }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-300">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-slate-500 text-xs"></i>
                            {{ $papa->date_debut->format('d/m/Y') }} – {{ $papa->date_fin->format('d/m/Y') }}
                        </span>
                        <span class="text-slate-600">·</span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-eye text-slate-500 text-xs"></i>
                            {{ $scopeLabel }}
                        </span>
                    </div>
                </div>
                <div class="flex items-stretch gap-4">
                    <div class="bg-white/10 border border-white/10 rounded-2xl px-5 py-4 text-center backdrop-blur-sm">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-300 mb-1">Physique</p>
                        <p class="text-4xl font-black text-white leading-none">{{ number_format($kpis['taux_execution_physique'], 0) }}<span class="text-2xl font-bold text-slate-400">%</span></p>
                        <div class="mt-2 h-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-slate-300" style="width:{{ min(100,$kpis['taux_execution_physique']) }}%"></div>
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
    {{-- KPI GRID                                                          --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-indigo-400 opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Actions prioritaires</p>
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-bullseye text-indigo-500 text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold text-indigo-700 leading-none">{{ $kpis['total_actions_prioritaires'] }}</div>
                <div class="mt-2 flex items-center gap-3 text-xs">
                    <span class="text-blue-600 font-semibold">{{ $kpis['actions_en_cours'] }} en cours</span>
                    <span class="text-gray-300">·</span>
                    <span class="text-emerald-600 font-semibold">{{ $kpis['actions_terminees'] }} term.</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full {{ $kpis['activites_en_retard'] > 0 ? 'bg-red-400' : 'bg-emerald-400' }} opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Activités en retard</p>
                    <span class="w-9 h-9 rounded-xl {{ $kpis['activites_en_retard'] > 0 ? 'bg-red-50 group-hover:bg-red-100' : 'bg-emerald-50 group-hover:bg-emerald-100' }} flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-clock {{ $kpis['activites_en_retard'] > 0 ? 'text-red-500' : 'text-emerald-500' }} text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold {{ $kpis['activites_en_retard'] > 0 ? 'text-red-600' : 'text-emerald-600' }} leading-none">{{ $kpis['activites_en_retard'] }}</div>
                <p class="text-xs text-gray-400 mt-2">sur {{ $kpis['total_activites'] }} activités</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-amber-400 opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Taux d'engagement</p>
                    <span class="w-9 h-9 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-hand-holding-dollar text-amber-500 text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold text-amber-600 leading-none">{{ number_format($kpis['taux_engagement'], 0) }}<span class="text-xl text-gray-400">%</span></div>
                <p class="text-xs text-gray-400 mt-2">{{ number_format($kpis['budget_engage'] / 1000000, 1) }} M / {{ number_format($kpis['budget_total'] / 1000000, 1) }} M XAF</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full {{ $kpis['alertes_critiques'] > 0 ? 'bg-red-400' : 'bg-emerald-400' }} opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Alertes actives</p>
                    <span class="w-9 h-9 rounded-xl {{ $kpis['alertes_critiques'] > 0 ? 'bg-red-50 group-hover:bg-red-100' : 'bg-emerald-50 group-hover:bg-emerald-100' }} flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-bell {{ $kpis['alertes_critiques'] > 0 ? 'text-red-500' : 'text-emerald-500' }} text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold {{ $kpis['alertes_critiques'] > 0 ? 'text-red-600' : 'text-emerald-600' }} leading-none">
                    {{ $kpis['alertes_critiques'] + ($kpis['alertes_attention'] ?? 0) }}
                </div>
                <div class="mt-2 flex items-center gap-2 text-xs">
                    @if($kpis['alertes_critiques'] > 0)
                        <span class="text-red-600 font-semibold">{{ $kpis['alertes_critiques'] }} crit.</span>
                    @else
                        <span class="text-emerald-600 font-semibold">Aucune critique</span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- GRAPHIQUES                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

        <div class="xl:col-span-3 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-chart-area text-slate-400"></i> Évolution de l'exécution
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Progression physique & financière par trimestre</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-gray-500"><span class="w-3 h-0.5 rounded-full bg-indigo-500 inline-block"></span> Physique</span>
                    <span class="flex items-center gap-1.5 text-gray-500"><span class="w-3 h-0.5 rounded-full bg-emerald-500 inline-block"></span> Financier</span>
                </div>
            </div>
            <div id="chart-evolution" style="min-height:260px;"></div>
        </div>

        <div class="xl:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="mb-5">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-radar text-slate-400"></i>
                    <i class="fas fa-building text-slate-400"></i> Performance départements
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Taux moyen d'exécution physique</p>
            </div>
            <div id="chart-dep" style="min-height:260px;"></div>
        </div>

    </div>

    {{-- Répartition activités --}}
    @if(!empty($graphes['activites']))
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-slate-400"></i> Répartition des activités
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Distribution par statut sur l'ensemble du PAPA</p>
            </div>
        </div>
        <div id="chart-activites" style="min-height:220px;"></div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sharedFont = 'ui-sans-serif, system-ui, sans-serif';

    // Évolution
    const evolution = @json($graphes['evolution']);
    new ApexCharts(document.querySelector('#chart-evolution'), {
        chart: { type: 'area', height: 260, toolbar: { show: false }, fontFamily: sharedFont },
        series: [
            { name: 'Physique (%)',  data: evolution.physique  },
            { name: 'Financier (%)', data: evolution.financier },
        ],
        xaxis: { categories: evolution.labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
        yaxis: { max: 100, min: 0, tickAmount: 5, labels: { formatter: v => v.toFixed(0) + '%', style: { fontSize: '11px', colors: '#9ca3af' } } },
        colors: ['#6366f1', '#10b981'],
        stroke: { curve: 'smooth', width: 2.5 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.20, opacityTo: 0.02, stops: [0, 100] } },
        markers: { size: 5, strokeWidth: 2.5, strokeColors: '#fff' },
        legend: { show: false },
        tooltip: { shared: true, intersect: false, y: { formatter: v => Number(v).toFixed(1) + '%' } },
        grid: { strokeDashArray: 4, borderColor: '#f1f5f9' },
    }).render();

    // Radar départements
    const depData    = @json($graphes['departements'] ?? []);
    const depEntries = Object.entries(depData || {});
    const depLabels  = depEntries.map(([k]) => k);
    const depTaux    = depEntries.map(([, v]) => Number(v?.taux_moyen ?? 0));
    const depEl      = document.querySelector('#chart-dep');
    if (depLabels.length > 0) {
        new ApexCharts(depEl, {
            chart: { type: 'radar', height: 260, toolbar: { show: false }, fontFamily: sharedFont },
            series: [{ name: 'Taux (%)', data: depTaux }],
            labels: depLabels,
            yaxis: { max: 100, min: 0, tickAmount: 5, labels: { style: { fontSize: '10px' } } },
            colors: ['#6366f1'],
            fill: { opacity: 0.12 },
            stroke: { width: 2 },
            markers: { size: 4, strokeWidth: 2, strokeColors: '#fff' },
            tooltip: { y: { formatter: v => v.toFixed(1) + '%' } },
        }).render();
    } else {
        depEl.innerHTML = '<div class="flex flex-col items-center justify-center h-40 text-gray-300 gap-2"><i class="fas fa-chart-pie text-3xl"></i><p class="text-sm">Aucune donnée</p></div>';
    }

    // Donut activités
    const actsEl = document.querySelector('#chart-activites');
    if (actsEl) {
        const acts     = @json($graphes['activites'] ?? {});
        const actKeys  = Object.keys(acts);
        const actVals  = Object.values(acts);
        new ApexCharts(actsEl, {
            chart: { type: 'donut', height: 220, toolbar: { show: false }, fontFamily: sharedFont },
            series: actVals,
            labels: actKeys.map(l => l.replace(/_/g, ' ')),
            colors: ['#94a3b8', '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#64748b'],
            legend: { position: 'right', fontSize: '12px' },
            plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#374151' } } } } },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: v => v + ' activité(s)' } },
        }).render();
    }
});
</script>
@endpush
