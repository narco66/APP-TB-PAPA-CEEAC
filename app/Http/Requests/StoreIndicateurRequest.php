<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndicateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('indicateur.creer');
    }

    public function rules(): array
    {
        return [
            'code'                    => 'required|string|max:50|unique:indicateurs,code',
            'libelle'                 => 'required|string|max:500',
            'resultat_attendu_id'     => 'nullable|exists:resultats_attendus,id',
            'objectif_immediat_id'    => 'nullable|exists:objectifs_immediats,id',
            'action_prioritaire_id'   => 'nullable|exists:actions_prioritaires,id',
            'definition'              => 'nullable|string',
            'unite_mesure'            => 'nullable|string|max:50',
            'type_indicateur'         => 'required|in:quantitatif,qualitatif,binaire',
            'valeur_baseline'         => 'nullable|numeric',
            'valeur_cible_annuelle'   => 'nullable|numeric',
            'methode_calcul'          => 'nullable|string',
            'frequence_collecte'      => 'required|in:mensuelle,trimestrielle,semestrielle,annuelle,ponctuelle',
            'source_donnees'          => 'nullable|string|max:300',
            'responsable_id'          => 'nullable|exists:users,id',
            'direction_id'            => 'nullable|exists:directions,id',
            'seuil_alerte_rouge'      => 'nullable|numeric|min:0|max:100',
            'seuil_alerte_orange'     => 'nullable|numeric|min:0|max:100',
            'seuil_alerte_vert'       => 'nullable|numeric|min:0|max:100',
            'notes'                   => 'nullable|string|max:2000',
        ];
    }
}
