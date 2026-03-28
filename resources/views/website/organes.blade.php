@extends('website.layouts.main')

@section('title', 'Organes institutionnels — CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <a href="{{ route('website.a-propos') }}" class="hover:text-amber-400 transition">À propos</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Organes institutionnels</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Organes <span class="text-amber-400">institutionnels</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            L'architecture institutionnelle de la CEEAC, conçue pour assurer une gouvernance communautaire
            efficace, démocratique et représentative de l'ensemble des États membres.
        </p>
    </div>
</section>

{{-- Intro --}}
<section class="py-10 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-700 text-lg leading-relaxed">
            Le Traité révisé de la CEEAC institue plusieurs organes principaux et institutions spécialisées
            qui forment ensemble le système institutionnel de la Communauté. Chaque organe joue un rôle
            spécifique et complémentaire dans la mise en oeuvre des politiques d'intégration régionale.
        </p>
    </div>
</section>

{{-- Organe 1 : Conférence des Chefs d'État --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-10">
            <div class="bg-blue-950 text-white px-8 py-5 flex items-center gap-4">
                <span class="bg-amber-400 text-blue-950 font-bold w-10 h-10 rounded-full flex items-center justify-center text-lg">1</span>
                <div>
                    <h2 class="text-xl font-bold">Conférence des Chefs d'État et de Gouvernement</h2>
                    <p class="text-blue-200 text-sm">Organe suprême de la CEEAC</p>
                </div>
            </div>
            <div class="p-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="md:col-span-2">
                        <h3 class="font-bold text-blue-950 mb-3 text-lg">Description et rôle</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            La Conférence des Chefs d'État et de Gouvernement constitue l'organe suprême de la
                            CEEAC. Elle réunit les dirigeants de l'ensemble des onze États membres et détient le
                            pouvoir de décision le plus élevé au sein de la Communauté. C'est elle qui définit
                            les grandes orientations politiques de l'organisation et trace les lignes directrices
                            de l'intégration régionale.
                        </p>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            La Conférence élit le Président de la Commission pour un mandat de quatre ans,
                            renouvelable une fois. Elle adopte les textes fondamentaux de la Communauté,
                            notamment les révisions du Traité, les protocoles additionnels et les actes
                            complémentaires. Ses décisions s'imposent aux organes subsidiaires et aux États membres.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            La Conférence peut également se réunir en session extraordinaire sur convocation du
                            Président de la Conférence, ou à la demande d'un État membre approuvée par la majorité
                            des États membres, pour traiter de questions urgentes ou exceptionnelles.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Composition</h4>
                            <p class="text-gray-600 text-sm">11 Chefs d'État et de Gouvernement des États membres</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Fréquence de réunion</h4>
                            <p class="text-gray-600 text-sm">Session ordinaire annuelle + sessions extraordinaires si nécessaire</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Présidence</h4>
                            <p class="text-gray-600 text-sm">Tournante entre États membres selon l'ordre alphabétique, mandat d'un an</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Mode de décision</h4>
                            <p class="text-gray-600 text-sm">Consensus ou vote à la majorité des deux tiers selon les matières</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Organe 2 : Conseil des Ministres --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-10">
            <div class="bg-blue-900 text-white px-8 py-5 flex items-center gap-4">
                <span class="bg-amber-400 text-blue-950 font-bold w-10 h-10 rounded-full flex items-center justify-center text-lg">2</span>
                <div>
                    <h2 class="text-xl font-bold">Conseil des Ministres</h2>
                    <p class="text-blue-200 text-sm">Organe de coordination et de contrôle</p>
                </div>
            </div>
            <div class="p-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="md:col-span-2">
                        <h3 class="font-bold text-blue-950 mb-3 text-lg">Description et rôle</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Le Conseil des Ministres est l'organe de coordination des politiques économiques,
                            sociales et sectorielles des États membres. Il est composé des ministres en charge
                            des questions d'intégration régionale ou des affaires étrangères de chacun des États
                            membres. Le Conseil des Ministres prépare les travaux de la Conférence des Chefs d'État
                            et assure le suivi de la mise en oeuvre de ses décisions.
                        </p>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Il a compétence pour adopter les règlements, directives et recommandations nécessaires
                            à la réalisation des objectifs fixés par le Traité et les décisions de la Conférence.
                            Le Conseil des Ministres peut déléguer certaines de ses attributions à la Commission,
                            dans des conditions et limites qu'il définit.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            Il examine et adopte également le budget de la Communauté, les programmes d'action
                            et les rapports annuels de la Commission, avant leur transmission à la Conférence
                            des Chefs d'État et de Gouvernement pour approbation finale.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Composition</h4>
                            <p class="text-gray-600 text-sm">Ministres représentant chacun des 11 États membres</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Fréquence de réunion</h4>
                            <p class="text-gray-600 text-sm">Deux sessions ordinaires par an (avant chaque Sommet et en milieu d'année)</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Attributions principales</h4>
                            <p class="text-gray-600 text-sm">Adoption d'actes communautaires, approbation du budget, supervision de la Commission</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Organe 3 : Commission --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-10">
            <div class="bg-amber-600 text-white px-8 py-5 flex items-center gap-4">
                <span class="bg-blue-950 text-amber-400 font-bold w-10 h-10 rounded-full flex items-center justify-center text-lg">3</span>
                <div>
                    <h2 class="text-xl font-bold">Commission de la CEEAC</h2>
                    <p class="text-amber-100 text-sm">Organe exécutif et administration permanente</p>
                </div>
            </div>
            <div class="p-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="md:col-span-2">
                        <h3 class="font-bold text-blue-950 mb-3 text-lg">Description et rôle</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            La Commission est l'organe exécutif permanent de la CEEAC. Instituée par le Traité
                            révisé de 2021 en remplacement du Secrétariat Général, elle représente l'ensemble
                            des intérêts de la Communauté. Dirigée par un Président élu par la Conférence,
                            assisté de huit Commissaires en charge des différents départements thématiques,
                            la Commission siège en permanence à Libreville.
                        </p>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            La Commission jouit d'une personnalité juridique internationale et agit en toute
                            indépendance dans l'exercice de ses attributions. Elle dispose du droit d'initiative
                            en matière législative communautaire et soumet au Conseil des Ministres et à la
                            Conférence les projets d'actes nécessaires à la réalisation des objectifs de la
                            Communauté.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            Les huit Commissaires se répartissent les portefeuilles suivants : Intégration
                            économique et commerce ; Paix, sécurité et gouvernance ; Infrastructures et énergie ;
                            Développement humain et affaires sociales ; Ressources naturelles et environnement ;
                            Agriculture et développement rural ; Sciences, technologies et innovation ;
                            Administration, finances et budget.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-amber-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Direction</h4>
                            <p class="text-gray-600 text-sm">Président + 8 Commissaires (mandat 4 ans renouvelable une fois)</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Siège</h4>
                            <p class="text-gray-600 text-sm">Libreville, Gabon — Boulevard de l'Indépendance, BP 2112</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Personnel</h4>
                            <p class="text-gray-600 text-sm">Fonctionnaires communautaires recrutés sur toute l'étendue des États membres</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COPAX --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-10">
            <div class="bg-blue-800 text-white px-8 py-5 flex items-center gap-4">
                <span class="bg-amber-400 text-blue-950 font-bold w-10 h-10 rounded-full flex items-center justify-center text-lg">4</span>
                <div>
                    <h2 class="text-xl font-bold">Conseil de Paix et Sécurité de l'Afrique Centrale (COPAX)</h2>
                    <p class="text-blue-200 text-sm">Pilier paix et sécurité de la CEEAC</p>
                </div>
            </div>
            <div class="p-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="md:col-span-2">
                        <h3 class="font-bold text-blue-950 mb-3 text-lg">Description et rôle</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Le COPAX, créé par le Protocole du 24 février 2000, est le mécanisme de la CEEAC
                            dédié à la prévention, à la gestion et à la résolution des conflits en Afrique centrale.
                            Il constitue l'un des piliers de l'Architecture Africaine de Paix et de Sécurité (AAPS)
                            définie par l'Union Africaine. Son action repose sur trois instruments complémentaires :
                            la Commission de Défense et de Sécurité (CDS), le Mécanisme d'Alerte Rapide de
                            l'Afrique Centrale (MARAC) et la Force Multinationale de l'Afrique Centrale (FOMAC).
                        </p>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Le MARAC collecte et analyse les informations relatives aux facteurs de risque de
                            conflits dans la région, permettant une alerte précoce et une réponse rapide aux
                            crises émergentes. La FOMAC est la force régionale de maintien de la paix, qui peut
                            être déployée sur décision du Conseil des Ministres de la CEEAC.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            Le COPAX a déjà conduit avec succès plusieurs missions de maintien de la paix,
                            notamment la MICOPAX en République Centrafricaine (2002–2013), démontrant ainsi
                            la capacité opérationnelle de la CEEAC dans le domaine de la paix et la sécurité.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Instruments</h4>
                            <p class="text-gray-600 text-sm">CDS + MARAC + FOMAC</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Création</h4>
                            <p class="text-gray-600 text-sm">Protocole du 24 février 2000, Malabo</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Réunions CDS</h4>
                            <p class="text-gray-600 text-sm">Réunions périodiques des Ministres de la Défense et de la Sécurité</p>
                        </div>
                        <a href="{{ route('website.domaine', 'paix-securite') }}" class="block bg-blue-950 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-900 transition text-sm font-medium">
                            En savoir plus sur le COPAX
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cour de Justice --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-10">
            <div class="bg-blue-950 text-white px-8 py-5 flex items-center gap-4">
                <span class="bg-amber-400 text-blue-950 font-bold w-10 h-10 rounded-full flex items-center justify-center text-lg">5</span>
                <div>
                    <h2 class="text-xl font-bold">Cour de Justice Communautaire</h2>
                    <p class="text-blue-200 text-sm">Organe judiciaire de la CEEAC</p>
                </div>
            </div>
            <div class="p-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="md:col-span-2">
                        <h3 class="font-bold text-blue-950 mb-3 text-lg">Description et rôle</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            La Cour de Justice Communautaire est l'organe judiciaire de la CEEAC. Elle est
                            compétente pour interpréter le Traité de la CEEAC et les actes pris par les organes
                            communautaires, pour statuer sur les litiges entre États membres ou entre États membres
                            et la Commission relatifs à l'application du droit communautaire, et pour garantir
                            le respect des engagements pris par les États membres.
                        </p>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            La Cour peut également être saisie par des personnes physiques ou morales pour
                            contester les actes communautaires qui leur font grief ou pour faire valoir leurs
                            droits dans le cadre du droit communautaire. Son rôle est fondamental pour assurer
                            la sécurité juridique et la cohérence du droit de la Communauté.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Composition</h4>
                            <p class="text-gray-600 text-sm">Juges nommés par la Conférence, représentant les États membres</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Compétences</h4>
                            <p class="text-gray-600 text-sm">Interprétation du droit communautaire, litiges inter-étatiques et recours individuels</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Parlement --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-10">
            <div class="bg-blue-900 text-white px-8 py-5 flex items-center gap-4">
                <span class="bg-amber-400 text-blue-950 font-bold w-10 h-10 rounded-full flex items-center justify-center text-lg">6</span>
                <div>
                    <h2 class="text-xl font-bold">Parlement Communautaire</h2>
                    <p class="text-blue-200 text-sm">Organe délibérant de la CEEAC</p>
                </div>
            </div>
            <div class="p-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="md:col-span-2">
                        <h3 class="font-bold text-blue-950 mb-3 text-lg">Description et rôle</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Le Parlement Communautaire de la CEEAC est l'organe représentatif des peuples
                            des États membres. Il exerce une fonction consultative et délibérative dans les
                            domaines relevant des politiques d'intégration régionale. Composé de parlementaires
                            désignés par les parlements nationaux des États membres, il contribue à renforcer
                            la dimension démocratique et représentative de la Communauté.
                        </p>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Le Parlement Communautaire émet des avis sur les projets d'actes communautaires
                            qui lui sont soumis, organise des débats sur les grandes questions de l'intégration
                            régionale et peut formuler des recommandations à l'attention de la Conférence,
                            du Conseil des Ministres et de la Commission.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Composition</h4>
                            <p class="text-gray-600 text-sm">Parlementaires désignés par les 11 parlements nationaux</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Fréquence</h4>
                            <p class="text-gray-600 text-sm">Sessions ordinaires semestrielles + sessions extraordinaires</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-950 text-sm mb-1">Siège</h4>
                            <p class="text-gray-600 text-sm">Malabo, Guinée Équatoriale</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Institutions spécialisées --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-amber-600 text-white px-8 py-5 flex items-center gap-4">
                <span class="bg-blue-950 text-amber-400 font-bold w-10 h-10 rounded-full flex items-center justify-center text-lg">7</span>
                <div>
                    <h2 class="text-xl font-bold">Institutions spécialisées</h2>
                    <p class="text-amber-100 text-sm">Organismes techniques communautaires</p>
                </div>
            </div>
            <div class="p-8">
                <p class="text-gray-700 leading-relaxed mb-6">
                    La CEEAC dispose également d'un réseau d'institutions spécialisées opérant dans des
                    domaines sectoriels précis et contribuant à la réalisation des objectifs communautaires
                    dans leurs domaines de compétence respectifs.
                </p>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-amber-400">
                        <h4 class="font-bold text-blue-950 mb-2">BDEAC</h4>
                        <p class="text-sm text-gray-600">Banque de Développement des États de l'Afrique Centrale — financement du développement régional</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-blue-950">
                        <h4 class="font-bold text-blue-950 mb-2">CEMAC</h4>
                        <p class="text-sm text-gray-600">Communauté Économique et Monétaire de l'Afrique Centrale — union monétaire et douanière</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-amber-400">
                        <h4 class="font-bold text-blue-950 mb-2">ECCAS Youth Council</h4>
                        <p class="text-sm text-gray-600">Conseil de la Jeunesse de la CEEAC — représentation et promotion de la jeunesse régionale</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-blue-950">
                        <h4 class="font-bold text-blue-950 mb-2">CICOS</h4>
                        <p class="text-sm text-gray-600">Commission Internationale du Bassin Congo-Oubangui-Sangha — gestion des ressources en eau</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-amber-400">
                        <h4 class="font-bold text-blue-950 mb-2">COMIFAC</h4>
                        <p class="text-sm text-gray-600">Commission des Forêts d'Afrique Centrale — conservation du Bassin du Congo</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5 border-l-4 border-blue-950">
                        <h4 class="font-bold text-blue-950 mb-2">CAPC-AC</h4>
                        <p class="text-sm text-gray-600">Centre Africain de Prévention et Contrôle des Maladies — santé publique régionale</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- Navigation --}}
<section class="py-10 bg-white border-t border-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ route('website.vision-mission') }}" class="text-blue-950 hover:text-amber-600 font-medium flex items-center gap-2 transition">
            &larr; Vision &amp; Mission
        </a>
        <a href="{{ route('website.president') }}" class="bg-blue-950 text-white hover:bg-blue-900 font-medium flex items-center gap-2 px-5 py-2 rounded-lg transition">
            Mot du Président &rarr;
        </a>
    </div>
</section>

@endsection
