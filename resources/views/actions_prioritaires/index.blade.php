@extends('layouts.app')
@section('title', 'Actions prioritaires')
@section('page-title', 'Actions prioritaires')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Actions prioritaires</li>
@endsection

@section('content')
<div class="space-y-6">

    <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $actions->total() }} action(s) prioritaire(s)</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('actions-prioritaires.print-index', request()->only(['papa_id', 'qualification', 'statut'])) }}"
               target="_blank"
               rel="noopener"
               class="flex items-center space-x-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-print"></i><span>Version imprimable</span>
            </a>
            @can('papa.modifier')
            <a href="{{ route('actions-prioritaires.create') }}"
               class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-plus"></i><span>Nouvelle AP</span>
            </a>
            @endcan
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">PAPA</label>
            <select name="papa_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous les PAPA</option>
                @foreach($papas as $p)
                <option value="{{ $p->id }}" {{ request('papa_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->code }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Qualification</label>
            <select name="qualification" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Toutes</option>
                <option value="technique" {{ request('qualification') === 'technique' ? 'selected' : '' }}>Technique</option>
                <option value="appui" {{ request('qualification') === 'appui' ? 'selected' : '' }}>Appui</option>
                <option value="transversal" {{ request('qualification') === 'transversal' ? 'selected' : '' }}>Transversal</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
            <select name="statut" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous</option>
                @foreach(['planifie' => 'Planifie', 'en_cours' => 'En cours', 'suspendu' => 'Suspendu', 'termine' => 'Termine', 'abandonne' => 'Abandonne'] as $v => $l)
                <option value="{{ $v }}" {{ request('statut') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>

        @if(request()->anyFilled(['papa_id', 'qualification', 'statut']))
        <a href="{{ route('actions-prioritaires.index') }}" class="px-4 py-1.5 text-gray-500 text-sm hover:text-gray-700">
            <i class="fas fa-times mr-1"></i>Reinitialiser
        </a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($actions as $ap)
        <div class="p-5 border-b border-gray-50 hover:bg-gray-50 transition">
            <div class="flex items-start justify-between flex-wrap gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="font-mono text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $ap->code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $ap->couleurStatut() }}-100 text-{{ $ap->couleurStatut() }}-700">
                            {{ ucfirst(str_replace('_', ' ', $ap->statut)) }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            @if($ap->qualification === 'technique') bg-blue-50 text-blue-600
                            @elseif($ap->qualification === 'appui') bg-purple-50 text-purple-600
                            @else bg-gray-50 text-gray-500 @endif">
                            {{ ucfirst($ap->qualification) }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-xs bg-{{ $ap->couleurPriorite() }}-100 text-{{ $ap->couleurPriorite() }}-700">
                            {{ ucfirst($ap->priorite) }}
                        </span>
                    </div>

                    <p class="font-semibold text-gray-800">{{ $ap->libelle }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        PAPA : {{ $ap->papa?->code ?? '-' }} •
                        {{ $ap->departement?->libelle ?? 'Tous departements' }}
                    </p>
                </div>

                <div class="flex items-center space-x-5">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-indigo-700">{{ number_format($ap->taux_realisation, 0) }}%</p>
                        <p class="text-xs text-gray-400">Realisation</p>
                    </div>

                    <div class="w-20">
                        <div class="h-2 bg-gray-100 rounded-full">
                            <div class="h-full rounded-full"
                                 style="width: {{ min(100, $ap->taux_realisation) }}%;
                                        background: {{ $ap->taux_realisation >= 75 ? '#22c55e' : ($ap->taux_realisation >= 50 ? '#f59e0b' : '#ef4444') }}">
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('actions-prioritaires.show', $ap) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-list-alt text-gray-200 text-5xl mb-4"></i>
            <p class="text-gray-400">Aucune action prioritaire trouvee.</p>
        </div>
        @endforelse
    </div>

    {{ $actions->links() }}
</div>
@endsection
