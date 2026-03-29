@extends('layouts.app')

@section('title', 'Gestion des PAPA')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Gestion des PAPA</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ modalArchive: null }">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-amber-600"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Gestion des PAPA</h1>
                    <p class="text-sm text-gray-500">Définir le PAPA actif, gérer les verrous et archivages</p>
                </div>
            </div>
            <a href="{{ route('parametres.papa.archives') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                <i class="fas fa-archive mr-2"></i>Voir les archives
            </a>
        </div>
    </div>

    {{-- Tableau des PAPA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Plans d'action (hors archives)</h2>
            <span class="text-xs text-gray-500">{{ $papas->count() }} PAPA(s)</span>
        </div>

        @if($papas->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-book text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-500 text-sm">Aucun PAPA disponible.</p>
            <a href="{{ route('papas.create') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>Créer un PAPA
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Code / Libellé</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Année</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Verrou</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Validé par</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($papas as $papa)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $papa->code }}</div>
                            <div class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ $papa->libelle }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="font-medium text-gray-700">{{ $papa->annee }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @php
                                $couleurs = [
                                    'brouillon'    => 'gray',
                                    'soumis'       => 'blue',
                                    'en_revision'  => 'yellow',
                                    'valide'       => 'green',
                                    'en_execution' => 'indigo',
                                    'cloture'      => 'orange',
                                    'rejete'       => 'red',
                                ];
                                $c = $couleurs[$papa->statut] ?? 'gray';
                                $labels = [
                                    'brouillon'    => 'Brouillon',
                                    'soumis'       => 'Soumis',
                                    'en_revision'  => 'En révision',
                                    'valide'       => 'Validé',
                                    'en_execution' => 'En exécution',
                                    'cloture'      => 'Clôturé',
                                    'rejete'       => 'Rejeté',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $c }}-100 text-{{ $c }}-700">
                                @if($papa->statut === 'en_execution')
                                    <i class="fas fa-circle text-indigo-500 mr-1 text-xs animate-pulse"></i>
                                @endif
                                {{ $labels[$papa->statut] ?? $papa->statut }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($papa->est_verrouille)
                                <span class="text-amber-600" title="Verrouillé">
                                    <i class="fas fa-lock"></i>
                                </span>
                            @else
                                <span class="text-gray-300" title="Non verrouillé">
                                    <i class="fas fa-lock-open"></i>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-xs text-gray-600">
                                {{ $papa->validePar?->nomComplet() ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end space-x-2">

                                {{-- Définir actif --}}
                                @can('parametres.papa.modifier')
                                @if(in_array($papa->statut, ['valide', 'en_execution']) && $papa->statut !== 'en_execution')
                                <form action="{{ route('parametres.papa.activer', $papa) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-medium transition"
                                            title="Définir comme PAPA actif"
                                            onclick="return confirm('Définir {{ $papa->code }} comme PAPA actif en exécution ?')">
                                        <i class="fas fa-play mr-1"></i>Activer
                                    </button>
                                </form>
                                @elseif($papa->statut === 'en_execution')
                                <span class="px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>Actif
                                </span>
                                @endif

                                {{-- Verrouiller / Déverrouiller --}}
                                @if(!$papa->est_verrouille)
                                <form action="{{ route('parametres.papa.verrouiller', $papa) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg text-xs font-medium transition"
                                            title="Verrouiller ce PAPA"
                                            onclick="return confirm('Verrouiller ce PAPA ? Il ne pourra plus être modifié.')">
                                        <i class="fas fa-lock mr-1"></i>Verrouiller
                                    </button>
                                </form>
                                @else
                                @role('super_admin')
                                <form action="{{ route('parametres.papa.deverrouiller', $papa) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-medium transition"
                                            title="Déverrouiller (super admin)">
                                        <i class="fas fa-lock-open mr-1"></i>Déverr.
                                    </button>
                                </form>
                                @endrole
                                @endif
                                @endcan

                                {{-- Archiver --}}
                                @can('parametres.papa.archiver')
                                @if(in_array($papa->statut, ['cloture', 'valide']))
                                <button type="button"
                                        @click="modalArchive = {{ $papa->id }}"
                                        class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium transition"
                                        title="Archiver ce PAPA">
                                    <i class="fas fa-archive mr-1"></i>Archiver
                                </button>
                                @endif
                                @endcan

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Modals d'archivage --}}
    @foreach($papas as $papa)
    @if(in_array($papa->statut, ['cloture', 'valide']))
    <div x-show="modalArchive === {{ $papa->id }}" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div @click.outside="modalArchive = null"
             class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-archive text-red-600 text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Archiver — {{ $papa->code }}</h3>
                </div>
                <button @click="modalArchive = null" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('parametres.papa.archiver', $papa) }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="text-xs text-red-700">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Attention :</strong> L'archivage est irréversible. Le PAPA sera verrouillé et ne pourra plus être modifié.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Motif d'archivage <span class="text-red-500">*</span>
                            <span class="font-normal text-gray-400">(min. 20 caractères)</span>
                        </label>
                        <textarea name="motif" rows="3" required minlength="20" maxlength="500"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Expliquez la raison de l'archivage de ce PAPA..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Confirmation <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-1.5">Tapez <strong class="text-red-600">ARCHIVER</strong> pour confirmer</p>
                        <input type="text" name="confirmation" required pattern="ARCHIVER"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="ARCHIVER">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
                    <button type="button" @click="modalArchive = null"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-archive mr-2"></i>Archiver définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endforeach

</div>
@endsection
