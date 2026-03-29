@extends('layouts.app')

@section('title', 'Workflows de validation')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Workflows</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-10 w-10 bg-violet-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-diagram-project text-violet-600"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Workflows de validation</h1>
                    <p class="text-sm text-gray-500">{{ $definitions->count() }} workflow(s) configuré(s) — circuits d'approbation des modules</p>
                </div>
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

    {{-- Résumé stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Total</p>
            <p class="text-2xl font-bold text-gray-800">{{ $definitions->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Workflows</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Actifs</p>
            <p class="text-2xl font-bold text-green-600">{{ $definitions->where('actif', true)->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">En service</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Désactivés</p>
            <p class="text-2xl font-bold text-gray-400">{{ $definitions->where('actif', false)->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Suspendus</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Étapes total</p>
            <p class="text-2xl font-bold text-violet-600">{{ $definitions->sum('steps_count') }}</p>
            <p class="text-xs text-gray-400 mt-1">Toutes définitions</p>
        </div>
    </div>

    {{-- Liste des workflows --}}
    @php $grouped = $definitions->groupBy('module_cible'); @endphp

    @foreach($grouped as $module => $defs)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-100 bg-violet-50 flex items-center space-x-2">
            <i class="fas fa-folder text-violet-400 text-sm"></i>
            <h2 class="text-xs font-semibold text-violet-800 uppercase tracking-wider">Module : {{ ucfirst($module) }}</h2>
            <span class="px-2 py-0.5 bg-violet-100 text-violet-600 rounded-full text-xs">{{ $defs->count() }}</span>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($defs as $def)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex items-start space-x-4 flex-1 min-w-0">
                    {{-- Status indicator --}}
                    <div class="mt-0.5 flex-shrink-0">
                        <span class="inline-block w-2.5 h-2.5 rounded-full {{ $def->actif ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                            <span class="text-xs font-mono bg-violet-50 text-violet-700 px-2 py-0.5 rounded border border-violet-100">{{ $def->code }}</span>
                            <h3 class="text-sm font-semibold text-gray-800">{{ $def->libelle }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $def->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $def->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        @if($def->description)
                        <p class="text-xs text-gray-400 mt-1 truncate">{{ $def->description }}</p>
                        @endif
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-list-ol text-violet-300 mr-1"></i>
                                {{ $def->steps_count }} étape(s)
                            </span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-code-branch text-violet-300 mr-1"></i>
                                v{{ $def->version ?? '1' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-layer-group text-violet-300 mr-1"></i>
                                {{ $def->type_objet ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 ml-4 flex-shrink-0">
                    @can('parametres.workflows.modifier')
                    <form action="{{ route('parametres.workflows.toggle', $def) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition
                                    {{ $def->actif
                                        ? 'bg-red-50 text-red-600 hover:bg-red-100'
                                        : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                            <i class="fas {{ $def->actif ? 'fa-toggle-off' : 'fa-toggle-on' }} mr-1"></i>
                            {{ $def->actif ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>
                    @endcan

                    @can('parametres.workflows.voir')
                    <a href="{{ route('parametres.workflows.edit', $def) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition">
                        <i class="fas fa-pen-to-square mr-1"></i>Configurer
                    </a>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($definitions->isEmpty())
    <div class="bg-white rounded-xl p-10 shadow-sm border border-gray-100 text-center">
        <i class="fas fa-diagram-project text-4xl text-gray-200 mb-3"></i>
        <p class="text-sm text-gray-500">Aucun workflow configuré.</p>
    </div>
    @endif

</div>
@endsection
