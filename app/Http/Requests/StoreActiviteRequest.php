<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActiviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('activite.creer');
    }

    public function rules(): array
    {
        return [
            'resultat_attendu_id' => 'required|exists:resultats_attendus,id',
            'direction_id'        => 'required|exists:directions,id',
            'service_id'          => 'nullable|exists:services,id',
            'code'                => 'required|string|max:50|unique:activites,code',
            'libelle'             => 'required|string|max:500',
            'description'         => 'nullable|string|max:3000',
            'date_debut_prevue'   => 'nullable|date',
            'date_fin_prevue'     => 'nullable|date|after_or_equal:date_debut_prevue',
            'responsable_id'      => 'nullable|exists:users,id',
            'point_focal_id'      => 'nullable|exists:users,id',
            'budget_prevu'        => 'nullable|numeric|min:0',
            'devise'              => 'nullable|string|max:10',
            'priorite'            => 'required|in:critique,haute,normale,basse',
            'est_jalon'           => 'boolean',
            'notes'               => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'resultat_attendu_id.required' => 'Le résultat attendu est obligatoire.',
            'resultat_attendu_id.exists'   => 'Le résultat attendu sélectionné est invalide.',
            'direction_id.required'        => 'La direction responsable est obligatoire.',
            'code.unique'                  => 'Ce code d\'activité existe déjà.',
            'date_fin_prevue.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ];
    }
}
