@extends('layouts.app')

@section('title', 'Workflows')

@section('breadcrumbs')
    <li>/</li>
    <li class="text-gray-700">Workflows</li>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Workflows de validation</h1>
        <p class="text-sm text-gray-500">Suivi des circuits de validation démarrés sur les objets métiers.</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Workflow</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Objet</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Étape courante</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Statut</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Démarré par</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($instances as $instance)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $instance->definition?->libelle }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ class_basename($instance->objet_type) }} #{{ $instance->objet_id }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $instance->etapeCourante?->libelle ?? 'Clôturé' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ str($instance->statut)->replace('_', ' ')->title() }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $instance->demarrePar?->nomComplet() ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('workflows.show', $instance) }}" class="text-indigo-600 hover:text-indigo-800">Ouvrir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucun workflow démarré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $instances->links() }}
        </div>
    </div>
</div>
@endsection
