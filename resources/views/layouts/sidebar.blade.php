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

            <a href="{{ route('objectifs-immediats.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-4 {{ request()->routeIs('objectifs-immediats.*') ? 'active' : '' }}">
                <i class="fas fa-bullseye w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm text-indigo-200">Objectifs immédiats</span>
            </a>

            <a href="{{ route('resultats-attendus.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-4 {{ request()->routeIs('resultats-attendus.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm text-indigo-200">Résultats attendus</span>
            </a>

            <a href="{{ route('reports.dashboard') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-4 {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm text-indigo-200">Centre de reporting</span>
            </a>

            <a href="{{ route('rapports.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-8 {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm text-indigo-200">Rapports narratifs</span>
                <span class="ml-auto rounded-full bg-amber-200 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-900">
                    Historique
                </span>
            </a>

            <a href="{{ route('reports.library.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-8 {{ request()->routeIs('reports.library.*') ? 'active' : '' }}">
                <i class="fas fa-folder-open w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm text-indigo-200">Bibliothèque</span>
            </a>
            @endcan

            @can('activite.voir')
            <a href="{{ route('activites.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('activites.*') && !request()->routeIs('activites.gantt') ? 'active' : '' }}">
                <i class="fas fa-tasks w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Activités</span>
            </a>

            <a href="{{ route('activites.gantt') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition ml-4 {{ request()->routeIs('activites.gantt') ? 'active' : '' }}">
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

            @canany(['budget.voir', 'risque.voir'])
            @php
                $papaCourant = auth()->user()
                    ? (\App\Models\Papa::query()->visibleTo(auth()->user())->where('statut', 'valide')->latest('annee')->first()
                        ?? \App\Models\Papa::query()->visibleTo(auth()->user())->latest('annee')->first())
                    : null;
            @endphp
            @if($papaCourant)
                @can('budget.voir')
                <a href="{{ route('budgets.index', $papaCourant) }}"
                   class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave w-5 mr-3 text-indigo-300"></i>
                    <span class="text-sm">Budget</span>
                    <span class="ml-auto text-xs text-indigo-400">{{ $papaCourant->annee }}</span>
                </a>
                @endcan
                @can('risque.voir')
                <a href="{{ route('risques.index', $papaCourant) }}"
                   class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('risques.*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle w-5 mr-3 text-indigo-300"></i>
                    <span class="text-sm">Risques</span>
                </a>
                @endcan
            @endif
            @endcanany
        </div>

        <!-- Section Pilotage -->
        <div class="pt-4">
            <p class="px-3 text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2">Pilotage</p>

            @can('workflow.voir')
            <a href="{{ route('workflows.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('workflows.*') ? 'active' : '' }}">
                <i class="fas fa-diagram-project w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Workflows</span>
            </a>
            @endcan

            @can('decision.voir')
            <a href="{{ route('decisions.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('decisions.*') ? 'active' : '' }}">
                <i class="fas fa-gavel w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Décisions</span>
            </a>
            @endcan

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
        @canany(['admin.utilisateurs', 'admin.audit_log'])
        <div class="pt-4">
            <p class="px-3 text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2">Administration</p>
            @can('admin.utilisateurs')
            <a href="{{ route('admin.utilisateurs.index') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('admin.utilisateurs.*') ? 'active' : '' }}">
                <i class="fas fa-users w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Utilisateurs</span>
            </a>

            {{-- Structure organisationnelle --}}
            <a href="{{ route('admin.structure.departements') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('admin.structure.*') ? 'active' : '' }}">
                <i class="fas fa-sitemap w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Structure org.</span>
            </a>
            <div class="{{ request()->routeIs('admin.structure.*') ? '' : 'hidden' }} ml-4 space-y-0.5" id="struct-submenu">
                <a href="{{ route('admin.structure.departements') }}"
                   class="sidebar-link flex items-center px-3 py-1.5 rounded-lg text-indigo-200 hover:bg-indigo-700 transition text-xs {{ request()->routeIs('admin.structure.departements*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap w-4 mr-2 text-indigo-400"></i> Départements
                </a>
                <a href="{{ route('admin.structure.directions') }}"
                   class="sidebar-link flex items-center px-3 py-1.5 rounded-lg text-indigo-200 hover:bg-indigo-700 transition text-xs {{ request()->routeIs('admin.structure.directions*') ? 'active' : '' }}">
                    <i class="fas fa-building w-4 mr-2 text-indigo-400"></i> Directions
                </a>
                <a href="{{ route('admin.structure.services') }}"
                   class="sidebar-link flex items-center px-3 py-1.5 rounded-lg text-indigo-200 hover:bg-indigo-700 transition text-xs {{ request()->routeIs('admin.structure.services*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group w-4 mr-2 text-indigo-400"></i> Services
                </a>
            </div>
            @endcan

            @can('admin.audit_log')
            <a href="{{ route('admin.audit-log') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">
                <i class="fas fa-history w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Journal d'audit</span>
            </a>
            @endcan
            @can('admin.audit_log')
            <a href="{{ route('admin.audit-events') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('admin.audit-events') ? 'active' : '' }}">
                <i class="fas fa-shield-halved w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Audit métier</span>
            </a>
            @endcan
            @can('notification_rule.gerer')
            <a href="{{ route('admin.notification-rules') }}"
               class="sidebar-link flex items-center px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-700 transition {{ request()->routeIs('admin.notification-rules*') ? 'active' : '' }}">
                <i class="fas fa-envelope-open-text w-5 mr-3 text-indigo-300"></i>
                <span class="text-sm">Notifications</span>
            </a>
            @endcan
        </div>
        @endcanany

        <!-- Section Paramètres -->
        @canany([
            'parametres.generaux.voir', 'parametres.papa.voir',
            'parametres.referentiels.voir', 'parametres.libelles.voir',
            'parametres.rbm.voir', 'parametres.alertes.voir',
            'parametres.workflows.voir', 'parametres.droits.voir',
            'parametres.journal.voir', 'parametres.technique.voir',
        ])
        <div class="pt-4" x-data="{ openParams: {{ request()->is('parametres*') ? 'true' : 'false' }} }">
            <button @click="openParams = !openParams"
                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium
                           {{ request()->is('parametres*') ? 'bg-amber-600 text-white' : 'text-indigo-100 hover:bg-indigo-700' }}
                           transition-colors duration-150">
                <span class="flex items-center">
                    <i class="fas fa-sliders-h w-5 mr-3 {{ request()->is('parametres*') ? 'text-amber-100' : 'text-indigo-300' }}"></i>
                    <span>Paramètres</span>
                </span>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="openParams && 'rotate-180'"></i>
            </button>

            <div x-show="openParams" x-collapse class="mt-1 ml-4 space-y-0.5 border-l border-indigo-700 pl-3">

                @can('parametres.generaux.voir')
                <a href="{{ route('parametres.hub') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-th-large w-4 mr-2 text-indigo-400"></i> Vue d'ensemble
                </a>
                <a href="{{ route('parametres.generaux') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/generaux*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-cog w-4 mr-2 text-indigo-400"></i> Paramètres généraux
                </a>
                @endcan

                @can('parametres.papa.voir')
                <a href="{{ route('parametres.papa.index') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/papa*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-file-alt w-4 mr-2 text-indigo-400"></i> Gestion PAPA
                </a>
                @endcan

                @can('parametres.referentiels.voir')
                <a href="{{ route('parametres.referentiels.index') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/referentiels*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-list-ul w-4 mr-2 text-indigo-400"></i> Référentiels
                </a>
                @endcan

                @can('parametres.libelles.voir')
                <a href="{{ route('parametres.libelles.index') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/libelles*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-language w-4 mr-2 text-indigo-400"></i> Libellés métier
                </a>
                @endcan

                @can('parametres.rbm.voir')
                <a href="{{ Route::has('parametres.rbm.index') ? route('parametres.rbm.index') : route('parametres.hub') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/rbm*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-chart-line w-4 mr-2 text-indigo-400"></i> Chaîne RBM
                </a>
                @endcan

                @can('parametres.alertes.voir')
                <a href="{{ Route::has('parametres.alertes.index') ? route('parametres.alertes.index') : route('parametres.hub') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/alertes*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-bell w-4 mr-2 text-indigo-400"></i> Alertes & Notifs
                </a>
                @endcan

                @can('parametres.workflows.voir')
                <a href="{{ Route::has('parametres.workflows.index') ? route('parametres.workflows.index') : route('parametres.hub') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/workflows*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-diagram-project w-4 mr-2 text-indigo-400"></i> Workflows
                </a>
                @endcan

                @can('parametres.droits.voir')
                <a href="{{ Route::has('parametres.droits.index') ? route('parametres.droits.index') : route('parametres.hub') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/droits*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-shield-halved w-4 mr-2 text-indigo-400"></i> Droits & Rôles
                </a>
                @endcan

                @can('parametres.journal.voir')
                <a href="{{ route('parametres.journal') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-indigo-200 hover:bg-indigo-700 transition
                          {{ request()->is('parametres/journal*') ? 'bg-indigo-700 text-white' : '' }}">
                    <i class="fas fa-history w-4 mr-2 text-indigo-400"></i> Journal
                </a>
                @endcan

                @can('parametres.technique.voir')
                <a href="{{ Route::has('parametres.technique.index') ? route('parametres.technique.index') : route('parametres.hub') }}"
                   class="flex items-center px-3 py-1.5 rounded-lg text-xs text-red-300 hover:bg-red-900/30 transition
                          {{ request()->is('parametres/technique*') ? 'bg-red-900/40 text-red-200' : '' }}">
                    <i class="fas fa-server w-4 mr-2 text-red-400"></i>
                    <span class="font-medium">Technique</span>
                </a>
                @endcan

            </div>
        </div>
        @endcanany

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

