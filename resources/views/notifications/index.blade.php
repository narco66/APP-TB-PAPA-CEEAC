@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Notifications</li>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Centre de notifications</h2>
        <p class="text-sm text-gray-500">{{ $notifications->total() }} notification(s)</p>
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notification)
        <a href="{{ route('notifications.read', $notification) }}"
           class="block rounded-xl border {{ $notification->estLue() ? 'border-gray-100 bg-white' : 'border-indigo-200 bg-indigo-50' }} p-4 shadow-sm hover:border-indigo-300 transition">
            <div class="flex items-start gap-3">
                <div class="mt-1 w-9 h-9 rounded-lg bg-{{ $notification->couleurNiveau() }}-100 text-{{ $notification->couleurNiveau() }}-600 flex items-center justify-center">
                    <i class="fas {{ $notification->icone ?: 'fa-bell' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
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
