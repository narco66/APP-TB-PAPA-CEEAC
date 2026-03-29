@extends('layouts.app')
@section('title', 'Audit metier')
@section('page-title', 'Audit metier')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('admin.utilisateurs.index') }}" class="hover:text-indigo-600">Administration</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Audit metier</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Evenements d'audit metier</h2>
            <p class="text-sm text-gray-500">{{ $events->total() }} evenement(s) tracables</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.audit-events.export', request()->query()) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Export CSV
            </a>
            <a href="{{ route('admin.audit-log') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Voir le journal applicatif</a>
        </div>
    </div>

    @if(request()->filled('auditable_type') || request()->filled('auditable_id'))
    <div class="flex items-center justify-between rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
        <div class="text-sm text-indigo-900">
            <span class="font-semibold">Filtre objet actif:</span>
            {{ class_basename((string) request('auditable_type')) ?: 'Objet' }}
            #{{ request('auditable_id') ?: '?' }}
        </div>
        <div class="flex items-center gap-4">
            @if(!empty($auditableContext['url']))
                <a href="{{ $auditableContext['url'] }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">
                    {{ $auditableContext['label'] }}
                </a>
            @endif
            <a href="{{ route('admin.audit-events') }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">
                Voir tout l'audit
            </a>
        </div>
    </div>
    @endif

    <div class="grid gap-4 md:grid-cols-4">
        <a href="{{ route('admin.audit-events', array_merge(request()->query(), ['niveau' => null])) }}" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition hover:border-gray-300">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['total'] }}</p>
        </a>
        <a href="{{ route('admin.audit-events', array_merge(request()->query(), ['niveau' => 'info'])) }}" class="rounded-xl border border-blue-100 bg-blue-50 p-4 shadow-sm transition hover:border-blue-300">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Info</p>
            <p class="mt-2 text-2xl font-bold text-blue-900">{{ $summary['info'] }}</p>
        </a>
        <a href="{{ route('admin.audit-events', array_merge(request()->query(), ['niveau' => 'warning'])) }}" class="rounded-xl border border-amber-100 bg-amber-50 p-4 shadow-sm transition hover:border-amber-300">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Warning</p>
            <p class="mt-2 text-2xl font-bold text-amber-900">{{ $summary['warning'] }}</p>
        </a>
        <a href="{{ route('admin.audit-events', array_merge(request()->query(), ['niveau' => 'critical'])) }}" class="rounded-xl border border-red-100 bg-red-50 p-4 shadow-sm transition hover:border-red-300">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Critical</p>
            <p class="mt-2 text-2xl font-bold text-red-900">{{ $summary['critical'] }}</p>
        </a>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Modules</span>
        <a href="{{ route('admin.audit-events', array_merge(request()->query(), ['module' => null])) }}" class="rounded-full border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">
            Tous
        </a>
        @foreach(['papa', 'workflow', 'decision'] as $moduleShortcut)
            <a href="{{ route('admin.audit-events', array_merge(request()->query(), ['module' => $moduleShortcut])) }}"
               class="rounded-full px-3 py-1 text-xs font-medium {{ request('module') === $moduleShortcut ? 'bg-indigo-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                {{ strtoupper($moduleShortcut) }}
            </a>
        @endforeach
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Acteur</label>
            <select name="acteur_id" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm">
                <option value="">Tous</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" @selected((string) request('acteur_id') === (string) $u->id)>{{ $u->prenom }} {{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Module</label>
            <select name="module" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm">
                <option value="">Tous</option>
                @foreach($modules as $module)
                <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Evenement</label>
            <select name="event_type" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm">
                <option value="">Tous</option>
                @foreach($eventTypes as $eventType)
                <option value="{{ $eventType }}" @selected(request('event_type') === $eventType)>{{ $eventType }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Niveau</label>
            <select name="niveau" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm">
                <option value="">Tous</option>
                @foreach(['info', 'warning', 'critical'] as $niveau)
                <option value="{{ $niveau }}" @selected(request('niveau') === $niveau)>{{ ucfirst($niveau) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">PAPA</label>
            <select name="papa_id" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm">
                <option value="">Tous</option>
                @foreach($papas as $papa)
                <option value="{{ $papa->id }}" @selected((string) request('papa_id') === (string) $papa->id)>{{ $papa->code }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Du</label>
            <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Au</label>
            <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-1.5 text-sm text-white hover:bg-indigo-700">Filtrer</button>
            <a href="{{ route('admin.audit-events') }}" class="rounded-lg border border-gray-300 px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50">
                Reinitialiser
            </a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Horodatage</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Module</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Action</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Acteur</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">PAPA</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Objet</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Checksum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ optional($event->horodate_evenement)->format('d/m/Y H:i:s') }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $event->module }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $event->niveau === 'critical' ? 'bg-red-100 text-red-700' : ($event->niveau === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $event->event_type }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $event->acteur?->nomComplet() ?? 'Systeme' }}</td>
                    <td class="px-4 py-3 text-gray-700">
                        @if($event->papa)
                            <a href="{{ route('papas.show', $event->papa) }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ $event->papa->code }}
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                        @if($event->auditableUrl())
                            <a href="{{ $event->auditableUrl() }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ $event->auditableLabel() }}
                            </a>
                        @else
                            {{ $event->auditableLabel() }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $event->description }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ \Illuminate\Support\Str::limit($event->checksum, 18) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-gray-400">Aucun evenement d'audit metier trouve.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $events->links() }}
</div>
@endsection
