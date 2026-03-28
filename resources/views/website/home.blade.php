@extends('website.layouts.main')

@section('title', 'Accueil')
@section('meta_description', 'La Commission de la CEEAC — Communauté Économique des États de l\'Afrique Centrale. Intégration régionale, paix, sécurité et développement durable en Afrique centrale.')

@push('styles')
<style>
    /* Hero */
    .hero-bg {
        background: linear-gradient(135deg, #0A2157 0%, #0e2e6e 40%, #1a4a8a 100%);
        min-height: 600px;
        position: relative;
        overflow: hidden;
    }
    .hero-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-shape {
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 80px;
        background: white;
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    /* Stats band */
    .stats-band { background: linear-gradient(90deg, #0A2157 0%, #1a3a7a 100%); }
    /* Priority card */
    .priority-card { border-top: 4px solid #C4922A; }
    /* News card image */
    .news-img { height: 200px; object-fit: cover; width: 100%; background: #e8edf5; }
    /* Program card */
    .prog-card { background: linear-gradient(135deg, #0A2157, #1a3a7a); }
    /* State flag */
    .state-flag { width: 64px; height: 44px; object-fit: cover; border-radius: 4px; }
    /* Quote bg */
    .quote-bg { background: linear-gradient(135deg, #0A2157 0%, #0e2e6e 100%); }
    /* Section divider */
    .divider-gold { border: none; height: 3px; background: linear-gradient(90deg, transparent, #C4922A, transparent); margin: 0 auto; width: 80px; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════
     SECTION 1 — HERO
════════════════════════════════════════════════ --}}
<section class="hero-bg flex items-center relative">
    <div class="max-w-7xl mx-auto px-4 py-20 lg:py-28 relative z-10 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Text -->
            <div>
                <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest mb-6 px-3 py-1.5 rounded-full border"
                     style="color:#C4922A; border-color:#C4922A; background:rgba(196,146,42,0.08);">
                    <i class="fas fa-star text-xs"></i> Depuis 1983 au service de l'Afrique centrale
                </div>
                <h1 class="text-3xl lg:text-5xl font-black text-white leading-tight mb-6" style="font-family:'Merriweather',serif;">
                    Construire une<br>
                    <span style="color:#C4922A;">Afrique Centrale</span><br>
                    unie et prospère
                </h1>
                <p class="text-blue-200 text-lg leading-relaxed mb-8 max-w-lg">
                    La Commission de la CEEAC coordonne et impulse les politiques d'intégration régionale
                    au service des 11 États membres et de leurs 180 millions de citoyens.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('website.a-propos') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm transition"
                       style="background:#C4922A; color:#fff; hover:background:#a37b24;">
                        <i class="fas fa-play-circle"></i> Découvrir la CEEAC
                    </a>
                    <a href="{{ route('website.programmes') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm border border-blue-400 text-white hover:border-white transition">
                        <i class="fas fa-th-large"></i> Nos programmes
                    </a>
                </div>
            </div>

            <!-- Visual — Map / Emblem -->
            <div class="hidden lg:flex items-center justify-center">
                <div class="relative">
                    <div class="h-80 w-80 rounded-full border-2 flex items-center justify-center"
                         style="border-color:rgba(196,146,42,0.3); background:rgba(255,255,255,0.04);">
                        <div class="h-64 w-64 rounded-full border flex items-center justify-center"
                             style="border-color:rgba(196,146,42,0.5); background:rgba(255,255,255,0.06);">
                            <img src="{{ asset('images/logo-ceeac.png') }}" alt="CEEAC" class="h-40 w-40 rounded-full object-cover opacity-90">
                        </div>
                    </div>
                    <!-- Floating badges -->
                    <div class="absolute -top-4 -right-4 bg-white rounded-xl shadow-lg px-4 py-2 text-center">
                        <div class="text-2xl font-black" style="color:#0A2157; font-family:'Merriweather',serif;">11</div>
                        <div class="text-xs text-gray-500">États membres</div>
                    </div>
                    <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-lg px-4 py-2 text-center">
                        <div class="text-2xl font-black" style="color:#C4922A; font-family:'Merriweather',serif;">1983</div>
                        <div class="text-xs text-gray-500">Fondée à Libreville</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-shape"></div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 2 — BANDE STATISTIQUES
════════════════════════════════════════════════ --}}
<section class="stats-band py-10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
            <div>
                <div class="text-4xl font-black text-white mb-1" style="font-family:'Merriweather',serif;">11</div>
                <div class="text-xs text-blue-300 uppercase tracking-widest">États membres</div>
                <div class="mt-2 text-xs text-blue-400">Angola, Burundi, Cameroun, RCA, Congo, RDC, Guinée Éq., Gabon, Rwanda, São Tomé, Tchad</div>
            </div>
            <div>
                <div class="text-4xl font-black mb-1" style="color:#C4922A; font-family:'Merriweather',serif;">180M+</div>
                <div class="text-xs text-blue-300 uppercase tracking-widest">Habitants</div>
                <div class="mt-2 text-xs text-blue-400">Population cumulée des États membres (2024)</div>
            </div>
            <div>
                <div class="text-4xl font-black text-white mb-1" style="font-family:'Merriweather',serif;">3,7M</div>
                <div class="text-xs text-blue-300 uppercase tracking-widest">km² de superficie</div>
                <div class="mt-2 text-xs text-blue-400">L'une des plus grandes sous-régions d'Afrique</div>
            </div>
            <div>
                <div class="text-4xl font-black mb-1" style="color:#C4922A; font-family:'Merriweather',serif;">40+</div>
                <div class="text-xs text-blue-300 uppercase tracking-widest">Années d'existence</div>
                <div class="mt-2 text-xs text-blue-400">Traité de Libreville du 18 octobre 1983</div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 3 — À PROPOS
════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Visual -->
            <div class="relative order-2 lg:order-1">
                <div class="rounded-2xl overflow-hidden shadow-2xl" style="background:#e8edf5; min-height:380px;">
                    <div class="flex items-center justify-center h-96" style="background:linear-gradient(135deg,#e8edf5,#c5d0e6);">
                        <div class="text-center">
                            <img src="{{ asset('images/logo-ceeac.png') }}" alt="CEEAC" class="h-24 w-24 rounded-full mx-auto mb-4 shadow-lg object-cover">
                            <p class="text-sm font-medium" style="color:#0A2157;">Commission de la CEEAC</p>
                            <p class="text-xs text-gray-500 mt-1">Libreville, Gabon</p>
                        </div>
                    </div>
                </div>
                <!-- Déco badge -->
                <div class="absolute -bottom-5 -right-5 bg-white rounded-2xl shadow-xl p-4 max-w-xs hidden lg:block">
                    <p class="text-xs text-gray-500 mb-1">Traité fondateur</p>
                    <p class="font-bold text-sm" style="color:#0A2157;">Libreville, 18 octobre 1983</p>
                    <p class="text-xs text-gray-500 mt-1">Révisé à Malabo, 14 juillet 2019</p>
                </div>
            </div>

            <!-- Text -->
            <div class="order-1 lg:order-2">
                <p class="text-xs font-semibold uppercase tracking-widest mb-3" style="color:#C4922A;">Qui sommes-nous</p>
                <h2 class="text-3xl font-black mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">
                    La Commission de la CEEAC
                </h2>
                <hr class="divider-gold mb-6" style="margin-left:0;">
                <p class="text-gray-600 leading-relaxed mb-4">
                    Instituée par le Traité de Libreville du 18 octobre 1983, la Communauté Économique des États
                    de l'Afrique Centrale (CEEAC) est l'une des huit Communautés Économiques Régionales reconnues
                    par l'Union Africaine. Elle regroupe onze États membres partageant un destin commun.
                </p>
                <p class="text-gray-600 leading-relaxed mb-6">
                    La Commission, organe exécutif permanent de la CEEAC, pilote la mise en œuvre des décisions
                    des Conférences des Chefs d'État et de Gouvernement ainsi que des orientations du Conseil des Ministres.
                    Elle incarne l'ambition collective de bâtir un espace communautaire intégré, pacifique et prospère.
                </p>
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(196,146,42,0.12);">
                            <i class="fas fa-check text-sm" style="color:#C4922A;"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" style="color:#0A2157;">Intégration régionale</p>
                            <p class="text-xs text-gray-500">Convergence des politiques</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(196,146,42,0.12);">
                            <i class="fas fa-check text-sm" style="color:#C4922A;"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" style="color:#0A2157;">Paix & Sécurité</p>
                            <p class="text-xs text-gray-500">Prévention des conflits</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(196,146,42,0.12);">
                            <i class="fas fa-check text-sm" style="color:#C4922A;"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" style="color:#0A2157;">Développement humain</p>
                            <p class="text-xs text-gray-500">Éducation, santé, jeunesse</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(196,146,42,0.12);">
                            <i class="fas fa-check text-sm" style="color:#C4922A;"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" style="color:#0A2157;">Développement durable</p>
                            <p class="text-xs text-gray-500">Environnement & climat</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('website.a-propos') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg font-semibold text-sm text-white transition"
                   style="background:#0A2157;">
                    En savoir plus <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 4 — PRIORITÉS STRATÉGIQUES
════════════════════════════════════════════════ --}}
<section class="py-20" style="background:#f8f9fb;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Nos axes d'intervention</p>
            <h2 class="text-3xl font-black" style="color:#0A2157; font-family:'Merriweather',serif;">Priorités stratégiques 2025–2030</h2>
            <hr class="divider-gold mt-4">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <a href="{{ route('website.domaine', 'paix-securite') }}" class="priority-card bg-white rounded-xl p-6 card-hover block">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(10,33,87,0.08);">
                    <i class="fas fa-shield-alt text-2xl" style="color:#0A2157;"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">Paix & Sécurité</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Prévention et gestion des conflits, maintien de la paix, lutte contre le terrorisme et la criminalité transnationale organisée dans la région.
                </p>
                <div class="mt-4 flex items-center gap-1 text-xs font-semibold" style="color:#C4922A;">
                    En savoir plus <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>

            <a href="{{ route('website.domaine', 'integration-economique') }}" class="priority-card bg-white rounded-xl p-6 card-hover block">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(10,33,87,0.08);">
                    <i class="fas fa-handshake text-2xl" style="color:#0A2157;"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">Intégration économique</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Convergence des politiques macroéconomiques, libre circulation des personnes et des biens, marché commun régional et union douanière.
                </p>
                <div class="mt-4 flex items-center gap-1 text-xs font-semibold" style="color:#C4922A;">
                    En savoir plus <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>

            <a href="{{ route('website.domaine', 'infrastructures') }}" class="priority-card bg-white rounded-xl p-6 card-hover block">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(10,33,87,0.08);">
                    <i class="fas fa-road text-2xl" style="color:#0A2157;"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">Infrastructures</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Développement des corridors de transport, interconnexion énergétique, déploiement des infrastructures numériques et des réseaux de communication.
                </p>
                <div class="mt-4 flex items-center gap-1 text-xs font-semibold" style="color:#C4922A;">
                    En savoir plus <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>

            <a href="{{ route('website.domaine', 'commerce-investissement') }}" class="priority-card bg-white rounded-xl p-6 card-hover block">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(10,33,87,0.08);">
                    <i class="fas fa-chart-line text-2xl" style="color:#0A2157;"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">Commerce & Investissement</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Promotion du commerce intra-régional, attraction des investissements directs, harmonisation des cadres réglementaires et amélioration du climat des affaires.
                </p>
                <div class="mt-4 flex items-center gap-1 text-xs font-semibold" style="color:#C4922A;">
                    En savoir plus <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>

            <a href="{{ route('website.domaine', 'ressources-naturelles') }}" class="priority-card bg-white rounded-xl p-6 card-hover block">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(10,33,87,0.08);">
                    <i class="fas fa-leaf text-2xl" style="color:#0A2157;"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">Ressources naturelles</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Gestion durable du Bassin du Congo, conservation de la biodiversité, valorisation des ressources en eau, forestières et minières dans le respect des écosystèmes.
                </p>
                <div class="mt-4 flex items-center gap-1 text-xs font-semibold" style="color:#C4922A;">
                    En savoir plus <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>

            <a href="{{ route('website.domaine', 'developpement-humain') }}" class="priority-card bg-white rounded-xl p-6 card-hover block">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(10,33,87,0.08);">
                    <i class="fas fa-users text-2xl" style="color:#0A2157;"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">Développement humain</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Renforcement des systèmes éducatifs et de santé, promotion de la jeunesse, égalité des genres, protection sociale et inclusion des communautés vulnérables.
                </p>
                <div class="mt-4 flex items-center gap-1 text-xs font-semibold" style="color:#C4922A;">
                    En savoir plus <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 5 — MOT DU PRÉSIDENT
════════════════════════════════════════════════ --}}
<section class="quote-bg py-20">
    <div class="max-w-5xl mx-auto px-4 text-center">
        <div class="h-16 w-16 rounded-full border-2 flex items-center justify-center mx-auto mb-6" style="border-color:#C4922A; background:rgba(196,146,42,0.1);">
            <i class="fas fa-quote-left text-2xl" style="color:#C4922A;"></i>
        </div>
        <blockquote class="text-xl lg:text-2xl text-white leading-relaxed font-light italic mb-8" style="font-family:'Merriweather',serif;">
            « L'Afrique centrale possède toutes les ressources nécessaires à son émergence. Notre rôle est de transformer
            cette richesse naturelle en prospérité partagée, de forger les chaînes de solidarité qui unissent nos peuples
            et d'assurer à chaque citoyen de notre espace commun un avenir digne et prometteur. »
        </blockquote>
        <div class="flex items-center justify-center gap-4">
            <div class="h-14 w-14 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold text-lg">
                GV
            </div>
            <div class="text-left">
                <p class="text-white font-semibold">Gilberto da Piedade Verissimo</p>
                <p class="text-xs" style="color:#C4922A;">Président de la Commission de la CEEAC</p>
            </div>
        </div>
        <div class="mt-6">
            <a href="{{ route('website.president') }}"
               class="inline-flex items-center gap-2 text-sm font-medium border rounded-lg px-4 py-2 transition text-white hover:text-white"
               style="border-color:rgba(196,146,42,0.5); hover:border-color:#C4922A;">
                Lire le message intégral <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 6 — ACTUALITÉS
════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Actualités</p>
                <h2 class="text-3xl font-black" style="color:#0A2157; font-family:'Merriweather',serif;">Dernières nouvelles</h2>
                <hr class="divider-gold mt-3" style="margin-left:0;">
            </div>
            <a href="{{ route('website.actualites') }}" class="text-sm font-medium flex items-center gap-1 hidden lg:flex" style="color:#0A2157;">
                Toutes les actualités <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            @php
            $actualites = [
                ['date'=>'20 mars 2026', 'categorie'=>'Sommet', 'icon'=>'fa-star',
                 'titre'=>'Le 18e Sommet de la CEEAC adopte la feuille de route 2025–2030',
                 'resume'=>"Les chefs d'État et de gouvernement des 11 États membres ont adopté à Libreville la nouvelle feuille de route stratégique qui guidera l'action de la Commission pour les cinq prochaines années.",
                 'lien'=>'#'],
                ['date'=>'15 mars 2026', 'categorie'=>'Sécurité', 'icon'=>'fa-shield-alt',
                 'titre'=>'La CEEAC renforce son dispositif de lutte contre le terrorisme en Afrique centrale',
                 'resume'=>"À l'issue de la réunion des ministres en charge de la sécurité, la Commission a présenté les nouvelles mesures adoptées pour renforcer la coopération sécuritaire régionale et contrer la menace terroriste.",
                 'lien'=>'#'],
                ['date'=>'8 mars 2026', 'categorie'=>'Économie', 'icon'=>'fa-chart-line',
                 'titre'=>'Lancement du programme de facilitation du commerce intra-CEEAC 2026',
                 'resume'=>'La Commission lance un nouveau programme visant à réduire les barrières non tarifaires, harmoniser les procédures douanières et dynamiser les échanges commerciaux entre les États membres.',
                 'lien'=>'#'],
            ];
            @endphp

            @foreach($actualites as $actu)
            <article class="bg-white rounded-xl border border-gray-100 overflow-hidden card-hover shadow-sm">
                <div class="news-img flex items-center justify-center" style="background:linear-gradient(135deg,#e8edf5,#c5d0e6);">
                    <i class="fas {{ $actu['icon'] }} text-5xl opacity-20" style="color:#0A2157;"></i>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:rgba(196,146,42,0.12); color:#C4922A;">{{ $actu['categorie'] }}</span>
                        <span class="text-xs text-gray-400">{{ $actu['date'] }}</span>
                    </div>
                    <h3 class="text-base font-bold mb-2 line-clamp-2" style="color:#0A2157; font-family:'Merriweather',serif;">
                        {{ $actu['titre'] }}
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-3 mb-4">{{ $actu['resume'] }}</p>
                    <a href="{{ $actu['lien'] }}" class="text-xs font-semibold flex items-center gap-1" style="color:#C4922A;">
                        Lire la suite <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
        <div class="text-center mt-8 lg:hidden">
            <a href="{{ route('website.actualites') }}" class="inline-flex items-center gap-2 text-sm font-medium" style="color:#0A2157;">
                Toutes les actualités <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 7 — PROGRAMMES PHARES
════════════════════════════════════════════════ --}}
<section class="py-20" style="background:#f8f9fb;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">En action</p>
            <h2 class="text-3xl font-black" style="color:#0A2157; font-family:'Merriweather',serif;">Programmes phares</h2>
            <hr class="divider-gold mt-4">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

            @php
            $programmes = [
                ['icon'=>'fa-route', 'titre'=>'PDCT-AC', 'desc'=>"Plan Directeur Consensuel des Transports en Afrique Centrale — 30 projets prioritaires d'interconnexion routière, ferroviaire et fluviale.", 'budget'=>'12,5 Mds $'],
                ['icon'=>'fa-bolt', 'titre'=>'PEAC', 'desc'=>"Pool Énergétique de l'Afrique Centrale — interconnexion des réseaux électriques nationaux pour garantir l'accès à l'énergie à tous.", 'budget'=>'8,2 Mds $'],
                ['icon'=>'fa-shield-alt', 'titre'=>'COPAX', 'desc'=>"Conseil de Paix et de Sécurité de l'Afrique Centrale — mécanisme régional de prévention, de gestion et de règlement des conflits.", 'budget'=>'450 M $'],
                ['icon'=>'fa-seedling', 'titre'=>'CICOS', 'desc'=>"Commission Internationale du Bassin Congo-Oubangui-Sangha — gestion durable du 2e plus grand fleuve d'Afrique.", 'budget'=>'230 M $'],
            ];
            @endphp

            @foreach($programmes as $prog)
            <div class="prog-card rounded-xl p-6 text-white card-hover">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center mb-4" style="background:rgba(196,146,42,0.2);">
                    <i class="fas {{ $prog['icon'] }} text-xl" style="color:#C4922A;"></i>
                </div>
                <h3 class="text-base font-bold mb-2" style="font-family:'Merriweather',serif;">{{ $prog['titre'] }}</h3>
                <p class="text-xs text-blue-200 leading-relaxed mb-4">{{ $prog['desc'] }}</p>
                <div class="pt-3 border-t border-blue-700">
                    <span class="text-xs text-blue-300">Budget estimé</span>
                    <p class="font-bold" style="color:#C4922A;">{{ $prog['budget'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('website.programmes') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg font-semibold text-sm text-white transition"
               style="background:#0A2157;">
                Tous nos programmes <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 8 — PUBLICATIONS
════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Médiathèque</p>
                <h2 class="text-3xl font-black" style="color:#0A2157; font-family:'Merriweather',serif;">Publications récentes</h2>
                <hr class="divider-gold mt-3" style="margin-left:0;">
            </div>
            <a href="{{ route('website.publications') }}" class="text-sm font-medium flex items-center gap-1 hidden lg:flex" style="color:#0A2157;">
                Toutes les publications <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            @php
            $publications = [
                ['type'=>'Rapport annuel', 'icon'=>'fa-file-alt', 'couleur'=>'#0A2157',
                 'titre'=>'Rapport annuel de la Commission 2024',
                 'desc'=>'Bilan complet des activités de la Commission, résultats obtenus et perspectives pour 2025.',
                 'date'=>'Janvier 2025', 'pages'=>'142 pages'],
                ['type'=>'Étude stratégique', 'icon'=>'fa-book-open', 'couleur'=>'#C4922A',
                 'titre'=>"Indice d'Intégration Régionale CEEAC 2024",
                 'desc'=>"Analyse comparative des progrès d'intégration des 11 États membres selon 8 dimensions clés.",
                 'date'=>'Novembre 2024', 'pages'=>'98 pages'],
                ['type'=>'Note de politique', 'icon'=>'fa-scroll', 'couleur'=>'#16a34a',
                 'titre'=>'Transition énergétique et Bassin du Congo',
                 'desc'=>'Orientations pour une stratégie régionale de valorisation du potentiel hydroélectrique du Bassin du Congo.',
                 'date'=>'Octobre 2024', 'pages'=>'56 pages'],
            ];
            @endphp

            @foreach($publications as $pub)
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden card-hover shadow-sm flex flex-col">
                <div class="p-5 flex items-center gap-3" style="background:#f8f9fb; border-bottom:3px solid {{ $pub['couleur'] }};">
                    <div class="h-12 w-12 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $pub['couleur'] }}20;">
                        <i class="fas {{ $pub['icon'] }} text-xl" style="color:{{ $pub['couleur'] }};"></i>
                    </div>
                    <div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full text-white" style="background:{{ $pub['couleur'] }};">{{ $pub['type'] }}</span>
                        <p class="text-xs text-gray-400 mt-1">{{ $pub['date'] }}</p>
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="text-base font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">{{ $pub['titre'] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed flex-1 mb-4">{{ $pub['desc'] }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">{{ $pub['pages'] }}</span>
                        <a href="{{ route('website.publications') }}" class="text-xs font-semibold flex items-center gap-1" style="color:#C4922A;">
                            <i class="fas fa-download"></i> Télécharger
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 9 — ÉVÉNEMENTS
════════════════════════════════════════════════ --}}
<section class="py-20" style="background:#f8f9fb;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Agenda -->
            <div class="lg:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Agenda</p>
                <h2 class="text-3xl font-black mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">Événements à venir</h2>
                <hr class="divider-gold mb-8" style="margin-left:0;">

                @php
                $evenements = [
                    ['mois'=>'AVR', 'jour'=>'15', 'titre'=>'Conseil des Ministres — Session ordinaire', 'lieu'=>'Libreville, Gabon', 'type'=>'Institutionnel'],
                    ['mois'=>'MAI', 'jour'=>'06', 'titre'=>"Forum régional sur l'intégration économique", 'lieu'=>'Douala, Cameroun', 'type'=>'Forum'],
                    ['mois'=>'MAI', 'jour'=>'22', 'titre'=>'Séminaire sur la gestion durable du Bassin du Congo', 'lieu'=>'Brazzaville, Congo', 'type'=>'Séminaire'],
                    ['mois'=>'JUN', 'jour'=>'10', 'titre'=>'Réunion des experts sur la facilitation du commerce', 'lieu'=>'Kinshasa, RDC', 'type'=>"Réunion d'experts"],
                    ['mois'=>'JUL', 'jour'=>'03', 'titre'=>"19e Sommet des Chefs d'État et de Gouvernement", 'lieu'=>'Luanda, Angola', 'type'=>'Sommet'],
                ];
                @endphp

                <div class="space-y-3">
                    @foreach($evenements as $evt)
                    <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-4 card-hover shadow-sm">
                        <div class="h-14 w-14 rounded-xl flex flex-col items-center justify-center flex-shrink-0 text-white" style="background:#0A2157;">
                            <span class="text-xs font-bold uppercase leading-none">{{ $evt['mois'] }}</span>
                            <span class="text-xl font-black leading-none">{{ $evt['jour'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold truncate" style="color:#0A2157;">{{ $evt['titre'] }}</h4>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt" style="color:#C4922A;"></i> {{ $evt['lieu'] }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(196,146,42,0.1); color:#C4922A;">{{ $evt['type'] }}</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs text-gray-300 flex-shrink-0"></i>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    <a href="{{ route('website.evenements') }}" class="text-sm font-medium flex items-center gap-1" style="color:#0A2157;">
                        Voir tout l'agenda <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Communiqués -->
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Presse</p>
                <h2 class="text-2xl font-black mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">Communiqués</h2>
                <hr class="divider-gold mb-8" style="margin-left:0;">
                <div class="space-y-4">
                    @php
                    $communiques = [
                        ['date'=>'18 mars 2026', 'titre'=>'Communiqué final du 18e Sommet de la CEEAC'],
                        ['date'=>'10 mars 2026', 'titre'=>'La CEEAC condamne les actes de violence en République Centrafricaine'],
                        ['date'=>'28 fév. 2026', 'titre'=>"Déclaration sur l'accélération de la Zone de Libre-Échange"],
                        ['date'=>'15 fév. 2026', 'titre'=>"La CEEAC salue l'accord de paix entre le Burundi et ses voisins"],
                        ['date'=>'2 fév. 2026', 'titre'=>'Déclaration sur la préservation du Bassin du Congo'],
                    ];
                    @endphp
                    @foreach($communiques as $com)
                    <a href="{{ route('website.communiques') }}" class="block group">
                        <p class="text-xs text-gray-400 mb-1">{{ $com['date'] }}</p>
                        <p class="text-sm font-medium text-gray-700 group-hover:text-navy-500 leading-snug" style="hover:color:#0A2157;">{{ $com['titre'] }}</p>
                        <div class="mt-1 h-px bg-gray-100"></div>
                    </a>
                    @endforeach
                    <a href="{{ route('website.communiques') }}" class="text-sm font-medium flex items-center gap-1 mt-4" style="color:#0A2157;">
                        Tous les communiqués <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 10 — ÉTATS MEMBRES
════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Notre communauté</p>
            <h2 class="text-3xl font-black" style="color:#0A2157; font-family:'Merriweather',serif;">Les 11 États membres</h2>
            <hr class="divider-gold mt-4">
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
            $etats = [
                ['nom'=>'Angola', 'capitale'=>'Luanda', 'code'=>'AO'],
                ['nom'=>'Burundi', 'capitale'=>'Bujumbura', 'code'=>'BI'],
                ['nom'=>'Cameroun', 'capitale'=>'Yaoundé', 'code'=>'CM'],
                ['nom'=>'Rép. Centrafricaine', 'capitale'=>'Bangui', 'code'=>'CF'],
                ['nom'=>'Congo', 'capitale'=>'Brazzaville', 'code'=>'CG'],
                ['nom'=>'R.D. Congo', 'capitale'=>'Kinshasa', 'code'=>'CD'],
                ['nom'=>'Guinée Équatoriale', 'capitale'=>'Malabo', 'code'=>'GQ'],
                ['nom'=>'Gabon', 'capitale'=>'Libreville', 'code'=>'GA'],
                ['nom'=>'Rwanda', 'capitale'=>'Kigali', 'code'=>'RW'],
                ['nom'=>'São Tomé-et-Príncipe', 'capitale'=>'São Tomé', 'code'=>'ST'],
                ['nom'=>'Tchad', 'capitale'=>'N\'Djaména', 'code'=>'TD'],
            ];
            @endphp
            @foreach($etats as $etat)
            <a href="{{ route('website.etats-membres') }}"
               class="bg-white border border-gray-100 rounded-xl p-4 text-center card-hover shadow-sm">
                <div class="h-10 w-14 rounded mx-auto mb-3 flex items-center justify-center font-bold text-white text-sm" style="background:#0A2157;">
                    {{ $etat['code'] }}
                </div>
                <p class="text-xs font-semibold" style="color:#0A2157;">{{ $etat['nom'] }}</p>
                <p class="text-xs text-gray-400">{{ $etat['capitale'] }}</p>
            </a>
            @endforeach
            <!-- Voir plus card -->
            <a href="{{ route('website.etats-membres') }}"
               class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center card-hover flex flex-col items-center justify-center hover:border-gold-400"
               style="hover:border-color:#C4922A;">
                <i class="fas fa-globe-africa text-2xl mb-2 text-gray-300"></i>
                <p class="text-xs font-semibold text-gray-400">En savoir plus</p>
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     SECTION 11 — ACCÈS RAPIDE
════════════════════════════════════════════════ --}}
<section class="py-16" style="background:#0A2157;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-black text-white" style="font-family:'Merriweather',serif;">Accès rapide</h2>
            <p class="text-blue-300 text-sm mt-2">Ressources et services en ligne de la Commission</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
            $acces = [
                ['icon'=>'fa-file-download', 'label'=>'Télécharger des documents', 'route'=>'website.publications'],
                ['icon'=>'fa-calendar-check', 'label'=>'Agenda institutionnel', 'route'=>'website.evenements'],
                ['icon'=>'fa-envelope', 'label'=>'Contacter la Commission', 'route'=>'website.contact'],
                ['icon'=>'fa-newspaper', 'label'=>'Espace presse', 'route'=>'website.communiques'],
            ];
            @endphp
            @foreach($acces as $item)
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center text-center p-5 rounded-xl border border-blue-800 hover:border-gold-400 transition group"
               style="hover:border-color:#C4922A;">
                <div class="h-12 w-12 rounded-full flex items-center justify-center mb-3 transition" style="background:rgba(196,146,42,0.15);">
                    <i class="fas {{ $item['icon'] }} text-xl" style="color:#C4922A;"></i>
                </div>
                <span class="text-sm text-blue-200 group-hover:text-white transition">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
