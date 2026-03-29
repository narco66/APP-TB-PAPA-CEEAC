@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Notifications</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Centre de notifications</h2>
            <p class="text-sm text-gray-500">{{ $notifications->total() }} notification(s)</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <form method="GET" action="{{ route('notifications.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div>
                    <label for="statut" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">Statut</label>
                    <select id="statut" name="statut" class="w-full rounded-lg border-gray-300 text-sm sm:w-44">
                        <option value="">Toutes</option>
                        <option value="non_lues" @selected(request('statut') === 'non_lues')>Non lues</option>
                        <option value="lues" @selected(request('statut') === 'lues')>Lues</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Filtrer
                    </button>
                    @if(request()->filled('statut'))
                        <a href="{{ route('notifications.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Reinitialiser
                        </a>
                    @endif
                </div>
            </form>

            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Tout marquer comme lu
                </button>
            </form>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notification)
        <a href="{{ route('notifications.read', $notification) }}"
           class="block rounded-xl border {{ $notification->estLue() ? 'border-gray-100 bg-white' : 'border-indigo-200 bg-indigo-50' }} p-4 shadow-sm transition hover:border-indigo-300">
            <div class="flex items-start gap-3">
                <div class="mt-1 flex h-9 w-9 items-center justify-center rounded-lg bg-{{ $notification->couleurNiveau() }}-100 text-{{ $notification->couleurNiveau() }}-600">
                    <i class="fas {{ $notification->icone ?: 'fa-bell' }}"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold text-gray-800">{{ $notification->titre }}</p>
                        <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                </div>
            </div>
        </a>
        @empty
        <div class="rounded-xl border border-gray-100 bg-white p-10 text-center text-gray-400">
            Aucune notification disponible.
        </div>
        @endforelse
    </div>

    {{ $notifications->links() }}
</div>
@endsection
