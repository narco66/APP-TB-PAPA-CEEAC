@extends('layouts.app')
@section('title', 'Vision Stratégique — ' . $papa->code)
@section('page-title', 'Vision Stratégique — ' . $papa->code)

@section('content')
<div class="space-y-6 max-w-screen-2xl mx-auto">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HERO — En-tête PAPA avec KPIs intégrés                           --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl shadow-lg"
         style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #3730a3 70%, #4338ca 100%);">

        {{-- Motif décoratif --}}
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-5"
             style="background: radial-gradient(circle, #818cf8, transparent 70%); transform: translate(30%, -30%);"></div>

        <div class="relative px-6 py-6 lg:px-8">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">

                {{-- Info PAPA --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-widest bg-white/10 text-indigo-200 border border-white/10">
                            <i class="fas fa-book-open text-[9px]"></i> PAPA
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold
                            {{ $papa->statut === 'en_cours' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-white/10 text-indigo-200 border border-white/10' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $papa->statut === 'en_cours' ? 'bg-emerald-400 animate-pulse' : 'bg-indigo-400' }}"></span>
                            {{ $papa->libelleStatut() }}
                        </span>
                        @if(($alertes['critique'] ?? 0) > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-red-500/25 text-red-300 border border-red-400/30 animate-pulse">
                            <i class="fas fa-exclamation-triangle text-[9px]"></i>
                            {{ $alertes['critique'] }} critique(s)
                        </span>
                        @endif
                    </div>
                    <h1 class="text-xl lg:text-2xl font-bold text-white leading-tight mb-2">
                        {{ $papa->libelle }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-indigo-200">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-indigo-400 text-xs"></i>
                            {{ $papa->date_debut->format('d/m/Y') }} – {{ $papa->date_fin->format('d/m/Y') }}
                        </span>
                        <span class="text-indigo-600">·</span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-eye text-indigo-400 text-xs"></i>
                            {{ $scopeLabel }}
                        </span>
                    </div>
                </div>

                {{-- Jauges exécution --}}
                <div class="flex items-stretch gap-4">
                    <div class="bg-white/10 border border-white/10 rounded-2xl px-5 py-4 text-center backdrop-blur-sm min-w-28">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-300 mb-1">Physique</p>
                        <p class="text-4xl font-black text-white leading-none">{{ number_format($kpis['taux_execution_physique'], 0) }}<span class="text-2xl font-bold text-indigo-300">%</span></p>
                        <div class="mt-2 h-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-indigo-300" style="width:{{ min(100,$kpis['taux_execution_physique']) }}%"></div>
                        </div>
                    </div>
                    <div class="bg-white/10 border border-white/10 rounded-2xl px-5 py-4 text-center backdrop-blur-sm min-w-28">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-300 mb-1">Financier</p>
                        <p class="text-4xl font-black text-white leading-none">{{ number_format($kpis['taux_execution_financiere'], 0) }}<span class="text-2xl font-bold text-emerald-300">%</span></p>
                        <div class="mt-2 h-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-400" style="width:{{ min(100,$kpis['taux_execution_financiere']) }}%"></div>
                        </div>
                    </div>
                    @if(($alertes['critique'] ?? 0) + ($alertes['attention'] ?? 0) > 0)
                    <div class="bg-red-500/15 border border-red-400/25 rounded-2xl px-5 py-4 text-center backdrop-blur-sm min-w-25">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-red-300 mb-1">Alertes</p>
                        <p class="text-4xl font-black text-red-200 leading-none">{{ ($alertes['critique'] ?? 0) + ($alertes['attention'] ?? 0) }}</p>
                        <p class="text-[10px] text-red-400 mt-1">dont {{ $alertes['critique'] ?? 0 }} crit.</p>
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

        {{-- Actions prioritaires --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-indigo-400 opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Actions prioritaires</p>
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-bullseye text-indigo-500 text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 leading-none">{{ $kpis['total_actions_prioritaires'] }}</div>
                <div class="mt-2 flex items-center gap-3 text-xs">
                    <span class="text-blue-600 font-semibold">{{ $kpis['actions_en_cours'] }} en cours</span>
                    <span class="text-gray-300">·</span>
                    <span class="text-emerald-600 font-semibold">{{ $kpis['actions_terminees'] }} terminées</span>
                </div>
            </div>
        </div>

        {{-- Activités --}}
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
                <p class="text-xs text-gray-400 mt-2">sur {{ $kpis['total_activites'] }} activités planifiées</p>
            </div>
        </div>

        {{-- Budget --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-amber-400 opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Budget décaissé</p>
                    <span class="w-9 h-9 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-wallet text-amber-500 text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 leading-none">{{ number_format($kpis['taux_decaissement'], 1) }}<span class="text-xl text-gray-400 ml-0.5">%</span></div>
                <p class="text-xs text-gray-400 mt-2">
                    {{ number_format($kpis['budget_decaisse'] / 1000000, 1) }} M
                    / {{ number_format($kpis['budget_total'] / 1000000, 1) }} M XAF
                </p>
                @if($kpis['budget_total'] > 0)
                <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-amber-400" style="width:{{ min(100,$kpis['taux_decaissement']) }}%"></div>
                </div>
                @endif
            </div>
        </div>

        {{-- Alertes --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full {{ ($alertes['critique'] ?? 0) > 0 ? 'bg-red-400' : 'bg-emerald-400' }} opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Alertes actives</p>
                    <span class="w-9 h-9 rounded-xl {{ ($alertes['critique'] ?? 0) > 0 ? 'bg-red-50 group-hover:bg-red-100' : 'bg-emerald-50 group-hover:bg-emerald-100' }} flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-bell {{ ($alertes['critique'] ?? 0) > 0 ? 'text-red-500' : 'text-emerald-500' }} text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold {{ ($alertes['critique'] ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600' }} leading-none">
                    {{ ($alertes['critique'] ?? 0) + ($alertes['attention'] ?? 0) }}
                </div>
                <div class="mt-2 flex items-center gap-3 text-xs">
                    @if(($alertes['critique'] ?? 0) > 0)
                    <span class="text-red-600 font-semibold">{{ $alertes['critique'] }} critique(s)</span>
                    @endif
                    @if(($alertes['attention'] ?? 0) > 0)
                    <span class="text-amber-600 font-semibold">{{ $alertes['attention'] }} attention</span>
                    @endif
                    @if(($alertes['critique'] ?? 0) + ($alertes['attention'] ?? 0) === 0)
                    <span class="text-emerald-600 font-semibold">Aucune alerte</span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- GRAPHIQUES                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

        {{-- Évolution trimestrielle (60%) --}}
        <div class="xl:col-span-3 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-chart-area text-indigo-400"></i> Évolution de l'exécution
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Progression physique & financière par trimestre</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-gray-500">
                        <span class="w-3 h-0.5 rounded-full bg-indigo-500 inline-block"></span> Physique
                    </span>
                    <span class="flex items-center gap-1.5 text-gray-500">
                        <span class="w-3 h-0.5 rounded-full bg-emerald-500 inline-block"></span> Financier
                    </span>
                </div>
            </div>
            <div id="chart-evolution" style="min-height: 260px;"></div>
        </div>

        {{-- Taux par département (40%) --}}
        <div class="xl:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="mb-5">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-building-columns text-indigo-400"></i> Par département
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Taux moyen d'exécution physique</p>
            </div>
            <div id="chart-departements" style="min-height: 260px;"></div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TABLEAU — Actions prioritaires                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-list-check text-indigo-400"></i> Actions prioritaires
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Top {{ $actionsPrioritaires->count() }} actions — périmètre {{ $scopeLabel }}</p>
            </div>
            <a href="{{ route('papas.show', $papa) }}"
               class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 font-semibold bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                Voir tout <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        @if($actionsPrioritaires->isEmpty())
        <div class="px-6 py-12 text-center">
            <i class="fas fa-inbox text-3xl text-gray-200 mb-3 block"></i>
            <p class="text-sm text-gray-400">Aucune action prioritaire dans votre périmètre.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Code</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Intitulé</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Département</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Progression</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($actionsPrioritaires as $ap)
                    @php
                        $taux  = (float) $ap->taux_realisation;
                        $barBg = $taux >= 75 ? '#22c55e' : ($taux >= 50 ? '#f59e0b' : '#ef4444');
                        $statut = strtolower($ap->statut ?? '');
                        $badgeMap = [
                            'en_cours'  => 'bg-blue-100 text-blue-700',
                            'termine'   => 'bg-emerald-100 text-emerald-700',
                            'en_retard' => 'bg-red-100 text-red-700',
                            'planifie'  => 'bg-gray-100 text-gray-600',
                            'abandonne' => 'bg-gray-100 text-gray-500',
                            'suspendu'  => 'bg-orange-100 text-orange-700',
                        ];
                        $badgeCls = $badgeMap[$statut] ?? ($badgeMap[$ap->couleurStatut()] ?? 'bg-gray-100 text-gray-600');
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-6 py-3.5">
                            <span class="font-mono text-xs font-semibold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded">{{ $ap->code }}</span>
                        </td>
                        <td class="px-6 py-3.5 max-w-xs">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $ap->libelle }}</p>
                            <p class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $ap->qualification ?? '') }}</p>
                        </td>
                        <td class="px-6 py-3.5 text-gray-500 text-sm hidden lg:table-cell">
                            {{ $ap->departement?->libelleAffichage() ?? '—' }}
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2 min-w-30">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full" style="width:{{ min(100,$taux) }}%; background:{{ $barBg }}"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-700 w-10 text-right tabular-nums">{{ number_format($taux, 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeCls }}">
                                {{ ucfirst(str_replace('_', ' ', $ap->statut ?? '')) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // ── Données ─────────────────────────────────────────────────────────
    const evolution  = @json($graphes['evolution']);
    const depData    = @json($graphes['departements'] ?? []);
    const depEntries = Object.entries(depData || {});
    const depLabels  = depEntries.map(([k]) => k);
    const depTaux    = depEntries.map(([, v]) => Number(v?.taux_moyen ?? 0));

    // ── Graphique : Évolution trimestrielle ─────────────────────────────
    new ApexCharts(document.querySelector('#chart-evolution'), {
        chart: {
            type: 'area', height: 260, toolbar: { show: false },
            fontFamily: 'ui-sans-serif, system-ui, sans-serif',
            animations: { enabled: true, speed: 600 },
        },
        series: [
            { name: 'Physique (%)',  data: evolution.physique  },
            { name: 'Financier (%)', data: evolution.financier },
        ],
        xaxis: {
            categories: evolution.labels,
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        yaxis: {
            max: 100, min: 0, tickAmount: 5,
            labels: { formatter: v => v.toFixed(0) + '%', style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        colors: ['#6366f1', '#10b981'],
        stroke: { curve: 'smooth', width: 2.5 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.20, opacityTo: 0.02, stops: [0, 100] },
        },
        markers: { size: 5, strokeWidth: 2.5, strokeColors: '#fff', hover: { size: 7 } },
        legend: { show: false },
        tooltip: {
            shared: true, intersect: false,
            y: { formatter: v => Number(v).toFixed(1) + '%' },
        },
        grid: { strokeDashArray: 4, borderColor: '#f1f5f9' },
    }).render();

    // ── Graphique : Taux par département ────────────────────────────────
    const depEl = document.querySelector('#chart-departements');
    if (depLabels.length > 0) {
        const depHeight = Math.max(220, depLabels.length * 48 + 60);
        depEl.style.minHeight = depHeight + 'px';
        new ApexCharts(depEl, {
            chart: {
                type: 'bar', height: depHeight, toolbar: { show: false },
                fontFamily: 'ui-sans-serif, system-ui, sans-serif',
            },
            plotOptions: {
                bar: { horizontal: true, borderRadius: 5, barHeight: '50%' },
            },
            series: [{ name: 'Taux moyen (%)', data: depTaux }],
            xaxis: {
                categories: depLabels,
                axisBorder: { show: false }, axisTicks: { show: false },
                labels: { formatter: v => v + '%', style: { fontSize: '10px', colors: '#9ca3af' } },
            },
            yaxis: {
                labels: { style: { fontSize: '11px', colors: '#374151' }, maxWidth: 150 },
            },
            colors: ['#6366f1'],
            dataLabels: {
                enabled: true,
                formatter: v => Number(v).toFixed(1) + '%',
                style: { fontSize: '11px', fontWeight: 700, colors: ['#374151'] },
                background: {
                    enabled: true, foreColor: '#374151', padding: 4,
                    borderRadius: 4, borderWidth: 1, borderColor: '#e0e7ff', opacity: 0.9,
                },
                dropShadow: { enabled: false },
            },
            tooltip: { x: { show: false }, y: { formatter: v => Number(v).toFixed(1) + '%' } },
            grid: { strokeDashArray: 4, borderColor: '#f1f5f9', yaxis: { lines: { show: false } } },
        }).render();
    } else {
        depEl.innerHTML = '<div class="flex flex-col items-center justify-center h-40 text-gray-300 gap-2"><i class="fas fa-chart-bar text-3xl"></i><p class="text-sm">Aucune donnée département</p></div>';
    }
});
</script>
@endpush
