@extends('layouts.app')

@section('title', $direction ? 'Modifier direction' : 'Nouvelle direction')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $direction ? 'Modifier : ' . $direction->libelle : 'Nouvelle direction' }}
        </h1>
        <nav class="text-xs text-gray-500 mt-2 flex items-center gap-1">
            <a href="{{ route('admin.structure.directions') }}" class="hover:underline">Directions</a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span>{{ $direction ? 'Modifier' : 'Créer' }}</span>
        </nav>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST"
              action="{{ $direction ? route('admin.structure.directions.update', $direction) : route('admin.structure.directions.store') }}">
            @csrf
            @if($direction) @method('PUT') @endif

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $direction?->code) }}"
                           placeholder="ex: DCPA" maxlength="20"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase @error('code') border-red-500 @enderror">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sigle / Libellé court</label>
                    <input type="text" name="libelle_court" value="{{ old('libelle_court', $direction?->libelle_court) }}"
                           maxlength="50"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Intitulé complet <span class="text-red-500">*</span></label>
                    <input type="text" name="libelle" value="{{ old('libelle', $direction?->libelle) }}"
                           maxlength="200"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('libelle') border-red-500 @enderror">
                    @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Département <span class="text-red-500">*</span></label>
                    <select name="departement_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('departement_id') border-red-500 @enderror">
                        <option value="">-- Choisir --</option>
                        @foreach($departements as $dep)
                        <option value="{{ $dep->id }}"
                                {{ old('departement_id', $direction?->departement_id) == $dep->id ? 'selected' : '' }}>
                            {{ $dep->code }} — {{ $dep->libelle }}
                        </option>
                        @endforeach
                    </select>
                    @error('departement_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type_direction"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('type_direction') border-red-500 @enderror">
                        <option value="">-- Choisir --</option>
                        <option value="technique" {{ old('type_direction', $direction?->type_direction) === 'technique' ? 'selected' : '' }}>Technique</option>
                        <option value="appui"     {{ old('type_direction', $direction?->type_direction) === 'appui'     ? 'selected' : '' }}>Appui</option>
                    </select>
                    @error('type_direction')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="ordre_affichage" value="{{ old('ordre_affichage', $direction?->ordre_affichage) }}"
                           min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" maxlength="1000"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $direction?->description) }}</textarea>
                </div>

                <div class="col-span-2 flex items-center gap-2">
                    <input type="checkbox" name="actif" id="actif" value="1"
                           {{ old('actif', $direction?->actif ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 accent-indigo-600">
                    <label for="actif" class="text-sm text-gray-700">Direction active</label>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                    <i class="fas fa-save mr-1"></i>
                    {{ $direction ? 'Mettre à jour' : 'Créer la direction' }}
                </button>
                <a href="{{ route('admin.structure.directions') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </form>
    </div>

</div>
@endsection
