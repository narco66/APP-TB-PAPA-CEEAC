@extends('layouts.app')
@section('title', 'Nouvel indicateur')
@section('page-title', 'Créer un indicateur de performance')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('indicateurs.index') }}" class="hover:text-indigo-600">Indicateurs</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Nouveau</li>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Définition de l'indicateur</h2>

        <form action="{{ route('indicateurs.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Identification -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           placeholder="Ex : IND-DPS-001"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type_indicateur"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('type_indicateur') border-red-500 @enderror">
                        <option value="quantitatif" {{ old('type_indicateur', 'quantitatif') === 'quantitatif' ? 'selected' : '' }}>Quantitatif</option>
                        <option value="qualitatif"  {{ old('type_indicateur') === 'qualitatif' ? 'selected' : '' }}>Qualitatif</option>
                        <option value="binaire"     {{ old('type_indicateur') === 'binaire' ? 'selected' : '' }}>Binaire (Oui/Non)</option>
                    </select>
                    @error('type_indicateur')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" name="libelle" value="{{ old('libelle') }}"
                       placeholder="Intitulé complet de l'indicateur"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Définition / Description</label>
                <textarea name="definition" rows="2"
                          placeholder="Définition précise de ce que mesure cet indicateur..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('definition') }}</textarea>
            </div>

            <!-- Responsabilité -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                    <select name="direction_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Direction --</option>
                        @foreach($directions as $dir)
                        <option value="{{ $dir->id }}" {{ old('direction_id') == $dir->id ? 'selected' : '' }}>
                            {{ $dir->sigle }} — {{ $dir->libelle }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <select name="responsable_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Responsable --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('responsable_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->nomComplet() }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Mesure -->
            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unité de mesure</label>
                    <input type="text" name="unite_mesure" value="{{ old('unite_mesure') }}"
                           placeholder="Ex : %, nombre, XAF"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valeur baseline</label>
                    <input type="number" name="valeur_baseline" value="{{ old('valeur_baseline') }}"
                           step="0.01" placeholder="Valeur de départ"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cible annuelle <span class="text-red-500">*</span></label>
                    <input type="number" name="valeur_cible_annuelle" value="{{ old('valeur_cible_annuelle', 100) }}"
                           step="0.01" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Collecte -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fréquence de collecte <span class="text-red-500">*</span>
                    </label>
                    <select name="frequence_collecte"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('frequence_collecte') border-red-500 @enderror">
                        @foreach(['mensuelle' => 'Mensuelle', 'trimestrielle' => 'Trimestrielle', 'semestrielle' => 'Semestrielle', 'annuelle' => 'Annuelle', 'ponctuelle' => 'Ponctuelle'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('frequence_collecte', 'trimestrielle') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('frequence_collecte')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Source des données</label>
                    <input type="text" name="source_donnees" value="{{ old('source_donnees') }}"
                           placeholder="Ex : Rapports activités, SIGFIP..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Méthode de calcul</label>
                <textarea name="methode_calcul" rows="2"
                          placeholder="Formule ou procédure de calcul de l'indicateur..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('methode_calcul') }}</textarea>
            </div>

            <!-- Seuils d'alerte -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-yellow-800 mb-3">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Seuils d'alerte (en % de réalisation)
                </h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-red-600 mb-1">
                            <i class="fas fa-circle text-red-500 mr-1"></i>Seuil rouge (critique)
                        </label>
                        <input type="number" name="seuil_alerte_rouge" value="{{ old('seuil_alerte_rouge', 30) }}"
                               min="0" max="100" step="5"
                               class="w-full border border-red-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400">
                        <p class="text-xs text-gray-400 mt-1">Alerte critique si ≤ ce %</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-orange-600 mb-1">
                            <i class="fas fa-circle text-orange-400 mr-1"></i>Seuil orange (attention)
                        </label>
                        <input type="number" name="seuil_alerte_orange" value="{{ old('seuil_alerte_orange', 60) }}"
                               min="0" max="100" step="5"
                               class="w-full border border-orange-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400">
                        <p class="text-xs text-gray-400 mt-1">Alerte si ≤ ce %</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-green-600 mb-1">
                            <i class="fas fa-circle text-green-500 mr-1"></i>Seuil vert (satisfaisant)
                        </label>
                        <input type="number" name="seuil_alerte_vert" value="{{ old('seuil_alerte_vert', 75) }}"
                               min="0" max="100" step="5"
                               class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400">
                        <p class="text-xs text-gray-400 mt-1">Performance OK si ≥ ce %</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('indicateurs.index') }}"
                   class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-1"></i>Créer l'indicateur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
