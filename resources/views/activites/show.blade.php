@extends('layouts.app')
@section('title', $activite->code)
@section('page-title', $activite->code . ' — ' . Str::limit($activite->libelle, 60))

@section('content')
<div class="space-y-6" x-data="{ onglet: 'detail' }">

    <!-- En-tête -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $activite->code }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        bg-{{ $activite->couleurStatut() }}-100 text-{{ $activite->couleurStatut() }}-700">
                        {{ ucfirst(str_replace('_', ' ', $activite->statut)) }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        bg-{{ $activite->couleurPriorite() }}-100 text-{{ $activite->couleurPriorite() }}-700">
                        {{ ucfirst($activite->priorite) }}
                    </span>
                    @if($activite->estEnRetard())
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-600 text-white animate-pulse">⚠ EN RETARD</span>
                    @endif
                </div>
                <h1 class="text-lg font-bold text-gray-800">{{ $activite->libelle }}</h1>
                <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2">
                    <span><i class="fas fa-building mr-1"></i>{{ $activite->direction?->libelle }}</span>
                    @if($activite->service) <span><i class="fas fa-layer-group mr-1"></i>{{ $activite->service->libelle }}</span> @endif
                    <span><i class="fas fa-user mr-1"></i>Resp. : {{ $activite->responsable?->nomComplet() }}</span>
                    @if($activite->pointFocal) <span><i class="fas fa-map-pin mr-1"></i>PF : {{ $activite->pointFocal->nomComplet() }}</span> @endif
                </div>
            </div>

            <div class="flex flex-col items-end">
                <div class="text-3xl font-bold text-indigo-700">{{ number_format($activite->taux_realisation, 0) }}%</div>
                <p class="text-xs text-gray-400">d'avancement</p>
            </div>
        </div>

        <!-- Barre avancement -->
        <div class="mt-4">
            <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full"
                     style="width: {{ min(100, $activite->taux_realisation) }}%;
                            background: {{ $activite->taux_realisation >= 75 ? '#22c55e' : ($activite->taux_realisation >= 50 ? '#f59e0b' : '#ef4444') }}">
                </div>
            </div>
        </div>

        <!-- Dates -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-400">Début prévu</p>
                <p class="font-semibold text-gray-700 text-sm">{{ $activite->date_debut_prevue?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-400">Fin prévue</p>
                <p class="font-semibold {{ $activite->estEnRetard() ? 'text-red-600' : 'text-gray-700' }} text-sm">
                    {{ $activite->date_fin_prevue?->format('d/m/Y') ?? '—' }}
                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-400">Début réel</p>
                <p class="font-semibold text-gray-700 text-sm">{{ $activite->date_debut_reelle?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-400">Fin réelle</p>
                <p class="font-semibold text-gray-700 text-sm">{{ $activite->date_fin_reelle?->format('d/m/Y') ?? '—' }}</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 flex flex-wrap gap-2">
            @can('mettreAJourAvancement', $activite)
            <button onclick="document.getElementById('modal-avancement').classList.remove('hidden')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-sync mr-1"></i>Mettre à jour l'avancement
            </button>
            @endcan
            @can('document.deposer')
            <a href="{{ route('documents.create') }}?documentable_type={{ urlencode(App\Models\Activite::class) }}&documentable_id={{ $activite->id }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                <i class="fas fa-paperclip mr-1"></i>Déposer un document
            </a>
            @endcan
        </div>
    </div>

    <!-- Onglets -->
    <div class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm border border-gray-100">
        @foreach(['detail' => 'Détail', 'taches' => 'Tâches (' . $activite->taches->count() . ')', 'jalons' => 'Jalons', 'budget' => 'Budget', 'documents' => 'Documents (' . $activite->documents->count() . ')'] as $key => $label)
        <button @click="onglet = '{{ $key }}'"
                :class="onglet === '{{ $key }}' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition">{{ $label }}</button>
        @endforeach
    </div>

    <!-- Détail -->
    <div x-show="onglet === 'detail'" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-700 mb-3">Description</h3>
        <p class="text-sm text-gray-600">{{ $activite->description ?: 'Aucune description.' }}</p>

        @if($activite->notes)
        <h3 class="font-semibold text-gray-700 mt-5 mb-2">Notes</h3>
        <p class="text-sm text-gray-600">{{ $activite->notes }}</p>
        @endif

        <h3 class="font-semibold text-gray-700 mt-5 mb-3">Rattachement</h3>
        <div class="text-sm text-gray-600 space-y-1">
            <p><span class="font-medium">PAPA :</span> {{ $activite->resultatAttendu?->objectifImmediats?->actionPrioritaire?->papa?->code }}</p>
            <p><span class="font-medium">Action prioritaire :</span> {{ $activite->resultatAttendu?->objectifImmediats?->actionPrioritaire?->libelle }}</p>
            <p><span class="font-medium">Objectif immédiat :</span> {{ $activite->resultatAttendu?->objectifImmediats?->libelle }}</p>
            <p><span class="font-medium">Résultat attendu :</span> {{ $activite->resultatAttendu?->libelle }}</p>
        </div>
    </div>

    <!-- Tâches -->
    <div x-show="onglet === 'taches'" class="bg-white rounded-xl shadow-sm border border-gray-100">
        @forelse($activite->taches->whereNull('parent_tache_id') as $tache)
        <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between">
            <div>
                <p class="font-medium text-sm text-gray-800">{{ $tache->libelle }}</p>
                <p class="text-xs text-gray-400">Assigné à : {{ $tache->assignee?->nomComplet() ?? 'Non assigné' }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm font-bold text-gray-700">{{ number_format($tache->taux_realisation, 0) }}%</span>
                <span class="px-2 py-0.5 rounded text-xs bg-{{ $tache->couleurStatut() }}-100 text-{{ $tache->couleurStatut() }}-700">
                    {{ str_replace('_', ' ', $tache->statut) }}
                </span>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">Aucune tâche définie.</div>
        @endforelse
    </div>

    <!-- Budget -->
    <div x-show="onglet === 'budget'" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="bg-gray-50 rounded-lg p-3 text-center">
                <p class="text-xs text-gray-400">Prévu</p>
                <p class="text-lg font-bold text-gray-800">{{ number_format($activite->budget_prevu / 1000000, 2) }} M XAF</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-3 text-center">
                <p class="text-xs text-gray-400">Engagé</p>
                <p class="text-lg font-bold text-blue-700">{{ number_format($activite->budget_engage / 1000000, 2) }} M XAF</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3 text-center">
                <p class="text-xs text-gray-400">Décaissé</p>
                <p class="text-lg font-bold text-green-700">{{ number_format($activite->budget_consomme / 1000000, 2) }} M XAF</p>
            </div>
        </div>
        <p class="text-xs text-gray-400">Reste à engager : {{ number_format($activite->resteAEngager() / 1000000, 2) }} M XAF</p>
    </div>

    <!-- Documents -->
    <div x-show="onglet === 'documents'" class="bg-white rounded-xl shadow-sm border border-gray-100">
        @forelse($activite->documents as $doc)
        <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">{{ $doc->iconeExtension() }}</span>
                <div>
                    <p class="font-medium text-sm text-gray-800">{{ $doc->titre }}</p>
                    <p class="text-xs text-gray-400">{{ $doc->tailleLisible() }} • v{{ $doc->version }} • {{ $doc->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            @can('telecharger', $doc)
            <a href="{{ route('documents.download', $doc) }}"
               class="text-indigo-600 text-sm hover:underline">
                <i class="fas fa-download mr-1"></i>Télécharger
            </a>
            @endcan
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">Aucun document attaché.</div>
        @endforelse
    </div>

</div>

<!-- Modal avancement -->
<div id="modal-avancement" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Mettre à jour l'avancement</h3>
        <form action="{{ route('activites.avancement', $activite) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Taux de réalisation (0–100%)</label>
                <input type="range" name="taux_realisation" min="0" max="100" step="5"
                       value="{{ $activite->taux_realisation }}"
                       class="w-full accent-indigo-600"
                       oninput="document.getElementById('taux-val').textContent = this.value + '%'">
                <div class="text-center text-2xl font-bold text-indigo-700 mt-1">
                    <span id="taux-val">{{ $activite->taux_realisation }}%</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['non_demarree' => 'Non démarrée', 'planifiee' => 'Planifiée', 'en_cours' => 'En cours', 'suspendue' => 'Suspendue', 'terminee' => 'Terminée', 'abandonnee' => 'Abandonnée'] as $val => $label)
                    <option value="{{ $val }}" {{ $activite->statut === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début réelle</label>
                <input type="date" name="date_debut_reelle" value="{{ $activite->date_debut_reelle?->format('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin réelle</label>
                <input type="date" name="date_fin_reelle" value="{{ $activite->date_fin_reelle?->format('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes de mise à jour</label>
                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none"
                          placeholder="Justification, observations...">{{ $activite->notes }}</textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-avancement').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
