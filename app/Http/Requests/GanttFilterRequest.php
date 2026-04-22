<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GanttFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // l'autorisation métier est gérée dans le contrôleur
    }

    public function rules(): array
    {
        return [
            'statut'       => ['nullable', 'array'],
            'statut.*'     => ['string', 'in:non_demarree,planifiee,en_cours,suspendue,terminee,abandonnee'],
            'direction_id' => ['nullable', 'integer', 'exists:directions,id'],
            'priorite'     => ['nullable', 'string', 'in:critique,haute,normale,basse'],
            'date_from'    => ['nullable', 'date'],
            'date_to'      => ['nullable', 'date', 'after_or_equal:date_from'],
        ];
    }

    public function messages(): array
    {
        return [
            'statut.*.in'          => 'Statut invalide.',
            'priorite.in'          => 'Priorité invalide.',
            'date_to.after_or_equal' => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}
