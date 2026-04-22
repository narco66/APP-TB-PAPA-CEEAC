@extends('layouts.app')

@section('title', 'Modifier ' . $user->nomComplet())

@section('content')
<div class="max-w-2xl mx-auto">
    <nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
        <a href="{{ route('admin.utilisateurs.index') }}" class="hover:text-indigo-600">Utilisateurs</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-medium">{{ $user->nomComplet() }}</span>
    </nav>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $user->nomComplet() }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
                <p class="text-sm text-indigo-700 mt-1">{{ $scopeLabel }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($user->actif)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Actif</span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">DÃƒÂ©sactivÃƒÂ©</span>
                @endif
                @foreach($user->roles as $role)
                <span class="inline-block bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded font-medium">
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                </span>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('admin.utilisateurs.update', $user) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PrÃƒÂ©nom</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" maxlength="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nouveau mot de passe <span class="text-gray-400 font-normal">(laisser vide pour conserver)</span>
                    </label>
                    <input type="password" name="password" minlength="8"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmation</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                    <input type="text" name="matricule" value="{{ old('matricule', $user->matricule) }}" maxlength="50"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">TÃƒÂ©lÃƒÂ©phone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" maxlength="30"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre / Grade</label>
                    <input type="text" name="titre" value="{{ old('titre', $user->titre) }}" maxlength="100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                    <input type="text" name="fonction" value="{{ old('fonction', $user->fonction) }}" maxlength="200"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                    <select name="direction_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Ã¢â‚¬â€ Non assignÃƒÂ© Ã¢â‚¬â€</option>
                        @foreach($directions as $dir)
                        <option value="{{ $dir->id }}" {{ old('direction_id', $user->direction_id) == $dir->id ? 'selected' : '' }}>
                            {{ $dir->code }} Ã¢â‚¬â€ {{ $dir->libelle }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RÃƒÂ´le <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($user->id !== auth()->id())
            <div class="flex items-center gap-2">
                <input type="checkbox" name="actif" value="1" id="actif" {{ old('actif', $user->actif) ? 'checked' : '' }} class="rounded">
                <label for="actif" class="text-sm text-gray-700">Compte actif</label>
            </div>
            @endif

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('admin.utilisateurs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                    <i class="fas fa-save"></i> Mettre ÃƒÂ  jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
