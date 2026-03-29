@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Rapports de suivi</h1>
        <p class="text-sm text-gray-500 mt-1">Historique des rapports narratifs par PAPA</p>
    </div>
    @can('create', \App\Models\Rapport::class)
    <a href="{{ route('rapports.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
        <i class="fas fa-plus"></i> Nouveau rapport
    </a>
    @endcan
</div>

@include('rapports.partials.legacy-bridge')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">PAPA</label>
            <select name="papa_id" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tous les PAPA</option>
                @foreach($papas as $p)
                <option value="{{ $p->id }}" {{ request('papa_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->code }} - {{ $p->libelle }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
            <select name="type_rapport" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tous types</option>
                @foreach(['mensuel','trimestriel','semestriel','annuel','ponctuel'] as $t)
                <option value="{{ $t }}" {{ request('type_rapport') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Statut</label>
            <select name="statut" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tous statuts</option>
                @foreach(['brouillon','soumis','valide','publie'] as $s)
                <option value="{{ $s }}" {{ request('statut') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-filter mr-1"></i> Filtrer
        </button>
        @if(request()->hasAny(['papa_id','type_rapport','statut']))
        <a href="{{ route('rapports.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">
            <i class="fas fa-times mr-1"></i> Reinitialiser
        </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($rapports->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <i class="fas fa-file-alt text-4xl mb-3"></i>
        <p class="font-medium">Aucun rapport trouve</p>
        <p class="text-sm mt-1">Creez le premier rapport de suivi.</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <th class="text-left px-4 py-3">Titre</th>
                <th class="text-left px-4 py-3">PAPA</th>
                <th class="text-left px-4 py-3">Type</th>
                <th class="text-left px-4 py-3">Periode</th>
                <th class="text-center px-4 py-3">Exec. phys.</th>
                <th class="text-center px-4 py-3">Exec. fin.</th>
                <th class="text-left px-4 py-3">Statut</th>
                <th class="text-left px-4 py-3">Redige par</th>
                <th class="text-left px-4 py-3">Date</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($rapports as $rapport)
            @php $couleur = $rapport->couleurStatut(); @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">
                    <a href="{{ route('rapports.show', $rapport) }}" class="hover:text-indigo-600">
                        {{ $rapport->titre }}
                    </a>
                </td>
                <td class="px-4 py-3">
                    @if($rapport->papa)
                    <span class="font-mono text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded">
                        {{ $rapport->papa->code }}
                    </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600">{{ ucfirst($rapport->type_rapport) }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $rapport->periode_couverte }}</td>
                <td class="px-4 py-3 text-center font-semibold">{{ $rapport->taux_execution_physique }}%</td>
                <td class="px-4 py-3 text-center font-semibold text-green-600">{{ $rapport->taux_execution_financiere }}%</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $couleur === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}
                        {{ $couleur === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $couleur === 'green' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $couleur === 'indigo' ? 'bg-indigo-100 text-indigo-700' : '' }}">
                        {{ ucfirst($rapport->statut) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $rapport->redigePar?->nomComplet() ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $rapport->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('rapports.show', $rapport) }}" class="text-gray-400 hover:text-indigo-600" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('export', $rapport)
                        <a href="{{ route('rapports.export-pdf', $rapport) }}" class="text-gray-400 hover:text-red-600" title="Export PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endcan
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $rapports->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
