@extends('website.layouts.main')

@section('title', 'Vision, Mission et Valeurs — CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <a href="{{ route('website.a-propos') }}" class="hover:text-amber-400 transition">À propos</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Vision & Mission</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Vision, Mission <span class="text-amber-400">&amp; Valeurs</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            Les principes directeurs qui guident l'action de la Commission de la CEEAC dans la construction
            d'une Afrique centrale intégrée, pacifique et prospère.
        </p>
    </div>
</section>

{{-- Vision --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12 items-center">
            <div class="lg:w-1/2">
                <div class="inline-block bg-amber-100 text-amber-700 font-bold text-sm px-4 py-2 rounded-full mb-4 uppercase tracking-wider">Notre Vision</div>
                <h2 class="text-3xl md:text-4xl font-bold text-blue-950 mb-6 leading-tight">
                    « Une Afrique centrale intégrée, pacifique et prospère »
                </h2>
                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                    La vision de la CEEAC est celle d'une région d'Afrique centrale où les frontières ne sont
                    plus des obstacles mais des ponts de rencontre entre les peuples ; où la paix et la stabilité
                    constituent le socle du développement économique et social ; et où la prospérité est partagée
                    équitablement par l'ensemble des 230 millions d'habitants.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Cette vision s'inscrit dans le cadre plus large de l'Agenda 2063 de l'Union Africaine,
                    intitulé <em>« L'Afrique que nous voulons »</em>, et reflète les aspirations profondes
                    des peuples d'Afrique centrale à vivre dans la dignité, la sécurité et le bien-être.
                    Elle implique une transformation structurelle profonde des économies de la région,
                    une gouvernance démocratique consolidée et une intégration effective des marchés.
                </p>
            </div>
            <div class="lg:w-1/2">
                <div class="bg-blue-950 text-white rounded-3xl p-10 text-center shadow-2xl">
                    <div class="text-6xl mb-6">&#127758;</div>
                    <blockquote class="text-2xl font-semibold text-amber-400 italic leading-relaxed">
                        « Une Afrique centrale intégrée, pacifique et prospère »
                    </blockquote>
                    <p class="text-blue-200 mt-4 text-sm">Vision stratégique de la CEEAC — Horizon 2025–2063</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Mission --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row-reverse gap-12 items-center">
            <div class="lg:w-1/2">
                <div class="inline-block bg-blue-100 text-blue-800 font-bold text-sm px-4 py-2 rounded-full mb-4 uppercase tracking-wider">Notre Mission</div>
                <h2 class="text-3xl md:text-4xl font-bold text-blue-950 mb-6 leading-tight">
                    Promouvoir la coopération harmonieuse et le développement durable
                </h2>
                <div class="bg-white border-l-4 border-amber-400 p-6 rounded-r-xl mb-6 shadow-sm">
                    <p class="text-blue-950 font-medium italic text-lg leading-relaxed">
                        « Promouvoir et renforcer la coopération harmonieuse et le développement dynamique,
                        équilibré et autoentretenu dans tous les domaines de l'activité économique et sociale
                        des États membres. »
                    </p>
                    <p class="text-sm text-gray-500 mt-3">— Article 3 du Traité révisé de la CEEAC</p>
                </div>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Pour accomplir cette mission, la Commission de la CEEAC articule son action autour de
                    plusieurs axes stratégiques interconnectés : l'intégration économique régionale, la paix
                    et la sécurité, le développement des infrastructures, la gouvernance démocratique, le
                    développement humain et l'économie verte.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    La Commission agit en tant que facilitateur entre les États membres, en développant des
                    politiques communes, en harmonisant les législations et en mobilisant des ressources pour
                    des projets d'intérêt régional. Elle joue également un rôle de représentation de la région
                    sur la scène internationale et dans les instances de l'Union Africaine.
                </p>
            </div>
            <div class="lg:w-1/2">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400 text-center">
                        <div class="text-3xl mb-3">&#9876;</div>
                        <h4 class="font-bold text-blue-950 text-sm">Paix &amp; Sécurité</h4>
                        <p class="text-xs text-gray-500 mt-1">Prévention et gestion des conflits</p>
                    </div>
                    <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-950 text-center">
                        <div class="text-3xl mb-3">&#127970;</div>
                        <h4 class="font-bold text-blue-950 text-sm">Intégration économique</h4>
                        <p class="text-xs text-gray-500 mt-1">Marché commun régional</p>
                    </div>
                    <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-950 text-center">
                        <div class="text-3xl mb-3">&#128747;</div>
                        <h4 class="font-bold text-blue-950 text-sm">Infrastructures</h4>
                        <p class="text-xs text-gray-500 mt-1">Connectivité régionale</p>
                    </div>
                    <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400 text-center">
                        <div class="text-3xl mb-3">&#127807;</div>
                        <h4 class="font-bold text-blue-950 text-sm">Développement durable</h4>
                        <p class="text-xs text-gray-500 mt-1">Agenda 2063 &amp; ODD</p>
                    </div>
                    <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400 text-center">
                        <div class="text-3xl mb-3">&#128104;&#8205;&#128105;&#8205;&#128103;</div>
                        <h4 class="font-bold text-blue-950 text-sm">Développement humain</h4>
                        <p class="text-xs text-gray-500 mt-1">Éducation, santé, genre</p>
                    </div>
                    <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-950 text-center">
                        <div class="text-3xl mb-3">&#9878;</div>
                        <h4 class="font-bold text-blue-950 text-sm">Bonne gouvernance</h4>
                        <p class="text-xs text-gray-500 mt-1">Démocratie &amp; État de droit</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Valeurs --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-block bg-amber-100 text-amber-700 font-bold text-sm px-4 py-2 rounded-full mb-4 uppercase tracking-wider">Nos Valeurs</div>
            <h2 class="text-3xl md:text-4xl font-bold text-blue-950">
                Les principes qui guident notre <span class="text-amber-500">action</span>
            </h2>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                Ces valeurs fondamentales orientent les décisions et les actions de l'ensemble des
                institutions et organes de la CEEAC.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            {{-- Solidarité --}}
            <div class="bg-gradient-to-br from-blue-950 to-blue-900 text-white rounded-2xl p-8 shadow-lg">
                <div class="text-4xl mb-4">&#129309;</div>
                <h3 class="text-xl font-bold text-amber-400 mb-3">Solidarité</h3>
                <p class="text-blue-100 leading-relaxed">
                    La CEEAC est fondée sur la conviction que les États membres progressent mieux ensemble
                    que séparément. La solidarité régionale signifie que les États plus avancés soutiennent
                    ceux qui font face à des défis particuliers, et que les ressources communes sont mobilisées
                    au bénéfice de l'ensemble de la Communauté.
                </p>
            </div>

            {{-- Transparence --}}
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-2xl p-8 shadow-lg">
                <div class="text-4xl mb-4">&#128065;</div>
                <h3 class="text-xl font-bold mb-3">Transparence</h3>
                <p class="text-amber-50 leading-relaxed">
                    L'exercice de l'autorité communautaire se fait dans le respect des exigences de transparence,
                    de redevabilité et de responsabilité. La Commission s'engage à rendre compte de sa gestion
                    aux États membres et aux citoyens de la Communauté, à travers des mécanismes de contrôle
                    efficaces et une communication ouverte.
                </p>
            </div>

            {{-- Excellence --}}
            <div class="bg-gradient-to-br from-blue-950 to-blue-900 text-white rounded-2xl p-8 shadow-lg">
                <div class="text-4xl mb-4">&#11088;</div>
                <h3 class="text-xl font-bold text-amber-400 mb-3">Excellence</h3>
                <p class="text-blue-100 leading-relaxed">
                    La Commission de la CEEAC s'engage à délivrer des prestations et des services de haute
                    qualité, à recruter et retenir les meilleurs talents africains, et à adopter les meilleures
                    pratiques en matière de gouvernance organisationnelle. L'excellence est une culture
                    institutionnelle et un engagement permanent envers les États membres.
                </p>
            </div>

            {{-- Intégration --}}
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-2xl p-8 shadow-lg">
                <div class="text-4xl mb-4">&#128279;</div>
                <h3 class="text-xl font-bold mb-3">Intégration</h3>
                <p class="text-amber-50 leading-relaxed">
                    L'intégration régionale est à la fois une valeur, un objectif et une méthode de travail
                    pour la CEEAC. Elle se traduit par la volonté de créer des espaces économiques et politiques
                    cohérents, de supprimer les obstacles aux échanges et à la mobilité des personnes, et de
                    construire une identité régionale commune ancrée dans la diversité culturelle africaine.
                </p>
            </div>

            {{-- Subsidiarité --}}
            <div class="bg-gradient-to-br from-blue-950 to-blue-900 text-white rounded-2xl p-8 shadow-lg">
                <div class="text-4xl mb-4">&#9878;</div>
                <h3 class="text-xl font-bold text-amber-400 mb-3">Subsidiarité</h3>
                <p class="text-blue-100 leading-relaxed">
                    La CEEAC agit conformément au principe de subsidiarité, selon lequel les décisions
                    doivent être prises au niveau le plus approprié et le plus proche des citoyens concernés.
                    La Commission n'intervient que lorsque les objectifs de l'action envisagée peuvent être
                    mieux réalisés au niveau communautaire qu'au niveau national.
                </p>
            </div>

            {{-- Droits humains --}}
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-2xl p-8 shadow-lg">
                <div class="text-4xl mb-4">&#9878;&#65039;</div>
                <h3 class="text-xl font-bold mb-3">Respect des droits humains</h3>
                <p class="text-amber-50 leading-relaxed">
                    La promotion et la protection des droits humains, de la dignité de la personne et de
                    l'égalité de genre constituent des impératifs non négociables dans l'action de la CEEAC.
                    Toutes les politiques et programmes communautaires sont conçus et évalués à l'aune
                    de leur contribution à la réalisation des droits fondamentaux des populations.
                </p>
            </div>

        </div>
    </div>
</section>

{{-- Objectifs stratégiques --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-950 mb-8 text-center">Objectifs stratégiques <span class="text-amber-500">2025–2030</span></h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm flex gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-950 text-amber-400 rounded-xl flex items-center justify-center font-bold text-xl">1</div>
                <div>
                    <h4 class="font-bold text-blue-950 mb-2">Accélérer l'intégration économique</h4>
                    <p class="text-gray-600 text-sm">Réaliser le marché commun, harmoniser les politiques fiscales et commerciales, et promouvoir l'industrialisation régionale.</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm flex gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-950 text-amber-400 rounded-xl flex items-center justify-center font-bold text-xl">2</div>
                <div>
                    <h4 class="font-bold text-blue-950 mb-2">Consolider la paix et la sécurité</h4>
                    <p class="text-gray-600 text-sm">Renforcer le COPAX, prévenir les conflits et soutenir les processus de réconciliation nationale dans les États en crise.</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm flex gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-amber-500 text-white rounded-xl flex items-center justify-center font-bold text-xl">3</div>
                <div>
                    <h4 class="font-bold text-blue-950 mb-2">Développer les infrastructures régionales</h4>
                    <p class="text-gray-600 text-sm">Financer et réaliser les projets prioritaires du PIDE dans les transports, l'énergie et les télécommunications.</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm flex gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-amber-500 text-white rounded-xl flex items-center justify-center font-bold text-xl">4</div>
                <div>
                    <h4 class="font-bold text-blue-950 mb-2">Promouvoir le développement humain</h4>
                    <p class="text-gray-600 text-sm">Améliorer les indicateurs de santé, d'éducation et d'égalité des genres dans l'ensemble des États membres.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Navigation --}}
<section class="py-10 bg-white border-t border-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ route('website.historique') }}" class="text-blue-950 hover:text-amber-600 font-medium flex items-center gap-2 transition">
            &larr; Notre histoire
        </a>
        <a href="{{ route('website.organes') }}" class="bg-blue-950 text-white hover:bg-blue-900 font-medium flex items-center gap-2 px-5 py-2 rounded-lg transition">
            Organes institutionnels &rarr;
        </a>
    </div>
</section>

@endsection
