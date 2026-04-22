@extends('layouts.app')
@section('title', 'Import RBM')
@section('page-title', 'Import RBM — Chargement en masse')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="importApp()">

    {{-- ── En-tête ──────────────────────────────────────────────── --}}
    <div class="bg-indigo-900 text-white rounded-2xl px-6 py-5 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold tracking-tight">Import RBM — Chargement en masse</h1>
            <p class="text-indigo-300 text-sm mt-0.5">Importation automatique de toute la chaîne RBM depuis un fichier Excel</p>
        </div>
        <a href="{{ route('import.rbm.modele') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-800 text-sm font-semibold rounded-xl hover:bg-indigo-50 transition shadow">
            <i class="fas fa-download"></i> Télécharger le modèle Excel
        </a>
    </div>

    {{-- ── Message flash erreur ─────────────────────────────────── --}}
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 flex items-start gap-2">
        <i class="fas fa-exclamation-circle mt-0.5 text-red-400"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- ── Résultats d'import ou de validation ─────────────────── --}}
    @if(isset($summary))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                @if(isset($rolledBack))
                    <i class="fas fa-undo text-orange-500"></i> Import annulé — erreurs détectées (mode strict)
                @elseif(isset($success) && $success)
                    <i class="fas fa-check-circle text-green-500"></i> Import réussi
                @elseif(isset($success) && !$success)
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i> Import partiel — lignes rejetées
                @else
                    <i class="fas fa-search text-indigo-500"></i> Résultat de la validation (aucune donnée enregistrée)
                @endif
            </h2>
            <span class="text-xs text-gray-400">{{ now()->format('d/m/Y H:i') }}</span>
        </div>

        {{-- Tableau par feuille --}}
        @if(!empty($summary['sheets']))
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                        <th class="px-5 py-3 text-left">Feuille</th>
                        <th class="px-5 py-3 text-center">Importées</th>
                        <th class="px-5 py-3 text-center">Ignorées</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($summary['sheets'] as $sheet)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-2.5 font-medium text-gray-700 font-mono text-xs">{{ $sheet['sheet'] }}</td>
                        <td class="px-5 py-2.5 text-center">
                            <span class="inline-flex items-center gap-1 text-green-700 font-semibold">
                                <i class="fas fa-check text-xs"></i> {{ $sheet['imported'] }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5 text-center text-gray-500">{{ $sheet['skipped'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Erreurs --}}
        @if($summary['total_errors'] > 0)
        <div class="px-5 py-4 bg-red-50 border-t border-red-100">
            <p class="text-sm font-semibold text-red-700 mb-2">
                <i class="fas fa-times-circle mr-1"></i>
                {{ $summary['total_errors'] }} erreur(s) détectée(s)
            </p>
            <ul class="space-y-1 max-h-60 overflow-y-auto">
                @foreach($summary['errors'] as $err)
                <li class="text-xs font-mono text-red-600 bg-red-100 rounded px-2 py-1">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    {{-- ── Zone d'upload et de configuration ───────────────────── --}}
    <div class="grid md:grid-cols-2 gap-6">

        {{-- Validation (dry-run) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-800 mb-1 flex items-center gap-2">
                <i class="fas fa-search text-indigo-500"></i> 1. Valider le fichier
            </h2>
            <p class="text-xs text-gray-500 mb-4">Vérification complète sans enregistrement. Idéal pour détecter toutes les erreurs avant l'import.</p>

            <form action="{{ route('import.rbm.valider') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="block text-xs font-medium text-gray-600 mb-1">Fichier Excel (.xlsx)</label>
                <input type="file" name="fichier" accept=".xlsx,.xls" required
                       class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100 mb-4">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-700 font-semibold text-sm rounded-xl hover:bg-indigo-100 transition border border-indigo-100">
                    <i class="fas fa-check-double"></i> Lancer la validation
                </button>
            </form>
        </div>

        {{-- Import réel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-800 mb-1 flex items-center gap-2">
                <i class="fas fa-upload text-green-600"></i> 2. Importer les données
            </h2>
            <p class="text-xs text-gray-500 mb-4">Enregistrement réel des données. Choisissez le mode selon la tolérance aux erreurs souhaitée.</p>

            <form action="{{ route('import.rbm.executer') }}" method="POST" enctype="multipart/form-data"
                  onsubmit="return confirm('Confirmer l\'import ? Les données existantes avec les mêmes codes seront mises à jour.')">
                @csrf
                <label class="block text-xs font-medium text-gray-600 mb-1">Fichier Excel (.xlsx)</label>
                <input type="file" name="fichier" accept=".xlsx,.xls" required
                       class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 file:font-medium hover:file:bg-green-100 mb-3">

                <label class="block text-xs font-medium text-gray-600 mb-1">Mode d'import</label>
                <select name="mode" class="w-full text-sm border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 mb-4">
                    <option value="strict">Strict — annuler si une erreur est détectée</option>
                    <option value="souple">Souple — importer les lignes valides, signaler les erreurs</option>
                </select>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold text-sm rounded-xl hover:bg-green-700 transition shadow">
                    <i class="fas fa-database"></i> Lancer l'import
                </button>
            </form>
        </div>
    </div>

    {{-- ── Chaîne RBM — rappel visuel ───────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm mb-3 flex items-center gap-2">
            <i class="fas fa-sitemap text-indigo-400"></i> Ordre d'import de la chaîne RBM
        </h2>
        <div class="flex flex-wrap items-center gap-1 text-xs">
            @foreach([
                ['Départements', '#6366f1'],
                ['Directions', '#6366f1'],
                ['Services', '#6366f1'],
                ['Utilisateurs', '#6366f1'],
                ['PAPA', '#dc2626'],
                ['Actions Prioritaires', '#ea580c'],
                ['Objectifs Immédiats', '#d97706'],
                ['Résultats Attendus', '#65a30d'],
                ['Activités', '#0891b2'],
                ['Tâches', '#0284c7'],
                ['Jalons', '#0284c7'],
                ['Indicateurs', '#7c3aed'],
                ['Valeurs Indicateurs', '#7c3aed'],
                ['Budgets', '#059669'],
                ['Risques', '#b91c1c'],
                ['Dépendances', '#4b5563'],
            ] as $i => [$label, $color])
            <span class="flex items-center gap-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-medium text-white"
                      style="background:{{ $color }}">
                    {{ $i + 1 }}. {{ $label }}
                </span>
                @if($i < 15)
                <i class="fas fa-chevron-right text-gray-300 text-[8px]"></i>
                @endif
            </span>
            @endforeach
        </div>
    </div>

</div>

<script>
function importApp() { return {}; }
</script>
@endsection
