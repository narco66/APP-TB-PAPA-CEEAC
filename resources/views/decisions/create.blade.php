@extends('layouts.app')

@section('title', 'Nouvelle dÃ©cision')

@section('breadcrumbs')
    <li>/</li>
    <li><a href="{{ route('decisions.index') }}" class="hover:text-indigo-600">DÃ©cisions</a></li>
    <li>/</li>
    <li class="text-gray-700">CrÃ©er</li>
@endsection

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Nouvelle dÃ©cision</h1>
        <p class="text-sm text-gray-500">CrÃ©ez une dÃ©cision institutionnelle traÃ§able et rattachÃ©e Ã  un pÃ©rimÃ¨tre mÃ©tier.</p>
    </div>

    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <form method="POST" action="{{ route('decisions.store') }}" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="papa_id" class="mb-2 block text-sm font-medium text-gray-700">PAPA</label>
                <select id="papa_id" name="papa_id" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Aucun</option>
                    @foreach($papas as $item)
                        <option value="{{ $item->id }}" @selected(old('papa_id', $papa?->id) == $item->id)>{{ $item->code }} - {{ $item->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="action_prioritaire_id" class="mb-2 block text-sm font-medium text-gray-700">Action prioritaire</label>
                <select id="action_prioritaire_id" name="action_prioritaire_id" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Aucune</option>
                    @foreach($actions as $action)
                        <option value="{{ $action->id }}" @selected(old('action_prioritaire_id') == $action->id)>{{ $action->code }} - {{ $action->libelle }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="type_decision" class="mb-2 block text-sm font-medium text-gray-700">Type</label>
                <select id="type_decision" name="type_decision" class="w-full rounded-lg border-gray-300 text-sm" required>
                    @foreach(['arbitrage', 'validation', 'orientation', 'reaffectation_budgetaire', 'report', 'suspension'] as $type)
                        <option value="{{ $type }}" @selected(old('type_decision') === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="niveau_decision" class="mb-2 block text-sm font-medium text-gray-700">Niveau</label>
                <select id="niveau_decision" name="niveau_decision" class="w-full rounded-lg border-gray-300 text-sm" required>
                    @foreach(['direction', 'commissaire', 'sg', 'presidence'] as $niveau)
                        <option value="{{ $niveau }}" @selected(old('niveau_decision') === $niveau)>{{ str($niveau)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="titre" class="mb-2 block text-sm font-medium text-gray-700">Titre</label>
            <input id="titre" name="titre" type="text" value="{{ old('titre') }}" required class="w-full rounded-lg border-gray-300 text-sm">
        </div>

        <div>
            <label for="description" class="mb-2 block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" name="description" rows="6" required class="w-full rounded-lg border-gray-300 text-sm">{{ old('description') }}</textarea>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label for="impact_budgetaire" class="mb-2 block text-sm font-medium text-gray-700">Impact budgÃ©taire</label>
                <input id="impact_budgetaire" name="impact_budgetaire" type="number" step="0.01" min="0" value="{{ old('impact_budgetaire') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label for="impact_calendrier_jours" class="mb-2 block text-sm font-medium text-gray-700">Impact calendrier (jours)</label>
                <input id="impact_calendrier_jours" name="impact_calendrier_jours" type="number" value="{{ old('impact_calendrier_jours') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="flex items-end">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="mise_en_oeuvre_obligatoire" value="1" @checked(old('mise_en_oeuvre_obligatoire')) class="rounded border-gray-300 text-indigo-600">
                    Mise en oeuvre obligatoire
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('decisions.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">CrÃ©er la dÃ©cision</button>
        </div>
    </form>
</div>
@endsection
