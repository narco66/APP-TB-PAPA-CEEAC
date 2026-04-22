@extends('layouts.app')
@section('title', 'Nouvelle action prioritaire')
@section('page-title', 'Créer une action prioritaire')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('actions-prioritaires.index') }}" class="hover:text-indigo-600">Actions prioritaires</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Nouvelle</li>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Informations de l'action prioritaire</h2>

        <form action="{{ route('actions-prioritaires.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- PAPA -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    PAPA <span class="text-red-500">*</span>
                </label>
                <select name="papa_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('papa_id') border-red-500 @enderror">
                    <option value="">-- Sélectionner un PAPA --</option>
                    @foreach($papas as $p)
                    <option value="{{ $p->id }}"
                            {{ old('papa_id', $papaId) == $p->id ? 'selected' : '' }}>
                        {{ $p->code }} — {{ Str::limit($p->libelle, 60) }}
                    </option>
                    @endforeach
                </select>
                @error('papa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           placeholder="Ex : AP-DPS-01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="ordre" value="{{ old('ordre', 1) }}" min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" name="libelle" value="{{ old('libelle') }}"
                       placeholder="Intitulé complet de l'action prioritaire"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <!-- Qualification & Priorité -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Qualification <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        @foreach(['technique' => ['label' => 'Technique', 'desc' => 'Direction technique', 'color' => 'blue'], 'appui' => ['label' => 'Appui', 'desc' => 'Direction d\'appui', 'color' => 'purple'], 'transversal' => ['label' => 'Transversal', 'desc' => 'Toutes directions', 'color' => 'gray']] as $val => $opt)
                        <label class="flex items-center space-x-3 p-2 rounded-lg border cursor-pointer hover:bg-gray-50 transition
                            {{ old('qualification', 'technique') === $val ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }}">
                            <input type="radio" name="qualification" value="{{ $val }}"
                                   {{ old('qualification', 'technique') === $val ? 'checked' : '' }}
                                   class="accent-indigo-600">
                            <div>
                                <p class="text-sm font-medium text-{{ $opt['color'] }}-700">{{ $opt['label'] }}</p>
                                <p class="text-xs text-gray-400">{{ $opt['desc'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('qualification')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Priorité <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        @foreach(['critique' => 'Critique', 'haute' => 'Haute', 'normale' => 'Normale', 'basse' => 'Basse'] as $val => $lbl)
                        <label class="flex items-center space-x-3 p-2 rounded-lg border cursor-pointer hover:bg-gray-50 transition
                            {{ old('priorite', 'normale') === $val ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }}">
                            <input type="radio" name="priorite" value="{{ $val }}"
                                   {{ old('priorite', 'normale') === $val ? 'checked' : '' }}
                                   class="accent-indigo-600">
                            <span class="text-sm text-gray-700">{{ $lbl }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('priorite')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Département -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Département porteur
                </label>
                <select name="departement_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Tous les départements --</option>
                    <optgroup label="Départements techniques">
                        @foreach($departements->where('type', 'technique') as $dep)
                        <option value="{{ $dep->id }}" {{ old('departement_id') == $dep->id ? 'selected' : '' }}>
                            {{ $dep->libelle_court ?? $dep->libelle }}
                        </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Départements d'appui">
                        @foreach($departements->where('type', 'appui') as $dep)
                        <option value="{{ $dep->id }}" {{ old('departement_id') == $dep->id ? 'selected' : '' }}>
                            {{ $dep->libelle_court ?? $dep->libelle }}
                        </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('actions-prioritaires.index') }}"
                   class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-1"></i>Créer l'action prioritaire
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
