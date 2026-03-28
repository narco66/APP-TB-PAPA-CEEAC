@extends('website.layouts.main')

@section('title', 'Contactez-nous — Commission de la CEEAC')

@section('content')

{{-- Hero --}}
<section class="bg-blue-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-6 text-blue-200">
            <a href="{{ route('website.home') }}" class="hover:text-amber-400 transition">Accueil</a>
            <span class="mx-2">/</span>
            <span class="text-amber-400">Contact</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Contactez <span class="text-amber-400">la Commission</span></h1>
        <p class="text-xl text-blue-100 max-w-3xl">
            Notre équipe est disponible pour répondre à toutes vos questions relatives aux activités,
            programmes et services de la Commission de la CEEAC.
        </p>
    </div>
</section>

{{-- Coordonnées rapides --}}
<section class="py-10 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-50 text-blue-950 rounded-xl flex items-center justify-center text-xl mx-auto mb-3">&#128205;</div>
                <h4 class="font-semibold text-blue-950 text-sm mb-1">Adresse</h4>
                <p class="text-xs text-gray-600">Boulevard de l'Indépendance<br>BP 2112, Libreville, Gabon</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl mx-auto mb-3">&#128222;</div>
                <h4 class="font-semibold text-blue-950 text-sm mb-1">Téléphone</h4>
                <p class="text-xs text-gray-600">+241 44 47 31<br>+241 44 47 32</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-50 text-blue-950 rounded-xl flex items-center justify-center text-xl mx-auto mb-3">&#128231;</div>
                <h4 class="font-semibold text-blue-950 text-sm mb-1">Email général</h4>
                <p class="text-xs text-gray-600"><a href="mailto:commission@ceeac-eccas.org" class="text-amber-600 hover:underline">commission@ceeac-eccas.org</a></p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl mx-auto mb-3">&#128337;</div>
                <h4 class="font-semibold text-blue-950 text-sm mb-1">Horaires</h4>
                <p class="text-xs text-gray-600">Lundi–Vendredi<br>08h00–17h00 (GMT+1)</p>
            </div>
        </div>
    </div>
</section>

