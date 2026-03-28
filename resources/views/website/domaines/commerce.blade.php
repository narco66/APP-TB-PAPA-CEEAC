@extends('website.layouts.main')

@section('title', 'Commerce et Marché Commun — Domaines d\'action CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span>Domaines d'action</span>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Commerce</span>
        </nav>
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-amber-500 text-white w-14 h-14 rounded-xl flex items-center justify-center text-2xl">&#128181;</div>
            <h1 class="text-4xl md:text-5xl font-bold">Commerce <span class="text-amber-400">&amp; Marché Commun</span></h1>
        </div>
        <p class="text-xl text-blue-100 max-w-3xl">
            Le développement du commerce intra-régional et la création d'un marché commun en Afrique
            centrale constituent des priorités stratégiques de la CEEAC pour stimuler la croissance
            économique et créer des emplois durables.
        </p>
    </div>
</section>

{{-- Contexte --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12">
            <div>
                <h2 class="text-3xl font-bold text-blue-950 mb-6">Le commerce intra-régional : un <span class="text-amber-500">potentiel immense à exploiter</span></h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Malgré la richesse extraordinaire de ses ressources naturelles et la taille de son marché,
                    l'Afrique centrale reste l'une des régions du monde où les échanges commerciaux intra-régionaux
                    sont les plus faibles, représentant moins de 5% du commerce total de la région. Cette réalité
                    contraste fortement avec le potentiel de la région et constitue un défi majeur que la CEEAC
                    s'emploie à surmonter.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Les obstacles au commerce intra-régional sont multiples : insuffisance des infrastructures
                    de transport et de logistique, multiplicité des barrières tarifaires et non tarifaires,
                    coûts élevés des transactions financières transfrontalières, manque d'harmonisation des
                    normes et des réglementations, et faiblesse du secteur privé régional.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    La stratégie de la CEEAC pour développer le commerce intra-régional s'articule autour de
                    la suppression progressive des obstacles aux échanges, de la facilitation des procédures
                    douanières, de l'harmonisation des politiques commerciales nationales et du renforcement
                    du secteur privé régional.
                </p>
            </div>
            <div class="space-y-4">
                <div class="bg-red-50 border border-red-100 rounded-xl p-5">
                    <h3 class="font-bold text-red-800 mb-2">Situation actuelle</h3>
                    <div class="flex items-center gap-4 mb-2">
                        <div class="text-3xl font-bold text-red-600">&lt;5%</div>
                        <p class="text-gray-600 text-sm">du commerce total est intra-CEEAC — l'un des taux les plus faibles au monde</p>
                    </div>
                </div>
                <div class="bg-green-50 border border-green-100 rounded-xl p-5">
                    <h3 class="font-bold text-green-800 mb-2">Objectif 2030</h3>
                    <div class="flex items-center gap-4">
                        <div class="text-3xl font-bold text-green-600">25%</div>
                        <p class="text-gray-600 text-sm">part de commerce intra-régional visée d'ici 2030 grâce aux réformes en cours</p>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                    <h3 class="font-bold text-blue-800 mb-2">Potentiel régional</h3>
                    <p class="text-gray-600 text-sm">
                        La région CEEAC dispose d'une complémentarité économique naturelle : pays pétroliers,
                        pays agricoles, pays à fort potentiel minier et pays de services peuvent s'appuyer
                        mutuellement pour créer des chaînes de valeur régionales.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Zone de libre-échange --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-950 mb-10 text-center">Instruments de <span class="text-amber-500">libéralisation commerciale</span></h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#128196;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Élimination des droits de douane</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Suppression progressive des droits de douane sur les produits originaires des États membres,
                    conformément au programme de libéralisation des échanges adopté par la CEEAC. Les produits
                    issus de l'agriculture, de l'élevage et de l'artisanat local bénéficient de procédures
                    d'origine simplifiées pour accéder aux marchés régionaux.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#128466;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Facilitation des échanges</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Simplification et harmonisation des procédures douanières à travers la mise en oeuvre
                    de l'Accord de facilitation des échanges de l'OMC, l'introduction de guichets uniques
                    aux frontières et la dématérialisation des documents commerciaux et douaniers pour
                    réduire les délais et les coûts de passage aux frontières.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#128203;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Normes et certifications</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Harmonisation des normes techniques, sanitaires et phytosanitaires pour faciliter
                    la circulation des marchandises tout en garantissant la protection des consommateurs
                    et de l'environnement. Un référentiel régional de normalisation est en cours d'élaboration
                    par la Commission de la CEEAC.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#128184;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Finance et paiements</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Développement d'instruments financiers régionaux pour faciliter les paiements
                    transfrontaliers, réduire les coûts des transferts de fonds et promouvoir
                    l'inclusion financière. Le développement des fintech régionales est encouragé
                    comme vecteur d'innovation financière au service du commerce.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#127970;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">Marchés publics régionaux</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Ouverture progressive des marchés publics des États membres aux entreprises de l'ensemble
                    de la CEEAC, créant un vaste marché de la commande publique pour les entreprises régionales
                    et encourageant la compétitivité et l'efficacité des prestataires locaux.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="text-3xl mb-4">&#127919;</div>
                <h3 class="text-lg font-bold text-blue-950 mb-3">ZLECAf et CEEAC</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    La CEEAC soutient activement la mise en oeuvre de la Zone de Libre-Échange Continentale
                    Africaine (ZLECAf) et coordonne la position des États membres dans les négociations
                    continentales pour défendre les intérêts de la région Afrique centrale.
                </p>
            </div>

        </div>
    </div>
</section>

{{-- Investissements --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="bg-blue-950 text-white rounded-2xl p-8">
                <h3 class="text-2xl font-bold text-amber-400 mb-6">Cadre d'investissement régional</h3>
                <p class="text-blue-100 leading-relaxed mb-4">
                    La CEEAC travaille à la création d'un cadre d'investissement régional attractif et
                    sécurisé, permettant d'attirer les investissements directs étrangers et de promouvoir
                    les investissements intra-régionaux. Ce cadre comprend l'harmonisation des codes
                    d'investissement, la protection des droits de propriété intellectuelle et la mise
                    en place de mécanismes de règlement des différends investisseur-État.
                </p>
                <div class="space-y-3 text-sm">
                    <div class="flex gap-3">
                        <span class="text-amber-400">&#10003;</span>
                        <span class="text-blue-100">Code d'investissement communautaire harmonisé</span>
                    </div>
                    <div class="flex gap-3">
                        <span class="text-amber-400">&#10003;</span>
                        <span class="text-blue-100">Guichet unique pour les investisseurs régionaux</span>
                    </div>
                    <div class="flex gap-3">
                        <span class="text-amber-400">&#10003;</span>
                        <span class="text-blue-100">Protection des investissements et arbitrage régional</span>
                    </div>
                    <div class="flex gap-3">
                        <span class="text-amber-400">&#10003;</span>
                        <span class="text-blue-100">Garanties contre les risques politiques et commerciaux</span>
                    </div>
                    <div class="flex gap-3">
                        <span class="text-amber-400">&#10003;</span>
                        <span class="text-blue-100">Forum régional d'affaires CEEAC — réunions annuelles</span>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-blue-950 mb-6">Secteurs <span class="text-amber-500">prioritaires</span></h2>
                <div class="space-y-4">
                    <div class="border-l-4 border-amber-400 pl-4">
                        <h4 class="font-bold text-blue-950 mb-1">Agro-industrie et chaînes de valeur agricoles</h4>
                        <p class="text-gray-600 text-sm">La région dispose d'immenses terres arables. Le développement de chaînes de valeur régionales dans le cacao, le café, le palmier à huile et les cultures vivrières représente une opportunité majeure d'emplois et de revenus.</p>
                    </div>
                    <div class="border-l-4 border-blue-950 pl-4">
                        <h4 class="font-bold text-blue-950 mb-1">Industries extractives et transformation locale</h4>
                        <p class="text-gray-600 text-sm">Promotion de la transformation locale des matières premières minières et forestières pour générer davantage de valeur ajoutée dans la région, plutôt que d'exporter des ressources brutes.</p>
                    </div>
                    <div class="border-l-4 border-amber-400 pl-4">
                        <h4 class="font-bold text-blue-950 mb-1">Services financiers et assurances régionaux</h4>
                        <p class="text-gray-600 text-sm">Développement d'un secteur financier régional robuste capable de financer les investissements nécessaires à la transformation économique de l'Afrique centrale.</p>
                    </div>
                    <div class="border-l-4 border-blue-950 pl-4">
                        <h4 class="font-bold text-blue-950 mb-1">Tourisme et économie culturelle</h4>
                        <p class="text-gray-600 text-sm">La richesse naturelle et culturelle de l'Afrique centrale offre un potentiel touristique considérable encore largement sous-exploité, représentant une source de devises et d'emplois.</p>
                    </div>
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
            <a href="{{ route('website.domaine', 'integration-regionale') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#127970;</span>
                <div><h4 class="font-semibold text-blue-950">Intégration régionale</h4><p class="text-sm text-gray-500">Marché commun, libre circulation</p></div>
            </a>
            <a href="{{ route('website.domaine', 'infrastructures') }}" class="bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-sm transition flex gap-4 items-start">
                <span class="text-2xl">&#128747;</span>
                <div><h4 class="font-semibold text-blue-950">Infrastructures</h4><p class="text-sm text-gray-500">Transport, énergie, numérique</p></div>
            </a>
        </div>
    </div>
</section>

@endsection
