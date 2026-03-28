@extends('layouts.app')

@section('title', 'Plans d\'Action Prioritaires')
@section('page-title', 'Plans d\'Action Prioritaires (PAPA)')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">PAPA</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- En-tête page -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">{{ $papas->total() }} plan(s) au total</p>
        </div>
        @can('papa.creer')
        <a href="{{ route('papas.create') }}"
           class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fas fa-plus"></i>
            <span>Nouveau PAPA</span>
        </a>
        @endcan
    </div>

    <!-- Liste des PAPA -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($papas as $papa)
        <div class="p-5 border-b border-gray-50 hover:bg-gray-50 transition">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3 mb-1">
                        <span class="font-mono text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $papa->code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            bg-{{ $papa->couleurStatut() }}-100 text-{{ $papa->couleurStatut() }}-700">
                            {{ $papa->libelleStatut() }}
                        </span>
                        @if($papa->est_verrouille)
                        <span class="text-gray-400 text-xs"><i class="fas fa-lock"></i> Verrouillé</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800">{{ $papa->libelle }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Exercice {{ $papa->annee }} •
                        {{ $papa->date_debut->format('d/m/Y') }} – {{ $papa->date_fin->format('d/m/Y') }} •
                        Budget : {{ number_format($papa->budget_total_prevu / 1000000, 1) }} M {{ $papa->devise }}
                    </p>
                </div>

                <!-- Taux -->
                <div class="flex items-center space-x-6">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-indigo-700">{{ $papa->taux_execution_physique }}%</p>
                        <p class="text-xs text-gray-400">Physique</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $papa->taux_execution_financiere }}%</p>
                        <p class="text-xs text-gray-400">Financier</p>
                    </div>
                    <a href="{{ route('papas.show', $papa) }}"
                       class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                        Détail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Barres de progression -->
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div>
                    <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                        <span>Exécution physique</span>
                        <span>{{ $papa->taux_execution_physique }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full">
                        <div class="h-full rounded-full transition-all"
                             style="width: {{ min(100, $papa->taux_execution_physique) }}%;
                                    background: {{ $papa->taux_execution_physique >= 75 ? '#22c55e' : ($papa->taux_execution_physique >= 50 ? '#f59e0b' : '#ef4444') }}">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                        <span>Exécution financière</span>
                        <span>{{ $papa->taux_execution_financiere }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full">
                        <div class="h-full bg-green-400 rounded-full"
                             style="width: {{ min(100, $papa->taux_execution_financiere) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-book text-gray-200 text-5xl mb-4"></i>
            <p class="text-gray-400">Aucun PAPA trouvé.</p>
            @can('papa.creer')
            <a href="{{ route('papas.create') }}" class="mt-4 inline-block text-indigo-600 hover:underline text-sm">
                Créer le premier PAPA
            </a>
            @endcan
        </div>
        @endforelse
    </div>

    {{ $papas->links() }}
</div>
@endsection
