@extends('layouts.app')

@section('title', 'Modifier la ligne budgÃ©taire')

@section('content')
<div class="max-w-2xl mx-auto">
    <nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
        <a href="{{ route('papas.show', $papa) }}" class="hover:text-indigo-600">{{ $papa->code }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('budgets.index', $papa) }}" class="hover:text-indigo-600">Budget</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-medium">Modifier</span>
    </nav>

    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800 mb-4">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">Modifier la ligne budgÃ©taire</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $budget->libelleSource() }} â€” {{ $papa->code }}</p>
        </div>

        <form method="POST" action="{{ route('budgets.update', [$papa, $budget]) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Source de financement <span class="text-red-500">*</span>
                    </label>
                    <select name="source_financement" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(['budget_ceeac' => 'Budget CEEAC', 'contribution_etat_membre' => 'Contribution Ã‰tat membre', 'partenaire_technique_financier' => 'PTF', 'fonds_propres' => 'Fonds propres', 'autre' => 'Autre'] as $val => $lab)
                        <option value="{{ $val }}" {{ old('source_financement', $budget->source_financement) === $val ? 'selected' : '' }}>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">AnnÃ©e budgÃ©taire <span class="text-red-500">*</span></label>
                    <input type="number" name="annee_budgetaire" value="{{ old('annee_budgetaire', $budget->annee_budgetaire) }}"
                           min="2020" max="2040" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">LibellÃ© de la ligne</label>
                <input type="text" name="libelle_ligne" value="{{ old('libelle_ligne', $budget->libelle_ligne) }}"
                       maxlength="300"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Action prioritaire rattachÃ©e</label>
                <select name="action_prioritaire_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">â€” Budget global PAPA â€”</option>
                    @foreach($actionsPrioritaires as $ap)
                    <option value="{{ $ap->id }}" {{ old('action_prioritaire_id', $budget->action_prioritaire_id) == $ap->id ? 'selected' : '' }}>
                        {{ $ap->code }} â€” {{ Str::limit($ap->libelle, 60) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant prÃ©vu (XAF) <span class="text-red-500">*</span></label>
                    <input type="number" name="montant_prevu" value="{{ old('montant_prevu', $budget->montant_prevu) }}"
                           min="0" step="0.01" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant engagÃ© (XAF)</label>
                    <input type="number" name="montant_engage" value="{{ old('montant_engage', $budget->montant_engage) }}"
                           min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant dÃ©caissÃ© (XAF)</label>
                    <input type="number" name="montant_decaisse" value="{{ old('montant_decaisse', $budget->montant_decaisse) }}"
                           min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $budget->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('budgets.index', $papa) }}" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                    <i class="fas fa-save"></i> Mettre Ã  jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
