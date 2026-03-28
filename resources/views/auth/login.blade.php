<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — TB-PAPA-CEEAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        <!-- Logo / En-tête CEEAC -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-xl mb-4">
                <!-- Emblème CEEAC (placeholder) -->
                <div class="text-indigo-700 text-3xl font-black">C</div>
            </div>
            <h1 class="text-white text-2xl font-bold tracking-tight">TB-PAPA-CEEAC</h1>
            <p class="text-indigo-300 text-sm mt-1">
                Tableau de Bord — Plan d'Action Prioritaire Annuel
            </p>
            <p class="text-slate-400 text-xs mt-0.5">
                Commission de la CEEAC — Secrétariat Général
            </p>
        </div>

        <!-- Carte de connexion -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-lg font-bold text-gray-800 mb-1">Connexion</h2>
            <p class="text-sm text-gray-500 mb-6">Accès réservé aux agents autorisés</p>

            <!-- Message d'erreur global -->
            @if(session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                {{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                <i class="fas fa-exclamation-circle mr-1"></i>
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Adresse e-mail
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               autofocus
                               placeholder="votre.email@ceeac-eccas.org"
                               class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                      @error('email') border-red-500 bg-red-50 @enderror">
                    </div>
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="password" name="password"
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                      @error('password') border-red-500 @enderror">
                    </div>
                </div>

                <!-- Se souvenir -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember"
                               {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-600">
                        <span class="text-sm text-gray-600">Se souvenir de moi</span>
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                        Mot de passe oublié ?
                    </a>
                    @endif
                </div>

                <button type="submit"
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg
                               text-sm font-semibold transition focus:ring-4 focus:ring-indigo-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-xs text-slate-500">
            <p>Usage strictement réservé au personnel autorisé de la CEEAC</p>
            <p class="mt-1">Toute tentative d'accès non autorisé est journalisée et punissable.</p>
        </div>

        <div class="text-center mt-4">
            <p class="text-xs text-slate-600">
                TB-PAPA-CEEAC v1.0 &bull; {{ date('Y') }} Commission de la CEEAC
            </p>
        </div>
    </div>

</body>
</html>
