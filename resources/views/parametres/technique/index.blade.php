@extends('layouts.app')
@section('title', 'Paramètres techniques')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <nav class="text-xs text-gray-500 mb-6 flex items-center gap-1.5">
        <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
        <span>/</span>
        <a href="{{ route('parametres.hub') }}" class="hover:underline">Paramètres</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Paramètres techniques</span>
    </nav>

    {{-- Danger banner --}}
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg px-5 py-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 text-lg mt-0.5"></i>
            <div>
                <p class="font-bold text-red-800 text-sm">Zone réservée aux super administrateurs techniques</p>
                <p class="text-red-600 text-xs mt-1">
                    Toute modification incorrecte peut impacter le fonctionnement de l'application.
                    Chaque action est enregistrée dans le journal d'audit.
                </p>
            </div>
        </div>
    </div>

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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Informations système (lecture seule) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">
                <i class="fas fa-server text-red-400 mr-1.5"></i>Informations système
                <span class="ml-1 text-xs text-gray-400 font-normal">(lecture seule)</span>
            </h2>
            <div class="space-y-2.5 text-sm">
                @foreach([
                    ['label' => 'PHP', 'value' => $info['php_version'], 'icon' => 'fas fa-code'],
                    ['label' => 'Laravel', 'value' => $info['laravel_version'], 'icon' => 'fab fa-laravel'],
                    ['label' => 'Environnement', 'value' => strtoupper($info['env']),
                     'class' => $info['env'] === 'production' ? 'text-red-600 font-bold' : 'text-green-600 font-bold',
                     'icon' => 'fas fa-globe'],
                    ['label' => 'Mode debug', 'value' => $info['debug'] ? 'ACTIVÉ' : 'Désactivé',
                     'class' => $info['debug'] ? 'text-red-600 font-bold' : 'text-gray-600',
                     'icon' => 'fas fa-bug'],
                    ['label' => 'Fuseau horaire', 'value' => $info['timezone'], 'icon' => 'fas fa-clock'],
                    ['label' => 'Cache driver', 'value' => $info['cache_driver'], 'icon' => 'fas fa-database'],
                    ['label' => 'Queue driver', 'value' => $info['queue_driver'], 'icon' => 'fas fa-layer-group'],
                ] as $item)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="{{ $item['icon'] }} w-3.5 text-gray-400"></i>
                        {{ $item['label'] }}
                    </span>
                    <span class="{{ $item['class'] ?? 'text-gray-800 font-mono' }} text-xs">
                        {{ $item['value'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Paramètres configurables --}}
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">
                <i class="fas fa-sliders-h text-red-400 mr-1.5"></i>Paramètres configurables
            </h2>
            <form action="{{ route('parametres.technique.save') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        <i class="fas fa-clock text-gray-400 mr-1"></i>Durée de session (minutes)
                    </label>
                    <input type="number" name="session_duree_minutes"
                           value="{{ old('session_duree_minutes', $config['session_duree_minutes'] ?? 120) }}"
                           min="5" max="1440" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400">
                    <p class="text-xs text-gray-400 mt-1">Entre 5 et 1440 minutes (24h)</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        <i class="fas fa-upload text-gray-400 mr-1"></i>Taille max upload (Mo)
                    </label>
                    <input type="number" name="upload_taille_max_mo"
                           value="{{ old('upload_taille_max_mo', $config_ged['upload_taille_max_mo'] ?? 20) }}"
                           min="1" max="100" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        <i class="fas fa-file text-gray-400 mr-1"></i>Formats autorisés (séparés par virgule)
                    </label>
                    <input type="text" name="upload_formats_autorises"
                           value="{{ old('upload_formats_autorises', $config_ged['upload_formats_autorises'] ?? 'pdf,docx,xlsx') }}"
                           maxlength="200" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-red-200 focus:border-red-400"
                           placeholder="pdf,docx,xlsx,pptx,jpg,png">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        <i class="fas fa-list text-gray-400 mr-1"></i>Éléments par page
                    </label>
                    <select name="pagination_items"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400">
                        @foreach([10, 15, 20, 25, 50, 100] as $n)
                        <option value="{{ $n }}" {{ ($config_affichage['pagination_items'] ?? 20) == $n ? 'selected' : '' }}>{{ $n }} par page</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        <i class="fas fa-file-export text-gray-400 mr-1"></i>Format d'export par défaut
                    </label>
                    <select name="export_format_defaut"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400">
                        @foreach(['xlsx' => 'Excel (.xlsx)', 'csv' => 'CSV (.csv)', 'pdf' => 'PDF (.pdf)'] as $val => $label)
                        <option value="{{ $val }}" {{ ($config_affichage['export_format_defaut'] ?? 'xlsx') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2"
                        onclick="return confirm('Confirmer la modification des paramètres techniques ?')">
                    <i class="fas fa-save"></i>Enregistrer les paramètres techniques
                </button>
            </form>
        </div>

    </div>

    {{-- Mode maintenance --}}
    @can('parametres.technique.modifier')
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-orange-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-700">
                    <i class="fas fa-tools text-orange-400 mr-1.5"></i>Mode maintenance
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Active une page de maintenance pour tous les utilisateurs non-admin.
                    Statut actuel :
                    @if($config['app_maintenance'] ?? false)
                        <span class="text-red-600 font-bold">ACTIVÉ</span>
                    @else
                        <span class="text-green-600 font-bold">Désactivé</span>
                    @endif
                </p>
            </div>
            <form action="{{ route('parametres.toggle-maintenance') }}" method="POST"
                  onsubmit="return confirm('{{ ($config['app_maintenance'] ?? false) ? 'Désactiver' : 'Activer' }} le mode maintenance ?')">
                @csrf
                <button type="submit"
                        class="px-4 py-2 {{ ($config['app_maintenance'] ?? false) ? 'bg-green-600 hover:bg-green-700' : 'bg-orange-500 hover:bg-orange-600' }} text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-{{ ($config['app_maintenance'] ?? false) ? 'check' : 'tools' }} mr-1.5"></i>
                    {{ ($config['app_maintenance'] ?? false) ? 'Désactiver la maintenance' : 'Activer la maintenance' }}
                </button>
            </form>
        </div>
    </div>
    @endcan

</div>
@endsection
