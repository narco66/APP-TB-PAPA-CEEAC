@extends('layouts.app')
@section('title', 'Diagramme Gantt — Activités')
@section('page-title', 'Diagramme Gantt — Activités')

@push('styles')
{{-- dhtmlx Gantt 9.1.3 — asset local (évite blocage CDN) --}}
<link rel="stylesheet" href="{{ asset('vendor/gantt/dhtmlxgantt.css') }}">
<style>
/* ── Conteneur ─────────────────────────────────────── */
#gantt_here {
    width: 100%;
    height: calc(100vh - 290px);
    min-height: 460px;
    border-radius: 8px;
    overflow: hidden;
}

/* ── Barres ────────────────────────────────────────── */
.gantt_task_line          { border-radius: 4px !important; }

/* ── Groupes (ResultatAttendu) ─────────────────────── */
.gantt_task_line.gantt_project {
    background: #e0e7ff !important;
    border: 1px solid #818cf8 !important;
    border-radius: 4px !important;
}
.gantt_task_line.gantt_project .gantt_task_content {
    color: #3730a3 !important;
    font-weight: 600 !important;
    font-size: 11px !important;
}

/* ── Jalons ────────────────────────────────────────── */
.gantt_task_line.gantt_milestone {
    transform: rotate(45deg);
    border-radius: 2px !important;
}

/* ── Couche baseline (dates réelles) ──────────────── */
.gantt-baseline-bar {
    position: absolute;
    height: 6px;
    border-radius: 3px;
    background: rgba(34, 197, 94, 0.55);
    border: 1px solid #15803d;
    pointer-events: none;
}

