@extends('layouts.app')
@section('title', 'Dashboard Commissaire')
@section('page-title', 'Pilotage Sectoriel — ' . ($departement?->libelle_court ?? '') . ' — ' . $papa->code)
@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-700 to-blue-900 rounded-2xl p-6 text-white">
        <h2 class="text-xl font-bold">{{ $papa->libelle }}</h2>
        <p class="text-blue-200 mt-1">{{ $departement?->libelle ?? 'Département non défini' }}</p>
        <div class="flex space-x-8 mt-3">
            <div>
                <p class="text-blue-300 text-xs">Physique global</p>
                <p class="text-2xl font-bold">{{ $kpisGlobaux['taux_execution_physique'] }}%</p>
            </div>
            <div>
                <p class="text-blue-300 text-xs">Financier global</p>
                <p class="text-2xl font-bold">{{ $kpisGlobaux['taux_execution_financiere'] }}%</p>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('activites.index') }}" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
            <i class="fas fa-tasks text-indigo-500 text-2xl"></i>
            <div><p class="font-semibold text-gray-700">Activités du département</p>
            <p class="text-sm text-gray-400">Suivre l'avancement</p></div>
        </a>
        <a href="{{ route('indicateurs.index') }}" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
            <i class="fas fa-chart-line text-green-500 text-2xl"></i>
            <div><p class="font-semibold text-gray-700">Indicateurs</p>
            <p class="text-sm text-gray-400">Valider les saisies</p></div>
        </a>
    </div>
</div>
@endsection
