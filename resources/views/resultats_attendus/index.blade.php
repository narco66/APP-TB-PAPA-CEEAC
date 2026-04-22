@extends('layouts.app')
@section('title', 'Résultats attendus')
@section('page-title', 'Résultats attendus')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Résultats attendus</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $resultats->total() }} résultat(s) attendu(s)</p>
    </div>

    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Périmètre de données :</span> {{ $scopeLabel }}
    </div>

    <!-- Filtres -->
    <form method="GET" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">PAPA</label>
            <select name="papa_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500"
                    onchange="this.form.submit()">
                <option value="">Tous les PAPA</option>
                @foreach($papas as $p)
                <option value="{{ $p->id }}" {{ request('papa_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->code }} - {{ Str::limit($p->libelle, 40) }}
                </option>
                @endforeach
            </select>
        </div>
        @if($objectifs->isNotEmpty())
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Objectif immédiat</label>
            <select name="objectif_immediat_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous les OI</option>
                @foreach($objectifs as $oi)
                <option value="{{ $oi->id }}" {{ request('objectif_immediat_id') == $oi->id ? 'selected' : '' }}>
                    {{ $oi->code }} - {{ Str::limit($oi->libelle, 40) }}
                </option>
                @endforeach
            </select>
        </div>
        @endif
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
            <select name="type_resultat" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous types</option>
                <option value="output"  {{ request('type_resultat') === 'output'  ? 'selected' : '' }}>Output</option>
                <option value="outcome" {{ request('type_resultat') === 'outcome' ? 'selected' : '' }}>Outcome</option>
                <option value="impact"  {{ request('type_resultat') === 'impact'  ? 'selected' : '' }}>Impact</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
            <select name="statut" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous</option>
                @foreach(['planifie' => 'Planifié', 'en_cours' => 'En cours', 'atteint' => 'Atteint', 'partiellement_atteint' => 'Partiellement atteint', 'non_atteint' => 'Non atteint'] as $v => $l)
                <option value="{{ $v }}" {{ request('statut') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>
        @if(request()->anyFilled(['papa_id', 'objectif_immediat_id', 'type_resultat', 'statut']))
        <a href="{{ route('resultats-attendus.index') }}" class="px-4 py-1.5 text-gray-500 text-sm hover:text-gray-700">
            <i class="fas fa-times mr-1"></i>Réinitialiser
        </a>
        @endif
    </form>

    <!-- En-tête avec bouton création -->
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $resultats->total() }} résultat(s) attendu(s)</p>
        @can('papa.modifier')
        <a href="{{ route('resultats-attendus.create', request()->only('objectif_immediat_id', 'papa_id')) }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus"></i> Nouveau résultat attendu
        </a>
        @endcan
    </div>

    <!-- Liste -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($resultats as $ra)
        @php
            $statutColors = [
                'planifie' => 'gray',
                'en_cours' => 'blue',
                'atteint' => 'green',
                'partiellement_atteint' => 'yellow',
                'non_atteint' => 'red',
            ];
            $typeColors = ['output' => 'indigo', 'outcome' => 'purple', 'impact' => 'amber'];
            $color = $statutColors[$ra->statut] ?? 'gray';
            $typeColor = $typeColors[$ra->type_resultat] ?? 'gray';
        @endphp
        <div class="p-5 border-b border-gray-50 hover:bg-gray-50 transition">
            <div class="flex items-start justify-between flex-wrap gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="font-mono text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded">{{ $ra->code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold uppercase bg-{{ $typeColor }}-100 text-{{ $typeColor }}-700">
                            {{ $ra->type_resultat }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700">
                            {{ str_replace('_', ' ', ucfirst($ra->statut)) }}
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800">{{ $ra->libelle }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        OI : {{ $ra->objectifImmediats?->code ?? '-' }} •
                        PAPA : {{ $ra->objectifImmediats?->actionPrioritaire?->papa?->code ?? '-' }}
                        @if($ra->responsable)
                        • <i class="fas fa-user-circle mr-0.5"></i>{{ $ra->responsable->prenom }} {{ $ra->responsable->name }}
                        @endif
                        @if($ra->preuve_requise)
                        • <span class="text-amber-600"><i class="fas fa-file-alt mr-0.5"></i>Preuve requise</span>
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('resultats-attendus.show', $ra) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                    @can('papa.modifier')
                    @if($ra->objectifImmediats?->actionPrioritaire?->estEditable())
                    <a href="{{ route('resultats-attendus.edit', $ra) }}"
                       class="text-gray-500 hover:text-indigo-600 text-sm">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form action="{{ route('resultats-attendus.destroy', $ra) }}" method="POST"
                          onsubmit="return confirm('Supprimer ce résultat attendu ?')">
                        @csrf @method('DELETE')
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
            <i class="fas fa-chart-bar text-gray-200 text-5xl mb-4"></i>
            <p class="text-gray-400">Aucun résultat attendu trouvé.</p>
        </div>
        @endforelse
    </div>

    {{ $resultats->links() }}
</div>
@endsection
