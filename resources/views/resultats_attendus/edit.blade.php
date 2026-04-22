@extends('layouts.app')
@section('title', 'Modifier â€” ' . $ra->code)
@section('page-title', 'Modifier le rÃ©sultat attendu')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('resultats-attendus.show', $ra) }}" class="hover:text-indigo-600">{{ $ra->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Modifier</li>
@endsection

@section('content')
<div class="max-w-3xl" x-data="{ preuveRequise: {{ old('preuve_requise', $ra->preuve_requise) ? 'true' : 'false' }} }">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center gap-3 mb-6">
            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $ra->code }}</span>
            <span class="text-sm font-semibold text-gray-700">{{ Str::limit($ra->libelle, 80) }}</span>
        </div>

        <form action="{{ route('resultats-attendus.update', $ra) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">LibellÃ© <span class="text-red-500">*</span></label>
                <input type="text" name="libelle" value="{{ old('libelle', $ra->libelle) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description', $ra->description) }}</textarea>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type_resultat"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['output' => 'Extrant', 'outcome' => 'Effet', 'impact' => 'Impact'] as $v => $l)
                        <option value="{{ $v }}" {{ old('type_resultat', $ra->type_resultat) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">AnnÃ©e de rÃ©fÃ©rence</label>
                    <input type="number" name="annee_reference" value="{{ old('annee_reference', $ra->annee_reference) }}"
                           min="2020" max="2040" placeholder="ex: 2025"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('annee_reference')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                    <select name="statut"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['planifie' => 'PlanifiÃ©', 'en_cours' => 'En cours', 'atteint' => 'Atteint', 'partiellement_atteint' => 'Part. atteint', 'non_atteint' => 'Non atteint'] as $v => $l)
                        <option value="{{ $v }}" {{ old('statut', $ra->statut) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <select name="responsable_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Non assignÃ© --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}"
                                {{ old('responsable_id', $ra->responsable_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->prenom }} {{ $u->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Preuve -->
            <div class="border border-gray-200 rounded-lg p-4">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="hidden" name="preuve_requise" value="0">
                    <input type="checkbox" name="preuve_requise" value="1"
                           x-model="preuveRequise"
                           {{ old('preuve_requise', $ra->preuve_requise) ? 'checked' : '' }}
                           class="w-4 h-4 accent-indigo-600">
                    <span class="text-sm font-medium text-gray-700">
                        <i class="fas fa-paperclip text-orange-500 mr-1"></i>Preuve documentaire requise
                    </span>
                </label>
                <div x-show="preuveRequise" class="mt-3">
                    <input type="text" name="type_preuve_attendue"
                           value="{{ old('type_preuve_attendue', $ra->type_preuve_attendue) }}"
                           placeholder="Ex : ProcÃ¨s-verbal signÃ©, rapport..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes', $ra->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('resultats-attendus.show', $ra) }}"
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
