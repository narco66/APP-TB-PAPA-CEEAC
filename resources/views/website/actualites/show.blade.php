@extends('website.layouts.main')

@section('title', 'Actualité')

@section('breadcrumb')
<li class="flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i>
    <a href="{{ route('website.actualites') }}" class="hover:text-amber-600">Actualités</a>
</li>
<li class="flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i><span class="text-gray-700 font-medium">Article</span></li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Article -->
        <article class="lg:col-span-2">
            <div class="flex items-center gap-2 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white" style="background:#C4922A;">Sommet</span>
                <span class="text-xs text-gray-400">20 mars 2026</span>
                <span class="text-xs text-gray-400">· 5 min de lecture</span>
            </div>

            <h1 class="text-3xl font-black mb-4 leading-tight" style="color:#0A2157; font-family:'Merriweather',serif;">
                Le 18e Sommet de la CEEAC adopte la feuille de route 2025–2030
            </h1>

            <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100">
                <div class="h-9 w-9 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background:#0A2157;">SG</div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Service de communication</p>
                    <p class="text-xs text-gray-400">Commission de la CEEAC</p>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <a href="#" class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-blue-100 hover:text-blue-600 text-sm transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-sky-100 hover:text-sky-500 text-sm transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-green-100 hover:text-green-600 text-sm transition"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- Image principale -->
            <div class="rounded-xl overflow-hidden mb-8 flex items-center justify-center" style="background:linear-gradient(135deg,#0A2157,#1a3a7a); height:300px;">
                <i class="fas fa-star text-9xl opacity-10 text-white"></i>
            </div>

            <!-- Corps de l'article -->
            <div class="prose max-w-none text-gray-700 leading-relaxed">
                <p class="text-lg font-medium text-gray-800 mb-4">
                    Libreville, 20 mars 2026 — Réunis les 19 et 20 mars 2026 au Palais du Bord de Mer à Libreville,
                    les chefs d'État et de gouvernement des onze États membres de la CEEAC ont adopté à l'unanimité
                    la feuille de route stratégique 2025–2030 de la Communauté.
                </p>
                <p class="mb-4">
                    Ce document ambitieux, fruit de dix-huit mois de consultations techniques et politiques, fixe les
                    grandes priorités de la Communauté pour les cinq prochaines années dans six domaines stratégiques :
                    paix et sécurité, intégration économique, développement des infrastructures, facilitation du commerce
                    et de l'investissement, gestion durable des ressources naturelles, et développement humain.
                </p>
                <p class="mb-4">
                    Le Président de la Commission, S.E. Gilberto da Piedade Verissimo, a salué l'adoption de ce document
                    comme « une étape historique dans le processus d'intégration régionale en Afrique centrale ».
                    Il a souligné la volonté politique des États membres de transformer les ressources considérables de
                    la région en une prospérité partagée et inclusive.
                </p>

                <h2 class="text-xl font-bold mt-8 mb-4" style="color:#0A2157; font-family:'Merriweather',serif;">Six priorités stratégiques pour 2030</h2>
                <p class="mb-4">
                    La feuille de route 2025–2030 s'articule autour de six axes prioritaires, chacun assorti d'objectifs
                    mesurables et d'indicateurs de performance permettant d'évaluer les progrès accomplis :
                </p>
                <ul class="list-disc list-inside mb-6 space-y-2 text-sm">
                    <li><strong>Paix et sécurité :</strong> Renforcement des mécanismes du COPAX, déploiement de la Force Multinationale de l'Afrique Centrale</li>
                    <li><strong>Intégration économique :</strong> Accélération de la Zone de Libre-Échange, harmonisation des politiques fiscales</li>
                    <li><strong>Infrastructures :</strong> Finalisation des corridors prioritaires du PDCT-AC, interconnexion électrique du PEAC</li>
                    <li><strong>Commerce :</strong> Réduction de 60% des barrières non tarifaires, digitalisation des procédures douanières</li>
                    <li><strong>Ressources naturelles :</strong> Protection de 30% du Bassin du Congo, énergies renouvelables à 40% du mix énergétique</li>
                    <li><strong>Développement humain :</strong> Accès universel à l'éducation de base, couverture santé régionale</li>
                </ul>

                <h2 class="text-xl font-bold mt-8 mb-4" style="color:#0A2157; font-family:'Merriweather',serif;">Décisions et résolutions adoptées</h2>
                <p class="mb-4">
                    En marge de l'adoption de la feuille de route, le 18e Sommet a également adopté plusieurs résolutions
                    importantes, dont la nomination des nouveaux commissaires de la Commission pour le mandat 2026–2030,
                    l'approbation du budget de la Communauté pour l'exercice 2026 et la désignation de Luanda comme siège
                    du prochain Sommet ordinaire prévu en juillet 2026.
                </p>
            </div>

            <!-- Tags -->
            <div class="flex flex-wrap gap-2 mt-8 pt-6 border-t border-gray-100">
                @foreach(['Sommet','Libreville','Feuille de route','Intégration','2026'] as $tag)
                <span class="px-3 py-1 rounded-full text-xs border border-gray-200 text-gray-600">#{{ $tag }}</span>
                @endforeach
            </div>

            <!-- Navigation -->
            <div class="mt-8 flex items-center justify-between">
                <a href="{{ route('website.actualites') }}" class="flex items-center gap-2 text-sm font-medium" style="color:#0A2157;">
                    <i class="fas fa-arrow-left text-xs"></i> Retour aux actualités
                </a>
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-4 py-3 border-b border-gray-100" style="background:#0A2157;">
                    <h3 class="text-sm font-bold text-white">Actualités récentes</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach([
                        ['15 mars 2026', 'La CEEAC renforce son dispositif anti-terroriste'],
                        ['8 mars 2026', 'Lancement du programme de facilitation du commerce'],
                        ['1er mars 2026', 'Le PDCT-AC valide le corridor Libreville-Brazzaville'],
                        ['22 fév. 2026', 'Partenariat renforcé CEEAC-Union Européenne'],
                    ] as $news)
                    <a href="{{ route('website.actualites') }}" class="block p-4 hover:bg-gray-50">
                        <p class="text-xs text-gray-400 mb-1">{{ $news[0] }}</p>
                        <p class="text-sm font-medium leading-snug" style="color:#374151;">{{ $news[1] }}</p>
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold mb-4" style="color:#0A2157;">Communiqués de presse</h3>
                <a href="{{ route('website.communiques') }}" class="flex items-center gap-2 text-sm font-medium" style="color:#C4922A;">
                    <i class="fas fa-bullhorn"></i> Voir les communiqués
                </a>
            </div>

            <div class="rounded-xl p-5 text-white" style="background:#0A2157;">
                <h3 class="font-bold mb-2">Newsletter</h3>
                <p class="text-blue-200 text-xs mb-4">Recevez les actualités de la CEEAC directement dans votre boîte mail.</p>
                <input type="email" placeholder="votre@email.com" class="w-full px-3 py-2 rounded-lg text-sm text-gray-800 mb-2">
                <button class="w-full py-2 rounded-lg text-sm font-semibold text-white transition" style="background:#C4922A;">S'inscrire</button>
            </div>
        </aside>
    </div>
</div>
@endsection
