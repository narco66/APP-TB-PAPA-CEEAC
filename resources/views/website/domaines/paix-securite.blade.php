@extends('website.layouts.main')

@section('title', 'Paix et Sécurité — Domaines d\'action CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span class="hover:text-amber-400 transition">Domaines d'action</span>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Paix &amp; Sécurité</span>
        </nav>
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-amber-500 text-white w-14 h-14 rounded-xl flex items-center justify-center text-2xl">&#9876;</div>
            <h1 class="text-4xl md:text-5xl font-bold">Paix &amp; <span class="text-amber-400">Sécurité</span></h1>
        </div>
        <p class="text-xl text-blue-100 max-w-3xl">
            La paix et la sécurité constituent les fondements indispensables de tout développement durable.
            La CEEAC s'est dotée d'un mécanisme régional robuste pour prévenir, gérer et résoudre les conflits
            en Afrique centrale.
        </p>
    </div>
</section>

{{-- COPAX Présentation --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block bg-blue-100 text-blue-800 font-bold text-sm px-4 py-2 rounded-full mb-4 uppercase tracking-wider">Architecture de paix</div>
                <h2 class="text-3xl font-bold text-blue-950 mb-6">Le COPAX — Conseil de Paix et Sécurité de l'Afrique Centrale</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Institué par le Protocole signé le 24 février 2000 à Malabo en Guinée Équatoriale, le
                    Conseil de Paix et Sécurité de l'Afrique Centrale (COPAX) est le principal mécanisme de
                    la CEEAC dédié à la prévention, à la gestion et à la résolution des conflits dans la région.
                    Il constitue l'un des cinq organes régionaux de l'Architecture Africaine de Paix et Sécurité
                    (AAPS) de l'Union Africaine.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Le COPAX repose sur trois instruments complémentaires qui assurent sa capacité opérationnelle :
                    la Commission de Défense et de Sécurité (CDS), le Mécanisme d'Alerte Rapide de l'Afrique
                    Centrale (MARAC) et la Force Multinationale de l'Afrique Centrale (FOMAC).
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Depuis sa création, le COPAX a démontré sa valeur ajoutée dans plusieurs crises régionales,
                    contribuant à la stabilisation de la République Centrafricaine, à la médiation dans les
                    conflits frontaliers et au renforcement des capacités des forces de sécurité nationales.
                </p>
            </div>
            <div class="bg-blue-950 rounded-2xl p-8 text-white">
                <h3 class="text-xl font-bold text-amber-400 mb-6">Les trois piliers du COPAX</h3>
                <div class="space-y-5">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 bg-amber-500 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold">1</div>
                        <div>
                            <h4 class="font-semibold text-amber-300 mb-1">CDS — Commission de Défense et Sécurité</h4>
                            <p class="text-blue-200 text-sm">Réunit les Ministres chargés de la Défense et de la Sécurité. Elle définit les orientations stratégiques du COPAX et supervise les opérations.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 bg-amber-500 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold">2</div>
                        <div>
                            <h4 class="font-semibold text-amber-300 mb-1">MARAC — Mécanisme d'Alerte Rapide</h4>
                            <p class="text-blue-200 text-sm">Système de collecte, d'analyse et de diffusion d'informations sur les facteurs de risques et de tensions. Il permet l'anticipation des crises.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 bg-amber-500 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold">3</div>
                        <div>
                            <h4 class="font-semibold text-amber-300 mb-1">FOMAC — Force Multinationale</h4>
                            <p class="text-blue-200 text-sm">Force militaire régionale de maintien de la paix, composée de contingents des États membres. Elle peut être déployée sur décision du Conseil des Ministres.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- MARAC --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-950 mb-4 text-center">Le <span class="text-amber-500">MARAC</span> — Système d'alerte précoce</h2>
        <p class="text-gray-600 text-center max-w-2xl mx-auto mb-12">
            Le Mécanisme d'Alerte Rapide de l'Afrique Centrale est le dispositif d'anticipation et de
            prévention des crises de la CEEAC.
        </p>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#128269;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Collecte d'information</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Le MARAC dispose d'un réseau de correspondants nationaux et de points focaux dans
                    chacun des États membres. Il collecte en temps réel des données relatives aux facteurs
                    politiques, économiques, sociaux, humanitaires et sécuritaires susceptibles de générer
                    des tensions ou des conflits.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#128202;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Analyse et évaluation</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Sur la base des informations collectées, les analystes du MARAC produisent des rapports
                    périodiques sur la situation sécuritaire dans chaque État membre et dans la région dans
                    son ensemble. Ces analyses permettent d'identifier les signaux précoces de crises potentielles.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#128276;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Alerte et recommandations</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Lorsque le MARAC identifie un risque de conflit ou d'instabilité, il émet une alerte
                    à l'attention de la Commission de la CEEAC et de la CDS. Des recommandations d'action
                    préventive ou de médiation sont formulées pour permettre une réponse rapide et appropriée.
                </p>
            </div>
        </div>
        <div class="mt-8 bg-blue-950 text-white rounded-2xl p-6 flex flex-col md:flex-row gap-4 items-center">
            <div class="text-4xl">&#127760;</div>
            <div>
                <h4 class="font-bold text-amber-400 mb-1">Interconnexion avec l'AAPS</h4>
                <p class="text-blue-100 text-sm leading-relaxed">
                    Le MARAC est pleinement intégré dans le Système Continental d'Alerte Rapide (SCAR) de
                    l'Union Africaine. Il échange des informations avec les autres mécanismes d'alerte régionaux
                    africains (ECOWAS, IGAD, SADC) pour une vision consolidée de la sécurité continentale.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- MICOPAX et opérations --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-950 mb-4">Opérations de maintien de la paix</h2>
        <p class="text-gray-600 mb-10 max-w-3xl">
            La CEEAC a conduit plusieurs opérations de maintien de la paix et de stabilisation, démontrant
            la capacité opérationnelle réelle de son architecture de paix et sécurité.
        </p>

        {{-- MICOPAX --}}
        <div class="bg-gray-50 rounded-2xl p-8 mb-8 border-l-4 border-amber-500">
            <div class="flex flex-col md:flex-row gap-6 items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full">2002 — 2013</span>
                        <h3 class="text-xl font-bold text-blue-950">MICOPAX — Mission de Consolidation de la Paix en Centrafrique</h3>
                    </div>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        La Mission de Consolidation de la Paix en Centrafrique (MICOPAX) est la première grande
                        opération de maintien de la paix conduite sous l'égide de la CEEAC. Déployée en
                        République Centrafricaine à la suite des crises politico-militaires récurrentes, elle
                        a contribué à la stabilisation du pays, à la protection des populations civiles et
                        au soutien aux processus de réconciliation nationale.
                    </p>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        À son apogée, la MICOPAX regroupait plus de 500 soldats provenant de neuf États membres
                        de la CEEAC, dont le Gabon, le Cameroun, le Congo, le Tchad et la Guinée Équatoriale.
                        Cette opération a représenté une démonstration concrète de la solidarité régionale
                        et de la capacité collective de la CEEAC à maintenir la paix sur son territoire.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        En décembre 2013, à la suite de l'évolution de la situation sécuritaire en RCA,
                        la MICOPAX a été transformée en opération de l'Union Africaine (MISCA), puis en
                        opération des Nations Unies (MINUSCA), marquant ainsi la transition vers un engagement
                        international plus large tout en préservant les acquis de l'engagement de la CEEAC.
                    </p>
                </div>
                <div class="md:w-56 flex-shrink-0">
                    <div class="bg-blue-950 text-white rounded-xl p-5 text-center">
                        <div class="text-3xl font-bold text-amber-400">500+</div>
                        <div class="text-sm text-blue-200 mt-1">Soldats déployés</div>
                        <div class="mt-3 text-3xl font-bold text-amber-400">9</div>
                        <div class="text-sm text-blue-200 mt-1">États contributeurs</div>
                        <div class="mt-3 text-3xl font-bold text-amber-400">11 ans</div>
                        <div class="text-sm text-blue-200 mt-1">Durée de la mission</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Réalisations --}}
        <h3 class="text-2xl font-bold text-blue-950 mb-6">Réalisations clés en matière de paix et sécurité</h3>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-amber-400 transition">
                <div class="flex gap-3 mb-3">
                    <span class="text-green-500 text-xl">&#10003;</span>
                    <h4 class="font-bold text-blue-950">Prévention des conflits frontaliers</h4>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    La CEEAC a joué un rôle de médiation dans plusieurs différends frontaliers entre États membres,
                    contribuant à prévenir l'escalade et à trouver des solutions diplomatiques.
                </p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-amber-400 transition">
                <div class="flex gap-3 mb-3">
                    <span class="text-green-500 text-xl">&#10003;</span>
                    <h4 class="font-bold text-blue-950">Renforcement des capacités nationales</h4>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Des programmes de formation des forces de sécurité nationales ont été développés,
                    améliorant la capacité des États membres à assurer eux-mêmes leur sécurité intérieure.
                </p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-amber-400 transition">
                <div class="flex gap-3 mb-3">
                    <span class="text-green-500 text-xl">&#10003;</span>
                    <h4 class="font-bold text-blue-950">Lutte contre le terrorisme et la criminalité</h4>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Des mécanismes de coopération inter-étatique contre le terrorisme, le trafic d'armes,
                    la piraterie maritime et la criminalité transnationale organisée ont été renforcés.
                </p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-amber-400 transition">
                <div class="flex gap-3 mb-3">
                    <span class="text-green-500 text-xl">&#10003;</span>
                    <h4 class="font-bold text-blue-950">Sécurité maritime dans le Golfe de Guinée</h4>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    En coordination avec la CEDEAO et la Commission du Golfe de Guinée, la CEEAC participe
                    aux efforts régionaux de sécurisation des voies maritimes contre la piraterie et les
                    trafics illicites dans le Golfe de Guinée.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Défis actuels --}}