{{-- Contenu principal --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12">

            {{-- Formulaire de contact --}}
            <div class="lg:w-2/3">
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-blue-950 mb-2">Envoyez-nous un message</h2>
                    <p class="text-gray-600 text-sm mb-6">Remplissez le formulaire ci-dessous. Nous nous engageons à vous répondre dans un délai de 3 à 5 jours ouvrables.</p>

                    <form action="{{ route('website.contact') }}" method="POST" class="space-y-5">
                        @csrf

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nom complet <span class="text-red-500">*</span></label>
                                <input type="text" name="nom" required
                                       class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none"
                                       placeholder="Votre nom et prénom">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Organisation / Institution</label>
                                <input type="text" name="organisation"
                                       class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none"
                                       placeholder="Votre organisation (facultatif)">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Adresse email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required
                                       class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none"
                                       placeholder="votre@email.com">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Téléphone</label>
                                <input type="tel" name="telephone"
                                       class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none"
                                       placeholder="+XXX XXXXXXXXX">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Département concerné <span class="text-red-500">*</span></label>
                            <select name="departement" required class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none text-gray-700">
                                <option value="">-- Sélectionnez un département --</option>
                                <option value="communication">Service de Communication et Relations Publiques</option>
                                <option value="protocole">Service du Protocole</option>
                                <option value="paix_securite">Département Paix, Sécurité et Gouvernance</option>
                                <option value="integration">Département Intégration Économique et Commerce</option>
                                <option value="infrastructures">Département Infrastructures et Énergie</option>
                                <option value="developpement">Département Développement Humain et Affaires Sociales</option>
                                <option value="ressources">Département Ressources Naturelles et Environnement</option>
                                <option value="administration">Département Administration, Finances et Budget</option>
                                <option value="autre">Autre / Non précisé</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Objet du message <span class="text-red-500">*</span></label>
                            <input type="text" name="objet" required
                                   class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none"
                                   placeholder="Objet de votre demande">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea name="message" rows="6" required
                                      class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none resize-none"
                                      placeholder="Décrivez votre demande, question ou commentaire..."></textarea>
                        </div>

                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="rgpd" id="rgpd" required class="mt-0.5">
                            <label for="rgpd" class="text-xs text-gray-600">
                                J'accepte que la Commission de la CEEAC traite mes données personnelles dans le cadre
                                de ma demande. Ces informations ne seront utilisées qu'à des fins de traitement de
                                cette demande et ne seront pas transmises à des tiers.
                            </label>
                        </div>

                        <button type="submit"
                                class="w-full bg-blue-950 hover:bg-blue-900 text-white font-semibold py-3 px-6 rounded-lg transition text-sm">
                            Envoyer le message &#9993;
                        </button>
                    </form>
                </div>
            </div>

            {{-- Sidebar contacts --}}
            <div class="lg:w-1/3 space-y-6">

                {{-- Siège --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-blue-950 mb-4 border-b-2 border-amber-400 pb-2">Siège de la Commission</h3>
                    <div class="space-y-3 text-sm text-gray-700">
                        <div class="flex gap-3">
                            <span class="text-amber-500 mt-0.5">&#128205;</span>
                            <div>
                                <p class="font-semibold">Commission de la CEEAC</p>
                                <p class="text-gray-600">Boulevard de l'Indépendance<br>BP 2112<br>Libreville, Gabon</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-amber-500 mt-0.5">&#128222;</span>
                            <div>
                                <p class="text-gray-600">+241 44 47 31</p>
                                <p class="text-gray-600">+241 44 47 32</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-amber-500 mt-0.5">&#128231;</span>
                            <div>
                                <a href="mailto:commission@ceeac-eccas.org" class="text-amber-600 hover:underline">commission@ceeac-eccas.org</a>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-amber-500 mt-0.5">&#127760;</span>
                            <div>
                                <a href="https://www.ceeac-eccas.org" class="text-amber-600 hover:underline" target="_blank">www.ceeac-eccas.org</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contacts départements --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-blue-950 mb-4 border-b-2 border-amber-400 pb-2">Contacts par département</h3>
                    <div class="space-y-4">

                        <div class="border-b border-gray-50 pb-3">
                            <h4 class="font-semibold text-blue-950 text-sm">Communication &amp; Relations Publiques</h4>
                            <p class="text-xs text-gray-500 mt-1">Médias, interviews, accréditations, partenariats</p>
                            <a href="mailto:communication@ceeac-eccas.org" class="text-xs text-amber-600 hover:underline">communication@ceeac-eccas.org</a>
                        </div>

                        <div class="border-b border-gray-50 pb-3">
                            <h4 class="font-semibold text-blue-950 text-sm">Protocole &amp; Événements</h4>
                            <p class="text-xs text-gray-500 mt-1">Accréditations diplomatiques, organisation de Sommets</p>
                            <a href="mailto:protocole@ceeac-eccas.org" class="text-xs text-amber-600 hover:underline">protocole@ceeac-eccas.org</a>
                        </div>

                        <div class="border-b border-gray-50 pb-3">
                            <h4 class="font-semibold text-blue-950 text-sm">Paix, Sécurité &amp; Gouvernance</h4>
                            <p class="text-xs text-gray-500 mt-1">COPAX, MARAC, médiation, prévention des conflits</p>
                            <a href="mailto:paix@ceeac-eccas.org" class="text-xs text-amber-600 hover:underline">paix@ceeac-eccas.org</a>
                        </div>

                        <div class="border-b border-gray-50 pb-3">
                            <h4 class="font-semibold text-blue-950 text-sm">Intégration Économique &amp; Commerce</h4>
                            <p class="text-xs text-gray-500 mt-1">Commerce régional, ZLE, politiques commerciales</p>
                            <a href="mailto:integration@ceeac-eccas.org" class="text-xs text-amber-600 hover:underline">integration@ceeac-eccas.org</a>
                        </div>

                        <div class="pb-3">
                            <h4 class="font-semibold text-blue-950 text-sm">Documentation &amp; Archives</h4>
                            <p class="text-xs text-gray-500 mt-1">Publications, documents officiels, bibliothèque</p>
                            <a href="mailto:documentation@ceeac-eccas.org" class="text-xs text-amber-600 hover:underline">documentation@ceeac-eccas.org</a>
                        </div>

                    </div>
                </div>

                {{-- Réseaux sociaux --}}
                <div class="bg-blue-950 text-white rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Suivez la CEEAC</h3>
                    <div class="space-y-3">
                        <a href="#" class="flex items-center gap-3 text-blue-100 hover:text-amber-400 transition text-sm">
                            <span class="w-8 h-8 bg-blue-800 rounded-lg flex items-center justify-center text-blue-200">f</span>
                            Facebook — CEEAC Officiel
                        </a>
                        <a href="#" class="flex items-center gap-3 text-blue-100 hover:text-amber-400 transition text-sm">
                            <span class="w-8 h-8 bg-blue-800 rounded-lg flex items-center justify-center text-blue-200">X</span>
                            Twitter / X — @CEEAC_ECCAS
                        </a>
                        <a href="#" class="flex items-center gap-3 text-blue-100 hover:text-amber-400 transition text-sm">
                            <span class="w-8 h-8 bg-blue-800 rounded-lg flex items-center justify-center text-blue-200">in</span>
                            LinkedIn — Commission CEEAC
                        </a>
                        <a href="#" class="flex items-center gap-3 text-blue-100 hover:text-amber-400 transition text-sm">
                            <span class="w-8 h-8 bg-blue-800 rounded-lg flex items-center justify-center text-blue-200">&#9654;</span>
                            YouTube — CEEAC TV
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

{{-- Carte --}}
<section class="py-0">
    <div class="bg-gray-100 h-72 flex items-center justify-center border-t border-gray-200">
        <div class="text-center text-gray-400">
            <div class="text-5xl mb-3">&#128205;</div>
            <p class="font-medium text-gray-600">Carte interactive — Siège de la CEEAC</p>
            <p class="text-sm text-gray-400 mt-1">Boulevard de l'Indépendance, Libreville, Gabon</p>
            <a href="https://maps.google.com/?q=Libreville+Gabon+CEEAC" target="_blank" rel="noopener"
               class="inline-block mt-3 text-sm font-medium text-amber-600 hover:text-amber-700">
                Voir sur Google Maps &#8599;
            </a>
        </div>
    </div>
</section>

@endsection
