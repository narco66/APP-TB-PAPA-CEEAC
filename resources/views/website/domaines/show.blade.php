@extends('website.layouts.main')

@section('title', $domaine['titre'])
@section('meta_description', 'Domaine d\'action de la CEEAC — ' . $domaine['titre'])

@section('breadcrumb')
<li class="flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i><span class="text-gray-700 font-medium">Domaines d'action</span></li>
<li class="flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i><span class="text-gray-700 font-medium">{{ $domaine['titre'] }}</span></li>
@endsection

@push('styles')
<style>
.domain-hero { background: linear-gradient(135deg,#0A2157,#1a3a7a); }
</style>
@endpush

@section('content')
<div class="domain-hero py-14">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <div class="h-14 w-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:rgba(196,146,42,0.2);">
            <i class="fas {{ $domaine['icon'] }} text-3xl" style="color:#C4922A;"></i>
        </div>
        <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Domaines d'action</p>
        <h1 class="text-4xl font-black text-white" style="font-family:'Merriweather',serif;">{{ $domaine['titre'] }}</h1>
    </div>
</div>

@php
$content = [
    'paix-securite' => [
        'intro' => 'La CEEAC constitue l\'architecture de paix et de sécurité de l\'Afrique centrale. Depuis la création du COPAX en 1999, la Communauté a développé des mécanismes robustes de prévention des conflits, de maintien de la paix et de stabilisation régionale.',
        'axes' => [
            ['icon'=>'fa-shield-alt', 'titre'=>'Mécanisme de prévention des conflits', 'desc'=>'Le MARAC (Mécanisme d\'Alerte Rapide de l\'Afrique Centrale) constitue le dispositif d\'observation et d\'analyse des situations de crise permettant l\'anticipation et la prévention des conflits.'],
            ['icon'=>'fa-users-cog', 'titre'=>'Force Multinationale de l\'Afrique Centrale', 'desc'=>'La FOMAC est la composante militaire du COPAX. Elle est composée de contingents des États membres et peut être déployée pour des opérations de maintien de la paix, d\'imposition de la paix et d\'intervention humanitaire.'],
            ['icon'=>'fa-balance-scale', 'titre'=>'Conseil de Paix et Sécurité', 'desc'=>'Le COPAX est l\'organe décisionnel en matière de paix et de sécurité. Il supervise la mise en œuvre des protocoles relatifs à la non-agression, à l\'assistance mutuelle en matière de défense et à la libre circulation.'],
            ['icon'=>'fa-ban', 'titre'=>'Lutte contre le terrorisme', 'desc'=>'La CEEAC coordonne les stratégies nationales et régionales de lutte contre le terrorisme, notamment dans la zone du Lac Tchad, la sous-région affectée par Boko Haram et les groupes armés transfrontaliers.'],
        ],
        'chiffres' => [['val'=>'1999','lab'=>'Création du COPAX'],['val'=>'11','lab'=>'États contributeurs à la FOMAC'],['val'=>'8','lab'=>'Opérations de paix menées'],['val'=>'MARAC','lab'=>'Système d\'alerte précoce']],
    ],
    'integration-economique' => [
        'intro' => 'L\'intégration économique constitue la finalité première de la CEEAC. Elle vise à créer un espace économique unifié dans lequel personnes, biens, services et capitaux circulent librement, pour le bénéfice de tous les citoyens de la région.',
        'axes' => [
            ['icon'=>'fa-globe','titre'=>'Zone de libre-échange','desc'=>'La CEEAC travaille à l\'élimination progressive des droits de douane et des barrières tarifaires entre les États membres, en cohérence avec la Zone de Libre-Échange Continentale Africaine (ZLECAf).'],
            ['icon'=>'fa-money-bill-wave','titre'=>'Convergence macroéconomique','desc'=>'Le Programme de convergence économique fixe des critères de stabilité macroéconomique communs : maîtrise de l\'inflation, du déficit budgétaire et de la dette publique pour créer les conditions d\'une union monétaire.'],
            ['icon'=>'fa-passport','titre'=>'Libre circulation des personnes','desc'=>'Le Protocole sur la libre circulation des personnes vise à permettre aux ressortissants des États membres de circuler, de s\'établir et d\'exercer une activité économique librement dans tout l\'espace CEEAC.'],
            ['icon'=>'fa-chart-bar','titre'=>'Harmonisation des politiques','desc'=>'La Commission coordonne l\'harmonisation des politiques commerciales, industrielles, agricoles et fiscales des États membres pour créer un environnement des affaires cohérent et attractif.'],
        ],
        'chiffres' => [['val'=>'3,7M','lab'=>'km² du marché commun'],['val'=>'180M','lab'=>'Consommateurs potentiels'],['val'=>'11','lab'=>'États membres engagés'],['val'=>'ZLECAf','lab'=>'Articulation continentale']],
    ],
    'infrastructures' => [
        'intro' => 'Le développement des infrastructures de transport, d\'énergie et de communication constitue le socle physique de l\'intégration régionale. La CEEAC pilote des projets structurants qui transforment la connectivité de l\'Afrique centrale.',
        'axes' => [
            ['icon'=>'fa-route','titre'=>'PDCT-AC — Transports','desc'=>'Le Plan Directeur Consensuel des Transports en Afrique Centrale (PDCT-AC) identifie 32 projets prioritaires d\'interconnexion routière, ferroviaire, fluviale et maritime pour un investissement total estimé à 12,5 milliards de dollars.'],
            ['icon'=>'fa-bolt','titre'=>'PEAC — Énergie','desc'=>'Le Pool Énergétique de l\'Afrique Centrale (PEAC) coordonne l\'interconnexion des réseaux électriques nationaux pour garantir l\'accès à l\'énergie à tous, valoriser le formidable potentiel hydroélectrique du Bassin du Congo et réduire les coûts.'],
            ['icon'=>'fa-wifi','titre'=>'Numérique et TIC','desc'=>'La stratégie régionale TIC de la CEEAC vise le déploiement d\'un backbone numérique régional, l\'harmonisation des cadres réglementaires des télécommunications et la promotion de l\'économie numérique.'],
            ['icon'=>'fa-ship','titre'=>'Transport fluvial et maritime','desc'=>'Le Bassin du Congo offre un réseau fluvial exceptionnel. La CEEAC coordonne les efforts d\'amélioration de la navigabilité du fleuve Congo et de ses affluents, infrastructure vitale pour les pays enclavés.'],
        ],
        'chiffres' => [['val'=>'32','lab'=>'Projets prioritaires PDCT-AC'],['val'=>'12,5Mds$','lab'=>'Budget estimé PDCT'],['val'=>'100GW','lab'=>'Potentiel hydroélectrique'],['val'=>'3','lab'=>'Pays enclavés desservis']],
    ],
    'commerce-investissement' => [
        'intro' => 'La CEEAC s\'engage à créer un environnement favorable au développement du commerce intra-régional et à l\'attraction des investissements directs étrangers, en s\'appuyant sur les réformes structurelles et l\'amélioration du cadre réglementaire.',
        'axes' => [
            ['icon'=>'fa-boxes','titre'=>'Commerce intra-régional','desc'=>'Avec moins de 3% du commerce total des États membres réalisé à l\'intérieur de la CEEAC, le potentiel est immense. La Commission met en œuvre un programme ambitieux de réduction des obstacles et de facilitation des échanges.'],
            ['icon'=>'fa-file-contract','titre'=>'Cadre réglementaire','desc'=>'L\'harmonisation des codes des investissements, des procédures d\'enregistrement des entreprises, de la propriété intellectuelle et du droit des affaires crée la sécurité juridique indispensable aux investisseurs.'],
            ['icon'=>'fa-industry','titre'=>'Politique industrielle','desc'=>'La stratégie industrielle régionale vise à développer des chaînes de valeur régionales, à promouvoir la transformation locale des matières premières et à développer l\'industrie manufacturière.'],
            ['icon'=>'fa-handshake','titre'=>'Partenariats public-privé','desc'=>'La CEEAC encourage les partenariats public-privé pour financer les projets d\'infrastructure et de développement économique, mobilisant des ressources au-delà des budgets publics des États membres.'],
        ],
        'chiffres' => [['val'=>'<3%','lab'=>'Commerce intra-régional actuel'],['val'=>'×5','lab'=>'Objectif de croissance à 2030'],['val'=>'28','lab'=>'Réformes réglementaires en cours'],['val'=>'10Mds$','lab'=>'IDE ciblés par an à 2030']],
    ],
    'ressources-naturelles' => [
        'intro' => 'L\'Afrique centrale est dépositaire d\'un patrimoine naturel exceptionnel. Le Bassin du Congo, deuxième poumon vert de la planète, abrite une biodiversité incomparable. La CEEAC s\'engage à en assurer la gestion durable pour les générations futures.',
        'axes' => [
            ['icon'=>'fa-tree','titre'=>'Conservation du Bassin du Congo','desc'=>'Le Bassin du Congo abrite 70% des forêts tropicales d\'Afrique et constitue un puits de carbone majeur pour la planète. La CEEAC coordonne les stratégies des États membres pour sa préservation tout en permettant son exploitation durable.'],
            ['icon'=>'fa-water','titre'=>'Gestion des ressources en eau','desc'=>'La Commission Internationale du Bassin Congo-Oubangui-Sangha (CICOS) assure la gestion durable du réseau hydrographique régional, essentiel pour l\'agriculture, l\'énergie, le transport et la biodiversité.'],
            ['icon'=>'fa-gem','titre'=>'Ressources minières','desc'=>'La CEEAC promeut l\'exploitation responsable des ressources minières (pétrole, gaz, minerais critiques) dans le respect des normes environnementales internationales et dans l\'objectif d\'une meilleure valorisation locale.'],
            ['icon'=>'fa-sun','titre'=>'Transition énergétique','desc'=>'La région dispose d\'un immense potentiel en énergies renouvelables : hydroélectricité du Bassin du Congo, énergie solaire, géothermie du Rift Albertin. La CEEAC coordonne la transition vers un mix énergétique décarboné.'],
        ],
        'chiffres' => [['val'=>'3,7M','lab'=>'km² de forêts tropicales'],['val'=>'N°2','lab'=>'Puits de carbone mondial'],['val'=>'4.700','lab'=>'km de voies navigables'],['val'=>'50%','lab'=>'Biodiversité africaine']],
    ],
    'developpement-humain' => [
        'intro' => 'Le développement humain est au cœur de la vision de la CEEAC. Sans la valorisation du capital humain et l\'amélioration des conditions de vie des populations, les efforts d\'intégration économique ne peuvent produire leurs pleins effets.',
        'axes' => [
            ['icon'=>'fa-graduation-cap','titre'=>'Éducation et formation','desc'=>'La CEEAC coordonne les politiques d\'harmonisation des systèmes éducatifs, de reconnaissance mutuelle des diplômes et de promotion de l\'enseignement supérieur régional pour créer un espace commun de la connaissance.'],
            ['icon'=>'fa-heartbeat','titre'=>'Santé et protection sociale','desc'=>'Le Programme régional de santé vise le renforcement des systèmes de santé, la lutte contre les pandémies transfrontalières, la mise en place d\'une couverture maladie régionale et la sécurité sanitaire communautaire.'],
            ['icon'=>'fa-female','titre'=>'Genre et autonomisation','desc'=>'La CEEAC intègre systématiquement la dimension genre dans toutes ses politiques. Des programmes spécifiques ciblent l\'autonomisation économique des femmes, leur accès à l\'éducation et leur participation à la vie politique.'],
            ['icon'=>'fa-child','titre'=>'Jeunesse et emploi','desc'=>'Avec plus de 60% de la population âgée de moins de 25 ans, la jeunesse est à la fois le défi et la chance de l\'Afrique centrale. La CEEAC développe des programmes de formation professionnelle, d\'entrepreneuriat et d\'insertion.'],
        ],
        'chiffres' => [['val'=>'60%','lab'=>'Population de moins de 25 ans'],['val'=>'4','lab'=>'Langues officielles'],['val'=>'12','lab'=>'Protocoles sociaux adoptés'],['val'=>'2030','lab'=>'ODD — horizon commun']],
    ],
];
$data = $content[$slug] ?? $content['paix-securite'];
@endphp

<div class="max-w-7xl mx-auto px-4 py-16">
    <!-- Intro -->
    <div class="max-w-3xl mx-auto text-center mb-14">
        <p class="text-lg text-gray-700 leading-relaxed">{{ $data['intro'] }}</p>
    </div>

    <!-- Chiffres clés -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-14">
        @foreach($data['chiffres'] as $chiffre)
        <div class="bg-white rounded-xl border border-gray-100 p-5 text-center shadow-sm">
            <div class="text-2xl font-black mb-1" style="color:#0A2157; font-family:'Merriweather',serif;">{{ $chiffre['val'] }}</div>
            <div class="text-xs text-gray-500">{{ $chiffre['lab'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Axes d'action -->
    <h2 class="text-2xl font-black text-center mb-8" style="color:#0A2157; font-family:'Merriweather',serif;">Axes d'intervention</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-14">
        @foreach($data['axes'] as $axe)
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm" style="border-top:3px solid #C4922A;">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(10,33,87,0.08);">
                    <i class="fas {{ $axe['icon'] }} text-xl" style="color:#0A2157;"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">{{ $axe['titre'] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $axe['desc'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Autres domaines -->
    <div class="border-t border-gray-100 pt-10">
        <h3 class="text-center text-sm font-semibold uppercase tracking-widest mb-6 text-gray-400">Autres domaines d'action</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @php
            $autres = [
                'paix-securite' => ['titre'=>'Paix & Sécurité','icon'=>'fa-shield-alt'],
                'integration-economique' => ['titre'=>'Intégration','icon'=>'fa-handshake'],
                'infrastructures' => ['titre'=>'Infrastructures','icon'=>'fa-road'],
                'commerce-investissement' => ['titre'=>'Commerce','icon'=>'fa-chart-line'],
                'ressources-naturelles' => ['titre'=>'Ressources','icon'=>'fa-leaf'],
                'developpement-humain' => ['titre'=>'Dév. humain','icon'=>'fa-users'],
            ];
            @endphp
            @foreach($autres as $autreSlug => $autre)
            @if($autreSlug !== $slug)
            <a href="{{ route('website.domaine', $autreSlug) }}"
               class="bg-white rounded-xl border border-gray-100 p-4 text-center shadow-sm hover:border-gold-400 transition"
               style="hover:border-color:#C4922A;">
                <i class="fas {{ $autre['icon'] }} text-xl mb-2 block" style="color:#0A2157;"></i>
                <span class="text-xs font-medium text-gray-700">{{ $autre['titre'] }}</span>
            </a>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
