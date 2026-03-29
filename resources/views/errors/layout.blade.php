<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Erreur') — TB-PAPA CEEAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="h-full font-sans flex items-center justify-center bg-gradient-to-br from-indigo-950 via-indigo-900 to-indigo-800 min-h-screen">

<div class="w-full max-w-lg px-6 text-center">

    {{-- Logo --}}
    <div class="mb-8">
        <div class="inline-flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">
                <i class="fas fa-shield-halved text-white text-xl"></i>
            </div>
            <div class="text-left">
                <div class="text-white font-bold text-lg leading-tight">TB-PAPA</div>
                <div class="text-indigo-300 text-xs">Commission CEEAC</div>
            </div>
        </div>
    </div>

    {{-- Code d'erreur --}}
    <div class="text-8xl font-black text-white/20 mb-2 leading-none select-none">
        @yield('code', '?')
    </div>

    {{-- Icône et titre --}}
    <div class="mb-6">
        <div class="w-20 h-20 rounded-full @yield('icon-bg', 'bg-red-500/20') flex items-center justify-center mx-auto mb-4">
            <i class="@yield('icon', 'fas fa-exclamation-triangle') text-3xl @yield('icon-color', 'text-red-400')"></i>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">@yield('heading', 'Une erreur est survenue')</h1>
        <p class="text-indigo-200 text-sm leading-relaxed">@yield('message', 'Veuillez réessayer ou contacter l\'administrateur.')</p>
    </div>

    {{-- Détails contextuels --}}
    @hasSection('details')
    <div class="bg-white/10 rounded-xl px-5 py-4 mb-6 text-left">
        <p class="text-xs text-indigo-300 font-semibold uppercase tracking-wider mb-2">Détails</p>
        <p class="text-sm text-indigo-100">@yield('details')</p>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-center gap-3 flex-wrap">
        @if(auth()->check())
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-800 font-semibold text-sm rounded-xl hover:bg-indigo-50 transition shadow-lg">
            <i class="fas fa-tachometer-alt"></i> Tableau de bord
        </a>
        @endif
        <button onclick="history.back()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-medium text-sm rounded-xl transition border border-white/20">
            <i class="fas fa-arrow-left"></i> Page précédente
        </button>
        @if(auth()->check())
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-medium text-sm rounded-xl transition border border-white/20">
            <i class="fas fa-home"></i> Accueil
        </a>
        @else
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-xl transition shadow-lg">
            <i class="fas fa-sign-in-alt"></i> Se connecter
        </a>
        @endif
    </div>

    {{-- Footer --}}
    <p class="mt-10 text-xs text-indigo-400">
        © Commission de la CEEAC · TB-PAPA
        @if(auth()->check())
         · Connecté en tant que <strong class="text-indigo-300">{{ auth()->user()->nomComplet() }}</strong>
        @endif
    </p>

</div>
</body>
</html>
