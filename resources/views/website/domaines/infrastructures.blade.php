@extends('website.layouts.main')

@section('title', 'Infrastructures — Domaines d\'action CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span>Domaines d'action</span>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Infrastructures</span>
        </nav>
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-amber-500 text-white w-14 h-14 rounded-xl flex items-center justify-center text-2xl">&#128747;</div>
            <h1 class="text-4xl md:text-5xl font-bold">Infrastructures <span class="text-amber-400">&amp; Connectivité</span></h1>
        </div>
        <p class="text-xl text-blue-100 max-w-3xl">
            Le développement des infrastructures régionales est le fondement matériel de l'intégration.
            Sans routes, sans énergie, sans réseaux numériques, l'union économique reste un idéal inaccessible.
        </p>
    </div>
</section>

{{-- PIDE --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block bg-amber-100 text-amber-700 font-bold text-sm px-4 py-2 rounded-full mb-4 uppercase tracking-wider">Programme phare</div>
                <h2 class="text-3xl font-bold text-blue-950 mb-6">Le PIDE — Programme Indicatif de Développement de la Communauté</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Le Programme Indicatif de Développement de la Communauté (PIDE) est le cadre stratégique
                    de référence de la CEEAC pour le développement des infrastructures régionales. Il identifie
                    et priorise les projets d'investissement à impact régional dans les secteurs des transports,
                    de l'énergie, des télécommunications et de l'eau.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Le PIDE est conçu comme un outil de planification à moyen et long terme, permettant aux
                    États membres et aux partenaires au développement de coordonner leurs investissements
                    pour un maximum d'impact. Il est régulièrement révisé pour tenir compte de l'évolution
                    des besoins et des priorités de la région.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Sa mise en oeuvre repose sur un partenariat étroit avec les institutions de financement
                    régionales et internationales, notamment la Banque de Développement des États de l'Afrique
                    Centrale (BDEAC), la Banque Africaine de Développement (BAD), la Banque Mondiale et
                    les partenaires bilatéraux.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-950 text-white rounded-xl p-5 text-center">
                    <div class="text-3xl font-bold text-amber-400">Trans-CEEAC</div>
                    <div class="text-sm text-blue-200 mt-1">Réseau routier régional</div>
                </div>
                <div class="bg-amber-500 text-white rounded-xl p-5 text-center">
                    <div class="text-3xl font-bold">Pool</div>
                    <div class="text-sm text-amber-100 mt-1">Énergétique de l'AC</div>
                </div>
                <div class="bg-blue-900 text-white rounded-xl p-5 text-center">
                    <div class="text-3xl font-bold text-amber-400">CAB</div>
                    <div class="text-sm text-blue-200 mt-1">Central Africa Backbone</div>
                </div>
                <div class="bg-blue-950 text-white rounded-xl p-5 text-center">
                    <div class="text-3xl font-bold text-amber-400">CICOS</div>
                    <div class="text-sm text-blue-200 mt-1">Navigation fluviale</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Transports --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-950 mb-4">Réseaux de <span class="text-amber-500">transport</span></h2>
        <p class="text-gray-600 mb-10 max-w-3xl">
            L'amélioration des infrastructures de transport est indispensable pour désenclaver les États,
            faciliter les échanges commerciaux et connecter les populations.
        </p>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-4xl mb-4">&#128739;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Réseau routier Trans-CEEAC</h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    Le réseau routier Trans-CEEAC identifie les axes prioritaires reliant les capitales et
                    centres économiques des États membres. Des projets de réhabilitation et de construction
                    de nouvelles routes transfrontalières sont en cours dans plusieurs corridors.
                </p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li>• Corridor Douala — N'Djamena</li>
                    <li>• Corridor Brazzaville — Bangui</li>
                    <li>• Corridor Libreville — Yaoundé</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-4xl mb-4">&#9992;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Transport aérien régional</h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    La CEEAC soutient le développement du transport aérien régional, notamment à travers
                    la mise en oeuvre de la Décision de Yamoussoukro sur la libéralisation du transport
                    aérien en Afrique. L'objectif est d'améliorer la connectivité aérienne entre les
                    onze États membres et de réduire les coûts des billets.
                </p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li>• Libéralisation du ciel régional</li>
                    <li>• Modernisation des aéroports</li>
                    <li>• Droits de trafic entre États membres</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-4xl mb-4">&#128674;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Voies fluviales et maritimes</h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    Le fleuve Congo et ses affluents constituent un réseau naturel de transport d'une
                    importance capitale. La CICOS (Commission Internationale du Bassin Congo-Oubangui-Sangha)
                    coordonne la gestion et le développement de ces voies navigables pour faciliter
                    le commerce fluvial régional.
                </p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li>• Voies navigables du Bassin Congo</li>
                    <li>• Sécurité maritime Golfe de Guinée</li>
                    <li>• Ports maritimes régionaux</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Énergie --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-blue-950 mb-6">Énergie et <span class="text-amber-500">électricité</span></h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    L'Afrique centrale possède l'un des plus grands potentiels hydroélectriques au monde,
                    notamment grâce au fleuve Congo et à son réseau de bassins versants. La CEEAC s'emploie
                    à exploiter ce potentiel au bénéfice des populations de la région, dont l'accès à
                    l'électricité reste encore insuffisant dans plusieurs États membres.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Le Pool Énergétique de l'Afrique Centrale (PEAC) est le mécanisme régional de coopération
                    et d'interconnexion des réseaux électriques. Il vise à mettre en place un marché régional
                    de l'électricité permettant les échanges d'énergie entre pays de la région, l'optimisation
                    de la production et la réduction des coûts.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Des projets d'interconnexion électrique transfrontalière sont en cours, notamment entre
                    le Cameroun et le Tchad, entre le Congo et la RDC, et entre le Gabon et la Guinée Équatoriale.
                    Ces interconnexions permettront le transit d'énergie et la solidarité énergétique régionale.
                </p>
            </div>
            <div class="space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 flex gap-4">
                    <span class="text-3xl">&#9889;</span>
                    <div>
                        <h4 class="font-bold text-blue-950 mb-1">Hydroélectricité</h4>
                        <p class="text-sm text-gray-600">Le potentiel hydroélectrique du bassin du Congo est estimé à 100 000 MW, dont moins de 3% est actuellement exploité.</p>
                    </div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-xl p-5 flex gap-4">
                    <span class="text-3xl">&#9728;</div>
                    <div>
                        <h4 class="font-bold text-blue-950 mb-1">Énergies renouvelables</h4>
                        <p class="text-sm text-gray-600">Développement de l'énergie solaire pour électrifier les zones rurales enclavées où le réseau centralisé n'est pas viable.</p>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 flex gap-4">
                    <span class="text-3xl">&#128268;</span>
                    <div>
                        <h4 class="font-bold text-blue-950 mb-1">Interconnexion régionale</h4>
                        <p class="text-sm text-gray-600">Lignes haute tension transfrontalières pour le commerce d'électricité entre États membres dans le cadre du PEAC.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Télécoms et numérique --}}
<section class="py-16 bg-blue-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold mb-4 text-center">Télécommunications <span class="text-amber-400">&amp; Économie numérique</span></h2>
        <p class="text-blue-100 text-center max-w-2xl mx-auto mb-12">
            La révolution numérique offre à l'Afrique centrale une opportunité sans précédent d'accélérer
            son développement en sautant des étapes de développement traditionnel.
        </p>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-blue-900 rounded-2xl p-7">
                <div class="text-3xl mb-4">&#127760;</div>
                <h3 class="text-xl font-bold text-amber-400 mb-4">Central Africa Backbone (CAB)</h3>
                <p class="text-blue-100 leading-relaxed mb-4">
                    Le projet Central Africa Backbone (CAB) est l'initiative phare de la CEEAC dans le
                    domaine des technologies de l'information et de la communication. Il vise à déployer
                    un réseau de câbles à fibre optique reliant les pays d'Afrique centrale entre eux et
                    aux réseaux mondiaux à haut débit.
                </p>
                <p class="text-blue-100 leading-relaxed">
                    Ce backbone régional permettra de réduire considérablement les coûts d'accès à Internet,
                    d'améliorer la qualité des communications et de créer les conditions d'émergence d'une
                    économie numérique régionale dynamique.
                </p>
            </div>
            <div class="space-y-4">
                <div class="bg-blue-900 rounded-xl p-5">
                    <h4 class="font-semibold text-amber-400 mb-2">Harmonisation règlementaire</h4>
                    <p class="text-blue-200 text-sm">Convergence des cadres réglementaires nationaux du secteur télécom pour faciliter les investissements transfrontaliers et réduire les coûts d'itinérance.</p>
                </div>
                <div class="bg-blue-900 rounded-xl p-5">
                    <h4 class="font-semibold text-amber-400 mb-2">Administration en ligne (e-Government)</h4>
                    <p class="text-blue-200 text-sm">Développement de services numériques transfrontaliers dans les domaines du commerce, de la douane, de la migration et des services aux citoyens.</p>
                </div>
                <div class="bg-blue-900 rounded-xl p-5">
                    <h4 class="font-semibold text-amber-400 mb-2">Formation et capacités numériques</h4>
                    <p class="text-blue-200 text-sm">Programmes de formation aux compétences numériques pour les jeunes et les entrepreneurs de la région, préparant la main-d'oeuvre aux métiers de demain.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Liens --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-2xl font-bold text-blue-950 mb-6">Autres domaines d'action</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('website.domaine', 'paix-securite') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#9876;</span>
                <div><h4 class="font-semibold text-blue-950">Paix &amp; Sécurité</h4><p class="text-sm text-gray-500">COPAX, FOMAC, MARAC</p></div>
            </a>
            <a href="{{ route('website.domaine', 'integration-economique') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#127970;</span>
                <div><h4 class="font-semibold text-blue-950">Intégration régionale</h4><p class="text-sm text-gray-500">Marché commun, libre circulation</p></div>
            </a>
            <a href="{{ route('website.domaine', 'commerce-investissement') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#128181;</span>
                <div><h4 class="font-semibold text-blue-950">Commerce</h4><p class="text-sm text-gray-500">Échanges, investissement</p></div>
            </a>
        </div>
    </div>
</section>

@endsection
