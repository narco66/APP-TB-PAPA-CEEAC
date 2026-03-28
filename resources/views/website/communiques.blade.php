@extends('website.layouts.main')

@section('title', 'Communiqués de presse')
@section('meta_description', 'Communiqués de presse officiels et déclarations de la Commission de la CEEAC.')

@section('breadcrumb')
<li class="flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i><span class="text-gray-700 font-medium">Communiqués de presse</span></li>
@endsection

@section('content')
<div class="py-14" style="background:linear-gradient(135deg,#0A2157,#1a3a7a);">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:#C4922A;">Espace presse</p>
        <h1 class="text-4xl font-black text-white" style="font-family:'Merriweather',serif;">Communiqués de presse</h1>
        <p class="text-blue-200 mt-3 max-w-2xl mx-auto text-sm">Toutes les déclarations officielles et communiqués de presse de la Commission de la CEEAC.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2">
            @php
            $communiques = [
                ['ref'=>'COM-2026-012','date'=>'20 mars 2026','titre'=>'Communiqué final du 18e Sommet des Chefs d\'État et de Gouvernement de la CEEAC',
                 'desc'=>'À l\'issue du 18e Sommet tenu à Libreville les 19 et 20 mars 2026, les Chefs d\'État et de Gouvernement adoptent à l\'unanimité la feuille de route stratégique 2025–2030 et prennent plusieurs décisions institutionnelles majeures.','type'=>'Sommet'],
                ['ref'=>'COM-2026-011','date'=>'10 mars 2026','titre'=>'La CEEAC condamne fermement les violations du cessez-le-feu en République Centrafricaine',
                 'desc'=>'La Commission de la CEEAC exprime sa vive préoccupation face à la recrudescence des violences armées en République Centrafricaine et appelle toutes les parties à respecter scrupuleusement les accords de paix en vigueur.','type'=>'Sécurité'],
                ['ref'=>'COM-2026-010','date'=>'28 fév. 2026','titre'=>'Déclaration sur l\'accélération de la mise en œuvre de la Zone de Libre-Échange CEEAC',
                 'desc'=>'La Commission annonce l\'adoption d\'un programme accéléré de suppression des barrières non tarifaires et de simplification des procédures douanières entre les États membres, en ligne avec les objectifs de la ZLECAf.','type'=>'Commerce'],
                ['ref'=>'COM-2026-009','date'=>'15 fév. 2026','titre'=>'La CEEAC salue la signature de l\'accord de normalisation des relations entre le Burundi et ses voisins',
                 'desc'=>'Le Président de la Commission se félicite de l\'accord intervenu entre le Burundi et les États voisins sous les auspices de la CEEAC et de l\'Union Africaine, et appelle à sa mise en œuvre rapide et inclusive.','type'=>'Paix & Sécurité'],
                ['ref'=>'COM-2026-008','date'=>'2 fév. 2026','titre'=>'Déclaration de Brazzaville sur la préservation du Bassin du Congo',
                 'desc'=>'À l\'issue du Sommet extraordinaire sur le Bassin du Congo, les onze chefs d\'État et de gouvernement adoptent la Déclaration de Brazzaville engageant leurs pays à préserver et valoriser durablement ce patrimoine mondial exceptionnel.','type'=>'Environnement'],
                ['ref'=>'COM-2026-007','date'=>'18 jan. 2026','titre'=>'Résultats de la 47e session ordinaire du Conseil des Ministres de la CEEAC',
                 'desc'=>'Le Conseil des Ministres a adopté dix règlements communautaires, approuvé le budget 2026 de la Commission et examiné l\'état d\'avancement des six programmes phares lors de sa 47e session ordinaire à Malabo.','type'=>'Institutionnel'],
            ];
            $type_colors = ['Sommet'=>'#C4922A','Sécurité'=>'#dc2626','Commerce'=>'#059669','Paix & Sécurité'=>'#0A2157','Environnement'=>'#16a34a','Institutionnel'=>'#374151'];
            @endphp

            <div class="space-y-4">
                @foreach($communiques as $com)
                <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full text-white"
                                      style="background:{{ $type_colors[$com['type']] ?? '#374151' }};">{{ $com['type'] }}</span>
                                <span class="text-xs text-gray-400">{{ $com['date'] }}</span>
                                <span class="text-xs font-mono text-gray-300">{{ $com['ref'] }}</span>
                            </div>
                            <h3 class="text-base font-bold mb-2" style="color:#0A2157; font-family:'Merriweather',serif;">{{ $com['titre'] }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $com['desc'] }}</p>
                        </div>
                        <div class="flex-shrink-0 flex flex-col gap-2">
                            <button class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:border-gray-400 transition whitespace-nowrap">
                                <i class="fas fa-eye text-xs"></i> Lire
                            </button>
                            <button class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:border-gray-400 transition whitespace-nowrap">
                                <i class="fas fa-download text-xs"></i> PDF
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-center gap-2 mt-8">
                <button class="h-9 w-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400"><i class="fas fa-chevron-left text-xs"></i></button>
                @foreach([1,2,3] as $p)
                <button class="h-9 w-9 rounded-lg border text-sm font-medium {{ $p===1 ? 'text-white border-transparent' : 'border-gray-200 text-gray-600' }}"
                        style="{{ $p===1 ? 'background:#0A2157;' : '' }}">{{ $p }}</button>
                @endforeach
                <button class="h-9 w-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600"><i class="fas fa-chevron-right text-xs"></i></button>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold mb-4" style="color:#0A2157;">Contact presse</h3>
                <p class="text-xs text-gray-500 mb-4">Pour toute demande d\'interview, accréditation ou information sur les activités de la Commission.</p>
                <ul class="space-y-2 text-xs text-gray-600">
                    <li class="flex items-center gap-2"><i class="fas fa-user" style="color:#C4922A;"></i> Direction de la Communication</li>
                    <li class="flex items-center gap-2"><i class="fas fa-envelope" style="color:#C4922A;"></i> presse@ceeac-eccas.org</li>
                    <li class="flex items-center gap-2"><i class="fas fa-phone" style="color:#C4922A;"></i> +241 01 72 32 05</li>
                </ul>
                <a href="{{ route('website.contact') }}" class="mt-4 block text-center py-2 rounded-lg text-sm font-semibold text-white transition" style="background:#0A2157;">Nous contacter</a>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold mb-3" style="color:#0A2157;">Archives</h3>
                <div class="space-y-1">
                    @foreach(['2025 (47 communiqués)','2024 (52 communiqués)','2023 (38 communiqués)','2022 (41 communiqués)'] as $archive)
                    <a href="#" class="flex items-center justify-between text-sm text-gray-600 hover:text-gold-500 py-1.5 border-b border-gray-50">
                        <span>{{ $archive }}</span>
                        <i class="fas fa-chevron-right text-xs text-gray-300"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
