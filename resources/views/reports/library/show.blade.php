@extends('layouts.app')

@section('title', $generatedReport->titre)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">
                <a href="{{ route('reports.library.index') }}" class="hover:text-indigo-600">Bibliotheque</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">{{ $generatedReport->titre }}</span>
            </p>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ $generatedReport->titre }}</h1>
        </div>
        <div class="flex items-center gap-3">
            @if($generatedReport->canRetry())
                <form method="POST" action="{{ route('reports.library.retry', $generatedReport) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-amber-300 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-50">
                        <i class="fas fa-rotate-right"></i>
                        Relancer
                    </button>
                </form>
            @endif
            @if($generatedReport->canBeDownloaded())
                <a href="{{ route('reports.library.download', $generatedReport) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    <i class="fas fa-download"></i>
                    Telecharger
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Format</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ strtoupper($generatedReport->format) }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Statut</p>
            <p class="mt-2"><span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $generatedReport->statusBadgeClass() }}">{{ ucfirst($generatedReport->statut) }}</span></p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">PAPA</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ $generatedReport->papa?->code ?? 'Global' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Taille</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ $generatedReport->file_size ? number_format($generatedReport->file_size / 1024, 1, ',', ' ') . ' Ko' : '-' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Contexte de generation</h2>
            <dl class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Modele</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $generatedReport->definition?->libelle ?? 'Rapport libre' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Genere par</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $generatedReport->user?->nomComplet() ?? 'Systeme' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Genere le</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ optional($generatedReport->generated_at ?? $generatedReport->created_at)->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Dernier telechargement</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ optional($generatedReport->last_downloaded_at)->format('d/m/Y H:i') ?? 'Jamais' }}</dd>
                </div>
            </dl>

            @if($generatedReport->error_message)
                <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    <p class="font-semibold">Erreur de generation</p>
                    <p class="mt-1">{{ $generatedReport->error_message }}</p>
                </div>
            @endif

            <h3 class="mt-6 text-sm font-semibold uppercase tracking-wide text-gray-500">Filtres</h3>
            <pre class="mt-3 overflow-x-auto rounded-xl bg-slate-900 p-4 text-xs text-slate-100">{{ json_encode($generatedReport->filters ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Historique des telechargements</h2>
            <div class="mt-4 space-y-3">
                @forelse($generatedReport->downloadLogs as $log)
                    <div class="rounded-xl border border-gray-100 p-3">
                        <p class="text-sm font-medium text-gray-900">{{ $log->user?->nomComplet() ?? 'Utilisateur supprime' }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $log->downloaded_at?->format('d/m/Y H:i') }} · {{ $log->ip_address ?? 'IP inconnue' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucun telechargement enregistre.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection