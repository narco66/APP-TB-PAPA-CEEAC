@extends('layouts.app')

@section('title', 'Dashboard Direction')
@section('page-title', 'Tableau de bord — ' . ($direction?->libelleAffichage() ?? 'Ma Direction'))

@section('content')
<div class="space-y-6">

    {{-- ── En-tête ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    @if($direction?->estTechnique())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                            <i class="fas fa-cogs text-[9px]"></i> Direction Technique
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700">
                            <i class="fas fa-hands-helping text-[9px]"></i> Direction d'Appui
                        </span>
                    @endif
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">{{ $direction?->departement?->libelle_court }}</span>
                </div>
                <h2 class="text-lg font-bold text-gray-800">{{ $direction?->libelle ?? 'Direction non définie' }}</h2>
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

    {{-- ── KPIs ──────────────────────────────────────────────────────── --}}
    @if(!empty($kpisDirection))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Taux moyen</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <i class="fas fa-percent text-indigo-500 text-sm"></i>
                </span>
            </div>
            <p class="text-3xl font-bold text-indigo-700">{{ number_format($kpisDirection['taux_moyen_activites'], 1) }}<span class="text-xl text-gray-400">%</span></p>
            <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full bg-indigo-500 transition-all"
                     style="width: {{ min(100, $kpisDirection['taux_moyen_activites']) }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">En cours</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-spinner text-blue-500 text-sm"></i>
                </span>
            </div>
            <p class="text-3xl font-bold text-blue-600">{{ $kpisDirection['activites_en_cours'] }}</p>
            <p class="text-xs text-gray-400 mt-1">/ {{ $kpisDirection['total_activites'] }} total</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">En retard</span>
                <span class="w-8 h-8 rounded-xl {{ $kpisDirection['activites_en_retard'] > 0 ? 'bg-red-50' : 'bg-emerald-50' }} flex items-center justify-center">
                    <i class="fas fa-clock {{ $kpisDirection['activites_en_retard'] > 0 ? 'text-red-500' : 'text-emerald-500' }} text-sm"></i>
                </span>
            </div>
            <p class="text-3xl font-bold {{ $kpisDirection['activites_en_retard'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $kpisDirection['activites_en_retard'] }}
            </p>
            <p class="text-xs text-gray-400 mt-1">activité(s) en retard</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Indicateurs alerte</span>
                <span class="w-8 h-8 rounded-xl {{ $kpisDirection['indicateurs_en_alerte'] > 0 ? 'bg-orange-50' : 'bg-emerald-50' }} flex items-center justify-center">
                    <i class="fas fa-bell {{ $kpisDirection['indicateurs_en_alerte'] > 0 ? 'text-orange-500' : 'text-emerald-500' }} text-sm"></i>
                </span>
            </div>
            <p class="text-3xl font-bold {{ $kpisDirection['indicateurs_en_alerte'] > 0 ? 'text-orange-600' : 'text-emerald-600' }}">
                {{ $kpisDirection['indicateurs_en_alerte'] }}
            </p>
            <p class="text-xs text-gray-400 mt-1">indicateur(s) en alerte</p>
        </div>

    </div>

    {{-- ── Budget ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-wallet text-amber-400"></i> Situation budgétaire
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">Budget prévu</p>
                <p class="text-lg font-bold text-gray-800">{{ number_format($kpisDirection['budget_prevu'] / 1000000, 2) }} M XAF</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4">
                <p class="text-xs text-blue-400 font-medium uppercase tracking-wide mb-1">Engagé</p>
                <p class="text-lg font-bold text-blue-700">{{ number_format($kpisDirection['budget_engage'] / 1000000, 2) }} M XAF</p>
                @if($kpisDirection['budget_prevu'] > 0)
                <div class="mt-1.5 h-1 bg-blue-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ min(100, $kpisDirection['budget_engage'] / $kpisDirection['budget_prevu'] * 100) }}%"></div>
                </div>
                <p class="text-xs text-blue-400 mt-1">{{ number_format($kpisDirection['budget_engage'] / $kpisDirection['budget_prevu'] * 100, 1) }}%</p>
                @endif
            </div>
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-xs text-green-400 font-medium uppercase tracking-wide mb-1">Décaissé</p>
                <p class="text-lg font-bold text-green-700">{{ number_format($kpisDirection['budget_consomme'] / 1000000, 2) }} M XAF</p>
                @if($kpisDirection['budget_prevu'] > 0)
                <div class="mt-1.5 h-1 bg-green-200 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full" style="width: {{ min(100, $kpisDirection['budget_consomme'] / $kpisDirection['budget_prevu'] * 100) }}%"></div>
                </div>
                <p class="text-xs text-green-400 mt-1">{{ number_format($kpisDirection['budget_consomme'] / $kpisDirection['budget_prevu'] * 100, 1) }}%</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ── Accès rapides ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('activites.index') }}" class="group bg-indigo-50 hover:bg-indigo-100 rounded-2xl p-4 flex items-center gap-3 transition">
            <div class="w-9 h-9 rounded-xl bg-white/60 flex items-center justify-center shrink-0 group-hover:bg-white/80 transition">
                <i class="fas fa-tasks text-indigo-600 text-sm"></i>
            </div>
            <span class="font-medium text-indigo-700 text-sm">Mes activités</span>
        </a>
        <a href="{{ route('indicateurs.index') }}" class="group bg-green-50 hover:bg-green-100 rounded-2xl p-4 flex items-center gap-3 transition">
            <div class="w-9 h-9 rounded-xl bg-white/60 flex items-center justify-center shrink-0 group-hover:bg-white/80 transition">
                <i class="fas fa-chart-line text-green-600 text-sm"></i>
            </div>
            <span class="font-medium text-green-700 text-sm">Indicateurs</span>
        </a>
        <a href="{{ route('documents.index') }}" class="group bg-amber-50 hover:bg-amber-100 rounded-2xl p-4 flex items-center gap-3 transition">
            <div class="w-9 h-9 rounded-xl bg-white/60 flex items-center justify-center shrink-0 group-hover:bg-white/80 transition">
                <i class="fas fa-folder-open text-amber-600 text-sm"></i>
            </div>
            <span class="font-medium text-amber-700 text-sm">Documents</span>
        </a>
        <a href="{{ route('alertes.index') }}" class="group bg-red-50 hover:bg-red-100 rounded-2xl p-4 flex items-center gap-3 transition">
            <div class="w-9 h-9 rounded-xl bg-white/60 flex items-center justify-center shrink-0 group-hover:bg-white/80 transition">
                <i class="fas fa-bell text-red-600 text-sm"></i>
            </div>
            <span class="font-medium text-red-700 text-sm">Alertes</span>
        </a>
    </div>

</div>
@endsection
