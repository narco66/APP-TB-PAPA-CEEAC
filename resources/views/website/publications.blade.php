@extends('website.layouts.main')

@section('title', 'Publications — Centre de ressources CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Publications</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Centre de <span class="text-amber-400">Publications</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            Accédez à l'ensemble de la documentation officielle de la CEEAC : rapports annuels,
            documents stratégiques, textes juridiques, appels d'offres et publications thématiques.
        </p>
    </div>
</section>

{{-- Barre de recherche --}}
<section class="py-6 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <div class="relative flex-1 max-w-xl">
                <input type="text" placeholder="Rechercher une publication, un document..."
                       class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none">
                <span class="absolute left-3 top-3.5 text-gray-400">&#128269;</span>
            </div>
            <select class="border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-600 focus:ring-2 focus:ring-amber-400 outline-none">
                <option>Toutes les années</option>
                <option>2024</option>
                <option>2023</option>
                <option>2022</option>
                <option>2021</option>
                <option>2020</option>
            </select>
            <button class="bg-blue-950 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-blue-900 transition">
                Rechercher
            </button>
        </div>
    </div>
</section>

{{-- Onglets --}}
<section class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex overflow-x-auto gap-0">
            <button class="px-6 py-4 text-sm font-semibold text-amber-600 border-b-2 border-amber-500 whitespace-nowrap">
                Rapports annuels
            </button>
            <button class="px-6 py-4 text-sm font-medium text-gray-600 hover:text-blue-950 whitespace-nowrap transition">
                Documents stratégiques
            </button>
            <button class="px-6 py-4 text-sm font-medium text-gray-600 hover:text-blue-950 whitespace-nowrap transition">
                Textes juridiques
            </button>
            <button class="px-6 py-4 text-sm font-medium text-gray-600 hover:text-blue-950 whitespace-nowrap transition">
                Appels d'offres
            </button>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Rapports annuels --}}
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-blue-950 mb-6 flex items-center gap-3">
                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-bold">PDF</span>
                Rapports annuels d'activités
            </h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">

                @php
                $rapports = [
                    ['2023', '2024', '4,2 Mo'],
                    ['2022', '2023', '3,8 Mo'],
                    ['2021', '2022', '3,1 Mo'],
                    ['2020', '2021', '2,9 Mo'],
                ];
                @endphp

                @foreach($rapports as $r)
                <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="flex gap-4 items-start">
                        <div class="flex-shrink-0 bg-red-100 text-red-600 rounded-lg p-3 text-2xl">&#128196;</div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-blue-950 text-sm mb-1">Rapport annuel d'activités {{ $r[0] }}</h3>
                            <p class="text-xs text-gray-500 mb-3">Commission de la CEEAC · {{ $r[1] }} · PDF · {{ $r[2] }}</p>
                            <a href="/documents/rapports/ceeac-rapport-annuel-{{ $r[0] }}.pdf" download
                               class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700">
                                &#11015; Télécharger
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- Documents stratégiques --}}
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-blue-950 mb-6 flex items-center gap-3">
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">Stratégie</span>
                Documents stratégiques
            </h2>
            <div class="grid md:grid-cols-2 gap-4">

                <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="flex gap-4 items-start">
                        <div class="flex-shrink-0 bg-blue-100 text-blue-700 rounded-lg p-3 text-2xl">&#128196;</div>
                        <div class="flex-1">
                            <h3 class="font-bold text-blue-950 mb-1">Vision CEEAC 2025 — Document cadre</h3>
                            <p class="text-xs text-gray-500 mb-1">Commission de la CEEAC · 2020 · PDF · 1,8 Mo</p>
                            <p class="text-xs text-gray-600 mb-3">Document de référence définissant la vision stratégique et les orientations de la CEEAC à l'horizon 2025.</p>
                            <a href="/documents/strategiques/ceeac-vision-2025.pdf" download class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700">&#11015; Télécharger</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="flex gap-4 items-start">
                        <div class="flex-shrink-0 bg-blue-100 text-blue-700 rounded-lg p-3 text-2xl">&#128196;</div>
                        <div class="flex-1">
                            <h3 class="font-bold text-blue-950 mb-1">Plan Directeur des Transports en Afrique Centrale (PDCT-AC)</h3>
                            <p class="text-xs text-gray-500 mb-1">Commission de la CEEAC · 2019 · PDF · 5,6 Mo</p>
                            <p class="text-xs text-gray-600 mb-3">Plan d'action pour les réseaux de transport régionaux : routes, chemins de fer, voies navigables, aéroports.</p>
                            <a href="/documents/strategiques/ceeac-pdct-ac.pdf" download class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700">&#11015; Télécharger</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="flex gap-4 items-start">
                        <div class="flex-shrink-0 bg-blue-100 text-blue-700 rounded-lg p-3 text-2xl">&#128196;</div>
                        <div class="flex-1">
                            <h3 class="font-bold text-blue-950 mb-1">Programme Indicatif de Développement de la Communauté (PIDE)</h3>
                            <p class="text-xs text-gray-500 mb-1">Commission de la CEEAC · 2021 · PDF · 3,4 Mo</p>
                            <p class="text-xs text-gray-600 mb-3">Cadre de planification des investissements régionaux en infrastructures, énergie et télécommunications.</p>
                            <a href="/documents/strategiques/ceeac-pide.pdf" download class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700">&#11015; Télécharger</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="flex gap-4 items-start">
                        <div class="flex-shrink-0 bg-blue-100 text-blue-700 rounded-lg p-3 text-2xl">&#128196;</div>
                        <div class="flex-1">
                            <h3 class="font-bold text-blue-950 mb-1">Stratégie de paix et sécurité de la CEEAC 2022–2026</h3>
                            <p class="text-xs text-gray-500 mb-1">Commission de la CEEAC · 2022 · PDF · 2,2 Mo</p>
                            <p class="text-xs text-gray-600 mb-3">Feuille de route pour le renforcement des mécanismes du COPAX, du MARAC et de la FOMAC.</p>
                            <a href="/documents/strategiques/ceeac-strategie-paix-2022.pdf" download class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700">&#11015; Télécharger</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="flex gap-4 items-start">
                        <div class="flex-shrink-0 bg-blue-100 text-blue-700 rounded-lg p-3 text-2xl">&#128196;</div>
                        <div class="flex-1">
                            <h3 class="font-bold text-blue-950 mb-1">Programme d'Appui à la Paix et à la Sécurité (PAPS) — Document de programme</h3>
                            <p class="text-xs text-gray-500 mb-1">Commission de la CEEAC · 2023 · PDF · 1,9 Mo</p>
                            <p class="text-xs text-gray-600 mb-3">Présentation complète du programme PAPS, ses objectifs, sa gouvernance et ses mécanismes de financement.</p>
                            <a href="/documents/strategiques/ceeac-paps-programme.pdf" download class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700">&#11015; Télécharger</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="flex gap-4 items-start">
                        <div class="flex-shrink-0 bg-blue-100 text-blue-700 rounded-lg p-3 text-2xl">&#128196;</div>
                        <div class="flex-1">
                            <h3 class="font-bold text-blue-950 mb-1">Étude sur le Commerce Intra-Régional en Afrique Centrale 2023</h3>
                            <p class="text-xs text-gray-500 mb-1">Commission de la CEEAC · 2024 · PDF · 2,7 Mo</p>
                            <p class="text-xs text-gray-600 mb-3">Analyse des obstacles et des opportunités de développement du commerce intra-CEEAC avec recommandations de politique commerciale.</p>
                            <a href="/documents/strategiques/ceeac-etude-commerce-2023.pdf" download class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-700">&#11015; Télécharger</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Textes juridiques --}}
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-blue-950 mb-6 flex items-center gap-3">
                <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-sm font-bold">Juridique</span>
                Textes juridiques fondamentaux
            </h2>
            <div class="grid md:grid-cols-2 gap-4">

                @php
                $textes = [
                    ['Traité de Libreville — Texte fondateur (1983)', 'Signé le 18 octobre 1983', '890 Ko', 'Traité instituant la Communauté Économique des États de l\'Afrique Centrale, signé à Libreville le 18 octobre 1983.', 'traite-1983'],
                    ['Traité révisé de la CEEAC (2021)', 'Adopté le 18 août 2021', '1,4 Mo', 'Traité révisé transformant le Secrétariat Général en Commission et renforçant les pouvoirs supranationaux de l\'organisation.', 'traite-revise-2021'],
                    ['Protocole relatif au COPAX (2000)', 'Signé le 24 février 2000', '650 Ko', 'Protocole instituant le COPAX, le MARAC et la FOMAC — piliers du système de paix et sécurité de la CEEAC.', 'protocole-copax-2000'],
                    ['Règlement financier de la CEEAC', 'Commission de la CEEAC · 2022', '780 Ko', 'Règlement fixant les modalités d\'établissement et d\'exécution du budget communautaire et les règles comptables.', 'reglement-financier'],
                ];
                @endphp

                @foreach($textes as $t)
                <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition border-l-4 border-amber-400">
                    <h3 class="font-bold text-blue-950 mb-1">{{ $t[0] }}</h3>
                    <p class="text-xs text-gray-500 mb-1">{{ $t[1] }} · PDF · {{ $t[2] }}</p>
                    <p class="text-xs text-gray-600 mb-3">{{ $t[3] }}</p>
                    <a href="/documents/juridiques/ceeac-{{ $t[4] }}.pdf" download class="text-xs font-semibold text-amber-600 hover:text-amber-700">&#11015; Télécharger</a>
                </div>
                @endforeach

            </div>
        </div>

        {{-- Appels d'offres --}}
        <div>
            <h2 class="text-2xl font-bold text-blue-950 mb-6 flex items-center gap-3">
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-bold">Marchés</span>
                Appels d'offres et marchés publics
            </h2>
            <div class="space-y-3">

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Ouvert</span>
                            <span class="text-xs text-gray-400">Publié le 15 mars 2025</span>
                        </div>
                        <h3 class="font-bold text-blue-950">AOI/CEEAC/2025-03 — Recrutement d'un cabinet d'études pour le suivi-évaluation du PIDE</h3>
                        <p class="text-xs text-gray-500 mt-1">Date limite de soumission : 15 avril 2025</p>
                    </div>
                    <a href="/documents/aao/ceeac-aoi-2025-03.pdf" download class="flex-shrink-0 bg-blue-950 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-900 transition whitespace-nowrap">
                        &#11015; Dossier d'appel
                    </a>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Ouvert</span>
                            <span class="text-xs text-gray-400">Publié le 1er mars 2025</span>
                        </div>
                        <h3 class="font-bold text-blue-950">AOI/CEEAC/2025-02 — Fourniture d'équipements informatiques pour la Commission</h3>
                        <p class="text-xs text-gray-500 mt-1">Date limite de soumission : 1er avril 2025</p>
                    </div>
                    <a href="/documents/aao/ceeac-aoi-2025-02.pdf" download class="flex-shrink-0 bg-blue-950 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-900 transition whitespace-nowrap">
                        &#11015; Dossier d'appel
                    </a>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 opacity-70">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">Clôturé</span>
                            <span class="text-xs text-gray-400">Publié le 10 janvier 2025</span>
                        </div>
                        <h3 class="font-bold text-gray-600">AOI/CEEAC/2025-01 — Services de traduction et interprétation pour les organes de la CEEAC</h3>
                        <p class="text-xs text-gray-500 mt-1">Date limite : 10 février 2025 — Clôturé</p>
                    </div>
                    <span class="flex-shrink-0 text-gray-400 text-xs font-medium px-4 py-2 border border-gray-200 rounded-lg">Clôturé</span>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 opacity-70">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">Clôturé</span>
                            <span class="text-xs text-gray-400">Publié le 5 novembre 2024</span>
                        </div>
                        <h3 class="font-bold text-gray-600">AOI/CEEAC/2024-08 — Consultant pour la révision du Protocole sur la libre circulation des personnes</h3>
                        <p class="text-xs text-gray-500 mt-1">Date limite : 20 décembre 2024 — Clôturé</p>
                    </div>
                    <span class="flex-shrink-0 text-gray-400 text-xs font-medium px-4 py-2 border border-gray-200 rounded-lg">Clôturé</span>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- CTA --}}
<section class="py-12 bg-blue-950 text-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-2xl font-bold mb-4">Vous cherchez un document spécifique ?</h3>
        <p class="text-blue-100 mb-6">Notre service de documentation peut vous aider à trouver tout document officiel de la CEEAC.</p>
        <a href="{{ route('website.contact') }}" class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-semibold px-8 py-3 rounded-lg transition">
            Contacter le service documentation
        </a>
    </div>
</section>

@endsection
