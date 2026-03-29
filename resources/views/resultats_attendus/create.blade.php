@extends('layouts.app')
@section('title', 'Nouveau résultat attendu')
@section('page-title', 'Créer un résultat attendu')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('resultats-attendus.index') }}" class="hover:text-indigo-600">Résultats attendus</a></li>
    @if($oi)
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('objectifs-immediats.show', $oi) }}" class="hover:text-indigo-600">{{ $oi->code }}</a></li>
    @endif
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Nouveau RA</li>
@endsection

@section('content')
<div class="max-w-3xl" x-data="raForm()" x-init="init()">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <form action="{{ route('resultats-attendus.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Sélection PAPA + OI (affiché si pas de contexte pré-défini) --}}
            @if(!$oi)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-4">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">
                    <i class="fas fa-link mr-1"></i> Rattachement institutionnel
                </p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        PAPA <span class="text-red-500">*</span>
                    </label>
                    <select x-model="papaId" @change="loadOIs()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Sélectionner un PAPA --</option>
                        @foreach($papas as $p)
                        <option value="{{ $p->id }}" {{ old('papa_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->code }} — {{ Str::limit($p->libelle, 60) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Objectif immédiat <span class="text-red-500">*</span>
                    </label>
                    <select name="objectif_immediat_id" x-model="oiId"
                            :disabled="!papaId"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-100 @error('objectif_immediat_id') border-red-500 @enderror">
                        <option value="">-- Choisir d'abord un PAPA --</option>
                        <template x-for="oi in ois" :key="oi.id">
                            <option :value="oi.id" x-text="oi.code + ' — ' + oi.libelle.substring(0, 60)"></option>
                        </template>
                    </select>
                    @error('objectif_immediat_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            @else
            <input type="hidden" name="objectif_immediat_id" value="{{ $oi->id }}">
            {{-- Contexte OI --}}
            <div class="bg-indigo-50 rounded-lg p-3 border border-indigo-100">
                <p class="text-xs text-indigo-600 font-medium mb-0.5">Objectif immédiat parent</p>
                <p class="font-semibold text-indigo-900">{{ $oi->code }} — {{ Str::limit($oi->libelle, 100) }}</p>
                <p class="text-xs text-indigo-500 mt-0.5">
                    {{ $oi->actionPrioritaire?->code }} → {{ $oi->actionPrioritaire?->papa?->code }}
                </p>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           placeholder="Ex : RA-DPS-01-01-01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre</label>
                    <input type="number" name="ordre" value="{{ old('ordre', 1) }}" min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" name="libelle" value="{{ old('libelle') }}"
                       placeholder="Intitulé du résultat attendu"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type de résultat <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        @foreach(['output' => ['label' => 'Extrant (Output)', 'desc' => 'Produit direct de l\'activité'], 'outcome' => ['label' => 'Effet (Outcome)', 'desc' => 'Changement attendu à moyen terme'], 'impact' => ['label' => 'Impact', 'desc' => 'Changement structurel à long terme']] as $v => $opt)
                        <label class="flex items-start space-x-3 p-2 rounded-lg border cursor-pointer hover:bg-gray-50 transition
                            {{ old('type_resultat', 'output') === $v ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }}">
                            <input type="radio" name="type_resultat" value="{{ $v }}"
                                   {{ old('type_resultat', 'output') === $v ? 'checked' : '' }}
                                   class="accent-indigo-600 mt-0.5">
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $opt['label'] }}</p>
                                <p class="text-xs text-gray-400">{{ $opt['desc'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('type_resultat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Année de référence</label>
                        <input type="number" name="annee_reference" value="{{ old('annee_reference') }}"
                               min="2020" max="2040" placeholder="ex: 2025"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @error('annee_reference')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                        <select name="responsable_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Non assigné --</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('responsable_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->prenom }} {{ $u->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Preuve requise -->
            <div class="border border-gray-200 rounded-lg p-4" x-data="{ preuveRequise: {{ old('preuve_requise', 0) ? 'true' : 'false' }} }">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="hidden" name="preuve_requise" value="0">
                    <input type="checkbox" name="preuve_requise" value="1"
                           x-model="preuveRequise"
                           {{ old('preuve_requise') ? 'checked' : '' }}
                           class="w-4 h-4 accent-indigo-600">
                    <div>
                        <p class="text-sm font-medium text-gray-700">
                            <i class="fas fa-paperclip text-orange-500 mr-1"></i>Preuve documentaire requise
                        </p>
                        <p class="text-xs text-gray-400">Un document justificatif devra être joint à ce résultat</p>
                    </div>
                </label>
                <div x-show="preuveRequise" class="mt-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Type de preuve attendue</label>
                    <input type="text" name="type_preuve_attendue" value="{{ old('type_preuve_attendue') }}"
                           placeholder="Ex : Procès-verbal signé, rapport d'activité, photos..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ $oi ? route('objectifs-immediats.show', $oi) : route('resultats-attendus.index') }}"
                   class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-1"></i>Créer le résultat attendu
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function raForm() {
    return {
        papaId: '{{ old('papa_id', '') }}',
        oiId: '{{ old('objectif_immediat_id', '') }}',
        ois: @json($objectifsImmediats),

        init() {
            if (this.papaId) this.loadOIs();
        },

        loadOIs() {
            if (!this.papaId) { this.ois = []; this.oiId = ''; return; }
            fetch(`/api/papa/${this.papaId}/objectifs-immediats`)
                .then(r => r.json())
                .then(data => { this.ois = data; this.oiId = ''; })
                .catch(() => this.ois = []);
        }
    }
}
</script>
@endpush
@endsection
