@extends('layouts.app')
@section('title', 'Modifier — ' . $activite->code)
@section('page-title', 'Modifier l\'activité')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('activites.index') }}" class="hover:text-indigo-600">Activités</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('activites.show', $activite) }}" class="hover:text-indigo-600">{{ $activite->code }}</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Modifier</li>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="mb-4 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <!-- Header info -->
        <div class="flex items-center space-x-3 mb-6">
            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $activite->code }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                bg-{{ $activite->couleurStatut() }}-100 text-{{ $activite->couleurStatut() }}-700">
                {{ ucfirst(str_replace('_', ' ', $activite->statut)) }}
            </span>
            <h2 class="text-base font-bold text-gray-800">{{ Str::limit($activite->libelle, 80) }}</h2>
        </div>

        <form action="{{ route('activites.update', $activite) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Rattachement (lecture seule) -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Rattachement PAPA (non modifiable)</h3>
                <div class="text-sm text-gray-700 space-y-1">
                    <p><span class="font-medium">PAPA :</span> {{ $activite->resultatAttendu?->objectifImmediats?->actionPrioritaire?->papa?->code ?? '—' }}</p>
                    <p><span class="font-medium">Résultat attendu :</span> {{ $activite->resultatAttendu?->code }} — {{ Str::limit($activite->resultatAttendu?->libelle, 100) }}</p>
                </div>
            </div>

            <!-- Libellé -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" name="libelle" value="{{ old('libelle', $activite->libelle) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description', $activite->description) }}</textarea>
            </div>

            <!-- Organisation -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Direction responsable <span class="text-red-500">*</span>
                    </label>
                    <select name="direction_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($directions as $dir)
                        <option value="{{ $dir->id }}"
                                {{ old('direction_id', $activite->direction_id) == $dir->id ? 'selected' : '' }}>
                            {{ $dir->sigle }} — {{ $dir->libelle }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                    <select name="service_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Aucun service --</option>
                        @foreach($services as $svc)
                        <option value="{{ $svc->id }}"
                                {{ old('service_id', $activite->service_id) == $svc->id ? 'selected' : '' }}>
                            {{ $svc->libelle }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Responsables -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <select name="responsable_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Non assigné --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}"
                                {{ old('responsable_id', $activite->responsable_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->nomComplet() }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Point Focal</label>
                    <select name="point_focal_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Aucun --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}"
                                {{ old('point_focal_id', $activite->point_focal_id) == $u->id ? 'selected' : '' }}>
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
                    @foreach(['critique' => 'Critique', 'haute' => 'Haute', 'normale' => 'Normale', 'basse' => 'Basse'] as $val => $label)
                    <label class="flex-1 flex items-center justify-center border-2 rounded-lg px-3 py-2 cursor-pointer transition
                        {{ old('priorite', $activite->priorite) === $val ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="priorite" value="{{ $val }}"
                               {{ old('priorite', $activite->priorite) === $val ? 'checked' : '' }}
                               class="sr-only">
                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Dates prévisionnelles -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début prévue</label>
                    <input type="date" name="date_debut_prevue"
                           value="{{ old('date_debut_prevue', $activite->date_debut_prevue?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin prévue</label>
                    <input type="date" name="date_fin_prevue"
                           value="{{ old('date_fin_prevue', $activite->date_fin_prevue?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('date_fin_prevue')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Budget -->
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Budget prévu (XAF)</label>
                    <input type="number" name="budget_prevu"
                           value="{{ old('budget_prevu', $activite->budget_prevu) }}"
                           step="100000" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-end">
                    <p class="text-xs text-gray-400">
                        Engagé : <strong>{{ number_format($activite->budget_engage / 1000000, 2) }} M XAF</strong><br>
                        Consommé : <strong>{{ number_format($activite->budget_consomme / 1000000, 2) }} M XAF</strong>
                    </p>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes / Observations</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes', $activite->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('activites.show', $activite) }}"
                   class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-1"></i>Retour
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-1"></i>Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
