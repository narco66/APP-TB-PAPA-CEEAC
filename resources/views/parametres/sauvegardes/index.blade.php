@extends('layouts.app')
@section('title', 'Sauvegardes logiques')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <nav class="text-xs text-gray-500 mb-6 flex items-center gap-1.5">
        <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <a href="{{ route('parametres.hub') }}" class="hover:underline">Paramètres</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Sauvegardes logiques</span>
    </nav>

    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Sauvegardes & restauration</h1>
        <p class="text-sm text-gray-500 mt-1">Export et import des référentiels, paramètres et libellés au format JSON.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-amber-600">{{ $stats['parametres'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Paramètres</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['referentiels'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Référentiels</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['libelles'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Libellés métier</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Export --}}
        @can('parametres.sauvegardes.exporter')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">
                <i class="fas fa-download text-blue-400 mr-1.5"></i>Exporter
            </h2>
            <p class="text-xs text-gray-500 mb-5">Génère un fichier JSON horodaté contenant les données sélectionnées.</p>

            <div class="space-y-2.5">
                @foreach([
                    ['type' => 'parametres',   'label' => 'Paramètres généraux',     'icon' => 'fa-cog',       'color' => 'amber'],
                    ['type' => 'referentiels', 'label' => 'Référentiels institutionnels', 'icon' => 'fa-list-ul',   'color' => 'blue'],
                    ['type' => 'libelles',     'label' => 'Libellés métier',          'icon' => 'fa-language',  'color' => 'green'],
                    ['type' => 'tout',         'label' => 'Export complet (tout)',    'icon' => 'fa-archive',   'color' => 'indigo'],
                ] as $export)
                <form action="{{ route('parametres.sauvegardes.exporter', $export['type']) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-between px-4 py-3 border border-{{ $export['color'] }}-200 rounded-lg
                                   hover:bg-{{ $export['color'] }}-50 text-{{ $export['color'] }}-700 transition group">
                        <span class="flex items-center gap-2.5 text-sm font-medium">
                            <i class="fas {{ $export['icon'] }} text-{{ $export['color'] }}-400"></i>
                            {{ $export['label'] }}
                        </span>
                        <i class="fas fa-download text-xs opacity-50 group-hover:opacity-100 transition"></i>
                    </button>
                </form>
                @endforeach
            </div>

            <p class="mt-4 text-xs text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Les valeurs sensibles (mots de passe, clés) sont masquées dans l'export.
            </p>
        </div>
        @endcan

        {{-- Import --}}
        @can('parametres.sauvegardes.importer')
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5" x-data="{ fichierOk: false }">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">
                <i class="fas fa-upload text-red-400 mr-1.5"></i>Importer
            </h2>
            <p class="text-xs text-gray-500 mb-5">
                Restaure les paramètres depuis un fichier JSON exporté précédemment.
                Les entrées système protégées ne seront pas écrasées.
            </p>

            <div class="bg-red-50 border border-red-200 rounded-lg px-3 py-2.5 mb-4 text-xs text-red-700">
                <i class="fas fa-exclamation-triangle mr-1.5"></i>
                <strong>Attention :</strong> L'import écrase les valeurs existantes non-système.
                Effectuez un export avant tout import.
            </div>

            <form action="{{ route('parametres.sauvegardes.importer') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fichier JSON <span class="text-red-500">*</span></label>
                    <input type="file" name="fichier" accept=".json" required
                           @change="fichierOk = $event.target.files.length > 0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700
                                  file:mr-3 file:py-1 file:px-3 file:border-0 file:text-xs file:font-medium
                                  file:bg-indigo-50 file:text-indigo-700 file:rounded cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">Fichier .json — max 2 Mo</p>
                </div>

                <div x-show="fichierOk">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Confirmez en tapant <code class="bg-gray-100 px-1 rounded font-mono">IMPORTER</code>
                    </label>
                    <input type="text" name="confirmation"
                           pattern="IMPORTER" title="Tapez exactement IMPORTER"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-red-200"
                           placeholder="IMPORTER">
                </div>

                <button type="submit" x-show="fichierOk"
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition"
                        onclick="return confirm('Importer ce fichier ? Les données existantes seront remplacées.')">
                    <i class="fas fa-upload mr-1.5"></i>Lancer l'import
                </button>
            </form>
        </div>
        @else
        <div class="bg-gray-50 rounded-xl border border-gray-100 p-5 flex items-center justify-center">
            <div class="text-center text-gray-400">
                <i class="fas fa-lock text-3xl mb-2"></i>
                <p class="text-sm">Import réservé aux super administrateurs</p>
            </div>
        </div>
        @endcan

    </div>
</div>
@endsection
