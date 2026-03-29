@extends('layouts.app')
@section('title', 'Gestion documentaire')
@section('page-title', 'Gestion Electronique des Documents (GED)')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Documents</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $documents->total() }} document(s)</p>
        @can('document.deposer')
            <a href="{{ route('documents.create') }}" class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-upload"></i>
                <span>Deposer un document</span>
            </a>
        @endcan
    </div>

    <form method="GET" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Titre du document..." class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 w-52">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Categorie</label>
            <select name="categorie_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Toutes les categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('categorie_id') == $cat->id ? 'selected' : '' }}>{{ $cat->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
            <select name="statut" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous les statuts</option>
                <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>Valide</option>
                <option value="archive" {{ request('statut') === 'archive' ? 'selected' : '' }}>Archive</option>
                <option value="obsolete" {{ request('statut') === 'obsolete' ? 'selected' : '' }}>Obsolete</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>
        @if(request()->anyFilled(['q', 'categorie_id', 'statut']))
            <a href="{{ route('documents.index') }}" class="px-4 py-1.5 text-gray-500 text-sm hover:text-gray-700">
                <i class="fas fa-times mr-1"></i>Reinitialiser
            </a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($documents as $doc)
            <div class="px-5 py-4 border-b border-gray-50 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center space-x-4">
                        <span class="text-3xl flex-shrink-0">{{ $doc->iconeExtension() }}</span>
                        <div class="min-w-0">
                            <div class="flex items-center flex-wrap gap-2 mb-1">
                                <a href="{{ route('documents.show', $doc) }}" class="font-medium text-sm text-gray-800 hover:text-indigo-600">{{ $doc->titre }}</a>
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $doc->statut === 'valide' ? 'bg-green-100 text-green-700' : ($doc->statut === 'archive' ? 'bg-gray-100 text-gray-600' : ($doc->statut === 'obsolete' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700')) }}">
                                    {{ ucfirst($doc->statut) }}
                                </span>
                                @if($doc->confidentialite !== 'public')
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-{{ $doc->couleurConfidentialite() }}-100 text-{{ $doc->couleurConfidentialite() }}-700">
                                        <i class="fas fa-lock text-xs mr-0.5"></i>{{ ucfirst(str_replace('_', ' ', $doc->confidentialite)) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400">
                                {{ $doc->categorie?->libelle ?? '-' }} •
                                {{ $doc->tailleLisible() }} •
                                v{{ $doc->version }} •
                                Depose le {{ $doc->created_at->format('d/m/Y') }}
                                @if($doc->deposePar) par {{ $doc->deposePar->nomComplet() }} @endif
                            </p>
                            @if($doc->reference)
                                <p class="text-xs text-gray-400">Ref : {{ $doc->reference }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('documents.show', $doc) }}" class="px-3 py-1.5 text-gray-600 hover:text-gray-800 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <i class="fas fa-eye mr-1"></i>Voir
                        </a>
                        @can('telecharger', $doc)
                            <a href="{{ route('documents.download', $doc) }}" class="px-3 py-1.5 text-indigo-600 hover:text-indigo-800 text-sm border border-indigo-200 rounded-lg hover:bg-indigo-50 transition">
                                <i class="fas fa-download mr-1"></i>Telecharger
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <i class="fas fa-folder-open text-gray-200 text-5xl mb-4"></i>
                <p class="text-gray-400">Aucun document trouve.</p>
                @can('document.deposer')
                    <a href="{{ route('documents.create') }}" class="mt-4 inline-block text-indigo-600 hover:underline text-sm">Deposer le premier document</a>
                @endcan
            </div>
        @endforelse
    </div>

    {{ $documents->links() }}
</div>
@endsection
