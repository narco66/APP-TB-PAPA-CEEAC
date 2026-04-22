@extends('layouts.app')

@section('title', $service ? 'Modifier service' : 'Nouveau service')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $service ? 'Modifier : ' . $service->libelle : 'Nouveau service' }}
        </h1>
        <nav class="text-xs text-gray-500 mt-2 flex items-center gap-1">
            <a href="{{ route('admin.structure.services') }}" class="hover:underline">Services</a>
            <i class="fas fa-chevron-right text-gray-300"></i>
            <span>{{ $service ? 'Modifier' : 'Créer' }}</span>
        </nav>
    </div>

    <div class="mb-4 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <i class="fas fa-shield-halved mr-2"></i>{{ $scopeLabel }}
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST"
              action="{{ $service ? route('admin.structure.services.update', $service) : route('admin.structure.services.store') }}">
            @csrf
            @if($service) @method('PUT') @endif

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $service?->code) }}"
                           placeholder="ex: SBF" maxlength="20"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase @error('code') border-red-500 @enderror">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sigle / Libellé court</label>
                    <input type="text" name="libelle_court" value="{{ old('libelle_court', $service?->libelle_court) }}"
                           maxlength="50"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Intitulé complet <span class="text-red-500">*</span></label>
                    <input type="text" name="libelle" value="{{ old('libelle', $service?->libelle) }}"
                           maxlength="200"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('libelle') border-red-500 @enderror">
                    @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Direction <span class="text-red-500">*</span></label>
                    <select name="direction_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('direction_id') border-red-500 @enderror">
                        <option value="">-- Choisir --</option>
                        @foreach($directions as $dir)
                        <option value="{{ $dir->id }}"
                                {{ old('direction_id', $service?->direction_id) == $dir->id ? 'selected' : '' }}>
                            {{ $dir->code }} — {{ $dir->libelle }}
                            @if($dir->departement) ({{ $dir->departement->libelle_court ?? $dir->departement->code }}) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('direction_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="ordre_affichage" value="{{ old('ordre_affichage', $service?->ordre_affichage) }}"
                           min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" maxlength="1000"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $service?->description) }}</textarea>
                </div>

                <div class="col-span-2 flex items-center gap-2">
                    <input type="checkbox" name="actif" id="actif" value="1"
                           {{ old('actif', $service?->actif ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 accent-indigo-600">
                    <label for="actif" class="text-sm text-gray-700">Service actif</label>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                    <i class="fas fa-save mr-1"></i>
                    {{ $service ? 'Mettre à jour' : 'Créer le service' }}
                </button>
                <a href="{{ route('admin.structure.services') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
            </div>
        </form>
    </div>

</div>
@endsection
