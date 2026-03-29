@extends('layouts.app')

@section('title', 'Décisions')

@section('breadcrumbs')
    <li>/</li>
    <li class="text-gray-700">Décisions</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Décisions et arbitrages</h1>
            <p class="text-sm text-gray-500">Journal institutionnel des décisions prises dans le cadre du pilotage PAPA.</p>
        </div>
        @can('create', App\Models\Decision::class)
            <a href="{{ route('decisions.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Nouvelle décision
            </a>
        @endcan
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Référence</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Titre</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Niveau</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Statut</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">PAPA</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($decisions as $decision)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $decision->reference }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $decision->titre }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ str($decision->niveau_decision)->replace('_', ' ')->title() }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ str($decision->statut)->replace('_', ' ')->title() }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $decision->papa?->code ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('decisions.show', $decision) }}" class="text-indigo-600 hover:text-indigo-800">Ouvrir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucune décision disponible.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $decisions->links() }}
        </div>
    </div>
</div>
@endsection
