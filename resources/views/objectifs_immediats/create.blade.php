@extends('layouts.app')
@section('title', 'Nouvel objectif immÃ©diat')
@section('page-title', 'CrÃ©er un objectif immÃ©diat')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('objectifs-immediats.index') }}" class="hover:text-indigo-600">Objectifs immÃ©diats</a></li>
    @if($ap)
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('actions-prioritaires.show', $ap) }}" class="hover:text-indigo-600">{{ $ap->code }}</a></li>
    @endif
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Nouvel OI</li>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="mb-4 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <form action="{{ route('objectifs-immediats.store') }}" method="POST" class="space-y-5"
              x-data="oiForm()" x-init="init()">
            @csrf

            {{-- SÃ©lection PAPA + AP (affichÃ© si pas de contexte prÃ©-dÃ©fini) --}}
            @if(!$ap)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-4">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">
                    <i class="fas fa-link mr-1"></i> Rattachement institutionnel
                </p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        PAPA <span class="text-red-500">*</span>
                    </label>
                    <select x-model="papaId" @change="loadAPs()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- SÃ©lectionner un PAPA --</option>
                        @foreach($papas as $p)
                        <option value="{{ $p->id }}" {{ old('papa_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->code }} â€” {{ Str::limit($p->libelle, 60) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Action prioritaire <span class="text-red-500">*</span>
                    </label>
                    <select name="action_prioritaire_id" x-model="apId"
                            :disabled="!papaId"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-100 @error('action_prioritaire_id') border-red-500 @enderror">
                        <option value="">-- Choisir d'abord un PAPA --</option>
                        <template x-for="ap in aps" :key="ap.id">
                            <option :value="ap.id" x-text="ap.code + ' â€” ' + ap.libelle.substring(0, 60)"></option>
                        </template>
                    </select>
                    @error('action_prioritaire_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            @else
            <input type="hidden" name="action_prioritaire_id" value="{{ $ap->id }}">
            {{-- Contexte AP --}}
            <div class="bg-indigo-50 rounded-lg p-3 border border-indigo-100">
                <p class="text-xs text-indigo-600 font-medium mb-0.5">Action prioritaire parente</p>
                <p class="font-semibold text-indigo-900">{{ $ap->code }} â€” {{ Str::limit($ap->libelle, 100) }}</p>
                <p class="text-xs text-indigo-500 mt-0.5">PAPA : {{ $ap->papa?->code }}</p>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           placeholder="Ex : OI-DPS-01-01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre</label>
                    <input type="number" name="ordre" value="{{ old('ordre', 1) }}" min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    LibellÃ© <span class="text-red-500">*</span>
                </label>
                <input type="text" name="libelle" value="{{ old('libelle') }}"
                       placeholder="IntitulÃ© de l'objectif immÃ©diat"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('libelle') border-red-500 @enderror">
                @error('libelle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                <select name="responsable_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Non assignÃ© --</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('responsable_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->prenom }} {{ $u->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ $ap ? route('actions-prioritaires.show', $ap) : route('objectifs-immediats.index') }}"
                   class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-1"></i>CrÃ©er l'objectif immÃ©diat
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function oiForm() {
    return {
        papaId: '{{ old('papa_id', '') }}',
        apId: '{{ old('action_prioritaire_id', '') }}',
        aps: @json($actionsPrioritaires),

        init() {
            if (this.papaId) this.loadAPs();
        },

        loadAPs() {
            if (!this.papaId) { this.aps = []; this.apId = ''; return; }
            fetch(`/api/papa/${this.papaId}/actions-prioritaires`)
                .then(r => r.json())
                .then(data => { this.aps = data; this.apId = ''; })
                .catch(() => this.aps = []);
        }
    }
}
</script>
@endpush
@endsection
