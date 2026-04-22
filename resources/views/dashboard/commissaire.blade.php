@extends('layouts.app')
@section('title', 'Pilotage Sectoriel — ' . ($departement?->libelle_court ?? '') . ' — ' . $papa->code)
@section('page-title', 'Pilotage Sectoriel — ' . ($departement?->libelle_court ?? $papa->code))

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HERO                                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl shadow-lg"
         style="background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 50%, #2563eb 100%);">
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>
        <div class="relative px-6 py-6 lg:px-8">
            <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-widest bg-white/10 text-blue-200 border border-white/10">
                            <i class="fas fa-building text-[9px]"></i> Commissaire
                        </span>
                    </div>
                    <h1 class="text-xl lg:text-2xl font-bold text-white leading-tight mb-1">{{ $papa->libelle }}</h1>
                    <p class="text-blue-200 text-sm flex items-center gap-1.5">
                        <i class="fas fa-building text-blue-400 text-xs"></i>
                        {{ $departement?->libelle ?? 'Département non défini' }}
                    </p>
                    <p class="text-blue-300 text-xs mt-1 flex items-center gap-1.5">
                        <i class="fas fa-eye text-blue-400 text-xs"></i>
                        {{ $scopeLabel }}
                    </p>
                </div>
                <div class="flex items-stretch gap-4">
                    <div class="bg-white/10 border border-white/10 rounded-2xl px-5 py-4 text-center backdrop-blur-sm">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-blue-200 mb-1">Physique</p>
                        <p class="text-4xl font-black text-white leading-none">{{ number_format($kpisGlobaux['taux_execution_physique'], 0) }}<span class="text-2xl font-bold text-blue-200">%</span></p>
                        <div class="mt-2 h-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-blue-200" style="width:{{ min(100,$kpisGlobaux['taux_execution_physique']) }}%"></div>
                        </div>
                    </div>
                    <div class="bg-white/10 border border-white/10 rounded-2xl px-5 py-4 text-center backdrop-blur-sm">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-300 mb-1">Financier</p>
                        <p class="text-4xl font-black text-white leading-none">{{ number_format($kpisGlobaux['taux_execution_financiere'], 0) }}<span class="text-2xl font-bold text-emerald-300">%</span></p>
                        <div class="mt-2 h-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-400" style="width:{{ min(100,$kpisGlobaux['taux_execution_financiere']) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ACCÈS RAPIDES                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <a href="{{ route('activites.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-indigo-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-tasks text-indigo-500 text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Activités du département</p>
                <p class="text-xs text-gray-400 mt-0.5">Suivre l'avancement des activités</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-indigo-400 transition shrink-0"></i>
        </a>

        <a href="{{ route('indicateurs.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-chart-line text-emerald-500 text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Indicateurs de performance</p>
                <p class="text-xs text-gray-400 mt-0.5">Valider et suivre les saisies</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-emerald-400 transition shrink-0"></i>
        </a>

        <a href="{{ route('alertes.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-red-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 group-hover:bg-red-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-bell text-red-500 text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Alertes</p>
                <p class="text-xs text-gray-400 mt-0.5">Consulter les alertes actives</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-red-400 transition shrink-0"></i>
        </a>

        <a href="{{ route('documents.index') }}"
           class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-amber-200 hover:shadow-md transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-folder-open text-amber-500 text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">Documents</p>
                <p class="text-xs text-gray-400 mt-0.5">Consulter la GED</p>
            </div>
            <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-amber-400 transition shrink-0"></i>
        </a>

    </div>

</div>
@endsection
