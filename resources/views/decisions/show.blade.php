@extends('layouts.app')

@section('title', $decision->reference)

@section('breadcrumbs')
    <li>/</li>
    <li><a href="{{ route('decisions.index') }}" class="hover:text-indigo-600">Decisions</a></li>
    <li>/</li>
    <li class="text-gray-700">{{ $decision->reference }}</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $decision->titre }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $decision->reference }} • {{ str($decision->niveau_decision)->replace('_', ' ')->title() }} • {{ str($decision->statut)->replace('_', ' ')->title() }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('valider', $decision)
                <form method="POST" action="{{ route('decisions.valider', $decision) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Valider</button>
                </form>
            @endcan
            @can('executer', $decision)
                <form method="POST" action="{{ route('decisions.executer', $decision) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Executer</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900">Description</h2>
            <p class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $decision->description }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Cadre</h2>
                @can('view', $decision)
                    <a href="{{ route('decisions.audit', $decision) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Voir l'audit</a>
                @endcan
            </div>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">PAPA</dt>
                    <dd class="font-medium text-gray-900">{{ $decision->papa?->code ?? 'Non rattache' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Action prioritaire</dt>
                    <dd class="font-medium text-gray-900">{{ $decision->actionPrioritaire?->code ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Prise par</dt>
                    <dd class="font-medium text-gray-900">{{ $decision->prisePar?->nomComplet() ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Validee par</dt>
                    <dd class="font-medium text-gray-900">{{ $decision->valideePar?->nomComplet() ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Pieces rattachees</h2>
                <span class="text-sm text-gray-500">{{ $decision->attachments->count() }} document(s)</span>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($decision->attachments as $attachment)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $attachment->titre }}</p>
                                <p class="text-sm text-gray-500">{{ $attachment->type_piece }} • {{ $attachment->document?->categorie?->libelle ?? 'Document' }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $attachment->valide ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $attachment->valide ? 'Valide' : 'En attente' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune piece rattachee pour le moment.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Rattacher un document</h2>
            <form method="POST" action="{{ route('decisions.rattacher-document', $decision) }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="document_id" class="mb-2 block text-sm font-medium text-gray-700">Document</label>
                    <select id="document_id" name="document_id" class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach($documents as $document)
                            <option value="{{ $document->id }}">{{ $document->titre }} ({{ $document->statut }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="type_piece" class="mb-2 block text-sm font-medium text-gray-700">Type de piece</label>
                    <input id="type_piece" name="type_piece" type="text" value="note_justificative" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="obligatoire" value="1" checked class="rounded border-gray-300 text-indigo-600">
                    Piece obligatoire
                </label>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Rattacher</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection