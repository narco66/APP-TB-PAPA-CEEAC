@extends('layouts.app')

@section('title', $rapport->titre)

@section('content')

<!-- En-tête -->
<div class="flex items-start justify-between mb-6">
    <div>
        <nav class="text-sm text-gray-500 mb-2 flex items-center gap-2">
            <a href="{{ route('rapports.index') }}" class="hover:text-indigo-600">Rapports</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 font-medium">{{ $rapport->titre }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">{{ $rapport->titre }}</h1>
        <div class="flex items-center gap-3 mt-2 flex-wrap">
            @php $couleur = $rapport->couleurStatut(); @endphp
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $couleur === 'gray'   ? 'bg-gray-100 text-gray-700'     : '' }}
                {{ $couleur === 'blue'   ? 'bg-blue-100 text-blue-700'     : '' }}
                {{ $couleur === 'green'  ? 'bg-green-100 text-green-700'   : '' }}
                {{ $couleur === 'indigo' ? 'bg-indigo-100 text-indigo-700' : '' }}">
                {{ ucfirst($rapport->statut) }}
            </span>
            <span class="text-sm text-gray-500">
                <i class="fas fa-calendar mr-1"></i> {{ $rapport->periode_couverte }}
            </span>
            <span class="text-sm text-gray-500">
                <i class="fas fa-tag mr-1"></i> {{ ucfirst($rapport->type_rapport) }}
            </span>
            @if($rapport->papa)
            <a href="{{ route('papas.show', $rapport->papa) }}" class="text-sm text-indigo-600 hover:underline">
                <i class="fas fa-link mr-1"></i> {{ $rapport->papa->code }}
            </a>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2">
        <!-- Export PDF -->
        <a href="{{ route('rapports.export-pdf', $rapport) }}"
           class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>

        @can('papa.modifier')
        @if($rapport->statut === 'brouillon')
        <form method="POST" action="{{ route('rapports.valider', $rapport) }}" class="inline">
            @csrf
            <button class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">
                <i class="fas fa-check"></i> Valider
            </button>
        </form>
        @endif
        @if($rapport->statut === 'valide')
        <form method="POST" action="{{ route('rapports.publier', $rapport) }}" class="inline">
            @csrf
            <button class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                <i class="fas fa-globe"></i> Publier
            </button>
        </form>
        @endif
        @endcan
    </div>
</div>

<!-- Flash -->
@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<!-- KPIs instantanés -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
        <div class="text-3xl font-bold text-indigo-700">{{ $rapport->taux_execution_physique }}%</div>
        <div class="text-xs text-gray-500 mt-1">Exécution physique</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
        <div class="text-3xl font-bold text-green-600">{{ $rapport->taux_execution_financiere }}%</div>
        <div class="text-xs text-gray-500 mt-1">Exécution financière</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
        <div class="text-xl font-bold text-gray-700">{{ $rapport->redigePar?->nomComplet() ?? '—' }}</div>
        <div class="text-xs text-gray-500 mt-1">Rédigé par</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
        <div class="text-xl font-bold text-gray-700">{{ $rapport->created_at->format('d/m/Y') }}</div>
        <div class="text-xs text-gray-500 mt-1">Date de création</div>
    </div>
</div>

<!-- Métadonnées validation -->
@if($rapport->valide_le || $rapport->publie_le)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <div class="flex flex-wrap gap-6 text-sm">
        @if($rapport->valide_le)
        <div>
            <span class="text-gray-500">Validé le :</span>
            <span class="font-medium ml-1">{{ $rapport->valide_le->format('d/m/Y à H:i') }}</span>
        </div>
        @endif
        @if($rapport->publie_le)
        <div>
            <span class="text-gray-500">Publié le :</span>
            <span class="font-medium ml-1">{{ $rapport->publie_le->format('d/m/Y à H:i') }}</span>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Contenu narratif -->
<div class="space-y-4">

    @if($rapport->faits_saillants)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-indigo-50 border-b border-indigo-100 px-5 py-3">
            <h2 class="text-sm font-semibold text-indigo-800">
                <i class="fas fa-star mr-2"></i> Faits saillants
            </h2>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $rapport->faits_saillants }}</p>
        </div>
    </div>
    @endif

    @if($rapport->difficultes_rencontrees)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-red-50 border-b border-red-100 px-5 py-3">
            <h2 class="text-sm font-semibold text-red-800">
                <i class="fas fa-exclamation-triangle mr-2"></i> Difficultés rencontrées
            </h2>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $rapport->difficultes_rencontrees }}</p>
        </div>
    </div>
    @endif

    @if($rapport->recommandations)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-amber-50 border-b border-amber-100 px-5 py-3">
            <h2 class="text-sm font-semibold text-amber-800">
                <i class="fas fa-lightbulb mr-2"></i> Recommandations
            </h2>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $rapport->recommandations }}</p>
        </div>
    </div>
    @endif

    @if($rapport->perspectives)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-green-50 border-b border-green-100 px-5 py-3">
            <h2 class="text-sm font-semibold text-green-800">
                <i class="fas fa-binoculars mr-2"></i> Perspectives
            </h2>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $rapport->perspectives }}</p>
        </div>
    </div>
    @endif

    @if(!$rapport->faits_saillants && !$rapport->difficultes_rencontrees && !$rapport->recommandations && !$rapport->perspectives)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center text-gray-400">
        <i class="fas fa-file-alt text-3xl mb-2"></i>
        <p>Aucun contenu narratif renseigné pour ce rapport.</p>
    </div>
    @endif

</div>

<!-- Lien vers la synthèse PAPA -->
@if($rapport->papa)
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
    <div class="text-sm text-gray-600">
        <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>
        Exporter la synthèse complète du PAPA <strong>{{ $rapport->papa->code }}</strong> :
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('rapports.export-papa-pdf', $rapport->papa) }}"
           class="inline-flex items-center gap-2 bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-file-pdf"></i> Synthèse PDF (paysage)
        </a>
        <a href="{{ route('rapports.export-excel', $rapport->papa) }}"
           class="inline-flex items-center gap-2 bg-green-100 text-green-700 hover:bg-green-200 px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-file-excel"></i> Export Excel complet
        </a>
    </div>
</div>
@endif

@endsection
