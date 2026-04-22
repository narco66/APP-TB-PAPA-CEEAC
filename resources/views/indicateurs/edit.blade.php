@extends('layouts.app')
@section('title', 'Modifier — ' . $indicateur->code)
@section('page-title', 'Modifier l\'indicateur')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('indicateurs.index') }}" class="hover:text-indigo-600">Indicateurs</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('indicateurs.show', $indicateur) }}" class="hover:text-indigo-600">{{ $indicateur->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Modifier</li>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="mb-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center space-x-3 mb-6">
            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $indicateur->code }}</span>
            <h2 class="text-base font-bold text-gray-800">{{ Str::limit($indicateur->libelle, 100) }}</h2>
        </div>

        <form action="{{ route('indicateurs.update', $indicateur) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" name="libelle" value="{{ old('libelle', $indicateur->libelle) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Définition</label>
                <textarea name="definition" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('definition', $indicateur->definition) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                    <select name="direction_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Direction --</option>
                        @foreach($directions as $dir)
                        <option value="{{ $dir->id }}"
                                {{ old('direction_id', $indicateur->direction_id) == $dir->id ? 'selected' : '' }}>
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
                        <option value="{{ $u->id }}"
                                {{ old('responsable_id', $indicateur->responsable_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->nomComplet() }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unité de mesure</label>
                    <input type="text" name="unite_mesure" value="{{ old('unite_mesure', $indicateur->unite_mesure) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valeur baseline</label>
                    <input type="number" name="valeur_baseline" step="0.01"
                           value="{{ old('valeur_baseline', $indicateur->valeur_baseline) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cible annuelle</label>
                    <input type="number" name="valeur_cible_annuelle" step="0.01" min="0"
                           value="{{ old('valeur_cible_annuelle', $indicateur->valeur_cible_annuelle) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Méthode de calcul</label>
                <textarea name="methode_calcul" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('methode_calcul', $indicateur->methode_calcul) }}</textarea>
            </div>

            <!-- Seuils -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-yellow-800 mb-3">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Seuils d'alerte (en % de réalisation)
                </h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-red-600 mb-1">Seuil rouge</label>
                        <input type="number" name="seuil_alerte_rouge" min="0" max="100" step="5"
                               value="{{ old('seuil_alerte_rouge', $indicateur->seuil_alerte_rouge) }}"
                               class="w-full border border-red-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-orange-600 mb-1">Seuil orange</label>
                        <input type="number" name="seuil_alerte_orange" min="0" max="100" step="5"
                               value="{{ old('seuil_alerte_orange', $indicateur->seuil_alerte_orange) }}"
                               class="w-full border border-orange-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-green-600 mb-1">Seuil vert</label>
                        <input type="number" name="seuil_alerte_vert" min="0" max="100" step="5"
                               value="{{ old('seuil_alerte_vert', $indicateur->seuil_alerte_vert) }}"
                               class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes', $indicateur->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('indicateurs.show', $indicateur) }}"
                   class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-1"></i>Retour
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-1"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
