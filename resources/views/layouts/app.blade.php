<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TB-PAPA') — CEEAC-ECCAS</title>

    <!-- Tailwind CSS via CDN (remplacer par Vite en production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active { @apply bg-indigo-700 text-white; }
        .progress-bar { transition: width 0.6s ease; }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sans" x-data="{ sidebarOpen: false }">

<div class="flex h-full">
    <!-- ── Sidebar ─────────────────────────────────────────────────── -->
    @include('layouts.sidebar')

    <!-- ── Contenu principal ─────────────────────────────────────────── -->
    <div class="flex flex-col flex-1 min-h-screen overflow-hidden">
        @include('layouts.topbar')

        <!-- Breadcrumbs -->
        @hasSection('breadcrumbs')
        <nav class="bg-white border-b border-gray-200 px-6 py-2">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Accueil</a></li>
                @yield('breadcrumbs')
            </ol>
        </nav>
        @endif

        <!-- Messages flash -->
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
            <span class="text-green-800 text-sm"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <span class="text-red-800 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
