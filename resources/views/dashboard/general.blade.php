@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 col-span-2">
        <h2 class="font-bold text-gray-700 mb-2">{{ $papa->libelle }}</h2>
        <p class="text-sm text-gray-500">Taux physique : {{ $papa->taux_execution_physique }}% | Taux financier : {{ $papa->taux_execution_financiere }}%</p>
    </div>
    <div class="space-y-3">
        <a href="{{ route('activites.index') }}" class="block bg-indigo-50 rounded-xl p-4 hover:bg-indigo-100 transition">
            <span class="font-medium text-indigo-700">Mes activités</span>
        </a>
        <a href="{{ route('indicateurs.index') }}" class="block bg-green-50 rounded-xl p-4 hover:bg-green-100 transition">
            <span class="font-medium text-green-700">Indicateurs</span>
        </a>
    </div>
</div>
@endsection
