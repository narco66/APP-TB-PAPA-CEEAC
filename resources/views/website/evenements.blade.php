@extends('website.layouts.main')

@section('title', 'Événements & Agenda — CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Événements</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Agenda <span class="text-amber-400">institutionnel</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            Suivez les sommets, réunions, forums et manifestations organisés par la Commission de la CEEAC
            et ses organes tout au long de l'année.
        </p>
    </div>
</section>

{{-- Filtres --}}
<section class="py-6 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-3 items-center">
            <button class="px-4 py-2 rounded-full bg-blue-950 text-white text-sm font-medium">Tous</button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm hover:bg-amber-100 hover:text-amber-800 transition">À venir</button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm hover:bg-amber-100 hover:text-amber-800 transition">Passés</button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm hover:bg-amber-100 hover:text-amber-800 transition">Sommets</button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm hover:bg-amber-100 hover:text-amber-800 transition">Réunions</button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm hover:bg-amber-100 hover:text-amber-800 transition">Forums</button>
            <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm hover:bg-amber-100 hover:text-amber-800 transition">Ateliers</button>
        </div>
    </div>
</section>

{{-- Événements à venir --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-2xl font-bold text-blue-950 mb-8 flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-green-500 inline-block animate-pulse"></span>
            Événements à venir
        </h2>

        <div class="space-y-4 mb-16">

            {{-- Sommet 35e --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="flex flex-col md:flex-row">
                    <div class="bg-blue-950 text-white md:w-36 flex-shrink-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-amber-400 font-bold text-sm uppercase tracking-widest">Juil.</div>
                        <div class="text-5xl font-black text-white">14</div>
                        <div class="text-blue-200 text-sm">2025</div>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="inline-block bg-amber-100 text-amber-700 text-xs font-bold px-2 py-1 rounded-full mb-2">Sommet</span>
                                <h3 class="text-xl font-bold text-blue-950">35e Sommet ordinaire des Chefs d'État et de Gouvernement de la CEEAC</h3>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">&#128197; À venir</span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                            <span class="flex items-center gap-1">&#128205; Luanda, Angola</span>
                            <span class="flex items-center gap-1">&#128336; 14–15 juillet 2025</span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Le 35e Sommet de la Conférence des Chefs d'État et de Gouvernement de la CEEAC se tiendra
                            à Luanda, République d'Angola. À l'ordre du jour : adoption de la feuille de route 2025–2030,
                            examen du rapport annuel d'activités 2024 et prise de décisions sur les questions de paix,
                            sécurité et développement régional.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Intégration régionale</span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Paix et sécurité</span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Gouvernance</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Réunion COPAX --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="flex flex-col md:flex-row">
                    <div class="bg-blue-900 text-white md:w-36 flex-shrink-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-amber-400 font-bold text-sm uppercase tracking-widest">Juin</div>
                        <div class="text-5xl font-black text-white">18</div>
                        <div class="text-blue-200 text-sm">2025</div>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="inline-block bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-full mb-2">Paix &amp; Sécurité</span>
                                <h3 class="text-xl font-bold text-blue-950">19e Session ordinaire de la Commission de Défense et Sécurité (CDS)</h3>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">&#128197; À venir</span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                            <span class="flex items-center gap-1">&#128205; Malabo, Guinée Équatoriale</span>
                            <span class="flex items-center gap-1">&#128336; 18–19 juin 2025</span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            La Commission de Défense et Sécurité du COPAX se réunira pour examiner la situation sécuritaire
                            dans la région, évaluer l'état de préparation de la FOMAC et adopter le programme d'action
                            2025–2026 en matière de paix et sécurité.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Forum économique --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="flex flex-col md:flex-row">
                    <div class="bg-amber-600 text-white md:w-36 flex-shrink-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-amber-100 font-bold text-sm uppercase tracking-widest">Mai</div>
                        <div class="text-5xl font-black text-white">22</div>
                        <div class="text-amber-100 text-sm">2025</div>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full mb-2">Forum</span>
                                <h3 class="text-xl font-bold text-blue-950">Forum économique CEEAC 2025 — « Financer l'intégration »</h3>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">&#128197; À venir</span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                            <span class="flex items-center gap-1">&#128205; Kinshasa, RDC</span>
                            <span class="flex items-center gap-1">&#128336; 22–24 mai 2025</span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Sous le thème « Financer l'intégration régionale en Afrique centrale », ce forum annuel
                            réunira gouvernements, institutions financières, secteur privé et société civile pour
                            identifier les mécanismes de mobilisation des ressources pour les projets communautaires.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Secteur privé</span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Investissement</span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Financement</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Atelier PIDE --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="flex flex-col md:flex-row">
                    <div class="bg-blue-950 text-white md:w-36 flex-shrink-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-amber-400 font-bold text-sm uppercase tracking-widest">Avr.</div>
                        <div class="text-5xl font-black text-white">8</div>
                        <div class="text-blue-200 text-sm">2025</div>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="inline-block bg-purple-100 text-purple-700 text-xs font-bold px-2 py-1 rounded-full mb-2">Atelier</span>
                                <h3 class="text-xl font-bold text-blue-950">Atelier de revue à mi-parcours du Programme Indicatif de Développement de la Communauté (PIDE)</h3>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">&#128197; À venir</span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                            <span class="flex items-center gap-1">&#128205; Libreville, Gabon</span>
                            <span class="flex items-center gap-1">&#128336; 8–10 avril 2025</span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Cet atelier technique réunira les experts des États membres, les partenaires techniques et
                            financiers pour évaluer les progrès accomplis dans la mise en oeuvre du PIDE et ajuster
                            les priorités pour la deuxième phase du programme.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Événements passés --}}
        <h2 class="text-2xl font-bold text-blue-950 mb-8 flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span>
            Événements passés
        </h2>

        <div class="space-y-4">

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden opacity-80 hover:opacity-100 transition">
                <div class="flex flex-col md:flex-row">
                    <div class="bg-gray-200 text-gray-600 md:w-36 flex-shrink-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-gray-500 font-bold text-sm uppercase tracking-widest">Janv.</div>
                        <div class="text-5xl font-black text-gray-600">16</div>
                        <div class="text-gray-500 text-sm">2025</div>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full mb-2">Sommet</span>
                                <h3 class="text-xl font-bold text-gray-700">34e Sommet ordinaire des Chefs d'État et de Gouvernement de la CEEAC</h3>
                            </div>
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">Terminé</span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-4">
                            <span>&#128205; Libreville, Gabon</span>
                            <span>&#128336; 15–16 janvier 2025</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Le 34e Sommet a adopté un plan d'action quinquennal pour l'intégration et approuvé
                            le budget communautaire 2025. Les Chefs d'État ont exprimé leur engagement renouvelé
                            en faveur de la paix et de la stabilité dans la région.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden opacity-80 hover:opacity-100 transition">
                <div class="flex flex-col md:flex-row">
                    <div class="bg-gray-200 text-gray-600 md:w-36 flex-shrink-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-gray-500 font-bold text-sm uppercase tracking-widest">Nov.</div>
                        <div class="text-5xl font-black text-gray-600">20</div>
                        <div class="text-gray-500 text-sm">2024</div>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full mb-2">Réunion</span>
                                <h3 class="text-xl font-bold text-gray-700">46e session ordinaire du Conseil des Ministres</h3>
                            </div>
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">Terminé</span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-3">
                            <span>&#128205; Brazzaville, Congo</span>
                            <span>&#128336; 20–21 novembre 2024</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Le Conseil des Ministres a examiné et adopté plusieurs actes communautaires, approuvé
                            le rapport d'activités 2024 et validé les ordres du jour du 34e Sommet.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden opacity-80 hover:opacity-100 transition">
                <div class="flex flex-col md:flex-row">
                    <div class="bg-gray-200 text-gray-600 md:w-36 flex-shrink-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-gray-500 font-bold text-sm uppercase tracking-widest">Sept.</div>
                        <div class="text-5xl font-black text-gray-600">10</div>
                        <div class="text-gray-500 text-sm">2024</div>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full mb-2">Forum</span>
                                <h3 class="text-xl font-bold text-gray-700">Conférence régionale sur le Genre et l'Intégration en Afrique Centrale</h3>
                            </div>
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">Terminé</span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-3">
                            <span>&#128205; Yaoundé, Cameroun</span>
                            <span>&#128336; 10–12 septembre 2024</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Cette conférence a réuni 200 participants de 11 pays pour élaborer une stratégie
                            régionale de promotion de l'égalité des genres dans les processus d'intégration
                            économique et politique de la CEEAC.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden opacity-80 hover:opacity-100 transition">
                <div class="flex flex-col md:flex-row">
                    <div class="bg-gray-200 text-gray-600 md:w-36 flex-shrink-0 flex flex-col items-center justify-center p-6 text-center">
                        <div class="text-gray-500 font-bold text-sm uppercase tracking-widest">Mai</div>
                        <div class="text-5xl font-black text-gray-600">14</div>
                        <div class="text-gray-500 text-sm">2024</div>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
                            <div>
                                <span class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full mb-2">Atelier</span>
                                <h3 class="text-xl font-bold text-gray-700">Atelier sur la mise en oeuvre de la Zone de Libre-Échange Continentale Africaine (ZLECAf) dans l'espace CEEAC</h3>
                            </div>
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">Terminé</span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-3">
                            <span>&#128205; Bangui, RCA</span>
                            <span>&#128336; 14–15 mai 2024</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Cet atelier a permis d'identifier les priorités de la CEEAC dans les négociations de
                            la ZLECAf et d'aligner les politiques commerciales des États membres avec les engagements
                            pris au niveau continental.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Abonnement --}}
        <div class="mt-12 bg-blue-950 text-white rounded-2xl p-8 flex flex-col md:flex-row gap-6 items-center">
            <div class="flex-1">
                <h3 class="text-xl font-bold text-amber-400 mb-2">Restez informé des prochains événements</h3>
                <p class="text-blue-100 text-sm">Inscrivez-vous pour recevoir les invitations et les informations sur les événements de la CEEAC.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                <input type="email" placeholder="votre@email.com"
                       class="border border-blue-700 bg-blue-900 text-white rounded-lg px-4 py-2 text-sm placeholder-blue-400 focus:ring-2 focus:ring-amber-400 outline-none">
                <button class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2 rounded-lg text-sm transition whitespace-nowrap">
                    S'inscrire
                </button>
            </div>
        </div>

    </div>
</section>

@endsection
