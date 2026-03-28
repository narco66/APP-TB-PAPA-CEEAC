@extends('layouts.app')
@section('title', $document->titre)
@section('page-title', 'Document — ' . Str::limit($document->titre, 60))

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('documents.index') }}" class="hover:text-indigo-600">Documents</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">{{ Str::limit($document->titre, 40) }}</li>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">

    <!-- En-tête -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start space-x-5">
            <div class="text-6xl flex-shrink-0">{{ $document->iconeExtension() }}</div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h1 class="text-lg font-bold text-gray-800">{{ $document->titre }}</h1>
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        {{ $document->statut === 'valide' ? 'bg-green-100 text-green-700' :
                           ($document->statut === 'archive' ? 'bg-gray-100 text-gray-600' :
                           ($document->statut === 'obsolete' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700')) }}">
                        {{ ucfirst($document->statut) }}
                    </span>
                    @if($document->confidentialite !== 'public')
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-{{ $document->couleurConfidentialite() }}-100 text-{{ $document->couleurConfidentialite() }}-700">
                        <i class="fas fa-lock text-xs mr-0.5"></i>{{ ucfirst(str_replace('_', ' ', $document->confidentialite)) }}
                    </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm mt-3">
                    <div>
                        <span class="text-xs text-gray-400">Catégorie</span>
                        <p class="font-medium text-gray-700">{{ $document->categorie?->libelle ?? '—' }}</p>
                    </div>
                    @if($document->reference)
                    <div>
                        <span class="text-xs text-gray-400">Référence</span>
                        <p class="font-medium text-gray-700">{{ $document->reference }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-xs text-gray-400">Version</span>
                        <p class="font-medium text-gray-700">v{{ $document->version }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Taille</span>
                        <p class="font-medium text-gray-700">{{ $document->tailleLisible() }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Déposé le</span>
                        <p class="font-medium text-gray-700">{{ $document->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Déposé par</span>
                        <p class="font-medium text-gray-700">{{ $document->deposePar?->nomComplet() ?? '—' }}</p>
                    </div>
                    @if($document->date_document)
                    <div>
                        <span class="text-xs text-gray-400">Date du document</span>
                        <p class="font-medium text-gray-700">{{ $document->date_document->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    @if($document->statut === 'valide' && $document->validePar)
                    <div>
                        <span class="text-xs text-gray-400">Validé par</span>
                        <p class="font-medium text-green-700">{{ $document->validePar->nomComplet() }}</p>
                    </div>
                    @endif
                </div>

                @if($document->description)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-1">Description</p>
                    <p class="text-sm text-gray-600">{{ $document->description }}</p>
                </div>
                @endif

                <!-- Intégrité SHA-256 -->
                @if($document->hash_sha256)
                <div class="mt-3 p-2 bg-gray-50 rounded text-xs text-gray-400 font-mono break-all">
                    <i class="fas fa-shield-alt text-green-500 mr-1"></i>SHA-256 : {{ $document->hash_sha256 }}
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-5 flex flex-wrap gap-2">
            @can('telecharger', $document)
            <a href="{{ route('documents.download', $document) }}"
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-download mr-1"></i>Télécharger
            </a>
            @endcan

            @can('valider', $document)
            @if($document->statut === 'brouillon')
            <form action="{{ route('documents.valider', $document) }}" method="POST" class="inline">
                @csrf
                <button class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                    <i class="fas fa-check mr-1"></i>Valider
                </button>
            </form>
            @endif
            @endcan

            @can('archiver', $document)
            @if(in_array($document->statut, ['valide', 'brouillon']))
            <form action="{{ route('documents.archiver', $document) }}" method="POST" class="inline"
                  onsubmit="return confirm('Archiver ce document ? Il passera en lecture seule.')">
                @csrf
                <button class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-sm hover:bg-orange-200 transition">
                    <i class="fas fa-archive mr-1"></i>Archiver
                </button>
            </form>
            @endif
            @endcan

            @can('supprimer', $document)
            @if($document->statut !== 'archive')
            <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline"
                  onsubmit="return confirm('Supprimer ce document ?')">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm hover:bg-red-100 transition">
                    <i class="fas fa-trash mr-1"></i>Supprimer
                </button>
            </form>
            @endif
            @endcan
        </div>
    </div>

    <!-- Entité rattachée -->
    @if($document->documentable)
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-700 mb-3">Rattachement</h3>
        <div class="flex items-center space-x-3 text-sm">
            <i class="fas fa-link text-gray-400"></i>
            <div>
                <p class="text-xs text-gray-400">{{ class_basename($document->documentable_type) }}</p>
                <p class="font-medium text-gray-700">
                    @if(method_exists($document->documentable, 'libelle'))
                        {{ $document->documentable->code ?? '' }} — {{ $document->documentable->libelle }}
                    @else
                        ID #{{ $document->documentable_id }}
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Historique versions -->
    @if($document->versions->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700">Historique des versions</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500">Version</th>
                    <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500">Titre</th>
                    <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500">Date</th>
                    <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($document->versions as $v)
                <tr>
                    <td class="px-4 py-2">v{{ $v->version }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('documents.show', $v) }}" class="text-indigo-600 hover:underline">
                            {{ $v->titre }}
                        </a>
                    </td>
                    <td class="px-4 py-2 text-gray-500">{{ $v->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-0.5 rounded text-xs
                            {{ $v->statut === 'valide' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($v->statut) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
