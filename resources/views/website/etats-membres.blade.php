@extends('website.layouts.main')

@section('title', 'États membres — CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <a href="{{ route('website.a-propos') }}" class="hover:text-amber-400 transition">À propos</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">États membres</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Les <span class="text-amber-400">11 États membres</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            Onze nations unies autour d'une même ambition : construire une Afrique centrale
            intégrée, pacifique et prospère pour 230 millions d'habitants.
        </p>
        <div class="flex flex-wrap gap-6 mt-8">
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-400">11</div>
                <div class="text-blue-200 text-sm">États membres</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-400">~230M</div>
                <div class="text-blue-200 text-sm">Habitants</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-400">6,6M km²</div>
                <div class="text-blue-200 text-sm">Superficie</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-400">1983</div>
                <div class="text-blue-200 text-sm">Fondation</div>
            </div>
        </div>
    </div>
</section>

{{-- Carte régionale placeholder --}}
<section class="py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-blue-50 rounded-2xl p-8 text-center border-2 border-dashed border-blue-200">
            <div class="text-5xl mb-4">&#127758;</div>
            <p class="text-gray-500 text-sm">Carte interactive de l'espace CEEAC — À intégrer</p>
            <p class="text-gray-400 text-xs mt-1">Afrique centrale — 11 États membres</p>
        </div>
    </div>
</section>

{{-- Grille des États membres --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-950 text-center mb-10">Fiche par <span class="text-amber-500">État membre</span></h2>

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">

            {{-- Angola --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-red-700 to-black p-4 flex items-center gap-4">
                    <span class="text-4xl">🇦🇴</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Angola</h3>
                        <p class="text-sm opacity-80">République d'Angola</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">Luanda</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">1 247 000 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~35 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        L'Angola, plus grand pays d'Afrique subsaharienne francophone par sa superficie, est
                        l'un des premiers exportateurs de pétrole du continent. Son économie diversifiée et
                        ses ressources naturelles en font un acteur majeur de la CEEAC.
                    </p>
                </div>
            </div>

            {{-- Burundi --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-red-600 to-green-700 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇧🇮</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Burundi</h3>
                        <p class="text-sm opacity-80">République du Burundi</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale (officielle)</p>
                            <p class="font-semibold text-blue-950">Gitega</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">27 834 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~13 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Petit pays enclavé des Grands Lacs, le Burundi bénéficie d'une position stratégique
                        entre l'Afrique de l'Est et l'Afrique centrale. Son économie agropastorale fait
                        l'objet de programmes de développement soutenus par la CEEAC.
                    </p>
                </div>
            </div>

            {{-- Cameroun --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-green-700 via-red-600 to-yellow-500 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇨🇲</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Cameroun</h3>
                        <p class="text-sm opacity-80">République du Cameroun</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">Yaoundé</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">475 442 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~28 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Surnommé l'« Afrique en miniature » pour sa diversité géographique et culturelle,
                        le Cameroun est l'une des économies les plus dynamiques de la région CEEAC,
                        avec un tissu industriel et agricole développé.
                    </p>
                </div>
            </div>

            {{-- Congo --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-green-700 to-red-600 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇨🇬</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Congo</h3>
                        <p class="text-sm opacity-80">République du Congo</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">Brazzaville</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">342 000 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~6 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        La République du Congo, riverain du fleuve Congo, dispose d'importantes ressources
                        pétrolières et forestières. Brazzaville, sa capitale, joue un rôle important dans
                        les négociations de paix régionales.
                    </p>
                </div>
            </div>

            {{-- Gabon (siège) --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition ring-2 ring-amber-400">
                <div class="bg-gradient-to-r from-green-600 via-yellow-400 to-blue-600 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇬🇦</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Gabon</h3>
                        <p class="text-sm opacity-90 font-medium">République Gabonaise — &#9733; Siège de la CEEAC</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm text-amber-800 font-medium text-center">
                        &#9733; État hôte — Siège de la Commission de la CEEAC
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">Libreville</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">267 668 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~2,4 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Le Gabon, pays fondateur et État hôte de la CEEAC, abrite le siège de la Commission
                        à Libreville. Riche en pétrole, manganèse et bois, il dispose de l'un des couvertures
                        forestières les plus denses du Bassin du Congo.
                    </p>
                </div>
            </div>

            {{-- Guinée Équatoriale --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-green-600 via-white to-red-600 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇬🇶</span>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Guinée Équatoriale</h3>
                        <p class="text-sm text-gray-600">République de Guinée Équatoriale</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">Malabo</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">28 051 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~1,5 million</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Seul pays hispanophone d'Afrique subsaharienne, la Guinée Équatoriale accueille
                        le Parlement Communautaire à Malabo. Son économie pétrolière lui confère l'un
                        des revenus par habitant les plus élevés du continent.
                    </p>
                </div>
            </div>

            {{-- RCA --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-blue-700 via-white to-green-600 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇨🇫</span>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Rép. Centrafricaine</h3>
                        <p class="text-sm text-gray-600">République Centrafricaine (RCA)</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">Bangui</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">622 984 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~5,5 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Pays enclavé au cœur du continent, la RCA bénéficie du soutien actif de la CEEAC
                        dans le cadre de ses efforts de consolidation de la paix. La MICOPAX y a conduit
                        une opération majeure de stabilisation entre 2002 et 2013.
                    </p>
                </div>
            </div>

            {{-- RDC --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-sky-600 to-yellow-500 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇨🇩</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Rép. Dém. du Congo</h3>
                        <p class="text-sm opacity-80">République Démocratique du Congo (RDC)</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">Kinshasa</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">2 344 858 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~105 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1999 — Réadmission</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Plus grand pays francophone du monde et premier pays d'Afrique subsaharienne par
                        la population, la RDC constitue le coeur géographique et démographique de la CEEAC.
                        Ses immenses ressources minières et forestières font d'elle un acteur économique
                        de premier plan.
                    </p>
                </div>
            </div>

            {{-- Rwanda --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-blue-700 via-yellow-400 to-green-600 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇷🇼</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Rwanda</h3>
                        <p class="text-sm opacity-80">République du Rwanda</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">Kigali</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">26 338 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~14 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1996</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Le Rwanda, souvent cité comme modèle de reconstruction post-conflit et de développement
                        numérique en Afrique, apporte à la CEEAC son expertise en matière de bonne gouvernance
                        et de modernisation de l'administration publique.
                    </p>
                </div>
            </div>

            {{-- Sao Tomé-et-Príncipe --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-green-700 to-yellow-500 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇸🇹</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Sao Tomé-et-Príncipe</h3>
                        <p class="text-sm opacity-80">République Démocratique</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">São Tomé</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">964 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~230 000</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Petit archipel insulaire lusophone du Golfe de Guinée, Sao Tomé-et-Príncipe apporte
                        à la CEEAC sa dimension maritime. Ses eaux territoriales riches en ressources halieutiques
                        et son potentiel touristique en font un partenaire précieux pour l'économie bleue régionale.
                    </p>
                </div>
            </div>

            {{-- Tchad --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="bg-gradient-to-r from-blue-700 via-yellow-400 to-red-600 p-4 flex items-center gap-4">
                    <span class="text-4xl">🇹🇩</span>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Tchad</h3>
                        <p class="text-sm opacity-80">République du Tchad</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Capitale</p>
                            <p class="font-semibold text-blue-950">N'Djamena</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Superficie</p>
                            <p class="font-semibold text-blue-950">1 284 000 km²</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Population</p>
                            <p class="font-semibold text-blue-950">~18 millions</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">Adhésion CEEAC</p>
                            <p class="font-semibold text-amber-700">1983 — Fondateur</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Le Tchad, pays enclavé d'une importance géopolitique stratégique, se situe au
                        carrefour de l'Afrique centrale, sahélienne et orientale. Deuxième plus grand pays
                        de la CEEAC par la superficie, il joue un rôle clé dans les initiatives de stabilité
                        régionale, notamment dans le bassin du Lac Tchad.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Navigation --}}
<section class="py-10 bg-white border-t border-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ route('website.president') }}" class="text-blue-950 hover:text-amber-600 font-medium flex items-center gap-2 transition">
            &larr; Mot du Président
        </a>
        <a href="{{ route('website.a-propos') }}" class="bg-blue-950 text-white hover:bg-blue-900 font-medium flex items-center gap-2 px-5 py-2 rounded-lg transition">
            &larr; Retour à propos
        </a>
    </div>
</section>

@endsection
