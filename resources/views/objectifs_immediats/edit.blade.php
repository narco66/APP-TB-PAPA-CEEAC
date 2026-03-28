@extends('layouts.app')
@section('title', 'Modifier — ' . $oi->code)
@section('page-title', 'Modifier l\'objectif immédiat')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('objectifs-immediats.show', $oi) }}" class="hover:text-indigo-600">{{ $oi->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Modifier</li>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center gap-3 mb-6">
            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $oi->code }}</span>
            <span class="text-sm font-semibold text-gray-700">{{ Str::limit($oi->libelle, 80) }}</span>
        </div>

        <form action="{{ route('objectifs-immediats.update', $oi) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                <input type="text" name="libelle" value="{{ old('libelle', $oi->libelle) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description', $oi->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                    <select name="statut"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['planifie' => 'Planifié', 'en_cours' => 'En cours', 'atteint' => 'Atteint', 'partiellement_atteint' => 'Partiellement atteint', 'non_atteint' => 'Non atteint'] as $v => $l)
                        <option value="{{ $v }}" {{ old('statut', $oi->statut) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <select name="responsable_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Non assigné --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}"
                                {{ old('responsable_id', $oi->responsable_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->prenom }} {{ $u->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes', $oi->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('objectifs-immediats.show', $oi) }}"
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
