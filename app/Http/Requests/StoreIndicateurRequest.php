<?php

namespace App\Http\Requests;

use App\Models\Direction;
use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreIndicateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('indicateur.creer');
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:indicateurs,code',
            'libelle' => 'required|string|max:500',
            'resultat_attendu_id' => 'nullable|exists:resultats_attendus,id',
            'objectif_immediat_id' => 'nullable|exists:objectifs_immediats,id',
            'action_prioritaire_id' => 'nullable|exists:actions_prioritaires,id',
            'definition' => 'nullable|string',
            'unite_mesure' => 'nullable|string|max:50',
            'type_indicateur' => 'required|in:quantitatif,qualitatif,binaire',
            'valeur_baseline' => 'nullable|numeric',
            'valeur_cible_annuelle' => 'nullable|numeric',
            'methode_calcul' => 'nullable|string',
            'frequence_collecte' => 'required|in:mensuelle,trimestrielle,semestrielle,annuelle,ponctuelle',
            'source_donnees' => 'nullable|string|max:300',
            'responsable_id' => 'nullable|exists:users,id',
            'direction_id' => 'nullable|exists:directions,id',
            'seuil_alerte_rouge' => 'nullable|numeric|min:0|max:100',
            'seuil_alerte_orange' => 'nullable|numeric|min:0|max:100',
            'seuil_alerte_vert' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user = $this->user();
            $resolver = app(UserScopeResolver::class);

            if ($this->filled('direction_id')) {
                $direction = Direction::find($this->integer('direction_id'));

                if (! $direction || ! $resolver->canAccessAttributes($user, departementId: $direction->departement_id, directionId: $direction->id)) {
                    $validator->errors()->add('direction_id', 'La direction selectionnee est hors de votre perimetre autorise.');
                }
            }

            if ($this->filled('responsable_id')) {
                $responsable = User::find($this->integer('responsable_id'));

                if (! $responsable || ! $resolver->canAccessAttributes(
                    $user,
                    departementId: $responsable->departement_id ?? $responsable->direction?->departement_id,
                    directionId: $responsable->direction_id,
                    serviceId: $responsable->service_id,
                )) {
                    $validator->errors()->add('responsable_id', 'Le responsable selectionne est hors de votre perimetre autorise.');
                }
            }
        });
    }
}