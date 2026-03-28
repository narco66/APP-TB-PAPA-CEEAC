<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePapaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $papa = $this->route('papa');
        return $this->user()->can('modifier', $papa);
    }

    public function rules(): array
    {
        $papaId = $this->route('papa')?->id;
        return [
            'code'               => "required|string|max:30|unique:papas,code,{$papaId}",
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
}
