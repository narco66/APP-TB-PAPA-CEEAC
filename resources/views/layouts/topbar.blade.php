<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0">
    <!-- Toggle sidebar mobile -->
    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <!-- Titre de la page -->
    <h1 class="text-lg font-semibold text-gray-800 hidden lg:block">@yield('page-title', 'TB-PAPA-CEEAC')</h1>

    <!-- Actions droite -->
    <div class="flex items-center space-x-4">
        <!-- Notifications -->
        @auth
        @php
            $notifs = auth()->user()->notificationsNonLues()->limit(5)->get();
            $countNotifs = auth()->user()->notificationsNonLues()->count();
        @endphp
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative text-gray-500 hover:text-indigo-600">
                <i class="fas fa-bell text-xl"></i>
                @if($countNotifs > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                        {{ $countNotifs > 9 ? '9+' : $countNotifs }}
                    </span>
                @endif
            </button>

            <div x-show="open" @click.away="open = false" x-cloak
                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">Notifications</span>
                    @if($countNotifs > 0)
                        <span class="text-xs text-indigo-600 font-medium">{{ $countNotifs }} nouvelle(s)</span>
                    @endif
                </div>
                <div class="max-h-64 overflow-y-auto divide-y divide-gray-50">
                    @forelse($notifs as $notif)
                    <a href="{{ $notif->lien ?? '#' }}" class="block px-4 py-3 hover:bg-gray-50 transition">
                        <p class="text-sm font-medium text-gray-800">{{ $notif->titre }}</p>
                        <p class="text-xs text-gray-500 mt-1 truncate">{{ $notif->message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </a>
                    @empty
                    <div class="px-4 py-6 text-center text-sm text-gray-400">
                        <i class="fas fa-check-circle text-green-400 text-2xl mb-2 block"></i>
                        Aucune nouvelle notification
                    </div>
                    @endforelse
                </div>
                @if($countNotifs > 5)
                <div class="p-3 border-t border-gray-100 text-center">
                    <a href="#" class="text-xs text-indigo-600 hover:underline">Voir toutes les notifications</a>
                </div>
                @endif
            </div>
        </div>
        @endauth

        <!-- Profil -->
        <div class="text-sm text-gray-600">
            <span class="font-medium">{{ auth()->user()?->nomComplet() }}</span>
        </div>
    </div>
</header>