<section class="py-16 bg-blue-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold mb-10 text-center">Défis sécuritaires <span class="text-amber-400">actuels</span></h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-blue-900 rounded-xl p-6">
                <div class="text-3xl mb-3">&#127987;</div>
                <h3 class="font-bold text-amber-400 mb-3">Fragilité en RCA</h3>
                <p class="text-blue-200 text-sm leading-relaxed">
                    La République Centrafricaine continue de faire face à des défis sécuritaires importants.
                    La CEEAC maintient son engagement diplomatique et humanitaire pour soutenir le processus
                    de stabilisation et de réconciliation nationale.
                </p>
            </div>
            <div class="bg-blue-900 rounded-xl p-6">
                <div class="text-3xl mb-3">&#127754;</div>
                <h3 class="font-bold text-amber-400 mb-3">Sécurité maritime</h3>
                <p class="text-blue-200 text-sm leading-relaxed">
                    La piraterie dans le Golfe de Guinée constitue une menace persistante pour les États
                    côtiers membres de la CEEAC. Des efforts concertés de patrouilles et de surveillance
                    maritime sont en cours pour sécuriser les eaux de la région.
                </p>
            </div>
            <div class="bg-blue-900 rounded-xl p-6">
                <div class="text-3xl mb-3">&#128099;</div>
                <h3 class="font-bold text-amber-400 mb-3">Extrémisme et terrorisme</h3>
                <p class="text-blue-200 text-sm leading-relaxed">
                    La menace terroriste au Sahel et dans le bassin du Lac Tchad impacte plusieurs États
                    membres du nord de la CEEAC. Une coopération renforcée en matière de renseignement
                    et d'action militaire conjointe est développée.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Liens vers domaines connexes --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-2xl font-bold text-blue-950 mb-6">Autres domaines d'action</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('website.domaine', 'integration-regionale') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#127970;</span>
                <div>
                    <h4 class="font-semibold text-blue-950">Intégration régionale</h4>
                    <p class="text-sm text-gray-500">Marché commun, libre circulation</p>
                </div>
            </a>
            <a href="{{ route('website.domaine', 'infrastructures') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#128747;</span>
                <div>
                    <h4 class="font-semibold text-blue-950">Infrastructures</h4>
                    <p class="text-sm text-gray-500">Transport, énergie, numérique</p>
                </div>
            </a>
            <a href="{{ route('website.domaine', 'commerce') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
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
