@extends('layouts.app')
@section('title', 'Activites')
@section('page-title', 'Activites PAPA')

@section('content')
<div class="space-y-4">
    <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Statut</label>
                <select name="statut" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    <option value="">Tous</option>
                    @foreach(['non_demarree' => 'Non demarree', 'planifiee' => 'Planifiee', 'en_cours' => 'En cours', 'suspendue' => 'Suspendue', 'terminee' => 'Terminee', 'abandonnee' => 'Abandonnee'] as $val => $label)
                    <option value="{{ $val }}" {{ request('statut') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1">Priorite</label>
                <select name="priorite" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    <option value="">Toutes</option>
                    @foreach(['critique', 'haute', 'normale', 'basse'] as $p)
                    <option value="{{ $p }}" {{ request('priorite') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>

            @can('activite.voir_toutes_directions')
            <div>
                <label class="block text-xs text-gray-500 mb-1">Direction</label>
                <select name="direction_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    <option value="">Toutes</option>
                    @foreach($directions as $dir)
                    <option value="{{ $dir->id }}" {{ request('direction_id') == $dir->id ? 'selected' : '' }}>
                        {{ $dir->libelleAffichage() }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endcan

            <div class="flex items-center space-x-2">
                <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="en_retard" value="1" {{ request('en_retard') ? 'checked' : '' }}
                           class="mr-1.5 rounded text-indigo-600">
                    En retard uniquement
                </label>
            </div>

            <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-filter mr-1"></i>Filtrer
            </button>

            <a href="{{ route('activites.gantt') }}" class="px-4 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-project-diagram mr-1"></i>Gantt
            </a>

            @can('activite.creer')
            <a href="{{ route('activites.create') }}" class="px-4 py-1.5 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 ml-auto">
                <i class="fas fa-plus mr-1"></i>Nouvelle activite
            </a>
            @endcan
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Code / Activite</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Direction</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Delai prevu</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Avancement</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($activites as $activite)
                <tr class="hover:bg-gray-50 transition {{ $activite->estEnRetard() ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3">
                        <p class="font-mono text-xs text-gray-400">{{ $activite->code }}</p>
                        <p class="font-medium text-gray-800 max-w-xs truncate">{{ $activite->libelle }}</p>
                        <span class="px-1.5 py-0.5 rounded text-xs bg-{{ $activite->couleurPriorite() }}-100 text-{{ $activite->couleurPriorite() }}-700">
                            {{ ucfirst($activite->priorite) }}
                        </span>
                        @if($activite->estEnRetard())
                            <span class="ml-1 text-xs text-red-600 font-medium">Retard</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs">
                        <p>{{ $activite->direction?->libelleAffichage() }}</p>
                        <span class="px-1 py-0.5 rounded text-xs {{ $activite->direction?->estAppui() ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $activite->direction?->estAppui() ? 'Appui' : 'Technique' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if($activite->date_debut_prevue)
                        <p>Du {{ $activite->date_debut_prevue->format('d/m/Y') }}</p>
                        <p>Au {{ $activite->date_fin_prevue?->format('d/m/Y') }}</p>
                        @else
                        <span class="text-gray-300">Non planifie</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 h-2 bg-gray-100 rounded-full min-w-16">
                                <div class="h-full rounded-full"
                                     style="width: {{ min(100, $activite->taux_realisation) }}%;
                                            background: {{ $activite->taux_realisation >= 75 ? '#22c55e' : ($activite->taux_realisation >= 50 ? '#f59e0b' : '#ef4444') }}">
                                </div>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">{{ number_format($activite->taux_realisation, 0) }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $activite->couleurStatut() }}-100 text-{{ $activite->couleurStatut() }}-700">
                            {{ ucfirst(str_replace('_', ' ', $activite->statut)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('activites.show', $activite) }}"
                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Voir</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-tasks text-gray-200 text-4xl mb-3 block"></i>
                        Aucune activite trouvee.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-100">
            {{ $activites->links() }}
        </div>
    </div>
</div>
@endsection
