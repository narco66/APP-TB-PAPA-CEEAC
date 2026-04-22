@extends('layouts.app')

@section('title', 'Nouvel utilisateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
        <a href="{{ route('admin.utilisateurs.index') }}" class="hover:text-indigo-600">Utilisateurs</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-medium">Nouvel utilisateur</span>
    </nav>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">Nouvel utilisateur</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $scopeLabel }}</p>
        </div>

        <form method="POST" action="{{ route('admin.utilisateurs.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PrÃƒÂ©nom</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" maxlength="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmation <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                    <input type="text" name="matricule" value="{{ old('matricule') }}" maxlength="50"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">TÃƒÂ©lÃƒÂ©phone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" maxlength="30"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre / Grade</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" maxlength="100"
                           placeholder="Ex : M., Dr., Pr., etc."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                    <input type="text" name="fonction" value="{{ old('fonction') }}" maxlength="200"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                    <select name="direction_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Ã¢â‚¬â€ Non assignÃƒÂ© Ã¢â‚¬â€</option>
                        @foreach($directions as $dir)
                        <option value="{{ $dir->id }}" {{ old('direction_id') == $dir->id ? 'selected' : '' }}>
                            {{ $dir->code }} Ã¢â‚¬â€ {{ $dir->libelle }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RÃƒÂ´le <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror">
                        <option value="">Ã¢â‚¬â€ Choisir Ã¢â‚¬â€</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                        @endforeach
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="actif" value="1" id="actif" checked class="rounded">
                <label for="actif" class="text-sm text-gray-700">Compte actif</label>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('admin.utilisateurs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                    <i class="fas fa-save"></i> CrÃƒÂ©er le compte
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
