<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvancementActiviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $activite = $this->route('activite');
        return $this->user()->can('mettreAJourAvancement', $activite);
    }

    public function rules(): array
    {
        return [
            'taux_realisation'  => 'required|numeric|min:0|max:100',
            'statut'            => 'required|in:non_demarree,planifiee,en_cours,suspendue,terminee,abandonnee',
            'date_debut_reelle' => 'nullable|date',
            'date_fin_reelle'   => 'nullable|date|after_or_equal:date_debut_reelle',
            'notes'             => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'taux_realisation.required' => 'Le taux de réalisation est obligatoire.',
            'taux_realisation.min'      => 'Le taux ne peut pas être inférieur à 0%.',
            'taux_realisation.max'      => 'Le taux ne peut pas dépasser 100%.',
            'statut.required'           => 'Le statut est obligatoire.',
        ];
    }
}
