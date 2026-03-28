@extends('website.layouts.main')

@section('title', 'À propos de la CEEAC — Commission Économique des États de l\'Afrique Centrale')

@section('content')

{{-- Hero Section --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">À propos</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">À propos de la <span class="text-amber-400">CEEAC</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            La Communauté Économique des États de l'Afrique Centrale, gardienne de l'intégration régionale
            et du développement durable pour 230 millions d'Africains.
        </p>
    </div>
</section>

{{-- Main Content with Sidebar --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12">

            {{-- Main Content --}}
            <div class="lg:w-2/3">

                {{-- Présentation générale --}}
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                    <h2 class="text-2xl font-bold text-blue-950 mb-4 border-b-2 border-amber-400 pb-2">Présentation générale</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        La Communauté Économique des États de l'Afrique Centrale (CEEAC) est une organisation
                        d'intégration régionale créée le <strong>18 octobre 1983</strong> à Libreville, Gabon,
                        par le Traité relatif à la création de la Communauté Économique des États de l'Afrique Centrale.
                        Elle regroupe onze États membres partageant la vision d'une Afrique centrale intégrée,
                        pacifique et prospère.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Reconnue comme l'un des huit piliers de la Communauté Économique Africaine (CEA) par l'Acte
                        constitutif de l'Union Africaine, la CEEAC constitue le cadre institutionnel de référence
                        pour la coopération économique, politique et sécuritaire en Afrique centrale. Son territoire
                        de compétence couvre environ <strong>6,6 millions de kilomètres carrés</strong>, abritant
                        une population de <strong>230 millions d'habitants</strong>.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Depuis sa relance en 1999, la CEEAC a considérablement renforcé ses mécanismes institutionnels,
                        élargi son agenda politique et développé des programmes ambitieux dans les domaines de la paix
                        et la sécurité, de l'intégration économique, du développement des infrastructures et de la
                        gouvernance démocratique.
                    </p>
                </div>

                {{-- Données clés --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-blue-950 text-white rounded-xl p-5 text-center">
                        <div class="text-3xl font-bold text-amber-400">11</div>
                        <div class="text-sm mt-1 text-blue-100">États membres</div>
                    </div>
                    <div class="bg-blue-950 text-white rounded-xl p-5 text-center">
                        <div class="text-3xl font-bold text-amber-400">230M</div>
                        <div class="text-sm mt-1 text-blue-100">Habitants</div>
                    </div>
                    <div class="bg-blue-950 text-white rounded-xl p-5 text-center">
                        <div class="text-3xl font-bold text-amber-400">6,6M</div>
                        <div class="text-sm mt-1 text-blue-100">km² de superficie</div>
                    </div>
                    <div class="bg-blue-950 text-white rounded-xl p-5 text-center">
                        <div class="text-3xl font-bold text-amber-400">1983</div>
                        <div class="text-sm mt-1 text-blue-100">Année de création</div>
                    </div>
                </div>

                {{-- Mandat --}}
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                    <h2 class="text-2xl font-bold text-blue-950 mb-4 border-b-2 border-amber-400 pb-2">Mandat institutionnel</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Conformément à l'article 3 du Traité de Libreville révisé, la CEEAC a pour objectif fondamental
                        de <em>«promouvoir et renforcer la coopération harmonieuse et le développement dynamique, équilibré
                        et autoentretenu dans tous les domaines de l'activité économique et sociale»</em>, en vue notamment
                        de réaliser l'autosuffisance collective et d'élever le niveau de vie des populations des États membres.
                    </p>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start gap-3">
                            <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                            <span>Promouvoir le développement économique harmonieux et équilibré des États membres</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                            <span>Assurer la paix, la stabilité et la sécurité dans la région de l'Afrique centrale</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                            <span>Favoriser la libre circulation des personnes, des biens, des capitaux et des services</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                            <span>Coordonner les politiques nationales de développement durable</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                            <span>Renforcer la démocratie, l'État de droit et la bonne gouvernance</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                            <span>Contribuer à la mise en oeuvre de l'Agenda 2063 de l'Union Africaine</span>
                        </li>
                    </ul>
                </div>

                {{-- Structure institutionnelle --}}
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                    <h2 class="text-2xl font-bold text-blue-950 mb-4 border-b-2 border-amber-400 pb-2">Structure institutionnelle</h2>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        La CEEAC dispose d'une architecture institutionnelle dotée de plusieurs organes principaux
                        et institutions spécialisées, chacun avec des attributions précises définies par le Traité
                        révisé et les protocoles additionnels.
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-amber-400 transition">
                            <h3 class="font-semibold text-blue-950 mb-2">Conférence des Chefs d'État</h3>
                            <p class="text-sm text-gray-600">Organe suprême de décision, se réunit en session ordinaire une fois par an.</p>
                        </div>
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-amber-400 transition">
                            <h3 class="font-semibold text-blue-950 mb-2">Conseil des Ministres</h3>
                            <p class="text-sm text-gray-600">Organe exécutif de coordination des politiques communautaires.</p>
                        </div>
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-amber-400 transition">
                            <h3 class="font-semibold text-blue-950 mb-2">Secrétariat Général</h3>
                            <p class="text-sm text-gray-600">Administration permanente de la Communauté, siège à Libreville.</p>
                        </div>
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-amber-400 transition">
                            <h3 class="font-semibold text-blue-950 mb-2">COPAX</h3>
                            <p class="text-sm text-gray-600">Conseil de Paix et Sécurité de l'Afrique Centrale, pilier sécuritaire.</p>
                        </div>
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-amber-400 transition">
                            <h3 class="font-semibold text-blue-950 mb-2">Cour de Justice Communautaire</h3>
                            <p class="text-sm text-gray-600">Garant de l'interprétation et de l'application du droit communautaire.</p>
                        </div>
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-amber-400 transition">
                            <h3 class="font-semibold text-blue-950 mb-2">Parlement Communautaire</h3>
                            <p class="text-sm text-gray-600">Organe délibérant représentant les populations de la Communauté.</p>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('website.organes') }}"
                           class="inline-block mt-2 bg-blue-950 text-white px-6 py-2 rounded-lg hover:bg-blue-900 transition font-medium">
                            Voir tous les organes
                        </a>
                    </div>
                </div>

                {{-- Siège --}}
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8">
                    <h2 class="text-2xl font-bold text-blue-950 mb-4">Siège de la Commission</h2>
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-1">
                            <p class="text-gray-700 leading-relaxed mb-3">
                                Le siège de la Commission de la CEEAC est établi à <strong>Libreville, République
                                Gabonaise</strong>, conformément aux dispositions du Traité fondateur signé le
                                18 octobre 1983. Ce choix stratégique reflète l'engagement du Gabon comme État
                                hôte et co-fondateur de l'organisation.
                            </p>
                            <p class="text-gray-700 text-sm">
                                <strong>Adresse :</strong> Boulevard de l'Indépendance, BP 2112, Libreville, Gabon<br>
                                <strong>Tél. :</strong> +241 44 47 31 / +241 44 47 32<br>
                                <strong>Email :</strong> commission@ceeac-eccas.org
                            </p>
                        </div>
                        <div class="md:w-48 flex items-center justify-center">
                            <div class="bg-blue-950 text-white rounded-xl p-6 text-center">
                                <div class="text-4xl mb-2">&#127468;&#127462;</div>
                                <div class="text-sm font-medium">Libreville<br>Gabon</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="lg:w-1/3 space-y-6">

                {{-- Navigation rapide --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-blue-950 mb-4 border-b-2 border-amber-400 pb-2">Navigation rapide</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('website.a-propos') }}"
                               class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 text-blue-950 font-medium">
                                <span class="text-amber-500">&#9679;</span> À propos (vue d'ensemble)
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('website.historique') }}"
                               class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-950 transition">
                                <span class="text-amber-500">&#9679;</span> Notre histoire
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('website.vision-mission') }}"
                               class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-950 transition">
                                <span class="text-amber-500">&#9679;</span> Vision & Mission
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('website.organes') }}"
                               class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-950 transition">
                                <span class="text-amber-500">&#9679;</span> Organes institutionnels
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('website.president') }}"
                               class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-950 transition">
                                <span class="text-amber-500">&#9679;</span> Mot du Président
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('website.etats-membres') }}"
                               class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-950 transition">
                                <span class="text-amber-500">&#9679;</span> États membres
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Documents clés --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-blue-950 mb-4 border-b-2 border-amber-400 pb-2">Documents fondateurs</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('website.publications') }}" class="flex items-start gap-3 hover:text-amber-600 transition">
                                <span class="text-red-500 text-xl mt-0.5">&#128196;</span>
                                <div>
                                    <div class="font-medium text-sm text-blue-950">Traité de Libreville (1983)</div>
                                    <div class="text-xs text-gray-500">Traité constitutif de la CEEAC</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('website.publications') }}" class="flex items-start gap-3 hover:text-amber-600 transition">
                                <span class="text-red-500 text-xl mt-0.5">&#128196;</span>
                                <div>
                                    <div class="font-medium text-sm text-blue-950">Protocole relatif au COPAX (1999)</div>
                                    <div class="text-xs text-gray-500">Paix et sécurité régionale</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('website.publications') }}" class="flex items-start gap-3 hover:text-amber-600 transition">
                                <span class="text-red-500 text-xl mt-0.5">&#128196;</span>
                                <div>
                                    <div class="font-medium text-sm text-blue-950">Vision CEEAC 2025</div>
                                    <div class="text-xs text-gray-500">Document stratégique de référence</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('website.publications') }}" class="flex items-start gap-3 hover:text-amber-600 transition">
                                <span class="text-red-500 text-xl mt-0.5">&#128196;</span>
                                <div>
                                    <div class="font-medium text-sm text-blue-950">Rapport annuel 2023</div>
                                    <div class="text-xs text-gray-500">Activités et réalisations</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Contact rapide --}}
                <div class="bg-blue-950 text-white rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-3">Contactez-nous</h3>
                    <p class="text-blue-100 text-sm mb-4">Pour toute demande d'information institutionnelle ou de coopération.</p>
                    <a href="{{ route('website.contact') }}"
                       class="block text-center bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Nous contacter
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
