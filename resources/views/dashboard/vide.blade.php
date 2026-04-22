@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-6">

    {{-- Illustration --}}
    <div class="relative mb-8">
        <div class="w-32 h-32 rounded-3xl bg-indigo-50 flex items-center justify-center mx-auto">
            <i class="fas fa-chart-line text-5xl text-indigo-200"></i>
        </div>
        <div class="absolute -top-2 -right-2 w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center border-2 border-white shadow-sm">
            <i class="fas fa-clock text-amber-400 text-sm"></i>
        </div>
    </div>

    {{-- Message --}}
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Aucun PAPA actif</h2>
    <p class="text-gray-400 text-sm max-w-sm mb-2">
        Aucun Plan d'Actions Prioritaires n'est actuellement en cours d'exécution.
    </p>
    <p class="text-gray-300 text-xs max-w-xs mb-8">
        Bonjour, <strong class="text-gray-400">{{ $user->nomComplet() }}</strong>.
        Contactez un administrateur si vous pensez qu'un PAPA devrait être actif.
    </p>

    {{-- Action --}}
    @can('papa.creer')
    <div class="flex flex-col sm:flex-row items-center gap-3">
        <a href="{{ route('papas.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition shadow-sm">
            <i class="fas fa-plus"></i> Créer un nouveau PAPA
        </a>
        <a href="{{ route('papas.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-sm rounded-xl transition shadow-sm border border-gray-200">
            <i class="fas fa-list"></i> Voir tous les PAPA
        </a>
    </div>
    @else
    <div class="inline-flex items-center gap-2 px-5 py-3 bg-gray-50 text-gray-500 text-sm rounded-xl border border-gray-200">
        <i class="fas fa-info-circle text-indigo-400"></i>
        Contactez votre administrateur pour activer un PAPA.
    </div>
    @endcan

</div>
@endsection
