@extends('layouts.app')

@section('title', 'Bibliotheque des rapports')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bibliotheque des rapports</h1>
            <p class="mt-1 text-sm text-gray-500">Historique des rapports generes, telechargements et reutilisation des sorties institutionnelles.</p>
        </div>
        <a href="{{ route('reports.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left"></i>
            Retour au centre
        </a>
    </div>

    <form method="GET" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Statut</label>
                <select name="statut" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Tous</option>
                    @foreach(['generated' => 'Genere', 'queued' => 'En file', 'processing' => 'En cours', 'failed' => 'Echec'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('statut') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Format</label>
                <select name="format" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Tous</option>
                    @foreach(['pdf', 'xlsx', 'csv'] as $format)
                        <option value="{{ $format }}" @selected(request('format') === $format)>{{ strtoupper($format) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Modele</label>
                <select name="definition_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">Tous</option>
                    @foreach($definitions as $definition)
                        <option value="{{ $definition->id }}" @selected((string) request('definition_id') === (string) $definition->id)>{{ $definition->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    <i class="fas fa-filter"></i>
                    Filtrer
                </button>
                <a href="{{ route('reports.library.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Reinitialiser
                </a>
            </div>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3">Rapport</th>
                    <th class="px-4 py-3">Modele</th>
                    <th class="px-4 py-3">Format</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3">Perimetre</th>
                    <th class="px-4 py-3">Genere le</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                @forelse($generatedReports as $report)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900">{{ $report->titre }}</p>
                            <p class="text-xs text-gray-500">{{ $report->file_name ?? 'Fichier non materialise' }}</p>
                        </td>
                        <td class="px-4 py-3">{{ $report->definition?->libelle ?? 'Rapport libre' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $report->formatBadgeClass() }}">{{ strtoupper($report->format) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $report->statusBadgeClass() }}">{{ ucfirst($report->statut) }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $report->papa?->code ?? 'Global' }}</td>
                        <td class="px-4 py-3">{{ optional($report->generated_at ?? $report->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('reports.library.show', $report) }}" class="text-indigo-600 hover:text-indigo-700" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($report->canBeDownloaded())
                                    <a href="{{ route('reports.library.download', $report) }}" class="text-emerald-600 hover:text-emerald-700" title="Telecharger">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                                @if($report->canRetry())
                                    <form method="POST" action="{{ route('reports.library.retry', $report) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-amber-600 hover:text-amber-700" title="Relancer">
                                            <i class="fas fa-rotate-right"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Aucun rapport genere ne correspond aux filtres en cours.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $generatedReports->links() }}
</div>
@endsection