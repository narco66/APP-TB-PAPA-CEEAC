@extends('layouts.app')

@section('title', 'Nouveau rapport')

@section('content')
<div class="max-w-3xl mx-auto">
    @include('rapports.partials.legacy-bridge')

    <!-- Fil d'ariane -->
    <nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
        <a href="{{ route('rapports.index') }}" class="hover:text-indigo-600">Rapports</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-medium">Nouveau rapport</span>
    </nav>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">Nouveau rapport de suivi</h1>
            <p class="text-sm text-gray-500 mt-1">
                PAPA sélectionné : <strong>{{ $papa->code }}</strong> — {{ $papa->libelle }}
            </p>
        </div>

        <form method="POST" action="{{ route('rapports.store') }}" class="p-6 space-y-6">
            @csrf

            <input type="hidden" name="papa_id" value="{{ $papa->id }}">

            <!-- Sélection PAPA (si plusieurs disponibles) -->
            @if($papas->count() > 1)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Changer de PAPA <span class="text-gray-400 font-normal">(optionnel)</span></label>
                <select name="papa_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        onchange="this.form.action='{{ route('rapports.create') }}?papa_id='+this.value; this.form.method='GET'; this.form.submit();">
                    @foreach($papas as $p)
                    <option value="{{ $p->id }}" {{ $p->id === $papa->id ? 'selected' : '' }}>
                        {{ $p->code }} — {{ $p->libelle }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Informations de base -->
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Titre du rapport <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="titre" value="{{ old('titre', 'Rapport de suivi ' . $papa->code . ' — ' . now()->format('F Y')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('titre') border-red-500 @enderror"
                           required maxlength="300">
                    @error('titre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Type de rapport <span class="text-red-500">*</span>
                    </label>
                    <select name="type_rapport" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('type_rapport') border-red-500 @enderror">
                        @foreach(['mensuel','trimestriel','semestriel','annuel','ponctuel'] as $t)
                        <option value="{{ $t }}" {{ old('type_rapport') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    @error('type_rapport')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Période couverte <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="periode_couverte" value="{{ old('periode_couverte') }}"
                           placeholder="Ex : T1 2025, Janvier 2025, S1 2025"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('periode_couverte') border-red-500 @enderror"
                           required maxlength="100">
                    @error('periode_couverte')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Année <span class="text-red-500">*</span></label>
                    <input type="number" name="annee" value="{{ old('annee', $papa->annee) }}"
                           min="2020" max="2040"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Numéro de période <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <input type="number" name="numero_periode" value="{{ old('numero_periode') }}"
                           min="1" max="12" placeholder="Ex : 1 pour T1 ou janvier"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Taux d'exécution (pré-remplis depuis PAPA) -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm font-medium text-blue-800 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    Taux d'exécution au moment de la rédaction (pré-remplis depuis le PAPA)
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-700">{{ $papa->taux_execution_physique }}%</div>
                        <div class="text-xs text-gray-500 mt-1">Exécution physique</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-700">{{ $papa->taux_execution_financiere }}%</div>
                        <div class="text-xs text-gray-500 mt-1">Exécution financière</div>
                    </div>
                </div>
            </div>

            <!-- Sections narratives -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Faits saillants <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <textarea name="faits_saillants" rows="4"
                          placeholder="Principaux résultats atteints durant la période..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('faits_saillants') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Difficultés rencontrées <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <textarea name="difficultes_rencontrees" rows="4"
                          placeholder="Obstacles, blocages, problèmes identifiés..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('difficultes_rencontrees') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Recommandations <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <textarea name="recommandations" rows="4"
                          placeholder="Mesures correctives, décisions à prendre..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('recommandations') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Perspectives <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <textarea name="perspectives" rows="3"
                          placeholder="Activités planifiées pour la prochaine période..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('perspectives') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('rapports.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                    <i class="fas fa-save"></i> Enregistrer le rapport
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
