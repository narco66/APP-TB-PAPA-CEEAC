@extends('layouts.app')
@section('title', 'Nouveau PAPA')
@section('page-title', 'Créer un nouveau PAPA')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('papas.index') }}" class="hover:text-indigo-600">PAPA</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Nouveau</li>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Informations du PAPA</h2>

        <form action="{{ route('papas.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code PAPA <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', 'PAPA-' . date('Y')) }}"
                           placeholder="Ex : PAPA-2025"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('code') border-red-500 @enderror">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Année <span class="text-red-500">*</span></label>
                    <input type="number" name="annee" value="{{ old('annee', date('Y')) }}"
                           min="2020" max="2050"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                <input type="text" name="libelle" value="{{ old('libelle') }}"
                       placeholder="Ex : Plan d'Action Prioritaire Annuel 2025 de la Commission de la CEEAC"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début <span class="text-red-500">*</span></label>
                    <input type="date" name="date_debut" value="{{ old('date_debut', date('Y') . '-01-01') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin <span class="text-red-500">*</span></label>
                    <input type="date" name="date_fin" value="{{ old('date_fin', date('Y') . '-12-31') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Budget total prévu (XAF)</label>
                    <input type="number" name="budget_total_prevu" value="{{ old('budget_total_prevu', 0) }}"
                           step="1000000" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Devise</label>
                    <select name="devise" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="XAF" {{ old('devise', 'XAF') === 'XAF' ? 'selected' : '' }}>XAF — Franc CFA BEAC</option>
                        <option value="EUR" {{ old('devise') === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                        <option value="USD" {{ old('devise') === 'USD' ? 'selected' : '' }}>USD — Dollar US</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description / Contexte</label>
                <textarea name="description" rows="4" placeholder="Contexte, priorités stratégiques, axes principaux..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('papas.index') }}" class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-1"></i>Créer le PAPA
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
