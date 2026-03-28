@extends('website.layouts.main')

@section('title', 'Histoire de la CEEAC — De Libreville à aujourd\'hui')

@section('content')

{{-- Hero Section --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <a href="{{ route('website.a-propos') }}" class="hover:text-amber-400 transition">À propos</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Notre histoire</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Notre <span class="text-amber-400">Histoire</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            Quatre décennies d'engagement au service de l'intégration régionale et du développement
            durable des peuples d'Afrique centrale.
        </p>
    </div>
</section>

{{-- Intro --}}
<section class="py-12 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-gray-700 text-lg leading-relaxed text-center">
            L'histoire de la CEEAC est indissociable de la marche vers l'intégration africaine. Née du rêve
            panafricain d'une Afrique centrale unie, l'organisation a traversé des périodes de crise et de
            renouveau pour s'imposer aujourd'hui comme un acteur incontournable de la paix, de la sécurité
            et du développement régional.
        </p>
    </div>
</section>

{{-- Timeline --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-950 text-center mb-12">Chronologie <span class="text-amber-500">historique</span></h2>

        <div class="relative">
            {{-- Timeline line --}}
            <div class="absolute left-1/2 transform -translate-x-0.5 h-full w-1 bg-amber-400 hidden md:block"></div>

            {{-- 1983 --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center mb-16">
                <div class="md:w-1/2 md:pr-12 md:text-right">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-400 md:border-l-0 md:border-r-4">
                        <span class="inline-block bg-amber-400 text-blue-950 font-bold text-sm px-3 py-1 rounded-full mb-3">18 octobre 1983</span>
                        <h3 class="text-xl font-bold text-blue-950 mb-3">Création de la CEEAC à Libreville</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Le Traité instituant la Communauté Économique des États de l'Afrique Centrale est signé
                            à Libreville, Gabon, par les dirigeants de dix États fondateurs : l'Angola, le Burundi,
                            le Cameroun, la République Centrafricaine, le Congo, le Gabon, la Guinée Équatoriale,
                            le Rwanda, Sao Tomé-et-Príncipe et le Tchad. Cet acte fondateur marque l'aboutissement
                            de plusieurs années de négociations diplomatiques sous l'impulsion de la Conférence des
                            Chefs d'État et de Gouvernement de l'Organisation de l'Unité Africaine (OUA).
                        </p>
                        <p class="text-gray-600 leading-relaxed mt-3">
                            Le Traité fixe comme objectif central la réalisation d'un marché commun en Afrique centrale
                            à travers l'élimination progressive des barrières tarifaires et non tarifaires, la
                            coordination des politiques économiques et l'harmonisation des législations nationales.
                        </p>
                    </div>
                </div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-amber-400 border-4 border-white rounded-full shadow items-center justify-center">
                    <span class="text-blue-950 font-bold text-xs">83</span>
                </div>
                <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
            </div>

            {{-- 1985 --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center mb-16">
                <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-blue-950 border-4 border-white rounded-full shadow items-center justify-center">
                    <span class="text-amber-400 font-bold text-xs">85</span>
                </div>
                <div class="md:w-1/2 md:pl-12">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-950">
                        <span class="inline-block bg-blue-950 text-amber-400 font-bold text-sm px-3 py-1 rounded-full mb-3">Décembre 1985</span>
                        <h3 class="text-xl font-bold text-blue-950 mb-3">Entrée en vigueur du Traité</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Après ratification par les États membres, le Traité de Libreville entre officiellement
                            en vigueur. Le Secrétariat Général de la CEEAC est établi à Libreville. Les premières
                            réunions des organes communautaires posent les bases du fonctionnement institutionnel.
                            Cette période initiale est marquée par la mise en place des structures administratives
                            et la définition des modalités pratiques de coopération inter-étatique.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 1992 --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center mb-16">
                <div class="md:w-1/2 md:pr-12 md:text-right">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-400 md:border-l-0 md:border-r-4">
                        <span class="inline-block bg-amber-400 text-blue-950 font-bold text-sm px-3 py-1 rounded-full mb-3">Années 1990 — Crise institutionnelle</span>
                        <h3 class="text-xl font-bold text-blue-950 mb-3">Difficultés et ralentissement</h3>
                        <p class="text-gray-600 leading-relaxed">
                            La décennie 1990 est marquée par de sérieuses difficultés pour la CEEAC. Les conflits
                            armés qui embrasent plusieurs États membres — notamment la République Démocratique du
                            Congo, la République Centrafricaine, l'Angola et le Burundi — paralysent les activités
                            de l'organisation. Les contributions financières des États membres deviennent irrégulières,
                            entraînant une crise budgétaire grave qui compromet le fonctionnement des institutions.
                        </p>
                        <p class="text-gray-600 leading-relaxed mt-3">
                            Les réunions des organes de la Communauté se raréfient. La Conférence des Chefs d'État
                            et de Gouvernement, qui devait se tenir annuellement, n'est plus convoquée de manière
                            régulière. Cette période difficile pousse les États membres à réfléchir à une profonde
                            réforme de l'organisation.
                        </p>
                    </div>
                </div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-amber-400 border-4 border-white rounded-full shadow items-center justify-center">
                    <span class="text-blue-950 font-bold text-xs">90</span>
                </div>
                <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
            </div>

            {{-- 1999 --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center mb-16">
                <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-green-600 border-4 border-white rounded-full shadow items-center justify-center">
                    <span class="text-white font-bold text-xs">99</span>
                </div>
                <div class="md:w-1/2 md:pl-12">
                    <div class="bg-green-50 rounded-2xl shadow-sm p-6 border-l-4 border-green-600">
                        <span class="inline-block bg-green-600 text-white font-bold text-sm px-3 py-1 rounded-full mb-3">1999 — Relance historique</span>
                        <h3 class="text-xl font-bold text-blue-950 mb-3">La renaissance de la CEEAC</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Le Sommet de Malabo, en Guinée Équatoriale, marque un tournant décisif dans l'histoire
                            de la CEEAC. Les Chefs d'État et de Gouvernement décident de relancer l'organisation
                            sur des bases rénovées. La République Démocratique du Congo, qui avait suspendu sa
                            participation, est réadmise au sein de la Communauté. Des réformes institutionnelles
                            profondes sont initiées pour rendre l'organisation plus efficace et adaptée aux enjeux
                            contemporains de la région.
                        </p>
                        <p class="text-gray-600 leading-relaxed mt-3">
                            La RDC rejoint ainsi les dix autres États membres, portant à onze le nombre total de
                            membres et élargissant considérablement le poids démographique et économique de la
                            Communauté.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 2000 COPAX --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center mb-16">
                <div class="md:w-1/2 md:pr-12 md:text-right">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-400 md:border-l-0 md:border-r-4">
                        <span class="inline-block bg-amber-400 text-blue-950 font-bold text-sm px-3 py-1 rounded-full mb-3">24 février 2000</span>
                        <h3 class="text-xl font-bold text-blue-950 mb-3">Création du COPAX</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Le Protocole relatif au Conseil de Paix et de Sécurité de l'Afrique Centrale (COPAX)
                            est adopté lors du Sommet de Malabo. Cette décision constitue l'un des actes fondateurs
                            les plus importants de l'histoire récente de la CEEAC. Le COPAX se compose de la
                            Commission de Défense et de Sécurité (CDS), du Mécanisme d'Alerte Rapide de l'Afrique
                            Centrale (MARAC) et de la Force Multinationale de l'Afrique Centrale (FOMAC).
                        </p>
                        <p class="text-gray-600 leading-relaxed mt-3">
                            Ce cadre institutionnel novateur permet à la CEEAC de se doter d'une capacité autonome
                            de prévention des conflits, de gestion des crises et de maintien de la paix, en
                            cohérence avec l'Architecture Africaine de Paix et de Sécurité (AAPS) de l'UA.
                        </p>
                    </div>
                </div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-amber-400 border-4 border-white rounded-full shadow items-center justify-center">
                    <span class="text-blue-950 font-bold text-xs">00</span>
                </div>
                <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
            </div>

            {{-- 2002-2010 MICOPAX --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center mb-16">
                <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-blue-950 border-4 border-white rounded-full shadow items-center justify-center">
                    <span class="text-amber-400 font-bold text-xs">08</span>
                </div>
                <div class="md:w-1/2 md:pl-12">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-950">
                        <span class="inline-block bg-blue-950 text-amber-400 font-bold text-sm px-3 py-1 rounded-full mb-3">2002–2013 — MICOPAX en RCA</span>
                        <h3 class="text-xl font-bold text-blue-950 mb-3">Premières opérations de maintien de la paix</h3>
                        <p class="text-gray-600 leading-relaxed">
                            La CEEAC déploie la Mission de Consolidation de la Paix en Centrafrique (MICOPAX),
                            l'une des premières opérations de paix conduites sous l'égide d'une organisation
                            sous-régionale africaine. Cette mission, déployée en République Centrafricaine,
                            contribue à stabiliser le pays après plusieurs années de crises politico-militaires.
                            Elle témoigne de la capacité opérationnelle croissante de la CEEAC en matière de paix
                            et sécurité.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 2010s Réformes --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center mb-16">
                <div class="md:w-1/2 md:pr-12 md:text-right">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-400 md:border-l-0 md:border-r-4">
                        <span class="inline-block bg-amber-400 text-blue-950 font-bold text-sm px-3 py-1 rounded-full mb-3">2010–2019 — Réformes institutionnelles</span>
                        <h3 class="text-xl font-bold text-blue-950 mb-3">Modernisation et renforcement</h3>
                        <p class="text-gray-600 leading-relaxed">
                            La décennie 2010 est celle des grandes réformes structurelles. Le Traité révisé de la
                            CEEAC est adopté, modernisant la gouvernance de l'organisation. La transformation du
                            Secrétariat Général en Commission est décidée, conférant à l'organe exécutif des
                            attributions plus larges et des pouvoirs d'initiative renforcés. De nouveaux domaines
                            d'intervention sont intégrés : changements climatiques, économie bleue, genre et
                            développement, jeunesse.
                        </p>
                        <p class="text-gray-600 leading-relaxed mt-3">
                            Le Programme Indicatif de Développement de la Communauté (PIDE) est lancé pour
                            harmoniser les investissements en infrastructures transfrontalières.
                        </p>
                    </div>
                </div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-amber-400 border-4 border-white rounded-full shadow items-center justify-center">
                    <span class="text-blue-950 font-bold text-xs">15</span>
                </div>
                <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
            </div>

            {{-- 2021 Transformation --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center mb-16">
                <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-amber-500 border-4 border-white rounded-full shadow items-center justify-center">
                    <span class="text-white font-bold text-xs">21</span>
                </div>
                <div class="md:w-1/2 md:pl-12">
                    <div class="bg-amber-50 rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
                        <span class="inline-block bg-amber-500 text-white font-bold text-sm px-3 py-1 rounded-full mb-3">2021 — Réforme majeure</span>
                        <h3 class="text-xl font-bold text-blue-950 mb-3">Transformation en Commission</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Le 18 août 2021, lors du Sommet extraordinaire des Chefs d'État et de Gouvernement,
                            la CEEAC adopte son Traité révisé qui transforme le Secrétariat Général en
                            <strong>Commission de la CEEAC</strong>. Cette réforme historique renforce
                            significativement le pouvoir supranational de l'organisation, lui conférant une
                            personnalité juridique renforcée et des compétences élargies. La Commission est
                            désormais dotée de huit départements thématiques couvrant l'ensemble des domaines
                            d'intégration communautaire.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Aujourd'hui --}}
            <div class="relative flex flex-col md:flex-row items-start md:items-center">
                <div class="md:w-1/2 md:pr-12 md:text-right">
                    <div class="bg-blue-950 text-white rounded-2xl shadow-sm p-6">
                        <span class="inline-block bg-amber-400 text-blue-950 font-bold text-sm px-3 py-1 rounded-full mb-3">Aujourd'hui</span>
                        <h3 class="text-xl font-bold text-amber-400 mb-3">La CEEAC en 2025–2026</h3>
                        <p class="text-blue-100 leading-relaxed">
                            Forte de plus de quarante ans d'existence, la CEEAC s'affirme comme un pilier
                            essentiel de l'intégration africaine. Avec ses programmes ambitieux (PAPS, PIDE, PACE),
                            ses mécanismes de paix et sécurité opérationnels, et sa nouvelle architecture
                            institutionnelle, la Commission de la CEEAC œuvre résolument à la réalisation de la
                            vision d'une Afrique centrale intégrée, pacifique et prospère, en parfaite cohérence
                            avec l'Agenda 2063 de l'Union Africaine.
                        </p>
                    </div>
                </div>
                <div class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-blue-950 border-4 border-amber-400 rounded-full shadow items-center justify-center">
                    <span class="text-amber-400 font-bold text-xs">&#9733;</span>
                </div>
                <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
            </div>
        </div>
    </div>
</section>

{{-- Navigation entre pages --}}
<section class="py-10 bg-white border-t border-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ route('website.a-propos') }}" class="text-blue-950 hover:text-amber-600 font-medium flex items-center gap-2 transition">
            &larr; À propos — Vue d'ensemble
        </a>
        <a href="{{ route('website.vision-mission') }}" class="bg-blue-950 text-white hover:bg-blue-900 font-medium flex items-center gap-2 px-5 py-2 rounded-lg transition">
            Vision &amp; Mission &rarr;
        </a>
    </div>
</section>

@endsection
