@extends('layouts.app')
@section('title', 'Objectifs immediats')
@section('page-title', 'Objectifs immediats')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Objectifs immediats</li>
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $objectifs->total() }} objectif(s) immediat(s)</p>
        @can('papa.modifier')
        <a href="{{ route('objectifs-immediats.create', request()->only('papa_id', 'action_prioritaire_id')) }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus"></i> Nouvel objectif immediat
        </a>
        @endcan
    </div>

    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <form method="GET" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">PAPA</label>
            <select name="papa_id" id="filter_papa"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500"
                    onchange="this.form.submit()">
                <option value="">Tous les PAPA</option>
                @foreach($papas as $p)
                <option value="{{ $p->id }}" {{ request('papa_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->code }} - {{ Str::limit($p->libelle, 40) }}
                </option>
                @endforeach
            </select>
        </div>

        @if($actions->isNotEmpty())
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Action prioritaire</label>
            <select name="action_prioritaire_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Toutes les AP</option>
                @foreach($actions as $ap)
                <option value="{{ $ap->id }}" {{ request('action_prioritaire_id') == $ap->id ? 'selected' : '' }}>
                    {{ $ap->code }} - {{ Str::limit($ap->libelle, 40) }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
            <select name="statut" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous</option>
                @foreach(['planifie' => 'Planifie', 'en_cours' => 'En cours', 'atteint' => 'Atteint', 'partiellement_atteint' => 'Partiellement atteint', 'non_atteint' => 'Non atteint'] as $v => $l)
                <option value="{{ $v }}" {{ request('statut') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>

        @if(request()->anyFilled(['papa_id', 'action_prioritaire_id', 'statut']))
        <a href="{{ route('objectifs-immediats.index') }}" class="px-4 py-1.5 text-gray-500 text-sm hover:text-gray-700">
            <i class="fas fa-times mr-1"></i>Reinitialiser
        </a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($objectifs as $oi)
        @php
            $statutColors = [
                'planifie' => 'gray',
                'en_cours' => 'blue',
                'atteint' => 'green',
                'partiellement_atteint' => 'yellow',
                'non_atteint' => 'red',
            ];
            $color = $statutColors[$oi->statut] ?? 'gray';
        @endphp
        <div class="p-5 border-b border-gray-50 hover:bg-gray-50 transition">
            <div class="flex items-start justify-between flex-wrap gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="font-mono text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $oi->code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700">
                            {{ str_replace('_', ' ', ucfirst($oi->statut)) }}
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800">{{ $oi->libelle }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        AP : {{ $oi->actionPrioritaire?->code ?? '-' }} •
                        PAPA : {{ $oi->actionPrioritaire?->papa?->code ?? '-' }}
                        @if($oi->responsable)
                        • <i class="fas fa-user-circle mr-0.5"></i>{{ $oi->responsable->prenom }} {{ $oi->responsable->name }}
                        @endif
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('objectifs-immediats.show', $oi) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        <i class="fas fa-eye"></i>
                    </a>
                    @can('papa.modifier')
                    @if($oi->actionPrioritaire?->estEditable())
                    <a href="{{ route('objectifs-immediats.edit', $oi) }}"
                       class="text-gray-500 hover:text-indigo-600 text-sm">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form action="{{ route('objectifs-immediats.destroy', $oi) }}" method="POST"
                          onsubmit="return confirm('Supprimer cet objectif ? Ses resultats attendus seront aussi supprimes.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-600 text-sm">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-bullseye text-gray-200 text-5xl mb-4"></i>
            <p class="text-gray-400">Aucun objectif immediat trouve.</p>
        </div>
        @endforelse
    </div>

    {{ $objectifs->links() }}
</div>
@endsection
