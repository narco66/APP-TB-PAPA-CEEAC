@extends('layouts.app')
@section('title', 'Workflow — ' . $definition->libelle)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-500 mb-6 flex items-center gap-1.5">
        <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <a href="{{ route('parametres.hub') }}" class="hover:underline">Paramètres</a>
        <span>/</span>
        <a href="{{ route('parametres.workflows.index') }}" class="hover:underline">Workflows</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">{{ $definition->code }}</span>
    </nav>

    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $definition->libelle }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-xs">{{ $definition->code }}</span>
                &nbsp;·&nbsp; Module : <strong>{{ $definition->module_cible }}</strong>
                &nbsp;·&nbsp; v{{ $definition->version }}
                &nbsp;·&nbsp;
                @if($definition->actif)
                    <span class="text-green-600 font-medium"><i class="fas fa-circle text-xs"></i> Actif</span>
                @else
                    <span class="text-gray-400 font-medium"><i class="fas fa-circle text-xs"></i> Inactif</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            @can('parametres.workflows.modifier')
            <form action="{{ route('parametres.workflows.toggle', $definition) }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border transition
                               {{ $definition->actif ? 'border-orange-300 text-orange-600 hover:bg-orange-50' : 'border-green-300 text-green-600 hover:bg-green-50' }}">
                    <i class="fas fa-power-off mr-1"></i>{{ $definition->actif ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
            @endcan
            <a href="{{ route('parametres.workflows.index') }}"
               class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Infos générales --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">
                <i class="fas fa-info-circle text-indigo-400 mr-1.5"></i>Informations générales
            </h2>
            @can('parametres.workflows.modifier')
            <form action="{{ route('parametres.workflows.update', $definition) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Libellé</label>
                    <input type="text" name="libelle" value="{{ old('libelle', $definition->libelle) }}"
                           required maxlength="200"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="description" rows="3" maxlength="500"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 resize-none">{{ old('description', $definition->description) }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="actif" value="0">
                    <input type="checkbox" name="actif" id="actif" value="1" {{ $definition->actif ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                    <label for="actif" class="text-sm text-gray-700">Workflow actif</label>
                </div>
                <button type="submit"
                        class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-save mr-1.5"></i>Sauvegarder
                </button>
            </form>
            @else
            <div class="space-y-3 text-sm text-gray-600">
                <div><span class="font-medium">Libellé :</span> {{ $definition->libelle }}</div>
                <div><span class="font-medium">Description :</span> {{ $definition->description ?? '—' }}</div>
            </div>
            @endcan
        </div>

        {{-- Étapes --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">
                <i class="fas fa-sitemap text-amber-400 mr-1.5"></i>Étapes du circuit
                <span class="ml-1.5 text-xs text-gray-400 font-normal">({{ $definition->steps->count() }} étapes)</span>
            </h2>

            <div class="space-y-3" id="steps-list">
                @forelse($definition->steps->sortBy('ordre') as $step)
                <div class="border border-gray-100 rounded-lg p-4 hover:border-indigo-200 transition" x-data="{ editStep: false }">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center flex-shrink-0">
                                {{ $step->ordre }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $step->libelle }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $step->code }}</p>
                            </div>
                            @if($step->est_etape_initiale)
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Initiale</span>
                            @endif
                            @if($step->est_etape_finale)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Finale</span>
                            @endif
                        </div>
                        @can('parametres.workflows.modifier')
                        <button @click="editStep = !editStep"
                                class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-lg transition text-xs">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endcan
                    </div>

                    <div class="mt-2 grid grid-cols-3 gap-2 text-xs text-gray-500">
                        <div><span class="font-medium">Rôle requis :</span> {{ $step->role_requis ?? '—' }}</div>
                        <div><span class="font-medium">Délai :</span> {{ $step->delai_jours ?? '—' }} j</div>
                        <div><span class="font-medium">Escalade :</span> {{ $step->escalade_apres_jours ?? '—' }} j</div>
                    </div>

                    @can('parametres.workflows.modifier')
                    <div x-show="editStep" x-collapse class="mt-4 pt-4 border-t border-gray-100">
                        <form action="{{ route('parametres.workflows.steps.update', [$definition, $step]) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Libellé</label>
                                    <input type="text" name="libelle" value="{{ $step->libelle }}" required maxlength="200"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-1 focus:ring-indigo-300">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Rôle requis</label>
                                    <input type="text" name="role_requis" value="{{ $step->role_requis }}" maxlength="100"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-1 focus:ring-indigo-300"
                                           placeholder="ex: secretaire_general">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Permission requise</label>
                                    <input type="text" name="permission_requise" value="{{ $step->permission_requise }}" maxlength="100"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-1 focus:ring-indigo-300"
                                           placeholder="ex: workflow.approuver">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Délai (jours)</label>
                                    <input type="number" name="delai_jours" value="{{ $step->delai_jours }}" min="1" max="365"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-1 focus:ring-indigo-300">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Escalade après (jours)</label>
                                    <input type="number" name="escalade_apres_jours" value="{{ $step->escalade_apres_jours }}" min="1" max="30"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-1 focus:ring-indigo-300">
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit"
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-lg font-medium transition">
                                    <i class="fas fa-save mr-1"></i>Enregistrer
                                </button>
                                <button type="button" @click="editStep = false"
                                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-lg transition">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                    @endcan
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">Aucune étape configurée.</p>
                @endforelse
            </div>

            <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                <i class="fas fa-exclamation-triangle mr-1.5"></i>
                <strong>Important :</strong> Toute modification des étapes affecte les workflows <strong>futurs</strong>.
                Les workflows en cours d'exécution conservent leur configuration d'origine.
            </div>
        </div>
    </div>
</div>
@endsection
