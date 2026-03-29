@extends('layouts.app')

@section('title', $libelleType)

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.referentiels.index') }}" class="hover:text-indigo-600">Référentiels</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">{{ $libelleType }}</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ modalAdd: false, modalEdit: null, editData: {} }">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('parametres.referentiels.index') }}"
                   class="h-10 w-10 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                    <i class="fas fa-arrow-left text-gray-600 text-sm"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">{{ $libelleType }}</h1>
                    <p class="text-xs text-gray-400 font-mono">type: {{ $type }}</p>
                </div>
            </div>
            @can('parametres.referentiels.gerer')
            <button type="button" @click="modalAdd = true"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-plus mr-2"></i>Ajouter une entrée
            </button>
            @endcan
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Entrées du référentiel</h2>
            <div class="flex items-center space-x-3">
                <span class="text-xs text-gray-500">{{ $referentiels->count() }} entrée(s)</span>
            </div>
        </div>

        @if($referentiels->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-list text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-500 text-sm">Aucune entrée pour ce référentiel.</p>
            @can('parametres.referentiels.gerer')
            <button type="button" @click="modalAdd = true"
                    class="mt-3 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                <i class="fas fa-plus mr-2"></i>Ajouter la première entrée
            </button>
            @endcan
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="referentiel-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="w-8 px-4 py-3"></th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Libellé</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Libellé court</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ordre</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="sortable-body">
                    @foreach($referentiels as $ref)
                    <tr class="hover:bg-gray-50 transition" data-id="{{ $ref->id }}">
                        <td class="px-4 py-3 cursor-grab text-gray-300 hover:text-gray-500">
                            <i class="fas fa-grip-vertical"></i>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">{{ $ref->code }}</span>
                            @if($ref->est_systeme)
                                <span class="ml-1 px-1.5 py-0.5 bg-indigo-100 text-indigo-600 rounded text-xs" title="Entrée système">
                                    <i class="fas fa-shield-halved"></i>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-800 font-medium">{{ $ref->libelle }}</span>
                            @if($ref->description)
                                <p class="text-xs text-gray-400 truncate max-w-xs mt-0.5">{{ $ref->description }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($ref->libelle_court)
                                <span class="text-xs text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded border border-gray-200">
                                    {{ $ref->libelle_court }}
                                </span>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs text-gray-500">{{ $ref->ordre }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($ref->actif)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Actif</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">Inactif</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center justify-end space-x-2">
                                @can('parametres.referentiels.gerer')

                                {{-- Modifier --}}
                                @unless($ref->est_systeme)
                                <button type="button"
                                        @click="modalEdit = {{ $ref->id }}; editData = {
                                            id: {{ $ref->id }},
                                            libelle: '{{ addslashes($ref->libelle) }}',
                                            libelle_court: '{{ addslashes($ref->libelle_court ?? '') }}',
                                            description: '{{ addslashes($ref->description ?? '') }}',
                                            couleur: '{{ $ref->couleur ?? '' }}',
                                            ordre: {{ $ref->ordre }}
                                        }"
                                        class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Modifier">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                @endunless

                                {{-- Toggle actif/inactif --}}
                                @unless($ref->est_systeme && $ref->actif)
                                <form action="{{ route('parametres.referentiels.toggle', [$type, $ref]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="p-1.5 {{ $ref->actif ? 'text-amber-600 hover:bg-amber-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition"
                                            title="{{ $ref->actif ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas {{ $ref->actif ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                                    </button>
                                </form>
                                @endunless

                                {{-- Supprimer --}}
                                @unless($ref->est_systeme)
                                <form action="{{ route('parametres.referentiels.destroy', [$type, $ref]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition"
                                            title="Supprimer"
                                            onclick="return confirm('Supprimer « {{ $ref->libelle }} » ?')">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                                @endunless

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

    {{-- Modal Ajouter --}}
    <div x-show="modalAdd" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div @click.outside="modalAdd = false"
             class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">
                    <i class="fas fa-plus text-green-600 mr-2"></i>Ajouter une entrée
                </h3>
                <button @click="modalAdd = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('parametres.referentiels.store', $type) }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Code <span class="text-red-500">*</span>
                                <span class="font-normal text-gray-400">(sera mis en majuscules)</span>
                            </label>
                            <input type="text" name="code" required maxlength="40"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="EX_CODE">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ordre</label>
                            <input type="number" name="ordre" min="0" value="99"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Libellé <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="libelle" required maxlength="200"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Libellé complet">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Libellé court</label>
                        <input type="text" name="libelle_court" maxlength="60"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Abréviation (optionnel)">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                        <textarea name="description" rows="2" maxlength="500"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Description optionnelle"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
                    <button type="button" @click="modalAdd = false"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-plus mr-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modals Modifier --}}
    @foreach($referentiels as $ref)
    @unless($ref->est_systeme)
    <div x-show="modalEdit === {{ $ref->id }}" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div @click.outside="modalEdit = null"
             class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">
                    <i class="fas fa-edit text-indigo-600 mr-2"></i>Modifier — {{ $ref->code }}
                </h3>
                <button @click="modalEdit = null" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('parametres.referentiels.update', [$type, $ref]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Libellé <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="libelle" required maxlength="200"
                               value="{{ $ref->libelle }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Libellé court</label>
                        <input type="text" name="libelle_court" maxlength="60"
                               value="{{ $ref->libelle_court }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                        <textarea name="description" rows="2" maxlength="500"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $ref->description }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ordre</label>
                        <input type="number" name="ordre" min="0"
                               value="{{ $ref->ordre }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
                    <button type="button" @click="modalEdit = null"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endunless
    @endforeach

</div>
@endsection
