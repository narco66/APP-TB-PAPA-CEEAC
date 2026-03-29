@extends('layouts.app')

@section('title', 'Workflow')

@section('breadcrumbs')
    <li>/</li>
    <li><a href="{{ route('workflows.index') }}" class="hover:text-indigo-600">Workflows</a></li>
    <li>/</li>
    <li class="text-gray-700">#{{ $instance->id }}</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $instance->definition?->libelle }}</h1>
            <p class="mt-1 text-sm text-gray-500">Objet: {{ class_basename($instance->objet_type) }} #{{ $instance->objet_id }} • Statut: {{ str($instance->statut)->replace('_', ' ')->title() }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('approuver', $instance)
                <form method="POST" action="{{ route('workflows.approuver', $instance) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Approuver</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">État courant</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">Étape</dt>
                    <dd class="font-medium text-gray-900">{{ $instance->etapeCourante?->libelle ?? 'Aucune' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">PAPA</dt>
                    <dd class="font-medium text-gray-900">{{ $instance->papa?->code ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Démarré par</dt>
                    <dd class="font-medium text-gray-900">{{ $instance->demarrePar?->nomComplet() ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900">Historique des actions</h2>
            <div class="mt-4 space-y-4">
                @forelse($instance->actions->sortByDesc('effectue_le') as $action)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ str($action->action)->replace('_', ' ')->title() }}</p>
                                <p class="text-sm text-gray-500">{{ $action->acteur?->nomComplet() ?? 'Système' }} • {{ optional($action->effectue_le)->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="text-xs font-semibold text-gray-500">{{ $action->step?->libelle ?? 'N/A' }}</span>
                        </div>
                        @if($action->commentaire)
                            <p class="mt-3 text-sm text-gray-700">{{ $action->commentaire }}</p>
                        @endif
                        @if($action->motif_rejet)
                            <p class="mt-3 text-sm font-medium text-red-600">Motif: {{ $action->motif_rejet }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune action enregistrée.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Commenter</h2>
            <form method="POST" action="{{ route('workflows.commenter', $instance) }}" class="mt-4 space-y-4">
                @csrf
                <textarea name="commentaire" rows="4" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Ajouter un commentaire de workflow" required></textarea>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Publier</button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Rejeter</h2>
            <form method="POST" action="{{ route('workflows.rejeter', $instance) }}" class="mt-4 space-y-4">
                @csrf
                <textarea name="motif" rows="4" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Motif de rejet" required></textarea>
                <input name="commentaire" type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Commentaire complémentaire">
                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
