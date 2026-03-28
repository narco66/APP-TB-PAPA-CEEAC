@extends('layouts.app')
@section('title', 'Modifier ' . $papa->code)
@section('page-title', 'Modifier — ' . $papa->code)

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Modifier le PAPA</h2>
        <form action="{{ route('papas.update', $papa) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code PAPA</label>
                    <input type="text" name="code" value="{{ old('code', $papa->code) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                    <input type="number" name="annee" value="{{ old('annee', $papa->annee) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                <input type="text" name="libelle" value="{{ old('libelle', $papa->libelle) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="date_debut" value="{{ old('date_debut', $papa->date_debut->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date" name="date_fin" value="{{ old('date_fin', $papa->date_fin->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Budget total prévu</label>
                <input type="number" name="budget_total_prevu" value="{{ old('budget_total_prevu', $papa->budget_total_prevu) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description', $papa->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('papas.show', $papa) }}" class="px-5 py-2 text-sm text-gray-600">Annuler</a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-save mr-1"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
