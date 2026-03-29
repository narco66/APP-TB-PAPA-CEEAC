<div class="mb-6 rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-900">Parcours historique de rapports narratifs</p>
            <p class="mt-1 text-sm text-indigo-700">
                Pour les exports institutionnels, les modeles standards et l'historique des generations,
                utilisez le nouveau centre de reporting.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reports.dashboard') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <i class="fas fa-chart-column"></i>
                Centre de reporting
            </a>
            <a href="{{ route('reports.library.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-indigo-300 bg-white px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                <i class="fas fa-folder-open"></i>
                Bibliotheque
            </a>
        </div>
    </div>
</div>
