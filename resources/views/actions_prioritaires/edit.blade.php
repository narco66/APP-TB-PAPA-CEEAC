@extends('layouts.app')
@section('title', 'Modifier — ' . $ap->code)
@section('page-title', 'Modifier l\'action prioritaire')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('actions-prioritaires.show', $ap) }}" class="hover:text-indigo-600">{{ $ap->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Modifier</li>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center gap-3 mb-6">
            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $ap->code }}</span>
            <span class="text-sm font-semibold text-gray-700">{{ Str::limit($ap->libelle, 80) }}</span>
        </div>

        <form action="{{ route('actions-prioritaires.update', $ap) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" name="libelle" value="{{ old('libelle', $ap->libelle) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description', $ap->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qualification <span class="text-red-500">*</span></label>
                    <select name="qualification"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['technique' => 'Technique', 'appui' => 'Appui', 'transversal' => 'Transversal'] as $v => $l)
                        <option value="{{ $v }}" {{ old('qualification', $ap->qualification) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priorité <span class="text-red-500">*</span></label>
                    <select name="priorite"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['critique' => 'Critique', 'haute' => 'Haute', 'normale' => 'Normale', 'basse' => 'Basse'] as $v => $l)
                        <option value="{{ $v }}" {{ old('priorite', $ap->priorite) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                    <select name="statut"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['planifie' => 'Planifié', 'en_cours' => 'En cours', 'suspendu' => 'Suspendu', 'termine' => 'Terminé', 'abandonne' => 'Abandonné'] as $v => $l)
                        <option value="{{ $v }}" {{ old('statut', $ap->statut) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Département porteur</label>
                    <select name="departement_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Tous les départements --</option>
                        <optgroup label="Techniques">
                            @foreach($departements->where('type', 'technique') as $dep)
                            <option value="{{ $dep->id }}"
                                    {{ old('departement_id', $ap->departement_id) == $dep->id ? 'selected' : '' }}>
                                {{ $dep->libelle_court ?? $dep->libelle }}
                            </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Appui">
                            @foreach($departements->where('type', 'appui') as $dep)
                            <option value="{{ $dep->id }}"
                                    {{ old('departement_id', $ap->departement_id) == $dep->id ? 'selected' : '' }}>
                                {{ $dep->libelle_court ?? $dep->libelle }}
                            </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes', $ap->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('actions-prioritaires.show', $ap) }}"
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
