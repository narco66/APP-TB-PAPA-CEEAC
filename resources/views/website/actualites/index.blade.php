@extends('website.layouts.main')

@section('title', 'Actualités')
@section('meta_description', 'Suivez les dernières actualités de la Commission de la CEEAC — sommets, réunions, partenariats, programmes et initiatives régionales.')

@section('breadcrumb')
<li class="flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i><span class="text-gray-700 font-medium">Actualités</span></li>
@endsection

@push('styles')
<style>
.news-hero { background: linear-gradient(135deg,#0A2157,#1a3a7a); }
.tag-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:999px; font-size:0.7rem; font-weight:600; }
</style>
@endpush

@section('content')
<div class="news-hero py-14">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Salle de presse</p>
        <h1 class="text-4xl font-black text-white" style="font-family:'Merriweather',serif;">Actualités de la CEEAC</h1>
        <p class="text-blue-200 mt-3 max-w-2xl mx-auto text-sm">Restez informé des activités, décisions et initiatives de la Commission et des organes de la Communauté.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-12">

    <!-- Filtres -->
    <div class="flex flex-wrap gap-3 mb-10">
        @php
        $categories = ['Toutes','Sommet','Réunions','Sécurité','Économie','Infrastructures','Partenariats','Environnement'];
        @endphp
        @foreach($categories as $i => $cat)
        <button class="px-4 py-1.5 rounded-full text-sm font-medium border transition
                       {{ $i===0 ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 hover:border-gray-400' }}"
                style="{{ $i===0 ? 'background:#0A2157;' : '' }}">{{ $cat }}</button>
        @endforeach
    </div>

    <!-- Article à la une -->
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 mb-10 flex flex-col lg:flex-row">
        <div class="lg:w-1/2 flex items-center justify-center" style="background:linear-gradient(135deg,#0A2157,#1a3a7a); min-height:300px;">
            <i class="fas fa-star text-8xl opacity-10 text-white"></i>
        </div>
        <div class="p-8 lg:w-1/2 flex flex-col justify-center">
            <div class="flex items-center gap-2 mb-4">
                <span class="tag-badge text-white" style="background:#C4922A;">À la une</span>
                <span class="tag-badge" style="background:rgba(10,33,87,0.08); color:#0A2157;">Sommet</span>
                <span class="text-xs text-gray-400">20 mars 2026</span>
            </div>
            <h2 class="text-2xl font-black mb-3" style="color:#0A2157; font-family:'Merriweather',serif;">
                Le 18e Sommet de la CEEAC adopte la feuille de route 2025–2030
            </h2>
            <p class="text-gray-600 leading-relaxed mb-5 text-sm">
                Réunis à Libreville les 19 et 20 mars 2026, les chefs d'État et de gouvernement des 11 États membres ont
                adopté à l'unanimité la nouvelle feuille de route stratégique de la Communauté pour la période 2025–2030.
                Ce document ambitieux fixe les priorités d'action dans six domaines clés : paix et sécurité, intégration
                économique, infrastructures, commerce, ressources naturelles et développement humain.
            </p>
            <a href="{{ route('website.actualite', '18e-sommet-ceeac-feuille-de-route') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold" style="color:#C4922A;">
                Lire l'article complet <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Grille d'articles -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
        $articles = [
            ['categorie'=>'Sécurité','cat_color'=>'#dc2626','date'=>'15 mars 2026','icon'=>'fa-shield-alt',
             'titre'=>'La CEEAC renforce son dispositif de lutte contre le terrorisme',
             'resume'=>"À l'issue de la réunion des ministres en charge de la sécurité, de nouvelles mesures ont été adoptées pour renforcer la coopération régionale face à la menace terroriste.",
             'slug'=>'lutte-contre-terrorisme-2026'],
            ['categorie'=>'Économie','cat_color'=>'#059669','date'=>'8 mars 2026','icon'=>'fa-chart-line',
             'titre'=>'Lancement du programme de facilitation du commerce intra-CEEAC 2026',
             'resume'=>'La Commission lance un nouveau programme visant à réduire les barrières non tarifaires et à harmoniser les procédures douanières entre les États membres.',
             'slug'=>'facilitation-commerce-2026'],
            ['categorie'=>'Infrastructures','cat_color'=>'#7c3aed','date'=>'1er mars 2026','icon'=>'fa-road',
             'titre'=>'Le PDCT-AC franchit une nouvelle étape avec le corridor Libreville-Brazzaville',
             'resume'=>'La réunion technique des ministres en charge des transports a validé le tracé définitif du corridor routier reliant Libreville à Brazzaville, projet phare du Plan Directeur des Transports.',
             'slug'=>'pdct-corridor-libreville-brazzaville'],
            ['categorie'=>'Partenariats','cat_color'=>'#0A2157','date'=>'22 fév. 2026','icon'=>'fa-handshake',
             'titre'=>"La CEEAC et l'Union Européenne renforcent leur partenariat stratégique",
             'resume'=>"La signature d'un nouvel accord-cadre de coopération entre la Commission et la Délégation de l'UE à Libreville marque une nouvelle étape du partenariat CEEAC-UE.",
             'slug'=>'partenariat-ceeac-ue-2026'],
            ['categorie'=>'Environnement','cat_color'=>'#16a34a','date'=>'14 fév. 2026','icon'=>'fa-leaf',
             'titre'=>'Sommet extraordinaire sur la préservation du Bassin du Congo',
             'resume'=>"Les 11 États membres ont adopté une déclaration commune appelant à l'intensification des efforts de préservation du Bassin du Congo, deuxième poumon vert de la planète.",
             'slug'=>'sommet-bassin-congo-2026'],
            ['categorie'=>'Réunions','cat_color'=>'#C4922A','date'=>'5 fév. 2026','icon'=>'fa-users',
             'titre'=>'La 47e session du Conseil des Ministres se tient à Malabo',
             'resume'=>'Le Conseil des Ministres a adopté dix règlements communautaires et examiné les progrès des programmes phares de la Commission lors de sa 47e session ordinaire.',
             'slug'=>'conseil-ministres-47e-session'],
        ];
        @endphp

        @foreach($articles as $article)
        <article class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm" style="transition:transform .25s,box-shadow .25s; hover:transform:translateY(-4px);">
            <div class="flex items-center justify-center" style="background:linear-gradient(135deg,#e8edf5,#c5d0e6); height:160px;">
                <i class="fas {{ $article['icon'] }} text-5xl opacity-20" style="color:#0A2157;"></i>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="tag-badge text-white" style="background:{{ $article['cat_color'] }};">{{ $article['categorie'] }}</span>
                    <span class="text-xs text-gray-400">{{ $article['date'] }}</span>
                </div>
                <h3 class="text-base font-bold mb-2 leading-snug" style="color:#0A2157; font-family:'Merriweather',serif;">{{ $article['titre'] }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $article['resume'] }}</p>
                <a href="{{ route('website.actualite', $article['slug']) }}" class="text-xs font-semibold flex items-center gap-1" style="color:#C4922A;">
                    Lire la suite <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </article>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-center gap-2 mt-12">
        <button class="h-9 w-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 hover:border-gray-400">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>
        @foreach([1,2,3,4,5] as $p)
        <button class="h-9 w-9 rounded-lg border text-sm font-medium {{ $p===1 ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 hover:border-gray-400' }}"
                style="{{ $p===1 ? 'background:#0A2157;' : '' }}">{{ $p }}</button>
        @endforeach
        <button class="h-9 w-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:border-gray-400">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>
    </div>
</div>
@endsection
