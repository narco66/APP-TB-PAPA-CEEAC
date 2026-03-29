@extends('layouts.app')
@section('title', 'Règles de notification')
@section('page-title', 'Règles de notification')

@section('breadcrumbs')
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li><a href="{{ route('admin.utilisateurs.index') }}" class="hover:text-indigo-600">Administration</a></li>
    <li><i class="fas fa-chevron-right mx-2 text-xs"></i></li>
    <li class="text-gray-700 font-medium">Notifications</li>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Règles de notification</h2>
        <p class="text-sm text-gray-500">Paramétrage des canaux, délais et destinataires par événement métier.</p>
    </div>

    <div class="space-y-4">
        @foreach($rules as $rule)
        <form method="POST" action="{{ route('admin.notification-rules.update', $rule) }}" class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-4">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $rule->code }}</p>
                    <p class="text-xs text-gray-500">{{ $rule->event_type }}</p>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="actif" value="1" @checked($rule->actif) class="rounded border-gray-300 text-indigo-600">
                    Active
                </label>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Libellé</label>
                    <input type="text" name="libelle" value="{{ $rule->libelle }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Canal</label>
                    <select name="canal" class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach(['in_app', 'email', 'sms'] as $canal)
                        <option value="{{ $canal }}" @selected($rule->canal === $canal)>{{ $canal }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Rôle cible</label>
                    <input type="text" name="role_cible" value="{{ $rule->role_cible }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Permission cible</label>
                    <input type="text" name="permission_cible" value="{{ $rule->permission_cible }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type d'événement</label>
                    <input type="text" name="event_type" value="{{ $rule->event_type }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Délai (minutes)</label>
                    <input type="number" name="delai_minutes" min="0" value="{{ $rule->delai_minutes }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="escalade" value="1" @checked($rule->escalade) class="rounded border-gray-300 text-indigo-600">
                        Escalade activée
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sujet</label>
                <input type="text" name="template_sujet" value="{{ $rule->template_sujet }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Message</label>
                <textarea name="template_message" rows="3" class="w-full rounded-lg border-gray-300 text-sm">{{ $rule->template_message }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Enregistrer</button>
            </div>
        </form>
        @endforeach
    </div>

    {{ $rules->links() }}
</div>
@endsection
