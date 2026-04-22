@extends('layouts.app')
@section('title', 'Mon suivi — ' . ($direction?->libelleAffichage() ?? 'Mon service'))
@section('page-title', 'Mon suivi — ' . ($direction?->libelleAffichage() ?? 'Mon service'))

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- EN-TÊTE                                                           --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    @if($direction?->estTechnique())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                            <i class="fas fa-cogs text-[9px]"></i> Direction Technique
                        </span>
                    @elseif($direction)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700">
                            <i class="fas fa-hands-helping text-[9px]"></i> Direction d'Appui
                        </span>
                    @endif
                    @if($direction?->departement)
                        <span class="text-gray-300">·</span>
                        <span class="text-xs text-gray-400">{{ $direction->departement->libelle_court }}</span>
                    @endif
                </div>
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $direction?->libelle ?? 'Aucune direction rattachée' }}
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-eye mr-1"></i> Périmètre : {{ $scopeLabel }}
                </p>
            </div>
            <div class="text-right bg-indigo-50 rounded-xl px-4 py-2.5">
                <p class="text-xs text-indigo-400 font-medium uppercase tracking-wide">PAPA en cours</p>
                <p class="font-bold text-indigo-700 text-lg">{{ $papa->code }}</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- KPIs (si direction disponible)                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if(!empty($kpisDirection))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-indigo-400 opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Taux moyen</p>
                    <span class="w-9 h-9 rounded-xl bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-percent text-indigo-500 text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold text-indigo-700 leading-none">{{ number_format($kpisDirection['taux_moyen_activites'], 1) }}<span class="text-xl text-gray-400">%</span></div>
                <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-indigo-500" style="width:{{ min(100, $kpisDirection['taux_moyen_activites']) }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-blue-400 opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">En cours</p>
                    <span class="w-9 h-9 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-spinner text-blue-500 text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold text-blue-600 leading-none">{{ $kpisDirection['activites_en_cours'] }}</div>
                <p class="text-xs text-gray-400 mt-2">/ {{ $kpisDirection['total_activites'] }} activités</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full {{ $kpisDirection['activites_en_retard'] > 0 ? 'bg-red-400' : 'bg-emerald-400' }} opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">En retard</p>
                    <span class="w-9 h-9 rounded-xl {{ $kpisDirection['activites_en_retard'] > 0 ? 'bg-red-50 group-hover:bg-red-100' : 'bg-emerald-50 group-hover:bg-emerald-100' }} flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-clock {{ $kpisDirection['activites_en_retard'] > 0 ? 'text-red-500' : 'text-emerald-500' }} text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold {{ $kpisDirection['activites_en_retard'] > 0 ? 'text-red-600' : 'text-emerald-600' }} leading-none">
                    {{ $kpisDirection['activites_en_retard'] }}
                </div>
                <p class="text-xs text-gray-400 mt-2">activité(s) en retard</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full {{ $kpisDirection['indicateurs_en_alerte'] > 0 ? 'bg-orange-400' : 'bg-emerald-400' }} opacity-[0.06] group-hover:opacity-[0.10] transition"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pr-2">Indicateurs alerte</p>
                    <span class="w-9 h-9 rounded-xl {{ $kpisDirection['indicateurs_en_alerte'] > 0 ? 'bg-orange-50 group-hover:bg-orange-100' : 'bg-emerald-50 group-hover:bg-emerald-100' }} flex items-center justify-center shrink-0 transition">
                        <i class="fas fa-bell {{ $kpisDirection['indicateurs_en_alerte'] > 0 ? 'text-orange-500' : 'text-emerald-500' }} text-sm"></i>
                    </span>
                </div>
                <div class="text-3xl font-bold {{ $kpisDirection['indicateurs_en_alerte'] > 0 ? 'text-orange-600' : 'text-emerald-600' }} leading-none">
                    {{ $kpisDirection['indicateurs_en_alerte'] }}
                </div>
                <p class="text-xs text-gray-400 mt-2">indicateur(s) en alerte</p>
            </div>
        </div>

    </div>

    {{-- Budget --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-wallet text-amber-400"></i> Situation budgétaire
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Budget prévu</p>
                <p class="text-lg font-bold text-gray-800">{{ number_format($kpisDirection['budget_prevu'] / 1000000, 2) }} M XAF</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4">
                <p class="text-xs text-blue-400 font-semibold uppercase tracking-wide mb-1">Engagé</p>
                <p class="text-lg font-bold text-blue-700">{{ number_format($kpisDirection['budget_engage'] / 1000000, 2) }} M XAF</p>
                @if($kpisDirection['budget_prevu'] > 0)
                <div class="mt-2 h-1 bg-blue-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width:{{ min(100, $kpisDirection['budget_engage'] / $kpisDirection['budget_prevu'] * 100) }}%"></div>
                </div>
                <p class="text-xs text-blue-400 mt-1">{{ number_format($kpisDirection['budget_engage'] / $kpisDirection['budget_prevu'] * 100, 1) }}%</p>
                @endif
            </div>
            <div class="bg-emerald-50 rounded-xl p-4">
                <p class="text-xs text-emerald-400 font-semibold uppercase tracking-wide mb-1">Décaissé</p>
                <p class="text-lg font-bold text-emerald-700">{{ number_format($kpisDirection['budget_consomme'] / 1000000, 2) }} M XAF</p>
                @if($kpisDirection['budget_prevu'] > 0)
                <div class="mt-2 h-1 bg-emerald-200 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width:{{ min(100, $kpisDirection['budget_consomme'] / $kpisDirection['budget_prevu'] * 100) }}%"></div>
                </div>
                <p class="text-xs text-emerald-400 mt-1">{{ number_format($kpisDirection['budget_consomme'] / $kpisDirection['budget_prevu'] * 100, 1) }}%</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ACCÈS RAPIDES                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('activites.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-indigo-200 hover:shadow-md transition flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-tasks text-indigo-500"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Mes activités</p>
                <p class="text-xs text-gray-400">Consulter & mettre à jour</p>
            </div>
        </a>
        <a href="{{ route('indicateurs.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-chart-line text-emerald-500"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Indicateurs</p>
                <p class="text-xs text-gray-400">Saisir les valeurs</p>
            </div>
        </a>
        <a href="{{ route('documents.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-amber-200 hover:shadow-md transition flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-folder-open text-amber-500"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Documents</p>
                <p class="text-xs text-gray-400">GED</p>
            </div>
        </a>
        <a href="{{ route('alertes.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-red-200 hover:shadow-md transition flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 group-hover:bg-red-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-bell text-red-500"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Alertes</p>
                <p class="text-xs text-gray-400">Consulter</p>
            </div>
        </a>
    </div>

</div>
@endsection
