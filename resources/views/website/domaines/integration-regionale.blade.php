@extends('website.layouts.main')

@section('title', 'Intégration Régionale — Domaines d\'action CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span class="hover:text-amber-400 transition">Domaines d'action</span>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Intégration régionale</span>
        </nav>
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-amber-500 text-white w-14 h-14 rounded-xl flex items-center justify-center text-2xl">&#127970;</div>
            <h1 class="text-4xl md:text-5xl font-bold">Intégration <span class="text-amber-400">Régionale</span></h1>
        </div>
        <p class="text-xl text-blue-100 max-w-3xl">
            L'intégration économique régionale est le coeur du projet communautaire de la CEEAC. Elle vise
            à créer un espace économique unifié, compétitif et attractif en Afrique centrale.
        </p>
    </div>
</section>

{{-- Vue d'ensemble --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-start">
            <div>
                <h2 class="text-3xl font-bold text-blue-950 mb-6">Un marché commun pour <span class="text-amber-500">230 millions d'Africains</span></h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    L'ambition de la CEEAC est de réaliser un marché commun régional permettant la libre
                    circulation des biens, des services, des capitaux et des personnes au sein de l'espace
                    communautaire. Cette intégration économique profonde doit permettre aux États membres
                    de tirer pleinement profit de leurs complémentarités et de leurs dotations en ressources
                    naturelles pour accélérer leur développement.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Le processus d'intégration économique de la CEEAC s'appuie sur une approche progressive,
                    tenant compte des différences de niveau de développement entre les États membres et des
                    défis structurels propres à la région. Il s'articule autour de quatre axes complémentaires :
                    la Zone de Libre-Échange (ZLE), l'Union Douanière, le Marché Commun et l'Union Économique.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    La coopération avec la Communauté Économique et Monétaire de l'Afrique Centrale (CEMAC)
                    est un élément central de cette stratégie, permettant d'éviter les doubles emplois et de
                    maximiser les synergies entre les deux organisations dont les membres se recoupent en partie.
                </p>
            </div>
            <div class="space-y-4">
                <div class="bg-blue-50 rounded-xl p-6 border-l-4 border-blue-950">
                    <h3 class="font-bold text-blue-950 mb-2">Phase 1 : Zone de Libre-Échange</h3>
                    <p class="text-gray-600 text-sm">Élimination progressive des droits de douane et des restrictions quantitatives sur les échanges entre États membres.</p>
                    <span class="inline-block mt-2 text-xs font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded">En cours</span>
                </div>
                <div class="bg-amber-50 rounded-xl p-6 border-l-4 border-amber-500">
                    <h3 class="font-bold text-blue-950 mb-2">Phase 2 : Union Douanière</h3>
                    <p class="text-gray-600 text-sm">Établissement d'un tarif extérieur commun (TEC) applicable aux importations provenant de pays tiers.</p>
                    <span class="inline-block mt-2 text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded">Coordination CEMAC</span>
                </div>
                <div class="bg-blue-50 rounded-xl p-6 border-l-4 border-blue-950">
                    <h3 class="font-bold text-blue-950 mb-2">Phase 3 : Marché Commun</h3>
                    <p class="text-gray-600 text-sm">Libre circulation des facteurs de production (capitaux, travailleurs) et harmonisation des politiques sectorielles.</p>
                    <span class="inline-block mt-2 text-xs font-bold text-orange-600 bg-orange-100 px-2 py-1 rounded">Perspective 2030</span>
                </div>
                <div class="bg-amber-50 rounded-xl p-6 border-l-4 border-amber-500">
                    <h3 class="font-bold text-blue-950 mb-2">Phase 4 : Union Économique</h3>
                    <p class="text-gray-600 text-sm">Harmonisation complète des politiques économiques, monétaires et fiscales dans un cadre communautaire unifié.</p>
                    <span class="inline-block mt-2 text-xs font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded">Vision long terme</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Libre circulation --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-950 mb-4">Libre circulation des <span class="text-amber-500">personnes et des biens</span></h2>
        <p class="text-gray-600 mb-10 max-w-3xl">
            La libre circulation des personnes est un élément fondamental de l'intégration régionale,
            permettant aux citoyens de l'espace CEEAC de se déplacer, de travailler et de s'établir
            librement dans tout État membre.
        </p>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                <div class="text-4xl mb-4">&#128100;</div>
                <h3 class="font-bold text-blue-950 mb-3">Mobilité des personnes</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Élimination progressive des visas pour les ressortissants des États membres.
                    Le Protocole sur la libre circulation prévoit un droit de résidence et d'établissement.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                <div class="text-4xl mb-4">&#128666;</div>
                <h3 class="font-bold text-blue-950 mb-3">Mobilité des marchandises</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Simplification des formalités douanières, harmonisation des documents de transit
                    et création de corridors commerciaux facilitant les échanges intra-régionaux.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                <div class="text-4xl mb-4">&#128184;</div>
                <h3 class="font-bold text-blue-950 mb-3">Capitaux et investissements</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Suppression des restrictions sur les mouvements de capitaux et création d'un cadre
                    juridique attractif pour les investissements directs étrangers dans la région.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                <div class="text-4xl mb-4">&#128203;</div>
                <h3 class="font-bold text-blue-950 mb-3">Services et professions</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Reconnaissance mutuelle des diplômes et qualifications professionnelles, facilitant
                    l'exercice des professions libérales et la prestation de services dans tout l'espace CEEAC.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- CEMAC --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="bg-blue-950 rounded-2xl p-8 text-white">
                <h3 class="text-xl font-bold text-amber-400 mb-4">Coordination avec la CEMAC</h3>
                <p class="text-blue-100 leading-relaxed mb-4">
                    La Communauté Économique et Monétaire de l'Afrique Centrale (CEMAC) regroupe six des onze
                    États membres de la CEEAC (Cameroun, Congo, Gabon, Guinée Équatoriale, RCA, Tchad) et
                    constitue un sous-ensemble intégré dans le cadre de la CEEAC.
                </p>
                <p class="text-blue-100 leading-relaxed mb-6">
                    La coordination entre les deux organisations est essentielle pour éviter les chevauchements
                    de compétences et maximiser l'efficacité des politiques d'intégration. Un mécanisme de
                    concertation permanente a été mis en place entre les deux commissions.
                </p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-blue-900 rounded-lg p-3 text-center">
                        <div class="font-bold text-amber-400 text-xl">6</div>
                        <div class="text-blue-200">États CEMAC</div>
                    </div>
                    <div class="bg-blue-900 rounded-lg p-3 text-center">
                        <div class="font-bold text-amber-400 text-xl">XAF</div>
                        <div class="text-blue-200">Franc CFA CEMAC</div>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-blue-950 mb-6">Intégration politique et institutionnelle</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Au-delà de l'intégration économique, la CEEAC promeut également l'intégration politique
                    à travers le renforcement des institutions démocratiques, la convergence des politiques
                    publiques et la construction d'une citoyenneté régionale commune.
                </p>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex gap-3">
                        <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                        <span>Harmonisation des politiques macroéconomiques pour assurer la stabilité et la convergence économique entre États membres</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                        <span>Coordination des politiques sectorielles dans l'agriculture, l'industrie, l'énergie et les services</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                        <span>Promotion de la démocratie, de l'État de droit et de la bonne gouvernance comme fondements de l'intégration</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                        <span>Renforcement de l'identité régionale à travers la culture, l'éducation et la jeunesse</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-amber-500 font-bold mt-1">&#9654;</span>
                        <span>Construction progressive d'une citoyenneté communautaire au profit des populations de la CEEAC</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="py-12 bg-amber-50 border-y border-amber-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-blue-950">11</div>
                <div class="text-gray-600 text-sm mt-1">États membres</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-950">~230M</div>
                <div class="text-gray-600 text-sm mt-1">Population régionale</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-950">&lt;5%</div>
                <div class="text-gray-600 text-sm mt-1">Commerce intra-CEEAC (à développer)</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-950">2030</div>
                <div class="text-gray-600 text-sm mt-1">Horizon marché commun</div>
            </div>
        </div>
    </div>
</section>

{{-- Liens vers domaines connexes --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-2xl font-bold text-blue-950 mb-6">Autres domaines d'action</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('website.domaine', 'paix-securite') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#9876;</span>
                <div>
                    <h4 class="font-semibold text-blue-950">Paix &amp; Sécurité</h4>
                    <p class="text-sm text-gray-500">COPAX, FOMAC, MARAC</p>
                </div>
            </a>
            <a href="{{ route('website.domaine', 'infrastructures') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#128747;</span>
                <div>
                    <h4 class="font-semibold text-blue-950">Infrastructures</h4>
                    <p class="text-sm text-gray-500">Transport, énergie, numérique</p>
                </div>
            </a>
            <a href="{{ route('website.domaine', 'commerce-investissement') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#128181;</span>
                <div>
                    <h4 class="font-semibold text-blue-950">Commerce</h4>
                    <p class="text-sm text-gray-500">Échanges, investissement</p>
                </div>
            </a>
        </div>
    </div>
</section>

@endsection
