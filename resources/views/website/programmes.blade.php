@extends('website.layouts.main')

@section('title', 'Programmes & Projets phares — CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Programmes</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Programmes <span class="text-amber-400">phares</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            La Commission de la CEEAC met en oeuvre six programmes intégrés qui constituent le coeur
            opérationnel de son action pour la réalisation de l'intégration régionale en Afrique centrale.
        </p>
    </div>
</section>

{{-- Intro --}}
<section class="py-10 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-700 text-lg leading-relaxed">
            Les programmes phares de la CEEAC sont des cadres d'action multidimensionnels, articulant
            des projets concrets, des réformes institutionnelles et des activités de renforcement des
            capacités dans les domaines prioritaires de l'intégration régionale. Ils mobilisent des
            financements nationaux, régionaux et internationaux pour un impact maximal au bénéfice des
            populations d'Afrique centrale.
        </p>
    </div>
</section>

{{-- Programme 1 : PAPS --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-8">
            <div class="flex flex-col lg:flex-row">
                <div class="bg-blue-950 text-white lg:w-64 flex-shrink-0 flex flex-col items-center justify-center p-8 text-center">
                    <div class="text-6xl mb-4">&#9876;</div>
                    <div class="text-2xl font-black text-amber-400 mb-1">PAPS</div>
                    <div class="text-blue-200 text-xs uppercase tracking-widest">Programme d'Appui à la Paix et à la Sécurité</div>
                </div>
                <div class="p-8 flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">Paix &amp; Sécurité</span>
                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">En cours — 2022–2027</span>
                        <span class="bg-amber-100 text-amber-700 text-xs px-3 py-1 rounded-full">&#128181; Financement UE/CEEAC</span>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-950 mb-4">Programme d'Appui à la Paix et à la Sécurité (PAPS)</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Le Programme d'Appui à la Paix et à la Sécurité (PAPS) est le programme phare de la CEEAC
                        dans le domaine de la paix et de la sécurité. Il vise à renforcer les capacités opérationnelles
                        du COPAX — le mécanisme régional de paix et sécurité — à travers le renforcement institutionnel
                        du MARAC, la professionnalisation de la FOMAC et le soutien aux processus de médiation et de
                        réconciliation dans les États membres en situation de fragilité.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Le PAPS comprend également un volet de prévention de la violence, de lutte contre la prolifération
                        des armes légères et de petit calibre (ALPC), et d'appui aux processus électoraux pour garantir
                        des transitions politiques pacifiques dans la région.
                    </p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">11</div>
                            <div class="text-xs text-gray-500">États bénéficiaires</div>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">5 ans</div>
                            <div class="text-xs text-gray-500">Durée du programme</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">UE / CEEAC</div>
                            <div class="text-xs text-gray-500">Sources de financement</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Programme 2 : PIDE --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-8">
            <div class="flex flex-col lg:flex-row">
                <div class="bg-amber-600 text-white lg:w-64 flex-shrink-0 flex flex-col items-center justify-center p-8 text-center">
                    <div class="text-6xl mb-4">&#128747;</div>
                    <div class="text-2xl font-black mb-1">PIDE</div>
                    <div class="text-amber-100 text-xs uppercase tracking-widest">Programme Indicatif de Développement</div>
                </div>
                <div class="p-8 flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">Infrastructures</span>
                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">En cours — 2019–2030</span>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-950 mb-4">Programme Indicatif de Développement de la Communauté (PIDE)</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Le Programme Indicatif de Développement de la Communauté (PIDE) est le cadre stratégique
                        de planification et de coordination des investissements en infrastructures régionales.
                        Il identifie et priorise les projets à impact transfrontalier dans les secteurs des
                        transports (routes, rail, voies navigables, aéroports), de l'énergie (interconnexion
                        électrique, énergies renouvelables), des télécommunications (fibre optique, CAB) et
                        de l'eau et assainissement.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Le PIDE est mis en oeuvre en partenariat avec la BDEAC, la BAD, la Banque Mondiale,
                        la Banque Islamique de Développement et d'autres bailleurs de fonds bilatéraux et
                        multilatéraux. Son portefeuille de projets représente un investissement total estimé
                        à plusieurs dizaines de milliards de dollars américains sur sa période d'exécution.
                    </p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-amber-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">4 secteurs</div>
                            <div class="text-xs text-gray-500">Transports, énergie, TIC, eau</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">2030</div>
                            <div class="text-xs text-gray-500">Horizon de mise en oeuvre</div>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">Multi-bailleurs</div>
                            <div class="text-xs text-gray-500">BDEAC, BAD, BM, BID…</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Programme 3 : PACE --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-8">
            <div class="flex flex-col lg:flex-row">
                <div class="bg-blue-800 text-white lg:w-64 flex-shrink-0 flex flex-col items-center justify-center p-8 text-center">
                    <div class="text-6xl mb-4">&#127970;</div>
                    <div class="text-2xl font-black text-amber-400 mb-1">PACE</div>
                    <div class="text-blue-200 text-xs uppercase tracking-widest">Programme d'Appui à la Commerce et à l'Économie</div>
                </div>
                <div class="p-8 flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Commerce &amp; Économie</span>
                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">En cours — 2023–2027</span>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-950 mb-4">Programme d'Appui au Commerce et à l'Économie (PACE)</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Le Programme d'Appui au Commerce et à l'Économie (PACE) vise à stimuler le commerce intra-régional
                        et à renforcer la compétitivité des économies des États membres de la CEEAC. Il comprend des
                        composantes relatives à la réduction des barrières tarifaires et non tarifaires, à la facilitation
                        des échanges commerciaux, au développement du secteur privé régional et à l'harmonisation des
                        politiques commerciales avec les standards internationaux.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Le PACE soutient également la mise en oeuvre des engagements de la CEEAC dans le cadre de la
                        Zone de Libre-Échange Continentale Africaine (ZLECAf) et promeut le développement de chaînes
                        de valeur régionales dans les secteurs de l'agro-industrie, de la transformation des ressources
                        naturelles et des services numériques.
                    </p>
                </div>
            </div>
        </div>

        {{-- Programme 4 : PRODOC --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-8">
            <div class="flex flex-col lg:flex-row">
                <div class="bg-green-800 text-white lg:w-64 flex-shrink-0 flex flex-col items-center justify-center p-8 text-center">
                    <div class="text-6xl mb-4">&#127807;</div>
                    <div class="text-2xl font-black text-amber-400 mb-1">PRODOC</div>
                    <div class="text-green-200 text-xs uppercase tracking-widest">Programme de Développement et d'Observation du Congo</div>
                </div>
                <div class="p-8 flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="bg-teal-100 text-teal-700 text-xs font-bold px-3 py-1 rounded-full">Environnement</span>
                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">En cours — 2021–2030</span>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-950 mb-4">Programme de Développement et d'Observation du Bassin du Congo (PRODOC)</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Le PRODOC est le programme de la CEEAC dédié à la préservation et à la valorisation durable
                        des ressources du Bassin du Congo, deuxième masse forestière tropicale du monde après l'Amazonie.
                        Il vise à concilier la conservation de la biodiversité exceptionnelle de la région avec les
                        impératifs de développement économique et de réduction de la pauvreté des populations locales.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Le PRODOC comprend des projets de reforestation, de gestion communautaire des forêts,
                        d'agriculture climato-intelligente, de surveillance satellitaire de la déforestation et
                        de valorisation du carbone forestier dans le cadre des mécanismes internationaux de
                        financement climatique (REDD+).
                    </p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-green-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-green-800 text-lg">3,3 M km²</div>
                            <div class="text-xs text-gray-500">Superficie du Bassin</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">100 000+</div>
                            <div class="text-xs text-gray-500">Espèces végétales</div>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">REDD+</div>
                            <div class="text-xs text-gray-500">Financement climatique</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Programme 5 : PAPED --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-8">
            <div class="flex flex-col lg:flex-row">
                <div class="bg-purple-800 text-white lg:w-64 flex-shrink-0 flex flex-col items-center justify-center p-8 text-center">
                    <div class="text-6xl mb-4">&#128104;&#8205;&#128105;&#8205;&#128103;</div>
                    <div class="text-2xl font-black text-amber-400 mb-1">PAPED</div>
                    <div class="text-purple-200 text-xs uppercase tracking-widest">Programme d'Appui au Développement Humain</div>
                </div>
                <div class="p-8 flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="bg-pink-100 text-pink-700 text-xs font-bold px-3 py-1 rounded-full">Développement humain</span>
                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">En cours — 2022–2027</span>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-950 mb-4">Programme d'Appui aux Politiques et à l'Épanouissement Démocratique (PAPED)</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Le PAPED est le programme de la CEEAC dédié au renforcement de la gouvernance démocratique,
                        à la promotion des droits humains et à l'amélioration des conditions de vie des populations.
                        Il comprend des composantes relatives à l'appui aux processus électoraux, au renforcement
                        des parlements nationaux et du Parlement communautaire, à la promotion de l'égalité des
                        genres et à la protection des droits des personnes vulnérables.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Le volet développement humain du PAPED couvre l'éducation, la santé, la nutrition, l'emploi
                        des jeunes et les filets de protection sociale. Il cherche à garantir que les bénéfices de
                        l'intégration économique se traduisent en amélioration concrète et mesurable du niveau de vie
                        des populations de la région.
                    </p>
                </div>
            </div>
        </div>

        {{-- Programme 6 : PEAC --}}
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <div class="bg-yellow-700 text-white lg:w-64 flex-shrink-0 flex flex-col items-center justify-center p-8 text-center">
                    <div class="text-6xl mb-4">&#9889;</div>
                    <div class="text-2xl font-black text-white mb-1">PEAC</div>
                    <div class="text-yellow-200 text-xs uppercase tracking-widest">Pool Énergétique de l'Afrique Centrale</div>
                </div>
                <div class="p-8 flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">Énergie</span>
                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">En cours — 2018–2030</span>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-950 mb-4">Pool Énergétique de l'Afrique Centrale (PEAC)</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Le Pool Énergétique de l'Afrique Centrale (PEAC) est le mécanisme régional de coopération
                        en matière d'énergie électrique. Il vise à créer un marché régional de l'électricité interconnecté,
                        permettant les échanges d'énergie entre les États membres, l'optimisation de la production
                        hydroélectrique et l'extension de l'accès à l'électricité aux populations rurales.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Le PEAC coordonne le développement de grandes centrales hydroélectriques à vocation régionale
                        (dont le projet Grand Inga en RDC), les lignes d'interconnexion transfrontalières haute tension,
                        et les programmes d'électrification rurale par énergies renouvelables. Il s'inscrit dans la
                        vision d'une Afrique centrale dotée d'une énergie propre, abordable et universellement accessible.
                    </p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-yellow-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-yellow-800 text-lg">100 000 MW</div>
                            <div class="text-xs text-gray-500">Potentiel hydroélectrique</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">&lt;3%</div>
                            <div class="text-xs text-gray-500">Taux d'exploitation actuel</div>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4 text-center">
                            <div class="font-bold text-blue-950 text-lg">Grand Inga</div>
                            <div class="text-xs text-gray-500">Projet phare continental</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- Liens vers publications --}}
<section class="py-12 bg-blue-950 text-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-2xl font-bold mb-4">Documentation des programmes</h3>
        <p class="text-blue-100 mb-8">Téléchargez les documents de programme, rapports de mise en oeuvre et évaluations dans notre centre de publications.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('website.publications') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-3 rounded-lg transition">
                Accéder aux publications
            </a>
            <a href="{{ route('website.contact') }}" class="border border-white text-white hover:bg-white hover:text-blue-950 font-semibold px-6 py-3 rounded-lg transition">
                Nous contacter
            </a>
        </div>
    </div>
</section>

@endsection
