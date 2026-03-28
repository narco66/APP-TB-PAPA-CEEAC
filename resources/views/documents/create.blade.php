@extends('layouts.app')
@section('title', 'Déposer un document')
@section('page-title', 'Déposer un document')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('documents.index') }}" class="hover:text-indigo-600">Documents</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Déposer</li>
@endsection

@section('content')
<div class="max-w-3xl" x-data="{
    fichierNom: '',
    fichierTaille: '',
    handleFile(e) {
        const f = e.target.files[0];
        if (f) {
            this.fichierNom = f.name;
            const mb = (f.size / 1048576).toFixed(2);
            this.fichierTaille = mb + ' Mo';
        }
    }
}">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-upload text-indigo-500 mr-2"></i>Dépôt en GED
        </h2>

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Informations passées en paramètre (rattachement) -->
            @if(request('documentable_type') && request('documentable_id'))
            <input type="hidden" name="documentable_type" value="{{ request('documentable_type') }}">
            <input type="hidden" name="documentable_id" value="{{ request('documentable_id') }}">
            <div class="bg-indigo-50 rounded-lg p-3 text-sm text-indigo-700">
                <i class="fas fa-link mr-1"></i>
                Ce document sera rattaché à :
                <strong>{{ class_basename(request('documentable_type')) }} #{{ request('documentable_id') }}</strong>
            </div>
            @endif

            <!-- Zone de dépôt fichier -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fichier <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-indigo-400 transition cursor-pointer"
                     onclick="document.getElementById('fichier-input').click()">
                    <input type="file" id="fichier-input" name="fichier"
                           @change="handleFile"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                           class="sr-only">
                    <div x-show="!fichierNom">
                        <i class="fas fa-cloud-upload-alt text-gray-300 text-4xl mb-3"></i>
                        <p class="text-sm text-gray-500">Cliquez ou glissez un fichier ici</p>
                        <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel, PowerPoint, Images, ZIP — max 50 Mo</p>
                    </div>
                    <div x-show="fichierNom" class="text-left">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file text-indigo-500 text-2xl"></i>
                            <div>
                                <p class="font-medium text-gray-700" x-text="fichierNom"></p>
                                <p class="text-xs text-gray-400" x-text="fichierTaille"></p>
                            </div>
                            <i class="fas fa-check-circle text-green-500 ml-auto"></i>
                        </div>
                    </div>
                </div>
                @error('fichier')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Titre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Titre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="titre" value="{{ old('titre') }}"
                       placeholder="Titre du document"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('titre') border-red-500 @enderror">
                @error('titre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catégorie <span class="text-red-500">*</span>
                    </label>
                    <select name="categorie_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('categorie_id') border-red-500 @enderror">
                        <option value="">-- Catégorie --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->libelle }}
                        </option>
                        @endforeach
                    </select>
                    @error('categorie_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
                    <input type="text" name="reference" value="{{ old('reference') }}"
                           placeholder="Numéro ou référence interne"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date du document</label>
                    <input type="date" name="date_document" value="{{ old('date_document') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confidentialité <span class="text-red-500">*</span>
                    </label>
                    <select name="confidentialite"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('confidentialite') border-red-500 @enderror">
                        <option value="public"                   {{ old('confidentialite', 'interne') === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="interne"                  {{ old('confidentialite', 'interne') === 'interne' ? 'selected' : '' }}>Interne</option>
                        <option value="confidentiel"             {{ old('confidentialite') === 'confidentiel' ? 'selected' : '' }}>Confidentiel</option>
                        <option value="strictement_confidentiel" {{ old('confidentialite') === 'strictement_confidentiel' ? 'selected' : '' }}>Strictement confidentiel</option>
                    </select>
                    @error('confidentialite')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                          placeholder="Description, contexte..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="p-3 bg-blue-50 rounded-lg text-xs text-blue-600">
                <i class="fas fa-shield-alt mr-1"></i>
                Un hash SHA-256 sera automatiquement calculé pour garantir l'intégrité du document.
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('documents.index') }}"
                   class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-upload mr-1"></i>Déposer le document
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
