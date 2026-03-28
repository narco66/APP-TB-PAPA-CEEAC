@extends('website.layouts.main')

@section('title', 'Mot du Président de la Conférence — CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <a href="{{ route('website.a-propos') }}" class="hover:text-amber-400 transition">À propos</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Mot du Président</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Mot du <span class="text-amber-400">Président</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            Message de Son Excellence le Président de la Conférence des Chefs d'État
            et de Gouvernement de la CEEAC.
        </p>
    </div>
</section>

{{-- Message --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12">

            {{-- Photo et identité --}}
            <div class="lg:w-1/3 flex flex-col items-center">
                <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-xs">
                    {{-- Photo placeholder --}}
                    <div class="bg-gradient-to-br from-blue-950 to-blue-800 h-72 flex flex-col items-center justify-center text-white">
                        <svg class="w-24 h-24 text-blue-300 mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                        <p class="text-blue-200 text-sm">Photo officielle</p>
                    </div>
                    <div class="p-5 text-center bg-white">
                        <h2 class="font-bold text-blue-950 text-lg">S.E. le Président de la Conférence</h2>
                        <p class="text-amber-600 font-medium text-sm mt-1">de la CEEAC</p>
                        <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                            <p><strong>Fonction :</strong> Président de la Conférence des Chefs d'État</p>
                            <p><strong>Mandat :</strong> En cours</p>
                        </div>
                    </div>
                </div>

                {{-- Partager --}}
                <div class="mt-6 w-full max-w-xs">
                    <p class="text-sm font-semibold text-gray-600 mb-3 text-center">Partager ce message</p>
                    <div class="flex justify-center gap-3">
                        <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Facebook</a>
                        <a href="#" class="bg-sky-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-sky-600 transition">Twitter / X</a>
                        <a href="#" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition">WhatsApp</a>
                    </div>
                </div>
            </div>

            {{-- Texte du message --}}
            <div class="lg:w-2/3">
                <div class="bg-white rounded-2xl shadow-sm p-8 md:p-10">
                    <div class="border-l-4 border-amber-400 pl-6 mb-8">
                        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Message officiel</p>
                        <h3 class="text-2xl font-bold text-blue-950 mt-1">Aux peuples d'Afrique centrale et à la Communauté internationale</h3>
                    </div>

                    <div class="prose prose-lg max-w-none text-gray-700 space-y-5 leading-relaxed">
                        <p>
                            <strong>Excellence, Mesdames et Messieurs,</strong><br>
                            Chères citoyennes et chers citoyens d'Afrique centrale,
                        </p>

                        <p>
                            Il m'échoit l'honneur insigne de vous adresser ce message en ma qualité de Président
                            en exercice de la Conférence des Chefs d'État et de Gouvernement de la Communauté
                            Économique des États de l'Afrique Centrale. C'est avec une profonde conviction et
                            un sentiment renouvelé de responsabilité que je m'acquitte de cette tâche, conscient
                            des immenses attentes que nos peuples placent dans notre organisation commune.
                        </p>

                        <p>
                            La CEEAC, fondée il y a plus de quatre décennies par les pères fondateurs de
                            l'intégration en Afrique centrale, demeure plus que jamais l'expression concrète
                            de notre volonté collective de bâtir ensemble un avenir meilleur pour nos populations.
                            Forte de ses onze États membres, abritant une population de 230 millions d'âmes sur
                            un territoire de 6,6 millions de kilomètres carrés, notre Communauté représente une
                            puissance régionale d'une importance stratégique considérable.
                        </p>

                        <p>
                            Les défis que nous affrontons sont certes nombreux et complexes : fragilité de la
                            paix et de la sécurité dans plusieurs parties de notre région, lenteur relative
                            du processus d'intégration économique, persistance d'une pauvreté inacceptable
                            malgré nos immenses ressources naturelles, effets dévastateurs des changements
                            climatiques sur nos populations les plus vulnérables. Mais je reste profondément
                            convaincu que notre capacité collective à surmonter ces obstacles est réelle et
                            que les progrès accomplis ces dernières années témoignent de la vitalité de notre
                            engagement commun.
                        </p>

                        <p>
                            En matière de paix et de sécurité, le COPAX, notre mécanisme régional de prévention
                            et de résolution des conflits, a démontré sa valeur à travers plusieurs opérations
                            de maintien de la paix. Nous devons continuer à renforcer cet instrument précieux,
                            à consolider la démocratie et la bonne gouvernance dans nos États, et à donner
                            à nos concitoyens les garanties de stabilité dont ils ont besoin pour s'épanouir
                            et contribuer au développement de notre région.
                        </p>

                        <p>
                            Sur le plan de l'intégration économique, des avancées significatives ont été
                            enregistrées dans l'harmonisation de nos politiques commerciales, la facilitation
                            des échanges transfrontaliers et la mise en place d'infrastructures régionales
                            structurantes. Le Programme Indicatif de Développement de la Communauté (PIDE)
                            trace la voie d'une intégration par les projets concrets, en matière de transport,
                            d'énergie et de connectivité numérique.
                        </p>

                        <p>
                            La réforme institutionnelle que nous avons engagée, avec la transformation du
                            Secrétariat Général en Commission de la CEEAC, marque notre volonté de doter
                            notre organisation des capacités opérationnelles à la hauteur de ses ambitions.
                            Cette réforme n'est pas simplement administrative ; elle exprime une ambition
                            politique nouvelle, celle d'une organisation supranationale au service de
                            l'intégration effective de nos économies et de nos sociétés.
                        </p>

                        <p>
                            Notre agenda est ambitieux, à la mesure des défis et des opportunités qui s'offrent
                            à nous. L'Agenda 2063 de l'Union Africaine, qui dessine le visage de l'Afrique
                            que nous voulons pour les générations futures, nous invite à accélérer notre marche
                            vers l'intégration. La CEEAC, en tant que pilier de la Communauté Économique
                            Africaine, se positionne résolument à l'avant-garde de cette marche historique.
                        </p>

                        <p>
                            Permettez-moi de lancer un appel solennel à l'ensemble des parties prenantes de
                            notre processus d'intégration : gouvernements, institutions, secteur privé,
                            société civile, jeunesse et femmes. L'intégration de l'Afrique centrale est
                            l'affaire de tous. Elle ne se décrète pas dans les palais et les salles de
                            conférences ; elle se construit chaque jour dans les actes concrets de coopération,
                            de solidarité et de fraternité entre nos peuples.
                        </p>

                        <p>
                            Je réitère l'engagement indéfectible des Chefs d'État et de Gouvernement de la
                            CEEAC à poursuivre inlassablement notre marche commune vers la réalisation de
                            la vision d'une <em>Afrique centrale intégrée, pacifique et prospère</em>,
                            au bénéfice de nos populations et de l'humanité tout entière.
                        </p>

                        <p class="mt-8 font-semibold text-blue-950">
                            Puisse Dieu bénir les peuples d'Afrique centrale et guider notre marche commune.<br><br>
                            Je vous remercie.
                        </p>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="font-bold text-blue-950">S.E. le Président de la Conférence des Chefs d'État et de Gouvernement</p>
                                <p class="text-amber-600 text-sm">Communauté Économique des États de l'Afrique Centrale (CEEAC)</p>
                            </div>
                            <div class="text-sm text-gray-500 text-right">
                                <p>Libreville, Gabon</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-12 bg-blue-950 text-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-2xl font-bold mb-4">En savoir plus sur la CEEAC</h3>
        <p class="text-blue-100 mb-8">Découvrez les organes, la vision et les programmes de notre organisation.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('website.organes') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-3 rounded-lg transition">
                Nos organes institutionnels
            </a>
            <a href="{{ route('website.vision-mission') }}" class="border border-amber-400 text-amber-400 hover:bg-amber-400 hover:text-blue-950 font-semibold px-6 py-3 rounded-lg transition">
                Vision &amp; Mission
            </a>
            <a href="{{ route('website.programmes') }}" class="border border-white text-white hover:bg-white hover:text-blue-950 font-semibold px-6 py-3 rounded-lg transition">
                Nos programmes
            </a>
        </div>
    </div>
</section>

{{-- Navigation --}}
<section class="py-10 bg-white border-t border-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ route('website.organes') }}" class="text-blue-950 hover:text-amber-600 font-medium flex items-center gap-2 transition">
            &larr; Organes institutionnels
        </a>
        <a href="{{ route('website.etats-membres') }}" class="bg-blue-950 text-white hover:bg-blue-900 font-medium flex items-center gap-2 px-5 py-2 rounded-lg transition">
            États membres &rarr;
        </a>
    </div>
</section>

@endsection
