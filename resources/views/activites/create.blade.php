@extends('layouts.app')
@section('title', 'Nouvelle activité')
@section('page-title', 'Créer une activité')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('activites.index') }}" class="hover:text-indigo-600">Activités</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Nouvelle</li>
@endsection

@section('content')
<div class="max-w-4xl" x-data="{
    estJalon: {{ old('est_jalon', 0) ? 'true' : 'false' }},
    selectedRA: '{{ old('resultat_attendu_id', '') }}',
    services: [],
    loadServices(directionId) {
        if (!directionId) { this.services = []; return; }
        fetch('/api/directions/' + directionId + '/services')
            .then(r => r.json())
            .then(data => this.services = data);
    }
}">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Informations de l'activité</h2>

        <form action="{{ route('activites.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Rattachement PAPA -->
            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                <h3 class="text-sm font-semibold text-indigo-800 mb-3">
                    <i class="fas fa-sitemap mr-1"></i>Rattachement au PAPA
                </h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Résultat attendu <span class="text-red-500">*</span>
                    </label>
                    <select name="resultat_attendu_id" x-model="selectedRA"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('resultat_attendu_id') border-red-500 @enderror">
                        <option value="">-- Sélectionner un résultat attendu --</option>
                        @foreach($resultatsAttendus as $ra)
                        <optgroup label="{{ $ra->objectifImmediats?->actionPrioritaire?->papa?->code ?? 'Inconnu' }} — {{ $ra->objectifImmediats?->actionPrioritaire?->libelle ?? '' }}">
                            <option value="{{ $ra->id }}" {{ old('resultat_attendu_id') == $ra->id ? 'selected' : '' }}>
                                {{ $ra->code }} — {{ Str::limit($ra->libelle, 80) }}
                            </option>
                        </optgroup>
                        @endforeach
                    </select>
                    @error('resultat_attendu_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Identification -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           placeholder="Ex : ACT-DPS-001"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="hidden" name="est_jalon" value="0">
                        <input type="checkbox" name="est_jalon" value="1"
                               x-model="estJalon"
                               {{ old('est_jalon') ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-600">
                        <span class="text-sm font-medium text-gray-700">
                            <i class="fas fa-flag text-purple-500 mr-1"></i>Jalon / Milestone
                        </span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" name="libelle" value="{{ old('libelle') }}"
                       placeholder="Intitulé complet de l'activité"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          placeholder="Contexte, objectif, livrables attendus..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <!-- Organisation -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Direction responsable <span class="text-red-500">*</span>
                    </label>
                    <select name="direction_id"
                            @change="loadServices($event.target.value)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('direction_id') border-red-500 @enderror">
                        <option value="">-- Direction --</option>
                        @foreach($directions as $dir)
                        <option value="{{ $dir->id }}"
                                {{ old('direction_id') == $dir->id ? 'selected' : '' }}>
                            {{ $dir->sigle }} — {{ $dir->libelle }}
                            @if($dir->type_direction === 'appui') (Appui) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('direction_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                    <select name="service_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Service (optionnel) --</option>
                        <template x-for="s in services" :key="s.id">
                            <option :value="s.id" x-text="s.libelle"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Responsables -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <select name="responsable_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Responsable --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('responsable_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->nomComplet() }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Point Focal</label>
                    <select name="point_focal_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Point Focal (optionnel) --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('point_focal_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->nomComplet() }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Priorité -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Priorité <span class="text-red-500">*</span>
                </label>
                <div class="flex space-x-3">
                    @foreach(['critique' => ['label' => 'Critique', 'color' => 'red'], 'haute' => ['label' => 'Haute', 'color' => 'orange'], 'normale' => ['label' => 'Normale', 'color' => 'blue'], 'basse' => ['label' => 'Basse', 'color' => 'gray']] as $val => $opt)
                    <label class="flex-1 flex items-center justify-center border-2 rounded-lg px-3 py-2 cursor-pointer transition
                        {{ old('priorite', 'normale') === $val ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="priorite" value="{{ $val }}"
                               {{ old('priorite', 'normale') === $val ? 'checked' : '' }}
                               class="sr-only">
                        <span class="text-sm font-medium text-{{ $opt['color'] }}-600">{{ $opt['label'] }}</span>
                    </label>
                    @endforeach
                </div>
                @error('priorite')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-5" x-show="!estJalon">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début prévue</label>
                    <input type="date" name="date_debut_prevue" value="{{ old('date_debut_prevue') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin prévue</label>
                    <input type="date" name="date_fin_prevue" value="{{ old('date_fin_prevue') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('date_fin_prevue')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Date jalon -->
            <div x-show="estJalon">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date du jalon</label>
                <input type="date" name="date_fin_prevue" value="{{ old('date_fin_prevue') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 max-w-xs">
            </div>

            <!-- Budget -->
            <div class="grid grid-cols-2 gap-5" x-show="!estJalon">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Budget prévu (XAF)</label>
                    <input type="number" name="budget_prevu" value="{{ old('budget_prevu', 0) }}"
                           step="100000" min="0"
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

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes / Observations</label>
                <textarea name="notes" rows="2"
                          placeholder="Observations, précisions complémentaires..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('activites.index') }}"
                   class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-1"></i>Créer l'activité
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Highlight selected radio
    document.querySelectorAll('input[name="priorite"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('input[name="priorite"]').forEach(r => {
                r.closest('label').classList.remove('border-indigo-500', 'bg-indigo-50');
                r.closest('label').classList.add('border-gray-200');
            });
            this.closest('label').classList.add('border-indigo-500', 'bg-indigo-50');
            this.closest('label').classList.remove('border-gray-200');
        });
    });
</script>
@endpush
