<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'La Communauté Économique des États de l\'Afrique Centrale — intégration régionale, paix, sécurité et développement durable.')">
    <meta property="og:title" content="@yield('title', 'CEEAC') — Commission de la CEEAC">
    <meta property="og:description" content="@yield('meta_description', 'Communauté Économique des États de l\'Afrique Centrale')">
    <title>@yield('title', 'Commission de la CEEAC') — CEEAC-ECCAS</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy:  { DEFAULT: '#0A2157', 50:'#e8edf5', 100:'#c5d0e6', 200:'#8fa3cc', 300:'#5a76b3', 400:'#2c4e96', 500:'#0A2157', 600:'#091c49', 700:'#07163a', 800:'#05102b', 900:'#030a1c' },
                        gold:  { DEFAULT: '#C4922A', 50:'#fdf4e3', 100:'#f9e4b7', 200:'#f4d08a', 300:'#efbc5d', 400:'#eaa830', 500:'#C4922A', 600:'#a37b24', 700:'#82641d', 800:'#614d17', 900:'#413610' },
                    },
                    fontFamily: {
                        serif: ['Merriweather', 'Georgia', 'serif'],
                        sans:  ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Merriweather', serif; }

        /* Topbar */
        .topbar { background: #0A2157; }

        /* Navigation */
        .nav-main { background: #fff; box-shadow: 0 2px 12px rgba(10,33,87,0.10); }
        .nav-link { font-size:0.875rem; font-weight:500; color:#0A2157; padding:0.5rem 0.75rem; border-bottom:2px solid transparent; transition:all .2s; }
        .nav-link:hover, .nav-link.active { color:#C4922A; border-bottom-color:#C4922A; }

        /* Dropdown */
        .dropdown-menu { background:#fff; border-top:3px solid #C4922A; box-shadow:0 8px 32px rgba(10,33,87,0.15); min-width:240px; }
        .dropdown-item { display:flex; align-items:center; gap:0.5rem; padding:0.625rem 1rem; font-size:0.8125rem; color:#374151; transition:background .15s; }
        .dropdown-item:hover { background:#f0f4fb; color:#0A2157; }

        /* Gold underline section titles */
        .section-title::after { content:''; display:block; width:60px; height:3px; background:#C4922A; margin:0.75rem auto 0; }
        .section-title-left::after { content:''; display:block; width:60px; height:3px; background:#C4922A; margin:0.75rem 0 0; }

        /* Card hover */
        .card-hover { transition: transform .25s, box-shadow .25s; }
        .card-hover:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(10,33,87,0.12); }

        /* Footer */
        .footer-main { background:#0A2157; }
        .footer-link { color:#94a3b8; font-size:0.8125rem; line-height:1.8; }
        .footer-link:hover { color:#C4922A; }

        /* Scrollbar */
        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#f1f1f1; }
        ::-webkit-scrollbar-thumb { background:#C4922A; border-radius:3px; }

        /* Breadcrumb */
        .breadcrumb-bar { background:#f8f9fb; border-bottom:1px solid #e5e7eb; }
    </style>
    @stack('styles')
</head>
<body class="bg-white text-gray-800" x-data="{ mobileMenuOpen: false, activeDropdown: null }">

<!-- ═══════════════════════════════════════════════
     TOPBAR
════════════════════════════════════════════════ -->
<div class="topbar hidden lg:block">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-9">
        <div class="flex items-center gap-6 text-xs text-blue-200">
            <a href="mailto:contact@ceeac-eccas.org" class="flex items-center gap-1.5 hover:text-white">
                <i class="fas fa-envelope text-gold-400"></i> contact@ceeac-eccas.org
            </a>
            <a href="tel:+24101723200" class="flex items-center gap-1.5 hover:text-white">
                <i class="fas fa-phone text-gold-400"></i> +241 01 72 32 00
            </a>
            <span class="flex items-center gap-1.5">
                <i class="fas fa-map-marker-alt text-gold-400"></i> Libreville, Gabon
            </span>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-xs">
                <a href="#" class="text-white font-semibold border-b border-gold-400 pb-0.5">FR</a>
                <span class="text-blue-400">|</span>
                <a href="#" class="text-blue-200 hover:text-white">EN</a>
                <span class="text-blue-400">|</span>
                <a href="#" class="text-blue-200 hover:text-white">PT</a>
            </div>
            <div class="flex items-center gap-2 ml-2">
                <a href="#" class="text-blue-200 hover:text-gold-400 text-sm"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-blue-200 hover:text-gold-400 text-sm"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-blue-200 hover:text-gold-400 text-sm"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="text-blue-200 hover:text-gold-400 text-sm"><i class="fab fa-youtube"></i></a>
            </div>
            <a href="{{ route('login') }}" class="ml-2 text-xs bg-gold-500 text-white px-3 py-1 rounded hover:bg-gold-600 transition" style="background:#C4922A;">
                <i class="fas fa-lock mr-1"></i> Espace membres
            </a>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════
     HEADER — Logo + Navigation
════════════════════════════════════════════════ -->
<header class="nav-main sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">

            <!-- Logo -->
            <a href="{{ route('website.home') }}" class="flex items-center gap-3 flex-shrink-0">
                <img src="{{ asset('images/logo-ceeac.png') }}" alt="CEEAC" class="h-11 w-11 rounded-full object-cover">
                <div class="leading-tight">
                    <div class="text-sm font-bold" style="color:#0A2157; font-family:'Merriweather',serif;">CEEAC — ECCAS</div>
                    <div class="text-xs" style="color:#C4922A;">Communauté Économique des États de l'Afrique Centrale</div>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-1">

                <a href="{{ route('website.home') }}"
                   class="nav-link {{ request()->routeIs('website.home') ? 'active' : '' }}">Accueil</a>

                <!-- À propos dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                    <button class="nav-link flex items-center gap-1 {{ request()->routeIs('website.a-propos*','website.historique*','website.vision*','website.organes*','website.president*','website.etats-membres*') ? 'active' : '' }}">
                        À propos <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="dropdown-menu absolute top-full left-0 rounded-b-lg z-50 py-1">
                        <a href="{{ route('website.a-propos') }}" class="dropdown-item"><i class="fas fa-landmark w-4 text-gold-500" style="color:#C4922A;"></i> Présentation</a>
                        <a href="{{ route('website.historique') }}" class="dropdown-item"><i class="fas fa-history w-4 text-gold-500" style="color:#C4922A;"></i> Historique</a>
                        <a href="{{ route('website.vision-mission') }}" class="dropdown-item"><i class="fas fa-binoculars w-4 text-gold-500" style="color:#C4922A;"></i> Vision & Mission</a>
                        <a href="{{ route('website.organes') }}" class="dropdown-item"><i class="fas fa-sitemap w-4 text-gold-500" style="color:#C4922A;"></i> Organes de décision</a>
                        <a href="{{ route('website.president') }}" class="dropdown-item"><i class="fas fa-user-tie w-4 text-gold-500" style="color:#C4922A;"></i> Mot du Président</a>
                        <a href="{{ route('website.etats-membres') }}" class="dropdown-item"><i class="fas fa-globe-africa w-4 text-gold-500" style="color:#C4922A;"></i> États membres</a>
                    </div>
                </div>

                <!-- Domaines dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                    <button class="nav-link flex items-center gap-1 {{ request()->routeIs('website.domaine*') ? 'active' : '' }}">
                        Domaines d'action <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="dropdown-menu absolute top-full left-0 rounded-b-lg z-50 py-1">
                        <a href="{{ route('website.domaine', 'paix-securite') }}" class="dropdown-item"><i class="fas fa-shield-alt w-4" style="color:#C4922A;"></i> Paix & Sécurité</a>
                        <a href="{{ route('website.domaine', 'integration-economique') }}" class="dropdown-item"><i class="fas fa-handshake w-4" style="color:#C4922A;"></i> Intégration économique</a>
                        <a href="{{ route('website.domaine', 'infrastructures') }}" class="dropdown-item"><i class="fas fa-road w-4" style="color:#C4922A;"></i> Infrastructures</a>
                        <a href="{{ route('website.domaine', 'commerce-investissement') }}" class="dropdown-item"><i class="fas fa-chart-line w-4" style="color:#C4922A;"></i> Commerce & Investissement</a>
                        <a href="{{ route('website.domaine', 'ressources-naturelles') }}" class="dropdown-item"><i class="fas fa-leaf w-4" style="color:#C4922A;"></i> Ressources naturelles</a>
                        <a href="{{ route('website.domaine', 'developpement-humain') }}" class="dropdown-item"><i class="fas fa-users w-4" style="color:#C4922A;"></i> Développement humain</a>
                    </div>
                </div>

                <a href="{{ route('website.programmes') }}"
                   class="nav-link {{ request()->routeIs('website.programmes*') ? 'active' : '' }}">Programmes</a>

                <!-- Actualités dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                    <button class="nav-link flex items-center gap-1 {{ request()->routeIs('website.actualites*','website.publications*','website.evenements*','website.communiques*') ? 'active' : '' }}">
                        Actualités <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="dropdown-menu absolute top-full left-0 rounded-b-lg z-50 py-1">
                        <a href="{{ route('website.actualites') }}" class="dropdown-item"><i class="fas fa-newspaper w-4" style="color:#C4922A;"></i> Actualités</a>
                        <a href="{{ route('website.publications') }}" class="dropdown-item"><i class="fas fa-book-open w-4" style="color:#C4922A;"></i> Publications</a>
                        <a href="{{ route('website.evenements') }}" class="dropdown-item"><i class="fas fa-calendar-alt w-4" style="color:#C4922A;"></i> Événements</a>
                        <a href="{{ route('website.communiques') }}" class="dropdown-item"><i class="fas fa-bullhorn w-4" style="color:#C4922A;"></i> Communiqués de presse</a>
                    </div>
                </div>

                <a href="{{ route('website.contact') }}"
                   class="nav-link {{ request()->routeIs('website.contact*') ? 'active' : '' }}">Contact</a>
            </nav>

            <!-- Search + Mobile toggle -->
            <div class="flex items-center gap-3">
                <button class="hidden lg:flex items-center gap-2 text-sm text-gray-500 border border-gray-200 rounded-lg px-3 py-1.5 hover:border-gold-400 hover:text-navy-500 transition" style="color:#6b7280;">
                    <i class="fas fa-search text-xs"></i>
                    <span class="text-xs">Rechercher…</span>
                </button>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100" style="color:#0A2157;">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         class="lg:hidden border-t border-gray-100 bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
            <a href="{{ route('website.home') }}" class="block px-3 py-2 text-sm font-medium rounded hover:bg-gray-50" style="color:#0A2157;">Accueil</a>

            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded hover:bg-gray-50" style="color:#0A2157;">
                    À propos <i class="fas fa-chevron-down text-xs" :class="open ? 'rotate-180' : ''" style="transition:.2s"></i>
                </button>
                <div x-show="open" class="ml-4 space-y-1 mt-1">
                    <a href="{{ route('website.a-propos') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:text-navy-500 hover:bg-gray-50 rounded">Présentation</a>
                    <a href="{{ route('website.historique') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:text-navy-500 hover:bg-gray-50 rounded">Historique</a>
                    <a href="{{ route('website.vision-mission') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:text-navy-500 hover:bg-gray-50 rounded">Vision & Mission</a>
                    <a href="{{ route('website.organes') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:text-navy-500 hover:bg-gray-50 rounded">Organes de décision</a>
                    <a href="{{ route('website.president') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:text-navy-500 hover:bg-gray-50 rounded">Mot du Président</a>
                    <a href="{{ route('website.etats-membres') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:text-navy-500 hover:bg-gray-50 rounded">États membres</a>
                </div>
            </div>

            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded hover:bg-gray-50" style="color:#0A2157;">
                    Domaines d'action <i class="fas fa-chevron-down text-xs" :class="open ? 'rotate-180' : ''" style="transition:.2s"></i>
                </button>
                <div x-show="open" class="ml-4 space-y-1 mt-1">
                    <a href="{{ route('website.domaine', 'paix-securite') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Paix & Sécurité</a>
                    <a href="{{ route('website.domaine', 'integration-economique') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Intégration économique</a>
                    <a href="{{ route('website.domaine', 'infrastructures') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Infrastructures</a>
                    <a href="{{ route('website.domaine', 'commerce-investissement') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Commerce & Investissement</a>
                    <a href="{{ route('website.domaine', 'ressources-naturelles') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Ressources naturelles</a>
                    <a href="{{ route('website.domaine', 'developpement-humain') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Développement humain</a>
                </div>
            </div>

            <a href="{{ route('website.programmes') }}" class="block px-3 py-2 text-sm font-medium rounded hover:bg-gray-50" style="color:#0A2157;">Programmes</a>

            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded hover:bg-gray-50" style="color:#0A2157;">
                    Actualités <i class="fas fa-chevron-down text-xs" :class="open ? 'rotate-180' : ''" style="transition:.2s"></i>
                </button>
                <div x-show="open" class="ml-4 space-y-1 mt-1">
                    <a href="{{ route('website.actualites') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Actualités</a>
                    <a href="{{ route('website.publications') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Publications</a>
                    <a href="{{ route('website.evenements') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Événements</a>
                    <a href="{{ route('website.communiques') }}" class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 rounded">Communiqués de presse</a>
                </div>
            </div>

            <a href="{{ route('website.contact') }}" class="block px-3 py-2 text-sm font-medium rounded hover:bg-gray-50" style="color:#0A2157;">Contact</a>
        </div>
    </div>
</header>

<!-- Breadcrumb (optional) -->
@hasSection('breadcrumb')
<div class="breadcrumb-bar">
    <div class="max-w-7xl mx-auto px-4 py-2">
        <ol class="flex items-center gap-2 text-xs text-gray-500">
            <li><a href="{{ route('website.home') }}" class="hover:text-gold-500" style="hover:color:#C4922A;">Accueil</a></li>
            @yield('breadcrumb')
        </ol>
    </div>
</div>
@endif

<!-- ═══════════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════════════ -->
<main>
    @yield('content')
</main>

<!-- ═══════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════ -->
<footer class="footer-main text-white mt-0">

    <!-- Newsletter band -->
    <div style="background:#C4922A;">
        <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-white font-bold text-lg" style="font-family:'Merriweather',serif;">Restez informé</h3>
                <p class="text-amber-100 text-sm mt-1">Recevez les dernières actualités et publications de la CEEAC.</p>
            </div>
            <form class="flex gap-2 w-full md:w-auto">
                <input type="email" placeholder="Votre adresse e-mail"
                       class="flex-1 md:w-72 px-4 py-2.5 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-white">
                <button type="submit" class="bg-white text-amber-700 px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-amber-50 transition whitespace-nowrap">
                    S'inscrire
                </button>
            </form>
        </div>
    </div>

    <!-- Main footer -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            <!-- Col 1 — Identité -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/logo-ceeac.png') }}" alt="CEEAC" class="h-12 w-12 rounded-full object-cover opacity-90">
                    <div>
                        <div class="font-bold text-white text-sm" style="font-family:'Merriweather',serif;">CEEAC — ECCAS</div>
                        <div class="text-xs" style="color:#C4922A;">Commission de la CEEAC</div>
                    </div>
                </div>
                <p class="text-xs text-blue-200 leading-relaxed mb-4">
                    La Communauté Économique des États de l'Afrique Centrale œuvre pour l'intégration régionale,
                    la paix, la sécurité et le développement durable de ses 11 États membres.
                </p>
                <div class="flex items-center gap-3">
                    <a href="#" class="h-8 w-8 rounded-full bg-blue-800 hover:bg-gold-500 flex items-center justify-center text-sm transition" style="hover:background:#C4922A;">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="h-8 w-8 rounded-full bg-blue-800 hover:bg-gold-500 flex items-center justify-center text-sm transition">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="h-8 w-8 rounded-full bg-blue-800 hover:bg-gold-500 flex items-center justify-center text-sm transition">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="h-8 w-8 rounded-full bg-blue-800 hover:bg-gold-500 flex items-center justify-center text-sm transition">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Col 2 — À propos -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 pb-2 border-b border-blue-700">À propos</h4>
                <ul class="space-y-1">
                    <li><a href="{{ route('website.a-propos') }}" class="footer-link hover:text-gold-400 block">Présentation</a></li>
                    <li><a href="{{ route('website.historique') }}" class="footer-link hover:text-gold-400 block">Historique</a></li>
                    <li><a href="{{ route('website.vision-mission') }}" class="footer-link hover:text-gold-400 block">Vision & Mission</a></li>
                    <li><a href="{{ route('website.organes') }}" class="footer-link hover:text-gold-400 block">Organes de décision</a></li>
                    <li><a href="{{ route('website.president') }}" class="footer-link hover:text-gold-400 block">Mot du Président</a></li>
                    <li><a href="{{ route('website.etats-membres') }}" class="footer-link hover:text-gold-400 block">États membres</a></li>
                </ul>
            </div>

            <!-- Col 3 — Domaines & Programmes -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 pb-2 border-b border-blue-700">Domaines & Programmes</h4>
                <ul class="space-y-1">
                    <li><a href="{{ route('website.domaine', 'paix-securite') }}" class="footer-link hover:text-gold-400 block">Paix & Sécurité</a></li>
                    <li><a href="{{ route('website.domaine', 'integration-economique') }}" class="footer-link hover:text-gold-400 block">Intégration économique</a></li>
                    <li><a href="{{ route('website.domaine', 'infrastructures') }}" class="footer-link hover:text-gold-400 block">Infrastructures</a></li>
                    <li><a href="{{ route('website.domaine', 'commerce-investissement') }}" class="footer-link hover:text-gold-400 block">Commerce & Investissement</a></li>
                    <li><a href="{{ route('website.programmes') }}" class="footer-link hover:text-gold-400 block">Programmes phares</a></li>
                    <li><a href="{{ route('website.publications') }}" class="footer-link hover:text-gold-400 block">Publications & rapports</a></li>
                </ul>
            </div>

            <!-- Col 4 — Contact -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4 pb-2 border-b border-blue-700">Contact</h4>
                <ul class="space-y-3 text-xs text-blue-200">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt mt-0.5 flex-shrink-0" style="color:#C4922A;"></i>
                        <span>Boulevard Triomphal Omar Bongo, BP 2112, Libreville, Gabon</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-phone flex-shrink-0" style="color:#C4922A;"></i>
                        <span>+241 01 72 32 00</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-fax flex-shrink-0" style="color:#C4922A;"></i>
                        <span>+241 01 72 32 01</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-envelope flex-shrink-0" style="color:#C4922A;"></i>
                        <a href="mailto:contact@ceeac-eccas.org" class="hover:text-white">contact@ceeac-eccas.org</a>
                    </li>
                </ul>
                <div class="mt-4">
                    <a href="{{ route('website.contact') }}" class="inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-lg border transition"
                       style="border-color:#C4922A; color:#C4922A; hover:background:#C4922A; hover:color:white;">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom bar -->
    <div class="border-t border-blue-900">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-2 text-xs text-blue-400">
            <span>© {{ date('Y') }} CEEAC — ECCAS. Tous droits réservés.</span>
            <div class="flex items-center gap-4">
                <a href="#" class="hover:text-white">Mentions légales</a>
                <a href="#" class="hover:text-white">Politique de confidentialité</a>
                <a href="#" class="hover:text-white">Accessibilité</a>
                <a href="{{ route('login') }}" class="hover:text-gold-400" style="hover:color:#C4922A;">Espace TB-PAPA</a>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
