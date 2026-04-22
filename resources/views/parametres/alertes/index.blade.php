@extends('layouts.app')

@section('title', 'Paramètres des alertes')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('parametres.hub') }}" class="hover:text-indigo-600">Paramètres</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Alertes &amp; Notifications</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ editRuleId: null, editRule: {} }">
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        <span class="font-semibold">Perimetre de donnees :</span> {{ $scopeLabel }}
    </div>

    {{-- En-tête --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-bell text-amber-600"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-800">Alertes &amp; Notifications</h1>
                <p class="text-sm text-gray-500">Seuils de déclenchement et règles de notification automatique</p>
            </div>
        </div>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center space-x-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Erreurs --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <p class="text-sm font-semibold text-red-800 mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Erreurs de validation</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li class="text-xs text-red-700">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Section 1 : Seuils d'alerte ────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-amber-50">
            <h2 class="text-sm font-semibold text-amber-800 flex items-center">
                <i class="fas fa-gauge-high mr-2 text-amber-500"></i>
                Seuils de déclenchement des alertes
            </h2>
            <p class="text-xs text-amber-600 mt-1">Ces seuils définissent quand le système génère une alerte automatique.</p>
        </div>
        <form action="{{ route('parametres.alertes.seuils.save') }}" method="POST">
            @csrf
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fas fa-clock text-amber-400 mr-1"></i>
                        Seuil retard (jours) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="alerte_seuil_retard_jours"
                               value="{{ old('alerte_seuil_retard_jours', $seuils['alerte_seuil_retard_jours'] ?? 7) }}"
                               min="1" max="90"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm pr-16 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 @error('alerte_seuil_retard_jours') border-red-400 @enderror">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">jours</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Une activité non démarrée sera signalée en alerte après ce délai.</p>
                    @error('alerte_seuil_retard_jours')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        <i class="fas fa-coins text-amber-400 mr-1"></i>
                        Seuil dépassement budgétaire (%) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="alerte_seuil_budget_pct"
                               value="{{ old('alerte_seuil_budget_pct', $seuils['alerte_seuil_budget_pct'] ?? 90) }}"
                               min="1" max="100"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm pr-8 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 @error('alerte_seuil_budget_pct') border-red-400 @enderror">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">%</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Alerte déclenchée lorsque le taux d'exécution budgétaire dépasse ce pourcentage.</p>
                    @error('alerte_seuil_budget_pct')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="px-6 pb-5 flex justify-end">
                @can('parametres.alertes.modifier')
                <button type="submit"
                        class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 transition flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Sauvegarder les seuils</span>
                </button>
                @endcan
            </div>
        </form>
    </div>

    {{-- ── Section 2 : Règles de notification ─────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-envelope-open-text text-indigo-400 mr-2"></i>
                        Règles de notification automatique
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $rules->count() }} règle(s) configurée(s)</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Événement</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Canal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rôle cible</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Délai</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rules as $rule)
                    <tr class="hover:bg-gray-50 transition {{ $rule->actif ? '' : 'opacity-60' }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800 text-xs">{{ $rule->libelle }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $rule->event_type }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $canalBadge = match($rule->canal) {
                                    'email'  => ['bg-blue-100 text-blue-700',   'fa-envelope'],
                                    'sms'    => ['bg-green-100 text-green-700', 'fa-mobile'],
                                    'app'    => ['bg-indigo-100 text-indigo-700','fa-bell'],
                                    default  => ['bg-gray-100 text-gray-600',   'fa-circle'],
                                };
                            @endphp
                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $canalBadge[0] }}">
                                <i class="fas {{ $canalBadge[1] }} text-xs"></i>
                                <span>{{ ucfirst($rule->canal) }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            {{ $rule->role_cible ?? $rule->permission_cible ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @if($rule->delai_minutes)
                                {{ $rule->delai_minutes >= 60
                                    ? round($rule->delai_minutes / 60) . 'h'
                                    : $rule->delai_minutes . 'min' }}
                            @else
                                <span class="text-gray-400">Immédiat</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @can('parametres.alertes.modifier')
                            <form action="{{ route('parametres.alertes.rules.toggle', $rule) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition
                                            {{ $rule->actif
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    <i class="fas {{ $rule->actif ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-1"></i>
                                    {{ $rule->actif ? 'Actif' : 'Inactif' }}
                                </button>
                            </form>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $rule->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $rule->actif ? 'Actif' : 'Inactif' }}
                            </span>
                            @endcan
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('parametres.alertes.modifier')
                            <button type="button"
                                    @click="editRuleId = {{ $rule->id }}; editRule = {
                                        id: {{ $rule->id }},
                                        libelle: {{ json_encode($rule->libelle) }},
                                        delai_minutes: {{ $rule->delai_minutes ?? 'null' }},
                                        template_sujet: {{ json_encode($rule->template_sujet ?? '') }},
                                        template_message: {{ json_encode($rule->template_message ?? '') }},
                                        actif: {{ $rule->actif ? 'true' : 'false' }}
                                    }"
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition">
                                <i class="fas fa-pen-to-square mr-1"></i>Modifier
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">
                            <i class="fas fa-bell-slash text-2xl mb-2 block"></i>
                            Aucune règle de notification configurée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Modal édition règle ─────────────────────────────────────────── --}}
    <div x-show="editRuleId !== null"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg"
             @click.outside="editRuleId = null">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 flex items-center">
                    <i class="fas fa-bell text-amber-500 mr-2"></i>
                    Modifier la règle de notification
                </h3>
                <button @click="editRuleId = null" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <template x-if="editRuleId">
                <form :action="`{{ url('parametres/alertes/rules') }}/${editRuleId}`" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Libellé <span class="text-red-500">*</span></label>
                            <input type="text" name="libelle" x-model="editRule.libelle"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400"
                                   required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Délai (minutes)</label>
                                <input type="number" name="delai_minutes" x-model="editRule.delai_minutes"
                                       min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400"
                                       placeholder="0 = immédiat">
                            </div>
                            <div class="flex items-end pb-1">
                                <label class="inline-flex items-center space-x-2 cursor-pointer">
                                    <input type="hidden" name="actif" value="0">
                                    <input type="checkbox" name="actif" value="1"
                                           x-bind:checked="editRule.actif"
                                           class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                                    <span class="text-xs font-semibold text-gray-600">Règle active</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sujet du message</label>
                            <input type="text" name="template_sujet" x-model="editRule.template_sujet"
                                   maxlength="300"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400"
                                   placeholder="Sujet email ou titre de notification…">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Corps du message</label>
                            <textarea name="template_message" x-model="editRule.template_message"
                                      rows="4" maxlength="2000"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 resize-y"
                                      placeholder="Contenu du message. Vous pouvez utiliser des variables comme {nom}, {date}…"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Variables disponibles : <code class="bg-gray-100 px-1 rounded">{nom}</code> <code class="bg-gray-100 px-1 rounded">{date}</code> <code class="bg-gray-100 px-1 rounded">{module}</code></p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-100">
                        <button type="button" @click="editRuleId = null"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 transition">
                            <i class="fas fa-save mr-1.5"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
