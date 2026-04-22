@extends('layouts.app')

@section('title', 'Référentiels métier')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Référentiels</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ search: '' }">
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list-ul text-green-600"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Référentiels métier</h1>
                    <p class="text-sm text-gray-500">Listes de valeurs utilisées dans l'application</p>
                </div>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" x-model="search" placeholder="Filtrer les référentiels..."
                       class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 w-56">
            </div>
        </div>
    </div>

    {{-- Statistiques globales --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats->sum('total') }}</p>
            <p class="text-xs text-gray-500 mt-1">Entrées total</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats->sum('actifs') }}</p>
            <p class="text-xs text-gray-500 mt-1">Entrées actives</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-indigo-600">{{ $stats->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Types de référentiels</p>
        </div>
    </div>

    {{-- Grille des types --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($stats as $item)
        <div x-show="search === '' || '{{ strtolower($item['libelle']) }}'.includes(search.toLowerCase()) || '{{ $item['type'] }}'.includes(search.toLowerCase())"
             class="bg-white rounded-xl shadow-sm border border-gray-100 hover:border-green-300 hover:shadow-md transition group">
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-green-700 truncate">
                            {{ $item['libelle'] }}
                        </h3>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $item['type'] }}</p>
                    </div>
                    <div class="ml-3 flex-shrink-0">
                        @if($item['total'] > 0)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                {{ $item['actifs'] }}/{{ $item['total'] }}
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">vide</span>
                        @endif
                    </div>
                </div>

                @if($item['total'] > 0)
                <div class="mb-3">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Actifs</span>
                        <span>{{ $item['total'] > 0 ? round($item['actifs'] / $item['total'] * 100) : 0 }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full"
                             style="width: {{ $item['total'] > 0 ? round($item['actifs'] / $item['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endif

                <a href="{{ route('parametres.referentiels.liste', $item['type']) }}"
                   class="w-full flex items-center justify-center px-3 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-xs font-medium transition">
                    <i class="fas fa-edit mr-2"></i>Gérer ce référentiel
                </a>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
