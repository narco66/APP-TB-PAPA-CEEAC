<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePapaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('papa.creer');
    }

    public function rules(): array
    {
        return [
            'code'               => 'required|string|max:30|unique:papas,code',
            'libelle'            => 'required|string|max:255',
            'annee'              => 'required|integer|min:2020|max:2050',
            'date_debut'         => 'required|date',
            'date_fin'           => 'required|date|after:date_debut',
            'description'        => 'nullable|string|max:2000',
            'budget_total_prevu' => 'nullable|numeric|min:0',
            'devise'             => 'nullable|string|max:10',
            'notes'              => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'    => 'Le code PAPA est obligatoire.',
            'code.unique'      => 'Ce code PAPA existe déjà.',
            'annee.required'   => 'L\'année est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.required'   => 'La date de fin est obligatoire.',
            'date_fin.after'      => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}
