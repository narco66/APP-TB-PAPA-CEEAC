@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('content')
<div class="flex flex-col items-center justify-center min-h-96 text-center">
    <div class="text-gray-300 text-8xl mb-6"><i class="fas fa-chart-line"></i></div>
    <h2 class="text-xl font-bold text-gray-600 mb-2">Aucun PAPA actif</h2>
    <p class="text-gray-400 mb-6">Aucun Plan d'Action Prioritaire n'est en cours d'exécution.</p>
    @can('papa.creer')
    <a href="{{ route('papas.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium">
        <i class="fas fa-plus mr-2"></i>Créer le PAPA
    </a>
    @endcan
</div>
@endsection
