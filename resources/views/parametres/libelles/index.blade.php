@extends('layouts.app')

@section('title', 'Libellés métier')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Libellés métier</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ moduleFiltre: '{{ request('module', '') }}', editId: null }">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-purple-600"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Libellés métier</h1>
                    <p class="text-sm text-gray-500">Personnaliser les termes affichés dans l'application</p>
                </div>
            </div>

            {{-- Filtre module --}}
            <div class="flex items-center space-x-3">
                <form method="GET" action="{{ route('parametres.libelles.index') }}" class="flex items-center space-x-2">
                    <label class="text-xs font-semibold text-gray-600">Module :</label>
                    <select name="module" onchange="this.form.submit()"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Tous les modules</option>
                        @foreach($modules as $m)
                            <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>
                                {{ ucfirst($m) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- Groupes par module --}}
    @php $groupes = $libelles->groupBy('module'); @endphp

    @foreach($groupes as $module => $items)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-purple-50">
            <div class="flex items-center space-x-3">
                <i class="fas fa-folder text-purple-500 text-sm"></i>
                <h2 class="text-sm font-semibold text-purple-800">Module : {{ ucfirst($module) }}</h2>
                <span class="px-2 py-0.5 bg-purple-100 text-purple-600 rounded-full text-xs">{{ $items->count() }} libellé(s)</span>
            </div>
            @can('parametres.libelles.modifier')
            <form action="{{ route('parametres.libelles.reinitialiser', $module) }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-3 py-1.5 bg-white border border-purple-200 hover:bg-purple-100 text-purple-700 rounded-lg text-xs font-medium transition"
                        onclick="return confirm('Réinitialiser tous les libellés du module « {{ $module }} » aux valeurs par défaut ?')">
                    <i class="fas fa-rotate-left mr-1"></i>Réinitialiser le module
                </button>
            </form>
            @endcan
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($items as $libelle)
            <div class="px-6 py-4 hover:bg-gray-50 transition" x-data="{ editing: false }">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ $libelle->cle }}</span>
                            @if($libelle->est_systeme)
                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-600 rounded text-xs font-medium" title="Libellé système">
                                    <i class="fas fa-shield-halved mr-1"></i>Système
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Valeur par défaut</p>
                                <p class="text-sm text-gray-700 font-medium">{{ $libelle->valeur_defaut }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Valeur courante</p>
                                @if($libelle->valeur_courante)
                                    <p class="text-sm text-purple-700 font-medium">
                                        <i class="fas fa-pen text-xs mr-1"></i>{{ $libelle->valeur_courante }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 italic">Idem valeur par défaut</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @can('parametres.libelles.modifier')
                    <div class="flex-shrink-0">
                        <button type="button" @click="editing = !editing"
                                class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Modifier">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                    </div>
                    @endcan
                </div>

                {{-- Formulaire d'édition inline --}}
                @can('parametres.libelles.modifier')
                <div x-show="editing" x-cloak class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <form action="{{ route('parametres.libelles.update', $libelle) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    Valeur courante
                                    <span class="font-normal text-gray-400">(laisser vide pour utiliser la valeur par défaut)</span>
                                </label>
                                <input type="text" name="valeur_courante"
                                       value="{{ old('valeur_courante', $libelle->valeur_courante) }}"
                                       maxlength="300"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="{{ $libelle->valeur_defaut }}">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Valeur courte</label>
                                <input type="text" name="valeur_courte"
                                       value="{{ old('valeur_courte', $libelle->valeur_courte) }}"
                                       maxlength="100"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="Abréviation">
                            </div>
                        </div>
                        <div class="flex items-center justify-end space-x-2 mt-3">
                            <button type="button" @click="editing = false"
                                    class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50 transition">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition">
                                <i class="fas fa-save mr-1"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
                @endcan

            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($libelles->isEmpty())
    <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-gray-100">
        <i class="fas fa-tags text-gray-300 text-4xl mb-4"></i>
        <p class="text-gray-500 text-sm">Aucun libellé trouvé.</p>
    </div>
    @endif

</div>
@endsection
