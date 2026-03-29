@extends('layouts.app')

@section('title', 'Centre de reporting')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Centre de reporting</h1>
            <p class="text-sm text-gray-500 mt-1">Catalogue institutionnel des rapports, historique des exports et acces rapide aux modeles de reporting.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create', \App\Models\Rapport::class)
            <a href="{{ route('rapports.create') }}" class="inline-flex items-center gap-2 rounded-lg border border-indigo-300 bg-white px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50">
                <i class="fas fa-file-signature"></i>
                Nouveau rapport narratif
            </a>
            @endcan
            <a href="{{ route('reports.library.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <i class="fas fa-folder-open"></i>
                Bibliotheque
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500">Modeles actifs</p>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ $stats['definitions'] }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-500">Exports prets</p>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ $stats['generated'] }}</p>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-500">En attente</p>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ $stats['queued'] }}</p>
        </div>
        <div class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-rose-500">Echecs</p>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ $stats['failed'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Catalogue des rapports</h2>
            </div>
            <div class="space-y-6 p-6">
                @forelse($definitions as $categorie => $items)
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-indigo-600">{{ $categorie }}</h3>
                        <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                            @foreach($items as $definition)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $definition->libelle }}</p>
                                            <p class="mt-1 text-sm text-gray-600">{{ $definition->description }}</p>
                                        </div>
                                        @if($definition->is_async_recommended)
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">Lourd</span>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($definition->formats ?? [] as $format)
                                            <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700 uppercase">{{ $format }}</span>
                                        @endforeach
                                    </div>
                                    <form method="POST" action="{{ route('reports.generate', $definition) }}" class="mt-4 grid grid-cols-1 gap-2">
                                        @csrf
                                        <select name="papa_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            <option value="">Selectionner un PAPA</option>
                                            @foreach($papas as $papa)
                                                <option value="{{ $papa->id }}">{{ $papa->code }} · {{ $papa->libelle }}</option>
                                            @endforeach
                                        </select>
                                        <div class="flex items-center gap-2">
                                            <select name="format" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                                @foreach($definition->formats ?? [] as $format)
                                                    <option value="{{ $format }}">{{ strtoupper($format) }}</option>
                                                @endforeach
                                            </select>
                                            <button class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                                <i class="fas fa-bolt"></i>
                                                Generer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucun modele de rapport n'est encore configure.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Exports recents</h2>
            </div>
            <div class="p-6">
                @forelse($recentReports as $report)
                    <div class="mb-4 rounded-xl border border-gray-100 p-4 last:mb-0">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $report->titre }}</p>
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $report->formatBadgeClass() }}">{{ strtoupper($report->format) }}</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $report->definition?->libelle ?? 'Rapport libre' }}
                            @if($report->papa)
                                · {{ $report->papa->code }}
                            @endif
                        </p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $report->statusBadgeClass() }}">{{ ucfirst($report->statut) }}</span>
                            <a href="{{ route('reports.library.show', $report) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Voir</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucun export genere pour le moment.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Rapports narratifs recents</h2>
                <p class="mt-1 text-sm text-gray-500">Consultation rapide du parcours historique sans quitter le centre de reporting.</p>
            </div>
            <a href="{{ route('rapports.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                Voir l'historique
            </a>
        </div>
        <div class="p-6">
            @forelse($recentNarrativeReports as $rapport)
                <div class="mb-4 flex items-center justify-between gap-4 rounded-xl border border-gray-100 p-4 last:mb-0">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $rapport->titre }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ ucfirst($rapport->type_rapport) }}
                            @if($rapport->papa)
                                · {{ $rapport->papa->code }}
                            @endif
                            · {{ $rapport->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium
                            {{ $rapport->couleurStatut() === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $rapport->couleurStatut() === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $rapport->couleurStatut() === 'green' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $rapport->couleurStatut() === 'indigo' ? 'bg-indigo-100 text-indigo-700' : '' }}">
                            {{ ucfirst($rapport->statut) }}
                        </span>
                        <a href="{{ route('rapports.show', $rapport) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                            Ouvrir
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Aucun rapport narratif recent visible dans votre perimetre.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection