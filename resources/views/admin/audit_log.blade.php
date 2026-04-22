@extends('layouts.app')
@section('title', "Journal d'audit")
@section('page-title', "Journal d'audit")

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('admin.utilisateurs.index') }}" class="hover:text-indigo-600">Administration</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Journal d'audit</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $logs->total() }} entrée(s)</p>
        <span class="text-xs text-gray-400"><i class="fas fa-lock mr-1"></i>Lecture seule — non altérable</span>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Utilisateur</label>
            <select name="causer_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('causer_id') == $u->id ? 'selected' : '' }}>
                    {{ $u->prenom }} {{ $u->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Journal</label>
            <select name="log_name" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">Tous</option>
                @foreach($logNames as $ln)
                <option value="{{ $ln }}" {{ request('log_name') === $ln ? 'selected' : '' }}>{{ $ln }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Type d'entité</label>
            <input type="text" name="subject_type" value="{{ request('subject_type') }}"
                   placeholder="ex: Papa, Activite…"
                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 w-40">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Du</label>
            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Au</label>
            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>
        @if(request()->anyFilled(['causer_id', 'log_name', 'subject_type', 'date_debut', 'date_fin']))
        <a href="{{ route('admin.audit-log') }}" class="px-4 py-1.5 text-gray-500 text-sm hover:text-gray-700">
            <i class="fas fa-times mr-1"></i>Réinitialiser
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date/Heure</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilisateur</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Entité</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Détails</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                @php
                    $eventColors = [
                        'created' => 'green',
                        'updated' => 'blue',
                        'deleted' => 'red',
                        'restored'=> 'purple',
                    ];
                    $color = $eventColors[$log->event] ?? 'gray';
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-mono text-xs text-gray-400 whitespace-nowrap">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-4 py-3">
                        @if($log->causer)
                            <span class="font-medium text-gray-700">{{ $log->causer->prenom }} {{ $log->causer->name }}</span>
                        @else
                            <span class="text-gray-400 italic">Système</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $color }}-100 text-{{ $color }}-700">
                            {{ $log->event ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if($log->subject_type)
                            <span class="font-mono">{{ class_basename($log->subject_type) }}</span>
                            @if($log->subject_id)
                            <span class="text-gray-300">#{{ $log->subject_id }}</span>
                            @endif
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700 max-w-xs truncate" title="{{ $log->description }}">
                        {{ $log->description }}
                    </td>
                    <td class="px-4 py-3">
                        @php $props = $log->properties; @endphp
                        @if($props->has('attributes') || $props->has('old'))
                        <button type="button"
                                onclick="document.getElementById('modal-log-{{ $log->id }}').classList.remove('hidden')"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            Voir diff
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-file-alt text-4xl mb-3 opacity-20 block"></i>
                        Aucune entrée d'audit trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>

{{-- Modals diff (inline) --}}
@foreach($logs as $log)
@php $props = $log->properties; @endphp
@if($props->has('attributes') || $props->has('old'))
<div id="modal-log-{{ $log->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Détail modification — {{ $log->created_at->format('d/m/Y H:i') }}</h3>
            <button onclick="document.getElementById('modal-log-{{ $log->id }}').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <div class="p-5 space-y-4">
            @if($props->has('old') && $props['old'])
            <div>
                <p class="text-xs font-semibold text-red-600 mb-1 uppercase tracking-wide">Avant</p>
                <pre class="bg-red-50 text-red-800 text-xs rounded-lg p-3 overflow-x-auto">{{ json_encode($props['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
            @if($props->has('attributes') && $props['attributes'])
            <div>
                <p class="text-xs font-semibold text-green-600 mb-1 uppercase tracking-wide">Après</p>
                <pre class="bg-green-50 text-green-800 text-xs rounded-lg p-3 overflow-x-auto">{{ json_encode($props['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@endforeach
@endsection
