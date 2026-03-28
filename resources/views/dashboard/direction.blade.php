@extends('layouts.app')

@section('title', 'Dashboard Direction')
@section('page-title', 'Tableau de bord — ' . ($direction?->libelleAffichage() ?? 'Ma Direction'))

@section('content')
<div class="space-y-6">

    <!-- En-tête -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">{{ $direction?->libelle ?? 'Direction non définie' }}</h2>
            <p class="text-sm text-gray-500">
                @if($direction?->estTechnique()) <span class="text-blue-600 font-medium">Direction Technique</span>
                @else <span class="text-orange-600 font-medium">Direction d'Appui</span>
                @endif
                — {{ $direction?->departement?->libelle_court }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400">PAPA en cours</p>
            <p class="font-bold text-indigo-700">{{ $papa->code }}</p>
        </div>
    </div>

    <!-- KPIs -->
    @if(!empty($kpisDirection))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Taux moyen</p>
            <p class="text-3xl font-bold text-indigo-700">{{ number_format($kpisDirection['taux_moyen_activites'], 1) }}%</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Activités en cours</p>
            <p class="text-3xl font-bold text-blue-600">{{ $kpisDirection['activites_en_cours'] }}</p>
            <p class="text-xs text-gray-400">/ {{ $kpisDirection['total_activites'] }} total</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">En retard</p>
            <p class="text-3xl font-bold {{ $kpisDirection['activites_en_retard'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $kpisDirection['activites_en_retard'] }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Indicateurs en alerte</p>
            <p class="text-3xl font-bold {{ $kpisDirection['indicateurs_en_alerte'] > 0 ? 'text-orange-600' : 'text-green-600' }}">
                {{ $kpisDirection['indicateurs_en_alerte'] }}
            </p>
        </div>
    </div>

    <!-- Budget -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Situation budgétaire de la direction</h3>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500">Budget prévu</p>
                <p class="text-lg font-bold text-gray-800">{{ number_format($kpisDirection['budget_prevu'] / 1000000, 2) }} M XAF</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Engagé</p>
                <p class="text-lg font-bold text-blue-600">{{ number_format($kpisDirection['budget_engage'] / 1000000, 2) }} M XAF</p>
                @if($kpisDirection['budget_prevu'] > 0)
                    <p class="text-xs text-gray-400">{{ number_format($kpisDirection['budget_engage'] / $kpisDirection['budget_prevu'] * 100, 1) }}%</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-500">Décaissé</p>
                <p class="text-lg font-bold text-green-600">{{ number_format($kpisDirection['budget_consomme'] / 1000000, 2) }} M XAF</p>
                @if($kpisDirection['budget_prevu'] > 0)
                    <p class="text-xs text-gray-400">{{ number_format($kpisDirection['budget_consomme'] / $kpisDirection['budget_prevu'] * 100, 1) }}%</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Liens rapides -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('activites.index') }}" class="bg-indigo-50 hover:bg-indigo-100 rounded-xl p-4 flex items-center space-x-3 transition">
            <i class="fas fa-tasks text-indigo-600 text-xl"></i>
            <span class="font-medium text-indigo-700 text-sm">Mes activités</span>
        </a>
        <a href="{{ route('indicateurs.index') }}" class="bg-green-50 hover:bg-green-100 rounded-xl p-4 flex items-center space-x-3 transition">
            <i class="fas fa-chart-line text-green-600 text-xl"></i>
            <span class="font-medium text-green-700 text-sm">Mes indicateurs</span>
        </a>
        <a href="{{ route('documents.index') }}" class="bg-yellow-50 hover:bg-yellow-100 rounded-xl p-4 flex items-center space-x-3 transition">
            <i class="fas fa-folder-open text-yellow-600 text-xl"></i>
            <span class="font-medium text-yellow-700 text-sm">Documents</span>
        </a>
        <a href="{{ route('alertes.index') }}" class="bg-red-50 hover:bg-red-100 rounded-xl p-4 flex items-center space-x-3 transition">
            <i class="fas fa-bell text-red-600 text-xl"></i>
            <span class="font-medium text-red-700 text-sm">Alertes</span>
        </a>
    </div>
</div>
@endsection
