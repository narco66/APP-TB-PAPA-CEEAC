@extends('layouts.app')

@section('title', 'Configuration RBM')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Configuration RBM</li>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl" x-data="{
    seuil_atteint: {{ old('rbm_seuil_atteint', $config['rbm_seuil_atteint'] ?? 80) }},
    seuil_risque: {{ old('rbm_seuil_risque', $config['rbm_seuil_risque'] ?? 50) }},
    seuil_non_atteint: {{ old('rbm_seuil_non_atteint', $config['rbm_seuil_non_atteint'] ?? 30) }},
    get conflict() {
        return parseInt(this.seuil_non_atteint) >= parseInt(this.seuil_risque)
            || parseInt(this.seuil_risque) >= parseInt(this.seuil_atteint);
    }
}">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-indigo-600"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-800">Configuration RBM</h1>
                <p class="text-sm text-gray-500">Seuils de performance et préfixes de codification pour la gestion axée sur les résultats</p>
            </div>
        </div>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center space-x-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Erreurs --}}
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

    {{-- Alerte conflit --}}
    <div x-show="conflict" x-cloak
         class="bg-amber-50 border border-amber-300 rounded-xl p-4 flex items-start space-x-3">
        <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-amber-800">Conflit de seuils détecté</p>
            <p class="text-xs text-amber-700 mt-1">Les seuils doivent respecter l'ordre suivant : <strong>Non atteint &lt; En risque &lt; Atteint</strong>. Corrigez les valeurs avant de sauvegarder.</p>
        </div>
    </div>

    <form action="{{ route('parametres.rbm.save') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Seuils de performance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                <h2 class="text-sm font-semibold text-indigo-800 flex items-center">
                    <i class="fas fa-sliders mr-2 text-indigo-500"></i>
                    Seuils de performance des indicateurs
                </h2>
                <p class="text-xs text-indigo-600 mt-1">Ces seuils déterminent la couleur du taux d'exécution affiché dans les tableaux de bord.</p>
            </div>

            <div class="p-6 space-y-8">

                {{-- Barre de visualisation --}}
                <div>
                    <p class="text-xs font-semibold text-gray-600 mb-3">Aperçu visuel de la répartition</p>
                    <div class="relative h-10 rounded-lg overflow-hidden flex shadow-inner border border-gray-200">
                        <div class="flex items-center justify-center text-xs font-bold text-white transition-all duration-300"
                             :style="'width: ' + seuil_non_atteint + '%; background: #ef4444;'">
                            <span x-show="seuil_non_atteint >= 8">Non atteint</span>
                        </div>
                        <div class="flex items-center justify-center text-xs font-bold text-white transition-all duration-300"
                             :style="'width: ' + (seuil_risque - seuil_non_atteint) + '%; background: #f59e0b;'">
                            <span x-show="(seuil_risque - seuil_non_atteint) >= 10">En risque</span>
                        </div>
                        <div class="flex items-center justify-center text-xs font-bold text-white transition-all duration-300 flex-1"
                             style="background: #10b981;">
                            <span>Atteint</span>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 mt-1 px-0.5">
                        <span>0%</span>
                        <span x-text="seuil_non_atteint + '%'" class="text-red-500 font-semibold"></span>
                        <span x-text="seuil_risque + '%'" class="text-amber-500 font-semibold"></span>
                        <span class="text-green-500 font-semibold">100%</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Seuil Non atteint --}}
                    <div class="group">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            <span class="inline-flex items-center space-x-1.5">
                                <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                                <span>Seuil "Non atteint" <span class="text-red-500">*</span></span>
                            </span>
                        </label>
                        <div class="relative">
                            <input type="number" name="rbm_seuil_non_atteint"
                                   x-model="seuil_non_atteint"
                                   min="1" max="99"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm pr-10 focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('rbm_seuil_non_atteint') border-red-400 @enderror">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">%</span>
                        </div>
                        <input type="range" x-model="seuil_non_atteint" min="1" max="99"
                               class="w-full mt-2 accent-red-500 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">En dessous de cette valeur → statut <span class="font-semibold text-red-600">Non atteint</span></p>
                        @error('rbm_seuil_non_atteint')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Seuil En risque --}}
                    <div class="group">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            <span class="inline-flex items-center space-x-1.5">
                                <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                                <span>Seuil "En risque" <span class="text-red-500">*</span></span>
                            </span>
                        </label>
                        <div class="relative">
                            <input type="number" name="rbm_seuil_risque"
                                   x-model="seuil_risque"
                                   min="1" max="99"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm pr-10 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 @error('rbm_seuil_risque') border-red-400 @enderror">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">%</span>
                        </div>
                        <input type="range" x-model="seuil_risque" min="1" max="99"
                               class="w-full mt-2 accent-amber-500 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">Entre non-atteint et cette valeur → statut <span class="font-semibold text-amber-600">En risque</span></p>
                        @error('rbm_seuil_risque')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Seuil Atteint --}}
                    <div class="group">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            <span class="inline-flex items-center space-x-1.5">
                                <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                                <span>Seuil "Atteint" <span class="text-red-500">*</span></span>
                            </span>
                        </label>
                        <div class="relative">
                            <input type="number" name="rbm_seuil_atteint"
                                   x-model="seuil_atteint"
                                   min="1" max="100"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm pr-10 focus:ring-2 focus:ring-green-400 focus:border-green-400 @error('rbm_seuil_atteint') border-red-400 @enderror">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">%</span>
                        </div>
                        <input type="range" x-model="seuil_atteint" min="1" max="100"
                               class="w-full mt-2 accent-green-500 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">À partir de cette valeur → statut <span class="font-semibold text-green-600">Atteint</span></p>
                        @error('rbm_seuil_atteint')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Règle récapitulative --}}
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-xs font-semibold text-gray-700 mb-2"><i class="fas fa-info-circle text-indigo-400 mr-1.5"></i>Règle de classification</p>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-medium">0% – <span x-text="seuil_non_atteint"></span>%</span>
                        <i class="fas fa-arrow-right text-gray-300"></i>
                        <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-semibold">Non atteint</span>
                        <i class="fas fa-pipe text-gray-200 mx-1"></i>
                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-medium"><span x-text="seuil_non_atteint"></span>% – <span x-text="seuil_risque"></span>%</span>
                        <i class="fas fa-arrow-right text-gray-300"></i>
                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-semibold">En risque</span>
                        <i class="fas fa-pipe text-gray-200 mx-1"></i>
                        <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 font-medium"><span x-text="seuil_risque"></span>% – 100%</span>
                        <i class="fas fa-arrow-right text-gray-300"></i>
                        <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 font-semibold">Atteint</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Préfixes de codification --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-purple-50">
                <h2 class="text-sm font-semibold text-purple-800 flex items-center">
                    <i class="fas fa-tag mr-2 text-purple-500"></i>
                    Préfixes de codification
                </h2>
                <p class="text-xs text-purple-600 mt-1">Ces préfixes sont utilisés pour générer les codes des éléments de la structure RBM (AP-001, OI-1.1, RA-1.1.1…).</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Préfixe Axe Prioritaire (AP) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="rbm_prefixe_ap"
                           value="{{ old('rbm_prefixe_ap', $config['rbm_prefixe_ap'] ?? 'AP') }}"
                           maxlength="10"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-purple-400 focus:border-purple-400 @error('rbm_prefixe_ap') border-red-400 @enderror"
                           placeholder="AP">
                    <p class="text-xs text-gray-400 mt-1">Ex : <code class="bg-gray-100 px-1 rounded">AP-001</code></p>
                    @error('rbm_prefixe_ap')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Préfixe Objectif Immédiat (OI) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="rbm_prefixe_oi"
                           value="{{ old('rbm_prefixe_oi', $config['rbm_prefixe_oi'] ?? 'OI') }}"
                           maxlength="10"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-purple-400 focus:border-purple-400 @error('rbm_prefixe_oi') border-red-400 @enderror"
                           placeholder="OI">
                    <p class="text-xs text-gray-400 mt-1">Ex : <code class="bg-gray-100 px-1 rounded">OI-1.1</code></p>
                    @error('rbm_prefixe_oi')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Préfixe Résultat Attendu (RA) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="rbm_prefixe_ra"
                           value="{{ old('rbm_prefixe_ra', $config['rbm_prefixe_ra'] ?? 'RA') }}"
                           maxlength="10"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-purple-400 focus:border-purple-400 @error('rbm_prefixe_ra') border-red-400 @enderror"
                           placeholder="RA">
                    <p class="text-xs text-gray-400 mt-1">Ex : <code class="bg-gray-100 px-1 rounded">RA-1.1.1</code></p>
                    @error('rbm_prefixe_ra')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('parametres.hub') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
            @can('parametres.rbm.modifier')
            <button type="submit"
                    :disabled="conflict"
                    :class="conflict ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                    class="px-6 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 transition flex items-center space-x-2">
                <i class="fas fa-save"></i>
                <span>Sauvegarder la configuration</span>
            </button>
            @endcan
        </div>
    </form>

</div>
@endsection
