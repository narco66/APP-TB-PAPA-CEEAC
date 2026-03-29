@extends('layouts.app')

@section('title', 'Archives PAPA')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.papa.index') }}" class="hover:text-indigo-600">Gestion des PAPA</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Archives</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-archive text-gray-600"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Archives PAPA</h1>
                    <p class="text-sm text-gray-500">Plans d'action archivés — lecture seule</p>
                </div>
            </div>
            <a href="{{ route('parametres.papa.index') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                <i class="fas fa-arrow-left mr-2"></i>Retour à la gestion
            </a>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">PAPA archivés</h2>
            <span class="text-xs text-gray-500">{{ $archives->total() }} PAPA(s) archivé(s)</span>
        </div>

        @if($archives->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-archive text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-500 text-sm">Aucun PAPA archivé pour le moment.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Code / Libellé</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Année</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Archivé par</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date archivage</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Motif</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($archives as $papa)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-gray-700">{{ $papa->code }}</span>
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-xs">
                                    <i class="fas fa-lock mr-1"></i>Archivé
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ $papa->libelle }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="font-medium text-gray-600">{{ $papa->annee }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="h-6 w-6 bg-gray-200 rounded-full flex items-center justify-center text-xs text-gray-600 font-bold flex-shrink-0">
                                    {{ strtoupper(substr($papa->archivePar?->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-xs text-gray-600">{{ $papa->archivePar?->nomComplet() ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-xs text-gray-600">
                                {{ $papa->archived_at ? $papa->archived_at->format('d/m/Y à H:i') : '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-600 max-w-sm truncate" title="{{ $papa->motif_archivage }}">
                                {{ $papa->motif_archivage ?? '—' }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($archives->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $archives->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