/* ── Icônes dans le texte de la tâche ──────────────── */
.gantt-alerte-icon { color: #fbbf24; margin-left: 4px; font-size: 10px; }
.gantt-ged-icon    { color: #60a5fa; margin-left: 4px; font-size: 10px; }

/* ── Chemin critique ───────────────────────────────── */
.on-critical-path .gantt_task_line {
    box-shadow: 0 0 0 2px #dc2626 !important;
}
/* Masquer les non-critiques quand le mode est actif */
.gantt-cpm-mode .gantt_task_line:not(.cpm-task) {
    opacity: 0.2 !important;
}
.gantt-cpm-mode .gantt_row:not(.cpm-row) .gantt_cell {
    opacity: 0.35;
}

/* ── Marqueur aujourd'hui ──────────────────────────── */
.today_marker { background: rgba(239, 68, 68, 0.6); }

/* ── Tooltip ───────────────────────────────────────── */
.gantt_tooltip {
    padding: 12px 14px;
    background: #1e293b;
    color: #f1f5f9;
    border-radius: 8px;
    font-size: 12px;
    line-height: 1.65;
    max-width: 340px;
    box-shadow: 0 10px 30px rgba(0,0,0,.4);
    border: none !important;
}
.gantt_tooltip hr       { border-color: #334155; margin: 6px 0; }
.gantt_tooltip .tl      { color: #94a3b8; }
.gantt_tooltip .tal     { color: #fbbf24; font-weight: 600; }
.gantt_tooltip .tged    { color: #60a5fa; }
.gantt_tooltip .tlink   { display: inline-block; margin-top: 6px; color: #818cf8; text-decoration: underline; cursor: pointer; }

/* ── Loading overlay ───────────────────────────────── */
#gantt_loading {
    position: absolute; inset: 0;
    background: rgba(255,255,255,.85);
    display: flex; align-items: center; justify-content: center;
    z-index: 50; border-radius: 8px;
}

/* ── Panneau latéral ───────────────────────────────── */
#detail-panel {
    width: 420px;
    transition: transform .3s ease;
}
</style>
@endpush

@section('content')
<div class="space-y-3"
     x-data="ganttApp()"
     x-init="init()"
     @keydown.escape.window="panel.open = false">

    {{-- ── Bandeau périmètre ──────────────────────────────── --}}
    <div class="flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-100 rounded-lg text-sm text-indigo-700">
        <i class="fas fa-shield-alt text-indigo-400"></i>
        <span class="font-medium">{{ $scopeLabel }}</span>
        <span x-show="totalTaches >= 0" class="ml-auto text-indigo-400 text-xs"
              x-text="totalTaches + ' activité(s) affichée(s)'"></span>
    </div>

    {{-- ── Barre d'outils ─────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Filtres --}}
        <button @click="showFilters = !showFilters"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-filter text-gray-400"></i> Filtres
            <span x-show="activeFilterCount > 0"
                  x-text="'(' + activeFilterCount + ')'"
                  class="bg-indigo-600 text-white text-xs rounded-full px-1.5 py-0.5"></span>
        </button>

        {{-- Zoom --}}
        <div class="flex items-center gap-0.5 bg-white border border-gray-200 rounded-lg p-0.5">
            @foreach(['day' => 'Jour', 'week' => 'Semaine', 'month' => 'Mois', 'quarter' => 'Trimestre'] as $key => $label)
            <button @click="setScale('{{ $key }}')"
                    :class="scale === '{{ $key }}' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    class="px-2.5 py-1 text-xs rounded-md transition">{{ $label }}</button>
            @endforeach
        </div>

        {{-- Chemin critique --}}
        <button @click="toggleCriticalPath()"
                :class="showCriticalPath ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 border-gray-200'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs border rounded-lg hover:opacity-90 transition"
                title="Mettre en évidence le chemin critique">
            <i class="fas fa-route"></i> Chemin critique
        </button>

        {{-- Baseline --}}
        <button @click="toggleBaseline()"
                :class="showBaseline ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-200'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs border rounded-lg hover:opacity-90 transition"
                title="Afficher/masquer les barres de dates réelles">
            <i class="fas fa-layer-group"></i> Baseline
        </button>

        {{-- Replier / Déplier --}}
        <button @click="gantt.eachTask(t => gantt.close(t.id))"
                class="px-2.5 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                title="Replier tout"><i class="fas fa-compress-alt"></i></button>
        <button @click="gantt.eachTask(t => gantt.open(t.id))"
                class="px-2.5 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                title="Déplier tout"><i class="fas fa-expand-alt"></i></button>

        {{-- Aujourd'hui --}}
        <button @click="gantt.showDate(new Date())"
                class="px-2.5 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                title="Aller à aujourd'hui"><i class="fas fa-crosshairs"></i></button>

        {{-- Plein écran --}}
        <button @click="toggleFullscreen()"
                class="px-2.5 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition"
                title="Plein écran"><i class="fas fa-expand"></i></button>

        {{-- Exports --}}
        <div class="relative" x-data="{ exportOpen: false }">
            <button @click="exportOpen = !exportOpen"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-600">
                <i class="fas fa-download"></i> Exporter <i class="fas fa-chevron-down text-[10px]"></i>
            </button>
            <div x-show="exportOpen" @click.outside="exportOpen = false"
                 x-transition
                 class="absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-xl shadow-lg z-30 py-1">
                <a :href="exportUrl('excel')"
                   class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50"
                   @click="exportOpen = false">
                    <i class="fas fa-file-excel text-green-600 w-4"></i> Excel (.xlsx)
                </a>
                <a :href="exportUrl('pdf')"
                   class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50"
                   @click="exportOpen = false">
                    <i class="fas fa-file-pdf text-red-600 w-4"></i> PDF (A4 paysage)
                </a>
            </div>
        </div>

        <a href="{{ route('activites.index') }}"
           aria-label="Vue liste des activités"
           class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-indigo-600 bg-white border border-indigo-200 rounded-lg hover:bg-indigo-50 transition">
            <i class="fas fa-list"></i> Vue liste
        </a>
    </div>

    {{-- ── Filtres ─────────────────────────────────────────── --}}
    <div x-show="showFilters" x-transition class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Statut</label>
                <select x-model="filters.statut" @change="loadData()"
                        class="w-full text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous</option>
                    <option value="non_demarree">Non démarrée</option>
                    <option value="planifiee">Planifiée</option>
                    <option value="en_cours">En cours</option>
                    <option value="suspendue">Suspendue</option>
                    <option value="terminee">Terminée</option>
                    <option value="abandonnee">Abandonnée</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Priorité</label>
                <select x-model="filters.priorite" @change="loadData()"
                        class="w-full text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes</option>
                    <option value="critique">Critique</option>
                    <option value="haute">Haute</option>
                    <option value="normale">Normale</option>
                    <option value="basse">Basse</option>
                </select>
            </div>

            @if($directions->isNotEmpty())
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Direction</label>
                <select x-model="filters.direction_id" @change="loadData()"
                        class="w-full text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes</option>
                    @foreach($directions as $dir)
                        <option value="{{ $dir->id }}">{{ $dir->libelle }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Période</label>
                <div class="flex gap-1">
                    <input type="date" x-model="filters.date_from" @change="loadData()"
                           class="w-full text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="date" x-model="filters.date_to" @change="loadData()"
                           class="w-full text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <button @click="resetFilters()"
                    class="text-xs text-gray-500 hover:text-gray-700 underline">
                Réinitialiser
            </button>
        </div>
    </div>

    {{-- ── Gantt + Panneau latéral ─────────────────────────── --}}
    <div class="flex gap-3">

        {{-- Gantt --}}
        <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 p-3 min-w-0">

            {{-- État vide --}}
            <div x-show="!loading && errorMessage"
                 class="flex flex-col items-center justify-center py-16 text-center text-red-500">
                <i class="fas fa-triangle-exclamation text-4xl mb-3 text-red-300"></i>
                <p class="text-sm font-medium" x-text="errorMessage"></p>
                <p class="text-xs mt-1 text-gray-400">Vérifiez aussi l’accès aux scripts CDN du diagramme.</p>
            </div>

            <div x-show="!loading && totalTaches === 0"
                 class="flex flex-col items-center justify-center py-16 text-gray-400">
                <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                <p class="text-sm font-medium text-gray-500">Aucune activité à afficher</p>
                <p class="text-xs mt-1">Modifiez les filtres ou élargissez la période.</p>
            </div>

            <div class="relative" x-show="(!errorMessage && totalTaches !== 0) || loading">
                <div id="gantt_loading" x-show="loading">
                    <div class="flex flex-col items-center gap-2 text-gray-500">
                        <svg class="animate-spin h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        <span class="text-xs">Chargement du planning…</span>
                    </div>
                </div>
                <div id="gantt_here"
                     role="region"
                     aria-label="Diagramme de Gantt des activités PAPA"></div>
            </div>
        </div>

        {{-- ── Panneau latéral de détail ──────────────────── --}}
        <div id="detail-panel"
             x-show="panel.open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden shrink-0 flex flex-col">

            {{-- En-tête panneau --}}
            <div class="sticky top-0 bg-white border-b border-gray-100 px-4 py-3 flex items-center justify-between z-10">
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-400 font-mono" x-text="panel.data?.code ?? ''"></p>
                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="panel.data?.libelle ?? 'Chargement…'"></p>
                </div>
                <button @click="panel.open = false"
                        class="ml-2 text-gray-400 hover:text-gray-600 transition"
                        aria-label="Fermer le panneau">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Loading --}}
            <div x-show="panel.loading" class="flex items-center justify-center py-12">
                <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </div>

            {{-- Contenu --}}
            <div x-show="!panel.loading && panel.data" class="flex-1 overflow-y-auto p-4 space-y-4 text-sm">

                {{-- Badges statut + priorité --}}
                <div class="flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                          :class="badgeStatutClass(panel.data?.statut)"
                          x-text="labelStatut(panel.data?.statut)"></span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                          :class="badgePrioriteClass(panel.data?.priorite)"
                          x-text="labelPriorite(panel.data?.priorite)"></span>
                    <span x-show="panel.data?.est_retard"
                          class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-600 text-white animate-pulse">
                        EN RETARD
                    </span>
                    <span x-show="panel.data?.est_jalon"
                          class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                        JALON
                    </span>
                </div>

                {{-- Avancement --}}
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Avancement</span>
                        <span class="font-semibold text-gray-700"
                              x-text="(panel.data?.taux_realisation ?? 0).toFixed(0) + '%'"></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                             :style="'width:' + Math.min(100, panel.data?.taux_realisation ?? 0) + '%;background:' + progressColor(panel.data?.taux_realisation)">
                        </div>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-gray-50 rounded-lg p-2.5">
                        <p class="text-xs text-gray-400">Début prévu</p>
                        <p class="font-medium text-gray-700 text-xs mt-0.5" x-text="panel.data?.date_debut_prevue ?? '—'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2.5">
                        <p class="text-xs text-gray-400">Fin prévue</p>
                        <p class="font-medium text-xs mt-0.5"
                           :class="panel.data?.est_retard ? 'text-red-600' : 'text-gray-700'"
                           x-text="panel.data?.date_fin_prevue ?? '—'"></p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-2.5">
                        <p class="text-xs text-gray-400">Début réel</p>
                        <p class="font-medium text-green-700 text-xs mt-0.5" x-text="panel.data?.date_debut_reelle ?? '—'"></p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-2.5">
                        <p class="text-xs text-gray-400">Fin réelle</p>
                        <p class="font-medium text-green-700 text-xs mt-0.5" x-text="panel.data?.date_fin_reelle ?? '—'"></p>
                    </div>
                </div>

                {{-- Budget --}}
                <div class="border border-gray-100 rounded-lg p-3 space-y-1.5">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Budget</p>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Prévu</span>
                        <span class="font-medium" x-text="fmtBudget(panel.data?.budget_prevu, panel.data?.devise)"></span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Engagé</span>
                        <span class="font-medium" x-text="fmtBudget(panel.data?.budget_engage, panel.data?.devise)"></span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Consommé</span>
                        <span class="font-medium"
                              :class="budgetAlertClass(panel.data)"
                              x-text="fmtBudget(panel.data?.budget_consomme, panel.data?.devise) + ' (' + budgetPct(panel.data) + ')'"></span>
                    </div>
                </div>

                {{-- Responsabilités --}}
                <div class="space-y-1 text-xs">
                    <p class="font-semibold text-gray-600 uppercase tracking-wide text-xs">Responsabilités</p>
                    <div class="flex items-center gap-1.5 text-gray-600">
                        <i class="fas fa-building w-4 text-gray-400"></i>
                        <span x-text="panel.data?.direction ?? '—'"></span>
                    </div>
                    <div class="flex items-center gap-1.5 text-gray-600" x-show="panel.data?.service">
                        <i class="fas fa-layer-group w-4 text-gray-400"></i>
                        <span x-text="panel.data?.service"></span>
                    </div>
                    <div class="flex items-center gap-1.5 text-gray-600">
                        <i class="fas fa-user w-4 text-gray-400"></i>
                        <span x-text="panel.data?.responsable ?? '—'"></span>
                    </div>
                    <div class="flex items-center gap-1.5 text-gray-600" x-show="panel.data?.point_focal">
                        <i class="fas fa-map-pin w-4 text-gray-400"></i>
                        <span x-text="'PF : ' + panel.data?.point_focal"></span>
                    </div>
                </div>

                {{-- Chaîne RBM --}}
                <div class="border border-indigo-100 bg-indigo-50 rounded-lg p-3 space-y-1.5">
                    <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wide">Chaîne RBM</p>
                    <div x-show="panel.data?.rbm?.action_prioritaire" class="text-xs text-indigo-600">
                        <i class="fas fa-flag w-4"></i>
                        <span x-text="panel.data?.rbm?.action_prioritaire"></span>
                    </div>
                    <div x-show="panel.data?.rbm?.objectif_immediat" class="text-xs text-indigo-600 pl-4">
                        <i class="fas fa-bullseye w-4"></i>
                        <span x-text="panel.data?.rbm?.objectif_immediat"></span>
                    </div>
                    <div x-show="panel.data?.rbm?.resultat_attendu" class="text-xs text-indigo-600 pl-8">
                        <i class="fas fa-check-circle w-4"></i>
                        <span x-text="panel.data?.rbm?.resultat_attendu"></span>
                    </div>
                </div>

                {{-- Alertes --}}
                <div x-show="panel.data?.alertes?.length > 0">
                    <p class="text-xs font-semibold text-yellow-700 uppercase tracking-wide mb-1.5">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Alertes actives
                    </p>
                    <template x-for="alerte in (panel.data?.alertes ?? [])" :key="alerte.titre">
                        <div class="border border-yellow-200 bg-yellow-50 rounded p-2 mb-1.5">
                            <p class="text-xs font-medium text-yellow-800" x-text="alerte.titre"></p>
                            <p class="text-xs text-yellow-700 mt-0.5" x-text="alerte.message"></p>
                        </div>
                    </template>
                </div>

                {{-- Documents GED --}}
                <div x-show="panel.data?.documents?.length > 0">
                    <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1.5">
                        <i class="fas fa-paperclip mr-1"></i>Documents GED
                    </p>
                    <template x-for="doc in (panel.data?.documents ?? [])" :key="doc.nom">
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 mb-1">
                            <i class="fas fa-file text-blue-400"></i>
                            <span x-text="doc.nom"></span>
                            <span class="text-gray-400 uppercase" x-text="doc.extension ? '.' + doc.extension : ''"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Pied panneau --}}
            <div x-show="!panel.loading && panel.data"
                 class="sticky bottom-0 bg-white border-t border-gray-100 p-3">
                <a :href="panel.data?.url_detail"
                   target="_blank"
                   class="block w-full text-center px-4 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-external-link-alt mr-1"></i> Ouvrir la fiche complète
                </a>
            </div>
        </div>
    </div>

    {{-- ── Légende ─────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 px-1 text-xs text-gray-600">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#6366f1"></span>Planifiée</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#3b82f6"></span>En cours</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#ef4444"></span>En retard</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#dc2626"></span>Critique</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#7f1d1d"></span>Retard+Critique</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#8b5cf6"></span>Priorité haute</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#f59e0b"></span>Suspendue</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#22c55e"></span>Terminée</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded" style="background:#9ca3af"></span>Abandonnée</span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-1.5 rounded" style="background:rgba(34,197,94,.55);border:1px solid #15803d"></span>
            Baseline (dates réelles)
        </span>
        <span class="flex items-center gap-1.5"><i class="fas fa-route text-red-600"></i>Chemin critique</span>
        <span class="flex items-center gap-1.5"><i class="fas fa-exclamation-triangle text-yellow-400"></i>Alerte</span>
        <span class="flex items-center gap-1.5"><i class="fas fa-paperclip text-blue-400"></i>GED</span>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/gantt/dhtmlxgantt.js') }}"></script>
<script>
function ganttApp() {
    return {
        // ── État global ───────────────────────────────────────
        loading:          true,
        errorMessage:     '',
        showFilters:      false,
        showBaseline:     true,
        showCriticalPath: false,
        totalTaches:      -1,
        scale:            'month',
        baselineLayerId:  null,

        filters: {
            statut: '', priorite: '', direction_id: '', date_from: '', date_to: '',
        },

        panel: {
            open:    false,
            loading: false,
            data:    null,
        },

        get activeFilterCount() {
            return Object.values(this.filters).filter(v => v !== '').length;
        },

        // ── Initialisation ─────────────────────────────────────
        init() {
            try {
                if (typeof window.gantt === 'undefined') {
                    throw new Error('La librairie Gantt n’a pas été chargée.');
                }

                this.configureGantt();
                this.addBaselineLayer();
                this.loadData();
            } catch (error) {
                console.error('Gantt init error:', error);
                this.errorMessage = 'Impossible de charger le planning.';
                this.loading = false;
                this.totalTaches = 0;
            }
        },

        // ── Configuration dhtmlx ───────────────────────────────
        configureGantt() {
            if (gantt.i18n?.locales?.fr) {
                gantt.i18n.setLocale('fr');
            }

            gantt.config.date_format    = '%d-%m-%Y';
            gantt.config.readonly       = true;
            gantt.config.drag_links     = false;
            gantt.config.drag_move      = false;
            gantt.config.drag_resize    = false;
            gantt.config.show_today_marker = typeof gantt.addMarker === 'function';

            // Marqueur "Aujourd'hui"
            if (typeof gantt.addMarker === 'function') {
                gantt.addMarker({ start_date: new Date(), css: 'today_marker', title: "Aujourd'hui", text: "▼" });
            }

            // Colonnes
            gantt.config.columns = [
                {
                    name: 'text', label: 'Activité / Résultat', width: 250, tree: true,
                    template(task) {
                        if (task.is_group) return task.text;
                        let icons = '';
                        if (task.has_alerte)    icons += '<i class="fas fa-exclamation-triangle gantt-alerte-icon" title="' + task.nb_alertes + ' alerte(s)"></i>';
                        if (task.has_documents) icons += '<i class="fas fa-paperclip gantt-ged-icon" title="' + task.nb_documents + ' document(s)"></i>';
                        return task.text + icons;
                    }
                },
                {
                    name: 'statut_badge', label: 'Statut', width: 82, align: 'center',
                    template(task) {
                        if (task.is_group) return '';
                        const cfg = {
                            non_demarree: ['#e5e7eb','#374151','Non démarrée'],
                            planifiee:    ['#dbeafe','#1d4ed8','Planifiée'],
                            en_cours:     ['#e0e7ff','#4338ca','En cours'],
                            suspendue:    ['#fef3c7','#92400e','Suspendue'],
                            terminee:     ['#dcfce7','#15803d','Terminée'],
                            abandonnee:   ['#fee2e2','#991b1b','Abandonnée'],
                        };
                        const [bg, tc, lbl] = cfg[task.statut] ?? ['#e5e7eb','#374151', task.statut];
                        return `<span style="background:${bg};color:${tc};padding:1px 6px;border-radius:9999px;font-size:10px;font-weight:600;">${lbl}</span>`;
                    }
                },
                { name: 'start_date', label: 'Début prévu',  width: 85, align: 'center' },
                { name: 'end_date',   label: 'Fin prévue',   width: 85, align: 'center' },
                {
                    name: 'progress', label: '%', width: 46, align: 'center',
                    template: task => task.is_group
                        ? Math.round(task.progress * 100) + '%'
                        : Math.round(task.progress * 100) + '%'
                },
                {
                    name: 'responsable', label: 'Responsable', width: 110,
                    template: task => task.is_group ? '' : (task.responsable ?? '—')
                },
            ];

            // Échelle initiale
            this.applyScale('month');

            // Tooltip
            gantt.templates.tooltip_text = (start, end, task) => {
                if (task.is_group)          return this.buildTooltipGroup(task);
                if (task.type === 'milestone') return this.buildTooltipJalon(task);
                return this.buildTooltip(task);
            };

            // Classes CSS dynamiques sur les barres
            gantt.templates.task_class = (start, end, task) => {
                if (task.is_group) return '';
                return task.est_chemin_critique ? 'cpm-task' : '';
            };
            gantt.templates.grid_row_class = (start, end, task) => {
                if (task.is_group) return '';
                return task.est_chemin_critique ? 'cpm-row' : '';
            };

            // Clic sur une barre → panneau latéral
            gantt.attachEvent('onTaskClick', (id, e) => {
                const task = gantt.getTask(id);
                if (!task || task.is_group) return true;
                this.openPanel(task.id);
                return false; // bloque le lightbox dhtmlx
            });

            gantt.init('gantt_here');
        },

        // ── Couche baseline (dates réelles) ───────────────────
        addBaselineLayer() {
            if (typeof gantt.addTaskLayer !== 'function') {
                this.showBaseline = false;
                return;
            }

            this.baselineLayerId = gantt.addTaskLayer((task) => {
                if (!this.showBaseline) return false;
                if (task.is_group || task.type === 'milestone') return false;
                if (!task.date_debut_reelle || !task.date_fin_reelle) return false;

                const start = gantt.date.parseDate(task.date_debut_reelle, '%d-%m-%Y');
                const end   = gantt.date.parseDate(task.date_fin_reelle,   '%d-%m-%Y');
                if (!start || !end) return false;

                const sizes = gantt.getTaskPosition(task, start, end);

                const el = document.createElement('div');
                el.className = 'gantt-baseline-bar';
                el.title     = 'Réel : ' + task.date_debut_reelle + ' → ' + task.date_fin_reelle;
                el.style.cssText = [
                    'left:'   + sizes.left + 'px',
                    'width:'  + Math.max(4, sizes.width) + 'px',
                    'top:'    + (sizes.top + sizes.height * 0.68) + 'px',
                ].join(';');

                return el;
            });
        },

        toggleBaseline() {
            if (typeof gantt.addTaskLayer !== 'function') {
                return;
            }

            this.showBaseline = !this.showBaseline;
            gantt.render();
        },

        toggleCriticalPath() {
            this.showCriticalPath = !this.showCriticalPath;
            const container = document.getElementById('gantt_here');
            if (this.showCriticalPath) {
                container.classList.add('gantt-cpm-mode');
            } else {
                container.classList.remove('gantt-cpm-mode');
            }
            gantt.render();
        },

        // ── Échelles de temps ──────────────────────────────────
        applyScale(scaleKey) {
            const scales = {
                day: [
                    { unit: 'month', step: 1, format: '%F %Y' },
                    { unit: 'day',   step: 1, format: '%j' },
                ],
                week: [
                    { unit: 'month', step: 1, format: '%F %Y' },
                    { unit: 'week',  step: 1, format: 'Sem %W' },
                ],
                month: [
                    { unit: 'year',  step: 1, format: '%Y' },
                    { unit: 'month', step: 1, format: '%M' },
                ],
                quarter: [
                    { unit: 'year', step: 1, format: '%Y' },
                    {
                        unit: 'quarter', step: 1,
                        format(d) {
                            return 'T' + (Math.floor(d.getMonth() / 3) + 1);
                        }
                    },
                ],
            };
            gantt.config.scales = scales[scaleKey] ?? scales.month;
        },

        setScale(key) {
            this.scale = key;
            this.applyScale(key);
            gantt.render();
        },

        // ── Chargement AJAX ────────────────────────────────────
        loadData() {
            this.loading = true;
            this.errorMessage = '';
            this.panel.open = false;

            const params = new URLSearchParams();
            if (this.filters.statut)       params.append('statut[]', this.filters.statut);
            if (this.filters.priorite)     params.append('priorite', this.filters.priorite);
            if (this.filters.direction_id) params.append('direction_id', this.filters.direction_id);
            if (this.filters.date_from)    params.append('date_from', this.filters.date_from);
            if (this.filters.date_to)      params.append('date_to', this.filters.date_to);

            fetch('{{ route('gantt.data') }}?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(data => {
                gantt.clearAll();
                gantt.parse({ data: data.data, links: data.links });
                this.totalTaches = data.total ?? 0;
            })
            .catch(err => {
                console.error('Gantt load error:', err);
                this.errorMessage = 'Le planning n’a pas pu être chargé.';
                this.totalTaches = 0;
            })
            .finally(() => {
                this.loading = false;
            });
        },

        resetFilters() {
            this.filters = { statut: '', priorite: '', direction_id: '', date_from: '', date_to: '' };
            this.loadData();
        },

        // ── Panneau latéral ────────────────────────────────────
        openPanel(activiteId) {
            this.panel.open    = true;
            this.panel.loading = true;
            this.panel.data    = null;

            fetch('{{ url('activites-gantt/detail') }}/' + activiteId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(data => { this.panel.data = data; this.panel.loading = false; })
            .catch(() => { this.panel.loading = false; });
        },

        // ── Exports ────────────────────────────────────────────
        exportUrl(format) {
            const base = format === 'excel'
                ? '{{ route('gantt.export.excel') }}'
                : '{{ route('gantt.export.pdf') }}';
            const params = new URLSearchParams();
            if (this.filters.statut)       params.append('statut[]', this.filters.statut);
            if (this.filters.priorite)     params.append('priorite', this.filters.priorite);
            if (this.filters.direction_id) params.append('direction_id', this.filters.direction_id);
            if (this.filters.date_from)    params.append('date_from', this.filters.date_from);
            if (this.filters.date_to)      params.append('date_to', this.filters.date_to);
            const qs = params.toString();
            return qs ? base + '?' + qs : base;
        },

        // ── Helpers UX ─────────────────────────────────────────
        toggleFullscreen() {
            const el = document.getElementById('gantt_here');
            if (!document.fullscreenElement) {
                el.requestFullscreen?.();
            } else {
                document.exitFullscreen?.();
            }
        },

        // ── Tooltips ───────────────────────────────────────────
        buildTooltip(task) {
            const retard  = task.est_retard   ? '<span style="color:#f87171;font-weight:700"> ⚠ En retard</span>' : '';
            const crit    = task.est_critique  ? '<span style="color:#fca5a5;font-weight:700"> ★ Critique</span>' : '';
            const pct     = task.budget_prevu > 0
                ? Math.round(task.budget_consomme / task.budget_prevu * 100) + '%' : '—';
            const alerteL = task.has_alerte    ? `<div class="tal">⚠ ${task.nb_alertes} alerte(s) active(s)</div>` : '';
            const gedL    = task.has_documents ? `<div class="tged">📄 ${task.nb_documents} document(s) GED</div>` : '';
            const fmtB    = (v, d) => v > 0 ? new Intl.NumberFormat('fr-FR').format(v) + ' ' + (d || 'XAF') : '—';

            return `<div style="font-weight:700;font-size:13px;margin-bottom:4px;">${task.text}${retard}${crit}</div>
<hr>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:3px;">
  <div><span class="tl">Début prévu </span>${task.start_date ?? '—'}</div>
  <div><span class="tl">Début réel </span>${task.date_debut_reelle ?? '—'}</div>
  <div><span class="tl">Fin prévue </span>${task.end_date ?? '—'}</div>
  <div><span class="tl">Fin réelle </span>${task.date_fin_reelle ?? '—'}</div>
</div>
<div><span class="tl">Avancement </span><strong>${Math.round(task.progress * 100)}%</strong></div>
<hr>
<div><span class="tl">Prévu </span>${fmtB(task.budget_prevu, task.devise)}</div>
<div><span class="tl">Consommé </span>${fmtB(task.budget_consomme, task.devise)} (${pct})</div>
<hr>
<div><span class="tl">Responsable </span>${task.responsable ?? '—'}</div>
${alerteL}${gedL}
<span class="tlink" onclick="window.open('${task.url_detail}','_blank')">Ouvrir la fiche →</span>`;
        },

        buildTooltipGroup(task) {
            return `<div style="font-weight:700;font-size:12px;">${task.text}</div>
<hr><div><span class="tl">Avancement moyen </span><strong>${Math.round(task.progress * 100)}%</strong></div>
<div><span class="tl">Période </span>${task.start_date ?? '—'} → ${task.end_date ?? '—'}</div>`;
        },

        buildTooltipJalon(task) {
            const dep = task.est_retard ? '<span style="color:#f87171;font-weight:700"> ⚠ Dépassé</span>' : '';
            return `<div style="font-weight:700;">◆ Jalon${dep}</div><hr>
<div>${task.text}</div><div><span class="tl">Date </span>${task.start_date ?? '—'}</div>
<span class="tlink" onclick="window.open('${task.url_detail}','_blank')">Ouvrir la fiche →</span>`;
        },

        // ── Helpers panneau ────────────────────────────────────
        labelStatut(s) {
            return { non_demarree: 'Non démarrée', planifiee: 'Planifiée', en_cours: 'En cours',
                     suspendue: 'Suspendue', terminee: 'Terminée', abandonnee: 'Abandonnée' }[s] ?? s;
        },
        labelPriorite(p) {
            return { critique: '🔴 Critique', haute: '🟠 Haute', normale: '🔵 Normale', basse: '⚪ Basse' }[p] ?? p;
        },
        badgeStatutClass(s) {
            return { non_demarree: 'bg-gray-100 text-gray-700', planifiee: 'bg-blue-100 text-blue-700',
                     en_cours: 'bg-indigo-100 text-indigo-700', suspendue: 'bg-yellow-100 text-yellow-800',
                     terminee: 'bg-green-100 text-green-700', abandonnee: 'bg-red-100 text-red-700' }[s] ?? 'bg-gray-100 text-gray-600';
        },
        badgePrioriteClass(p) {
            return { critique: 'bg-red-100 text-red-700', haute: 'bg-orange-100 text-orange-700',
                     normale: 'bg-blue-100 text-blue-700', basse: 'bg-gray-100 text-gray-600' }[p] ?? 'bg-gray-100 text-gray-600';
        },
        progressColor(v) {
            if (!v) return '#ef4444';
            return v >= 75 ? '#22c55e' : v >= 50 ? '#f59e0b' : '#ef4444';
        },
        fmtBudget(v, devise) {
            if (!v || v === 0) return '—';
            return new Intl.NumberFormat('fr-FR').format(v) + ' ' + (devise ?? 'XAF');
        },
        budgetPct(d) {
            if (!d || !d.budget_prevu || d.budget_prevu === 0) return '—';
            return Math.round(d.budget_consomme / d.budget_prevu * 100) + '%';
        },
        budgetAlertClass(d) {
            if (!d || !d.budget_prevu || d.budget_prevu === 0) return 'text-gray-700';
            const pct = d.budget_consomme / d.budget_prevu;
            return pct > 1.0 ? 'text-red-600 font-bold' : pct > 0.85 ? 'text-orange-600' : 'text-gray-700';
        },
    };
}
</script>
@endpush
