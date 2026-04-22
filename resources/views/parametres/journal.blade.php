@extends('layouts.app')

@section('title', 'Journal des modifications')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Journal</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-gray-600"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Journal des modifications</h1>
                    <p class="text-sm text-gray-500">Historique de toutes les modifications des paramètres</p>
                </div>
            </div>

            {{-- Filtre --}}
            <form method="GET" action="{{ route('parametres.journal') }}" class="flex items-center space-x-2">
                <label class="text-xs font-semibold text-gray-600">Type :</label>
                <select name="event_type" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les types</option>
                    @foreach($eventTypes as $et)
                        <option value="{{ $et }}" {{ request('event_type') === $et ? 'selected' : '' }}>
                            {{ $et }}
                        </option>
                    @endforeach
                </select>
                @if(request('event_type'))
                <a href="{{ route('parametres.journal') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs transition">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Événements enregistrés</h2>
            <span class="text-xs text-gray-500">{{ $events->total() }} événement(s)</span>
        </div>

        @if($events->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-500 text-sm">Aucun événement enregistré.</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($events as $event)
            <div class="px-6 py-4 hover:bg-gray-50 transition" x-data="{ expanded: false }">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start space-x-4 flex-1 min-w-0">

                        {{-- Icône selon niveau --}}
                        @php
                            $niveauConfig = [
                                'info'    => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'fa-info-circle'],
                                'warning' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'icon' => 'fa-exclamation-triangle'],
                                'error'   => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'icon' => 'fa-times-circle'],
                                'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'fa-check-circle'],
                            ];
                            $nc = $niveauConfig[$event->niveau ?? 'info'] ?? $niveauConfig['info'];
                        @endphp
                        <div class="h-8 w-8 {{ $nc['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas {{ $nc['icon'] }} {{ $nc['text'] }} text-sm"></i>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 flex-wrap gap-2 mb-1">
                                <span class="font-mono text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">
                                    {{ $event->event_type }}
                                </span>
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                    {{ $event->action }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-800">{{ $event->description }}</p>

                            <div class="flex items-center space-x-4 mt-1.5 text-xs text-gray-400">
                                @if($event->acteur)
                                <span>
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $event->acteur->nomComplet() }}
                                </span>
                                @endif
                                <span>
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $event->created_at->format('d/m/Y à H:i:s') }}
                                </span>
                                @if($event->ip_address)
                                <span>
                                    <i class="fas fa-network-wired mr-1"></i>
                                    {{ $event->ip_address }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Toggle données avant/après --}}
                    @if($event->donnees_avant || $event->donnees_apres)
                    <button type="button" @click="expanded = !expanded"
                            class="flex-shrink-0 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                            title="Voir les données">
                        <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    @endif
                </div>

                {{-- Données avant / après (collapsible) --}}
                @if($event->donnees_avant || $event->donnees_apres)
                <div x-show="expanded" x-cloak class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($event->donnees_avant)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-2 flex items-center">
                            <i class="fas fa-history text-amber-500 mr-1.5"></i>Données avant
                        </p>
                        <pre class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-900 overflow-auto max-h-40 font-mono">{{ json_encode($event->donnees_avant, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                    @if($event->donnees_apres)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-2 flex items-center">
                            <i class="fas fa-check text-green-500 mr-1.5"></i>Données après
                        </p>
                        <pre class="bg-green-50 border border-green-200 rounded-lg p-3 text-xs text-green-900 overflow-auto max-h-40 font-mono">{{ json_encode($event->donnees_apres, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                </div>
                @endif

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($events->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $events->appends(request()->query())->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
