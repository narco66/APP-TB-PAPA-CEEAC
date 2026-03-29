@extends('layouts.app')

@section('title', 'Centre de paramétrage')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Paramètres</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-cogs text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Centre de paramétrage</h1>
                    <p class="text-sm text-gray-500">Configuration générale de l'application TB-PAPA-CEEAC</p>
                </div>
            </div>
            @can('parametres.technique.modifier')
            <form action="{{ route('parametres.toggle-maintenance') }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition
                        {{ $stats['maintenance'] ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas {{ $stats['maintenance'] ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-2"></i>
                    {{ $stats['maintenance'] ? 'Désactiver maintenance' : 'Activer maintenance' }}
                </button>
            </form>
            @endcan
        </div>
    </div>

    {{-- Bannière maintenance --}}
    @if($stats['maintenance'])
    <div class="bg-amber-50 border border-amber-300 rounded-xl p-4 flex items-center space-x-3">
        <i class="fas fa-exclamation-triangle text-amber-500 text-lg"></i>
        <div>
            <p class="text-sm font-semibold text-amber-800">Mode maintenance actif</p>
            <p class="text-xs text-amber-700">L'application est actuellement en mode maintenance. Les utilisateurs non-administrateurs ne peuvent pas se connecter.</p>
        </div>
    </div>
    @endif

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Paramètres</span>
                <div class="h-8 w-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sliders text-indigo-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_parametres']) }}</p>
            <p class="text-xs text-gray-500 mt-1">Paramètres configurés</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Référentiels</span>
                <div class="h-8 w-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list-check text-green-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['referentiels_actifs']) }}</p>
            <p class="text-xs text-gray-500 mt-1">Actifs sur {{ number_format($stats['referentiels']) }} total</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">PAPA actif</span>
                <div class="h-8 w-8 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book-open text-amber-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['papa_actif'] ?? '—' }}</p>
            <p class="text-xs text-gray-500 mt-1">En exécution</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Libellés</span>
                <div class="h-8 w-8 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tag text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['libelles_modifies']) }}</p>
            <p class="text-xs text-gray-500 mt-1">Libellés personnalisés</p>
        </div>
    </div>

    {{-- Modules --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Modules de configuration</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Paramètres généraux --}}
            @can('parametres.generaux.voir')
            <a href="{{ route('parametres.generaux') }}"
               class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-indigo-300 hover:shadow-md transition group">
                <div class="flex items-start space-x-4">
                    <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-200 transition">
                        <i class="fas fa-gear text-indigo-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-indigo-700">Paramètres généraux</h3>
                        <p class="text-xs text-gray-500 mt-1">Nom, organisation, langue, fuseau horaire, devise, formats</p>
                        <span class="mt-2 inline-flex items-center text-xs text-indigo-600 font-medium">
                            Configurer <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endcan

            {{-- Gestion PAPA --}}
            @can('parametres.papa.voir')
            <a href="{{ route('parametres.papa.index') }}"
               class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-amber-300 hover:shadow-md transition group">
                <div class="flex items-start space-x-4">
                    <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-amber-200 transition">
                        <i class="fas fa-book text-amber-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-amber-700">Gestion des PAPA</h3>
                        <p class="text-xs text-gray-500 mt-1">Définir le PAPA actif, verrouiller, archiver les plans d'action</p>
                        <span class="mt-2 inline-flex items-center text-xs text-amber-600 font-medium">
                            Gérer <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endcan

            {{-- Référentiels --}}
            @can('parametres.referentiels.voir')
            <a href="{{ route('parametres.referentiels.index') }}"
               class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-green-300 hover:shadow-md transition group">
                <div class="flex items-start space-x-4">
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition">
                        <i class="fas fa-list-ul text-green-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-green-700">Référentiels métier</h3>
                        <p class="text-xs text-gray-500 mt-1">Catégories, types, unités, sources, fréquences — listes de valeurs</p>
                        <span class="mt-2 inline-flex items-center text-xs text-green-600 font-medium">
                            Gérer <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endcan

            {{-- Libellés métier --}}
            @can('parametres.libelles.voir')
            <a href="{{ route('parametres.libelles.index') }}"
               class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-purple-300 hover:shadow-md transition group">
                <div class="flex items-start space-x-4">
                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-purple-200 transition">
                        <i class="fas fa-tags text-purple-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-purple-700">Libellés métier</h3>
                        <p class="text-xs text-gray-500 mt-1">Personnaliser les termes et labels affichés dans l'application</p>
                        <span class="mt-2 inline-flex items-center text-xs text-purple-600 font-medium">
                            Personnaliser <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endcan

            {{-- Journal des modifications --}}
            @can('parametres.journal.voir')
            <a href="{{ route('parametres.journal') }}"
               class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-gray-300 hover:shadow-md transition group">
                <div class="flex items-start space-x-4">
                    <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-gray-200 transition">
                        <i class="fas fa-history text-gray-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-gray-700">Journal des modifications</h3>
                        <p class="text-xs text-gray-500 mt-1">Historique de toutes les modifications apportées aux paramètres</p>
                        <span class="mt-2 inline-flex items-center text-xs text-gray-600 font-medium">
                            Consulter <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endcan

            {{-- Archives PAPA --}}
            @can('parametres.papa.voir')
            <a href="{{ route('parametres.papa.archives') }}"
               class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-gray-300 hover:shadow-md transition group">
                <div class="flex items-start space-x-4">
                    <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-gray-200 transition">
                        <i class="fas fa-archive text-gray-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-gray-700">Archives PAPA</h3>
                        <p class="text-xs text-gray-500 mt-1">Consulter les plans d'action archivés et leur historique</p>
                        <span class="mt-2 inline-flex items-center text-xs text-gray-600 font-medium">
                            Consulter <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endcan

        </div>
    </div>

</div>
@endsection
