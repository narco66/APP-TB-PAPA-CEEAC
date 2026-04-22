@extends('layouts.app')

@section('title', 'Paramètres généraux')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Généraux</li>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-gear text-indigo-600"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-800">Paramètres généraux</h1>
                <p class="text-sm text-gray-500">Configuration de base de l'application</p>
            </div>
        </div>
    </div>

    <form action="{{ route('parametres.generaux.save') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Erreurs de validation --}}
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-red-800 mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Erreurs de validation</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-xs text-red-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Identité de l'application --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-id-card text-indigo-400 mr-2"></i>
                    Identité de l'application
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Nom de l'application <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="app_nom"
                           value="{{ old('app_nom', $parametres['app_nom']?->valeur ?? $parametres['app_nom']?->valeur_defaut ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('app_nom') border-red-400 @enderror"
                           placeholder="TB-PAPA-CEEAC">
                    @error('app_nom')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Sigle <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="app_sigle"
                           value="{{ old('app_sigle', $parametres['app_sigle']?->valeur ?? $parametres['app_sigle']?->valeur_defaut ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('app_sigle') border-red-400 @enderror"
                           placeholder="TB-PAPA" maxlength="20">
                    @error('app_sigle')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Organisation <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="app_organisation"
                           value="{{ old('app_organisation', $parametres['app_organisation']?->valeur ?? $parametres['app_organisation']?->valeur_defaut ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('app_organisation') border-red-400 @enderror"
                           placeholder="Commission de la CEEAC">
                    @error('app_organisation')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Texte pied de page</label>
                    <input type="text" name="app_pied_page"
                           value="{{ old('app_pied_page', $parametres['app_pied_page']?->valeur ?? $parametres['app_pied_page']?->valeur_defaut ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="© Commission de la CEEAC — Tous droits réservés" maxlength="500">
                    @error('app_pied_page')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Localisation --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-globe text-indigo-400 mr-2"></i>
                    Localisation & formats
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Langue par défaut <span class="text-red-500">*</span>
                    </label>
                    <select name="app_langue_defaut"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @php $langueActuelle = old('app_langue_defaut', $parametres['app_langue_defaut']?->valeur ?? 'fr'); @endphp
                        <option value="fr" {{ $langueActuelle === 'fr' ? 'selected' : '' }}>Français</option>
                        <option value="en" {{ $langueActuelle === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Fuseau horaire <span class="text-red-500">*</span>
                    </label>
                    <select name="app_fuseau_horaire"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('app_fuseau_horaire') border-red-400 @enderror">
                        @php $fh = old('app_fuseau_horaire', $parametres['app_fuseau_horaire']?->valeur ?? 'Africa/Libreville'); @endphp
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ $fh === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                    @error('app_fuseau_horaire')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Devise <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="app_devise"
                           value="{{ old('app_devise', $parametres['app_devise']?->valeur ?? 'FCFA') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('app_devise') border-red-400 @enderror"
                           placeholder="FCFA" maxlength="10">
                    @error('app_devise')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Format de date <span class="text-red-500">*</span>
                    </label>
                    <select name="app_format_date"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @php $fd = old('app_format_date', $parametres['app_format_date']?->valeur ?? 'd/m/Y'); @endphp
                        <option value="d/m/Y" {{ $fd === 'd/m/Y' ? 'selected' : '' }}>JJ/MM/AAAA (ex: 29/03/2026)</option>
                        <option value="Y-m-d" {{ $fd === 'Y-m-d' ? 'selected' : '' }}>AAAA-MM-JJ (ex: 2026-03-29)</option>
                        <option value="d-m-Y" {{ $fd === 'd-m-Y' ? 'selected' : '' }}>JJ-MM-AAAA (ex: 29-03-2026)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Année de référence <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="app_annee_reference"
                           value="{{ old('app_annee_reference', $parametres['app_annee_reference']?->valeur ?? now()->year) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('app_annee_reference') border-red-400 @enderror"
                           min="2020" max="2040">
                    @error('app_annee_reference')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Couleur primaire</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="app_couleur_primaire"
                               value="{{ old('app_couleur_primaire', $parametres['app_couleur_primaire']?->valeur ?? '#4338ca') }}"
                               class="h-9 w-20 border border-gray-300 rounded-lg cursor-pointer">
                        <span class="text-xs text-gray-500">Couleur principale de l'interface</span>
                    </div>
                    @error('app_couleur_primaire')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('parametres.hub') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
            @can('parametres.generaux.modifier')
            <button type="submit"
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-save mr-2"></i>Enregistrer les paramètres
            </button>
            @endcan
        </div>

    </form>

</div>
@endsection
