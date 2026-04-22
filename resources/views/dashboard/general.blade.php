@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- CARTE PAPA                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-widest mb-1.5">
                    <i class="fas fa-book-open text-indigo-300 mr-1"></i> Plan d'Actions Prioritaires actif
                </p>
                <h2 class="text-lg font-bold text-gray-800 leading-tight truncate">{{ $papa->libelle }}</h2>
                <p class="text-xs text-gray-400 mt-1 flex items-center gap-1.5">
                    <i class="fas fa-eye text-gray-300"></i> Périmètre : {{ $scopeLabel }}
                </p>
            </div>
            <div class="flex items-stretch gap-3 shrink-0">
                <div class="text-center bg-indigo-50 rounded-xl px-4 py-3">
                    <p class="text-[10px] text-indigo-400 font-semibold uppercase tracking-wide mb-0.5">Physique</p>
                    <p class="text-2xl font-bold text-indigo-700 leading-none">{{ number_format($papa->taux_execution_physique, 0) }}<span class="text-base text-indigo-300">%</span></p>
                </div>
                <div class="text-center bg-emerald-50 rounded-xl px-4 py-3">
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase tracking-wide mb-0.5">Financier</p>
                    <p class="text-2xl font-bold text-emerald-700 leading-none">{{ number_format($papa->taux_execution_financiere, 0) }}<span class="text-base text-emerald-300">%</span></p>
                </div>
            </div>
        </div>

        {{-- Barres de progression --}}
        <div class="mt-5 space-y-2">
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400 w-20 shrink-0">Physique</span>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    @php $pPhy = min(100, $papa->taux_execution_physique); $cPhy = $pPhy >= 75 ? '#22c55e' : ($pPhy >= 50 ? '#f59e0b' : '#ef4444'); @endphp
                    <div class="h-full rounded-full" style="width:{{ $pPhy }}%; background:{{ $cPhy }}"></div>
                </div>
                <span class="text-xs font-bold text-gray-600 w-10 text-right tabular-nums">{{ number_format($pPhy, 0) }}%</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400 w-20 shrink-0">Financier</span>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    @php $pFin = min(100, $papa->taux_execution_financiere); @endphp
                    <div class="h-full rounded-full bg-emerald-500" style="width:{{ $pFin }}%"></div>
                </div>
                <span class="text-xs font-bold text-gray-600 w-10 text-right tabular-nums">{{ number_format($pFin, 0) }}%</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- NAVIGATION RAPIDE                                                 --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <a href="{{ route('activites.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-indigo-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-tasks text-indigo-500 text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Mes activités</p>
                <p class="text-xs text-gray-400 mt-0.5">Consulter et mettre à jour</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0 group-hover:text-indigo-400 transition"></i>
        </a>

        <a href="{{ route('indicateurs.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-chart-line text-emerald-500 text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Indicateurs</p>
                <p class="text-xs text-gray-400 mt-0.5">Saisir et valider les valeurs</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0 group-hover:text-emerald-400 transition"></i>
        </a>

        @can('workflow.voir')
        <a href="{{ route('workflows.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-amber-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-sitemap text-amber-500 text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Workflows institutionnels</p>
                <p class="text-xs text-gray-400 mt-0.5">Suivre les processus</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0 group-hover:text-amber-400 transition"></i>
        </a>
        @endcan

        @can('decision.voir')
        <a href="{{ route('decisions.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-rose-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 group-hover:bg-rose-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-gavel text-rose-500 text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Décisions et arbitrages</p>
                <p class="text-xs text-gray-400 mt-0.5">Consulter les décisions</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0 group-hover:text-rose-400 transition"></i>
        </a>
        @endcan

    </div>

</div>
@endsection
