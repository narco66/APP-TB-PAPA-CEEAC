@extends('layouts.app')
@section('title', $indicateur->code)
@section('page-title', $indicateur->code . ' - ' . Str::limit($indicateur->libelle, 60))

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('indicateurs.index') }}" class="hover:text-indigo-600">Indicateurs</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">{{ $indicateur->code }}</li>
@endsection

@push('apexcharts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush
@section('content')
<div class="space-y-6" x-data="{ onglet: 'suivi' }">

    <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $indicateur->code }}</span>
                    @php
                        $niveau = $indicateur->niveauAlerte();
                        $niveauColors = [
                            'rouge' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                            'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
                            'vert' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                            'neutre' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'],
                        ];
                        $nc = $niveauColors[$niveau];
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $nc['bg'] }} {{ $nc['text'] }}">
                        Alerte : {{ ucfirst($niveau) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs text-gray-500 bg-gray-50 border border-gray-200">
                        {{ ucfirst(str_replace('_', ' ', $indicateur->type_indicateur)) }}
                    </span>
                    <span class="text-lg font-bold text-{{ $indicateur->couleurTendance() }}-600">
                        {{ $indicateur->iconesTendance() }}
                    </span>
                </div>

                <h1 class="text-lg font-bold text-gray-800">{{ $indicateur->libelle }}</h1>
                @if($indicateur->definition)
                <p class="text-sm text-gray-500 mt-1">{{ $indicateur->definition }}</p>
                @endif

                <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2">
                    <span><i class="fas fa-building mr-1"></i>{{ $indicateur->direction?->libelle ?? '-' }}</span>
                    <span><i class="fas fa-user mr-1"></i>{{ $indicateur->responsable?->nomComplet() ?? '-' }}</span>
                    <span><i class="fas fa-sync mr-1"></i>{{ ucfirst($indicateur->frequence_collecte ?? '-') }}</span>
                    @if($indicateur->unite_mesure)
                    <span><i class="fas fa-ruler mr-1"></i>Unite : {{ $indicateur->unite_mesure }}</span>
                    @endif
                </div>
            </div>

            <div class="text-right">
                <div class="text-4xl font-bold
                    @if($niveau === 'rouge') text-red-600
                    @elseif($niveau === 'orange') text-orange-500
                    @elseif($niveau === 'vert') text-green-600
                    @else text-indigo-700 @endif">
                    {{ number_format($indicateur->taux_realisation_courant, 0) }}%
                </div>
                <p class="text-xs text-gray-400">de realisation</p>
                <p class="text-xs text-gray-400 mt-1">
                    Cible : <strong>{{ number_format($indicateur->valeur_cible_annuelle, 0) }} {{ $indicateur->unite_mesure }}</strong>
                </p>
                @if($indicateur->valeur_baseline)
                <p class="text-xs text-gray-400">Baseline : {{ number_format($indicateur->valeur_baseline, 0) }}</p>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <div class="relative h-4 bg-gray-100 rounded-full overflow-visible">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ min(100, $indicateur->taux_realisation_courant) }}%;
                            background: {{ $niveau === 'rouge' ? '#ef4444' : ($niveau === 'orange' ? '#f97316' : ($niveau === 'vert' ? '#22c55e' : '#6366f1')) }}">
                </div>
                @if($indicateur->seuil_alerte_rouge)
                <div class="absolute top-0 bottom-0 w-0.5 bg-red-500 opacity-70"
                     style="left: {{ $indicateur->seuil_alerte_rouge }}%"
                     title="Seuil rouge : {{ $indicateur->seuil_alerte_rouge }}%"></div>
                @endif
                @if($indicateur->seuil_alerte_orange)
                <div class="absolute top-0 bottom-0 w-0.5 bg-orange-400 opacity-70"
                     style="left: {{ $indicateur->seuil_alerte_orange }}%"
                     title="Seuil orange : {{ $indicateur->seuil_alerte_orange }}%"></div>
                @endif
                @if($indicateur->seuil_alerte_vert)
                <div class="absolute top-0 bottom-0 w-0.5 bg-green-500 opacity-70"
                     style="left: {{ $indicateur->seuil_alerte_vert }}%"
                     title="Seuil vert : {{ $indicateur->seuil_alerte_vert }}%"></div>
                @endif
            </div>
            <div class="flex justify-between text-xs text-gray-400 mt-1">
                <span>0%</span>
                @if($indicateur->seuil_alerte_rouge) <span class="text-red-500">| {{ $indicateur->seuil_alerte_rouge }}%</span> @endif
                @if($indicateur->seuil_alerte_orange) <span class="text-orange-500">| {{ $indicateur->seuil_alerte_orange }}%</span> @endif
                @if($indicateur->seuil_alerte_vert) <span class="text-green-500">| {{ $indicateur->seuil_alerte_vert }}%</span> @endif
                <span>100%</span>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('indicateurs.print', $indicateur) }}"
               target="_blank"
               class="px-4 py-2 bg-white text-gray-700 rounded-lg text-sm border border-gray-200 hover:bg-gray-50 transition">
                <i class="fas fa-print mr-1"></i>Version imprimable
            </a>
            @can('modifier', $indicateur)
            <a href="{{ route('indicateurs.edit', $indicateur) }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                <i class="fas fa-edit mr-1"></i>Modifier
            </a>
            @endcan
            @can('saisirValeur', $indicateur)
            <button onclick="document.getElementById('modal-valeur').classList.remove('hidden')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-1"></i>Saisir une valeur
            </button>
            @endcan
        </div>
    </div>

    <div class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm border border-gray-100">
        @foreach(['suivi' => 'Suivi (' . $indicateur->valeurs->count() . ')', 'definition' => 'Definition', 'seuils' => 'Seuils & Alertes'] as $key => $label)
        <button @click="onglet = '{{ $key }}'"
                :class="onglet === '{{ $key }}' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition">{{ $label }}</button>
        @endforeach
    </div>

    <div x-show="onglet === 'suivi'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($indicateur->valeurs->count() > 0)
        <div class="p-5 border-b border-gray-100">
            <div id="chart-indicateur" style="min-height: 220px;"></div>
        </div>
        @endif

        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Periode</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500">Cible</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500">Realise</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500">Taux</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Statut</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Saisi par</th>
                    @can('validerValeur', $indicateur)
                    <th class="px-4 py-3"></th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($indicateur->valeurs->sortByDesc('created_at') as $v)
                <tr class="{{ $v->statut_validation === 'rejete' ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $v->periode_libelle }}</p>
                        <p class="text-xs text-gray-400">{{ $v->annee }} • {{ ucfirst($v->periode_type) }}</p>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">
                        {{ $v->valeur_cible_periode ? number_format($v->valeur_cible_periode, 2) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">
                        {{ $v->valeur_realisee !== null ? number_format($v->valeur_realisee, 2) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-bold {{ $v->taux_realisation >= 75 ? 'text-green-600' : ($v->taux_realisation >= 50 ? 'text-orange-500' : 'text-red-600') }}">
                            {{ number_format($v->taux_realisation, 0) }}%
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $v->statut_validation === 'valide' ? 'bg-green-100 text-green-700' : ($v->statut_validation === 'rejete' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst(str_replace('_', ' ', $v->statut_validation)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">
                        {{ $v->saisiPar?->nomComplet() ?? '-' }}<br>
                        {{ $v->created_at->format('d/m/Y') }}
                    </td>
                    @can('validerValeur', $indicateur)
                    @if($v->statut_validation === 'brouillon')
                    <td class="px-4 py-3">
                        <form action="{{ route('indicateurs.valider-valeur', $v) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="valide">
                            <button class="text-green-600 hover:text-green-800 text-xs mr-2">
                                <i class="fas fa-check"></i> Valider
                            </button>
                        </form>
                        <form action="{{ route('indicateurs.valider-valeur', $v) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="rejete">
                            <button class="text-red-500 hover:text-red-700 text-xs">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                        </form>
                    </td>
                    @else
                    <td></td>
                    @endif
                    @endcan
                </tr>
                @if($v->commentaire)
                <tr class="bg-gray-50">
                    <td colspan="7" class="px-4 py-2 text-xs text-gray-500 italic">
                        <i class="fas fa-comment mr-1"></i>{{ $v->commentaire }}
                    </td>
                </tr>
                @endif
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">Aucune valeur saisie.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="onglet === 'definition'" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="grid grid-cols-2 gap-6 text-sm">
            <div class="space-y-3">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Methode de calcul</p>
                    <p class="text-gray-700 mt-1">{{ $indicateur->methode_calcul ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Source des donnees</p>
                    <p class="text-gray-700 mt-1">{{ $indicateur->source_donnees ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Outil de collecte</p>
                    <p class="text-gray-700 mt-1">{{ $indicateur->outil_collecte ?: '-' }}</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Valeur baseline</p>
                    <p class="text-gray-700 mt-1">{{ $indicateur->valeur_baseline !== null ? number_format($indicateur->valeur_baseline, 2) . ' ' . $indicateur->unite_mesure : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Cible annuelle</p>
                    <p class="text-gray-700 mt-1">{{ number_format($indicateur->valeur_cible_annuelle, 2) }} {{ $indicateur->unite_mesure }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Frequence de collecte</p>
                    <p class="text-gray-700 mt-1">{{ ucfirst($indicateur->frequence_collecte ?? '-') }}</p>
                </div>
            </div>
        </div>
        @if($indicateur->notes)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Notes</p>
            <p class="text-sm text-gray-600">{{ $indicateur->notes }}</p>
        </div>
        @endif
    </div>

    <div x-show="onglet === 'seuils'" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-700 mb-4">Seuils de declenchement des alertes</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center">
                <i class="fas fa-exclamation-circle text-red-500 text-2xl mb-2"></i>
                <p class="text-xs text-gray-500 mb-1">Seuil rouge (critique)</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ $indicateur->seuil_alerte_rouge ? $indicateur->seuil_alerte_rouge . '%' : '-' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Alerte critique si <= ce seuil</p>
            </div>
            <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-center">
                <i class="fas fa-exclamation-triangle text-orange-500 text-2xl mb-2"></i>
                <p class="text-xs text-gray-500 mb-1">Seuil orange (attention)</p>
                <p class="text-2xl font-bold text-orange-500">
                    {{ $indicateur->seuil_alerte_orange ? $indicateur->seuil_alerte_orange . '%' : '-' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Alerte si <= ce seuil</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-center">
                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                <p class="text-xs text-gray-500 mb-1">Seuil vert (bon)</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ $indicateur->seuil_alerte_vert ? $indicateur->seuil_alerte_vert . '%' : '-' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Performance satisfaisante si >=</p>
            </div>
        </div>
        <div class="mt-4 p-3 bg-indigo-50 rounded-lg text-sm text-indigo-700">
            <strong>Niveau actuel :</strong> {{ ucfirst($niveau) }}
            ({{ number_format($indicateur->taux_realisation_courant, 1) }}% de realisation)
        </div>
    </div>

</div>

<div id="modal-valeur" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Saisir une valeur</h3>
        <form action="{{ route('indicateurs.saisir-valeur', $indicateur) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de periode <span class="text-red-500">*</span></label>
                    <select name="periode_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="mensuelle">Mensuelle</option>
                        <option value="trimestrielle" selected>Trimestrielle</option>
                        <option value="semestrielle">Semestrielle</option>
                        <option value="annuelle">Annuelle</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Annee <span class="text-red-500">*</span></label>
                    <input type="number" name="annee" value="{{ date('Y') }}"
                           min="2020" max="2050"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Libelle periode <span class="text-red-500">*</span></label>
                    <input type="text" name="periode_libelle" placeholder="Ex : T1-2025, Janvier 2025"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trimestre</label>
                    <select name="trimestre" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">-</option>
                        @for($q = 1; $q <= 4; $q++)
                        <option value="{{ $q }}">T{{ $q }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Cible periode
                        @if($indicateur->unite_mesure) <span class="text-gray-400">({{ $indicateur->unite_mesure }})</span> @endif
                    </label>
                    <input type="number" name="valeur_cible_periode" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Valeur realisee
                        @if($indicateur->unite_mesure) <span class="text-gray-400">({{ $indicateur->unite_mesure }})</span> @endif
                    </label>
                    <input type="number" name="valeur_realisee" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire / Analyse</label>
                <textarea name="commentaire" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none"
                          placeholder="Justification, observations..."></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-valeur').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@if($indicateur->valeurs->count() > 0)
<script>
const data = @json($valeursCourbes);
new ApexCharts(document.querySelector('#chart-indicateur'), {
    chart: { type: 'line', height: 220, toolbar: { show: false } },
    series: [
        { name: 'Realise', data: data.map(d => d.realise) },
        { name: 'Cible', data: data.map(d => d.cible) },
    ],
    xaxis: { categories: data.map(d => d.periode) },
    colors: ['#6366f1', '#94a3b8'],
    stroke: { curve: 'smooth', width: [3, 2], dashArray: [0, 5] },
    markers: { size: 4 },
    legend: { position: 'top' },
    yaxis: { title: { text: '{{ $indicateur->unite_mesure }}' } },
    tooltip: { y: { formatter: v => v !== null ? v.toFixed(2) + ' {{ $indicateur->unite_mesure }}' : '-' } },
}).render();
</script>
@endif
@endpush
