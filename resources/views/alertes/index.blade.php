@extends('layouts.app')
@section('title', 'Alertes')
@section('page-title', 'Centre d\'alertes')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Alertes</li>
@endsection

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-red-50 rounded-xl p-4 border border-red-100 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $alertes->where('niveau', 'critique')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Alertes critiques</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100 text-center">
            <p class="text-3xl font-bold text-yellow-600">{{ $alertes->where('niveau', 'attention')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Alertes attention</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $alertes->where('statut', 'nouvelle')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Non lues</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Niveau</label>
            <select name="niveau" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous les niveaux</option>
                <option value="critique"  {{ request('niveau') === 'critique' ? 'selected' : '' }}>Critique</option>
                <option value="attention" {{ request('niveau') === 'attention' ? 'selected' : '' }}>Attention</option>
                <option value="info"      {{ request('niveau') === 'info' ? 'selected' : '' }}>Info</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
            <select name="statut" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous les statuts</option>
                <option value="nouvelle" {{ request('statut') === 'nouvelle' ? 'selected' : '' }}>Nouvelle</option>
                <option value="vue" {{ request('statut') === 'vue' ? 'selected' : '' }}>Vue</option>
                <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="resolue" {{ request('statut') === 'resolue' ? 'selected' : '' }}>Resolue</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>
        @if(request()->anyFilled(['niveau', 'statut']))
            <a href="{{ route('alertes.index') }}" class="px-4 py-1.5 text-gray-500 text-sm hover:text-gray-700">
                <i class="fas fa-times mr-1"></i>Reinitialiser
            </a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($alertes as $alerte)
            @php
                $borderColors = ['critique' => 'border-red-400', 'attention' => 'border-yellow-400', 'info' => 'border-blue-300'];
                $borderColor = $borderColors[$alerte->niveau] ?? 'border-gray-200';
            @endphp
            <div class="px-5 py-4 border-b border-gray-50 border-l-4 {{ $borderColor }} hover:bg-gray-50 transition {{ $alerte->statut === 'nouvelle' ? 'bg-yellow-50/30' : '' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start space-x-3 flex-1 min-w-0">
                        <span class="text-xl flex-shrink-0 mt-0.5">{{ $alerte->iconeNiveau() }}</span>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <a href="{{ route('alertes.show', $alerte) }}" class="font-medium text-sm text-gray-800 hover:text-indigo-600">{{ $alerte->titre }}</a>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $alerte->couleurNiveau() }}-100 text-{{ $alerte->couleurNiveau() }}-700">
                                    {{ ucfirst($alerte->niveau) }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-xs {{ $alerte->statut === 'resolue' ? 'bg-green-100 text-green-700' : ($alerte->statut === 'nouvelle' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ ucfirst(str_replace('_', ' ', $alerte->statut)) }}
                                </span>
                                @if($alerte->escaladee)
                                    <span class="px-2 py-0.5 rounded text-xs bg-purple-100 text-purple-700">
                                        <i class="fas fa-arrow-up mr-0.5"></i>Escaladee
                                    </span>
                                @endif
                                @if($alerte->auto_generee)
                                    <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-500">Auto</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 truncate">{{ $alerte->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                @if($alerte->papa) PAPA {{ $alerte->papa->code }} • @endif
                                {{ $alerte->type_alerte ? ucfirst(str_replace('_', ' ', $alerte->type_alerte)) . ' • ' : '' }}
                                {{ $alerte->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('alertes.show', $alerte) }}" class="flex-shrink-0 text-indigo-600 hover:text-indigo-800 text-sm">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <i class="fas fa-bell-slash text-gray-200 text-5xl mb-4"></i>
                <p class="text-gray-400">Aucune alerte trouvee.</p>
            </div>
        @endforelse
    </div>

    {{ $alertes->links() }}
</div>
@endsection
