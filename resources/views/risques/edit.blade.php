@extends('layouts.app')

@section('title', 'Modifier le risque ' . $risque->code)

@section('content')
<div class="max-w-2xl mx-auto" x-data="{
    probabilite: '{{ old('probabilite', $risque->probabilite) }}',
    impact: '{{ old('impact', $risque->impact) }}',
    get score() {
        const p = {tres_faible:1, faible:2, moyenne:3, elevee:4, tres_elevee:5};
        const i = {negligeable:1, mineur:2, modere:3, majeur:4, catastrophique:5};
        return (p[this.probabilite] || 1) * (i[this.impact] || 1);
    },
    get niveau() {
        const s = this.score;
        if (s >= 15) return {label:'Critique', cls:'text-red-700 bg-red-100'};
        if (s >= 8)  return {label:'Élevé',    cls:'text-orange-700 bg-orange-100'};
        if (s >= 3)  return {label:'Modéré',   cls:'text-yellow-700 bg-yellow-100'};
        return {label:'Faible', cls:'text-green-700 bg-green-100'};
    }
}">

    <nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
        <a href="{{ route('papas.show', $papa) }}" class="hover:text-indigo-600">{{ $papa->code }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('risques.index', $papa) }}" class="hover:text-indigo-600">Risques</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-medium">{{ $risque->code }}</span>
    </nav>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">{{ $risque->code }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $risque->libelle }}</p>
        </div>

        <form method="POST" action="{{ route('risques.update', [$papa, $risque]) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                <input type="text" name="libelle" value="{{ old('libelle', $risque->libelle) }}" required maxlength="400"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                    <select name="categorie" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(['strategique'=>'Stratégique','operationnel'=>'Opérationnel','financier'=>'Financier','juridique'=>'Juridique','reputationnel'=>'Réputationnel','securitaire'=>'Sécuritaire','naturel'=>'Naturel','autre'=>'Autre'] as $v => $l)
                        <option value="{{ $v }}" {{ old('categorie', $risque->categorie) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(['identifie'=>'Identifié','en_traitement'=>'En traitement','residu'=>'Résiduel','clos'=>'Clos'] as $v => $l)
                        <option value="{{ $v }}" {{ old('statut', $risque->statut) === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $risque->description) }}</textarea>
            </div>

            <!-- Évaluation -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Évaluation du risque</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold" :class="niveau.cls">
                        Score : <span x-text="score" class="mx-1"></span>/25 — <span x-text="niveau.label"></span>
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Probabilité</label>
                        <select name="probabilite" x-model="probabilite"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['tres_faible'=>'Très faible (1)','faible'=>'Faible (2)','moyenne'=>'Moyenne (3)','elevee'=>'Élevée (4)','tres_elevee'=>'Très élevée (5)'] as $v => $l)
                            <option value="{{ $v }}" {{ old('probabilite', $risque->probabilite) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Impact</label>
                        <select name="impact" x-model="impact"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['negligeable'=>'Négligeable (1)','mineur'=>'Mineur (2)','modere'=>'Modéré (3)','majeur'=>'Majeur (4)','catastrophique'=>'Catastrophique (5)'] as $v => $l)
                            <option value="{{ $v }}" {{ old('impact', $risque->impact) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mesures de mitigation</label>
                <textarea name="mesures_mitigation" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('mesures_mitigation', $risque->mesures_mitigation) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan de contingence</label>
                <textarea name="plan_contingence" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('plan_contingence', $risque->plan_contingence) }}</textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <select name="responsable_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">— Non assigné —</option>
                        @foreach($responsables as $u)
                        <option value="{{ $u->id }}" {{ old('responsable_id', $risque->responsable_id) == $u->id ? 'selected' : '' }}>
                            {{ trim($u->prenom . ' ' . $u->name) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Échéance traitement</label>
                    <input type="date" name="date_echeance_traitement" value="{{ old('date_echeance_traitement', $risque->date_echeance_traitement?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dernière revue</label>
                    <input type="date" name="date_derniere_revue" value="{{ old('date_derniere_revue', $risque->date_derniere_revue?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('risques.index', $papa) }}" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
