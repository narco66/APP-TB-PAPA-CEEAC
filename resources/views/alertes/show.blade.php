@extends('layouts.app')
@section('title', 'Alerte - ' . $alerte->titre)
@section('page-title', 'Detail de l\'alerte')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('alertes.index') }}" class="hover:text-indigo-600">Alertes</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">{{ Str::limit($alerte->titre, 40) }}</li>
@endsection

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-{{ $alerte->couleurNiveau() }}-400 border border-gray-100">
        <div class="flex items-start space-x-4">
            <div class="text-3xl flex-shrink-0">{{ $alerte->iconeNiveau() }}</div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h1 class="text-lg font-bold text-gray-800">{{ $alerte->titre }}</h1>
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-{{ $alerte->couleurNiveau() }}-100 text-{{ $alerte->couleurNiveau() }}-700">
                        {{ ucfirst($alerte->niveau) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $alerte->statut === 'resolue' ? 'bg-green-100 text-green-700' : ($alerte->statut === 'nouvelle' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst(str_replace('_', ' ', $alerte->statut)) }}
                    </span>
                </div>

                <p class="text-sm text-gray-700 leading-relaxed">{{ $alerte->message }}</p>

                <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                    @if($alerte->papa)
                        <div>
                            <p class="text-xs text-gray-400">PAPA concerne</p>
                            <a href="{{ route('papas.show', $alerte->papa) }}" class="font-medium text-indigo-600 hover:underline">{{ $alerte->papa->code }}</a>
                        </div>
                    @endif
                    @if($alerte->type_alerte)
                        <div>
                            <p class="text-xs text-gray-400">Type d'alerte</p>
                            <p class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $alerte->type_alerte)) }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400">Generee le</p>
                        <p class="font-medium text-gray-700">{{ $alerte->created_at->format('d/m/Y a H:i') }}</p>
                    </div>
                    @if($alerte->lue_le)
                        <div>
                            <p class="text-xs text-gray-400">Vue le</p>
                            <p class="font-medium text-gray-700">{{ $alerte->lue_le->format('d/m/Y a H:i') }}</p>
                        </div>
                    @endif
                    @if($alerte->escaladee)
                        <div>
                            <p class="text-xs text-gray-400">Escaladee le</p>
                            <p class="font-medium text-purple-600">{{ $alerte->escaladee_le?->format('d/m/Y') }}</p>
                        </div>
                    @endif
                    @if($alerte->auto_generee)
                        <div>
                            <p class="text-xs text-gray-400">Origine</p>
                            <p class="text-gray-500 text-xs">Generee automatiquement</p>
                        </div>
                    @endif
                </div>

                @if($alerte->alertable)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
                        <p class="text-xs text-gray-400 mb-1">Element concerne</p>
                        <p class="font-medium text-gray-700">
                            {{ class_basename($alerte->alertable_type) }} :
                            @if(method_exists($alerte->alertable, 'libelle'))
                                {{ $alerte->alertable->code ?? '' }} - {{ $alerte->alertable->libelle }}
                            @else
                                #{{ $alerte->alertable_id }}
                            @endif
                        </p>
                        @if($alerte->alertable instanceof \App\Models\Activite)
                            <a href="{{ route('activites.show', $alerte->alertable) }}" class="text-xs text-indigo-600 hover:underline mt-1 block">
                                Voir l'activite <i class="fas fa-arrow-right ml-0.5"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            @can('alerte.traiter')
                @if(in_array($alerte->statut, ['nouvelle', 'vue', 'en_cours']))
                    <button onclick="document.getElementById('modal-traiter').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                        <i class="fas fa-check mr-1"></i>Marquer comme resolue
                    </button>
                @endif
            @endcan

            @can('alerte.escalader')
                @if($alerte->statut !== 'resolue' && ! $alerte->escaladee)
                    <button onclick="document.getElementById('modal-escalader').classList.remove('hidden')" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg text-sm hover:bg-purple-200 transition">
                        <i class="fas fa-arrow-up mr-1"></i>Escalader
                    </button>
                @endif
            @endcan
        </div>
    </div>

    @if($alerte->statut === 'resolue' && $alerte->resolution)
        <div class="bg-green-50 rounded-xl p-5 border border-green-100">
            <h3 class="font-semibold text-green-800 mb-2">
                <i class="fas fa-check-circle mr-1"></i>Resolution
            </h3>
            <p class="text-sm text-green-700">{{ $alerte->resolution }}</p>
            @if($alerte->traitee_le)
                <p class="text-xs text-green-600 mt-2">
                    Resolue le {{ $alerte->traitee_le->format('d/m/Y a H:i') }}
                    @if($alerte->traitePar) par {{ $alerte->traitePar->nomComplet() }} @endif
                </p>
            @endif
        </div>
    @endif

    @if($alerte->actionsCorrectives->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">Actions correctives ({{ $alerte->actionsCorrectives->count() }})</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($alerte->actionsCorrectives as $ac)
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-sm text-gray-800">{{ $ac->libelle }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Resp. : {{ $ac->responsable?->nomComplet() ?? '-' }} •
                                    Echeance : {{ $ac->echeance ? $ac->echeance->format('d/m/Y') : '-' }}
                                </p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $ac->statut === 'realisee' ? 'bg-green-100 text-green-700' : ($ac->statut === 'en_cours' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst(str_replace('_', ' ', $ac->statut)) }}
                            </span>
                        </div>
                        @if($ac->description)
                            <p class="text-xs text-gray-500 mt-1">{{ $ac->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<div id="modal-traiter" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Resoudre l'alerte</h3>
        <form action="{{ route('alertes.traiter', $alerte) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Resolution <span class="text-red-500">*</span></label>
                <textarea name="resolution" rows="4" placeholder="Decrivez les actions prises pour resoudre cette alerte..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modal-traiter').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                    <i class="fas fa-check mr-1"></i>Resoudre
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modal-escalader" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Escalader l'alerte</h3>
        <form action="{{ route('alertes.escalader', $alerte) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Escalader vers <span class="text-red-500">*</span></label>
                <select name="destinataire_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Models\User::actif()->orderBy('name')->get() as $u)
                        <option value="{{ $u->id }}">{{ $u->nomComplet() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modal-escalader').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700">
                    <i class="fas fa-arrow-up mr-1"></i>Escalader
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
