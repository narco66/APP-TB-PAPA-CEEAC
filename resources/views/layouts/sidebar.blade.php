<!-- Sidebar mobile overlay -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
     class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"></div>

<!-- Sidebar -->
<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
     class="fixed inset-y-0 left-0 z-30 w-64 bg-indigo-900 text-white flex flex-col transition-transform duration-300 lg:relative lg:translate-x-0">

    <!-- Logo -->
    <div class="flex items-center h-16 px-4 bg-indigo-950 border-b border-indigo-800">
        <img src="{{ asset('images/logo-ceeac.png') }}" alt="CEEAC" class="h-10 w-10 rounded-full object-cover mr-3">
        <div>
            <div class="font-bold text-sm leading-tight">TB-PAPA</div>
            <div class="text-indigo-300 text-xs">Commission CEEAC</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt w-5 mr-3 text-indigo-300"></i>
            <span class="text-sm">Tableau de bord</span>
        </a>

        <!-- Section PAPA -->
        <div class="pt-4">
            <p class="px-3 text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2">PAPA</p>

            @can('papa.voir')
            <a href="{{ route('papas.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('papas.*') ? 'active' : '' }}">
                <i class="fas fa-book w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Plans d'action</span>
            </a>

            <a href="{{ route('actions-prioritaires.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-4 {{ request()->routeIs('actions-prioritaires.*') ? 'active' : '' }}">
                <i class="fas fa-list-ol w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm text-indigo-200">Actions prioritaires</span>
            </a>

            <a href="{{ route('rapports.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-4 {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm text-indigo-200">Rapports</span>
            </a>
            @endcan

            @can('activite.voir')
            <a href="{{ route('activites.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('activites.*') ? 'active' : '' }}">
                <i class="fas fa-tasks w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Activités</span>
            </a>

            <a href="{{ route('activites.gantt') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-4">
                <i class="fas fa-project-diagram w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm text-indigo-200">Diagramme Gantt</span>
            </a>
            @endcan

            @can('indicateur.voir')
            <a href="{{ route('indicateurs.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('indicateurs.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Indicateurs</span>
            </a>
            @endcan
        </div>

        <!-- Section Pilotage -->
        <div class="pt-4">
            <p class="px-3 text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2">Pilotage</p>

            @can('alerte.voir')
            <a href="{{ route('alertes.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('alertes.*') ? 'active' : '' }}">
                <i class="fas fa-bell w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Alertes</span>
                @php
                    $nbAlertes = auth()->user() ? \App\Models\Alerte::where('destinataire_id', auth()->id())->where('statut', 'nouvelle')->count() : 0;
                @endphp
                @if($nbAlertes > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2">{{ $nbAlertes }}</span>
                @endif
            </a>
            @endcan

            @can('document.voir')
            <a href="{{ route('documents.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                <i class="fas fa-folder-open w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">GED / Documents</span>
            </a>
            @endcan
        </div>

        <!-- Section Admin -->
        @can('admin.utilisateurs')
        <div class="pt-4">
            <p class="px-3 text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2">Administration</p>
            <a href="{{ route('admin.utilisateurs.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="fas fa-users w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Utilisateurs</span>
            </a>
        </div>
        @endcan

    </nav>

    <!-- Utilisateur connecté -->
    <div class="border-t border-indigo-800 p-4">
        <div class="flex items-center">
            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold mr-3">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->nomComplet() }}</p>
                <p class="text-xs text-indigo-300 truncate">{{ auth()->user()->getRoleNames()->first() }}</p>
            </div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-indigo-400 hover:text-white ml-2" title="Déconnexion">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
