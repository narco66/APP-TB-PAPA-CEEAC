<?php

namespace App\Http\Requests;

use App\Models\Direction;
use App\Models\ResultatAttendu;
use App\Models\Service;
use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'direction_id' => 'required|exists:directions,id',
            'service_id' => 'nullable|exists:services,id',
            'code' => 'required|string|max:50|unique:activites,code',
            'libelle' => 'required|string|max:500',
            'description' => 'nullable|string|max:3000',
            'date_debut_prevue' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut_prevue',
            'responsable_id' => 'nullable|exists:users,id',
            'point_focal_id' => 'nullable|exists:users,id',
            'budget_prevu' => 'nullable|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'priorite' => 'required|in:critique,haute,normale,basse',
            'est_jalon' => 'boolean',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'resultat_attendu_id.required' => 'Le resultat attendu est obligatoire.',
            'resultat_attendu_id.exists' => 'Le resultat attendu selectionne est invalide.',
            'direction_id.required' => 'La direction responsable est obligatoire.',
            'code.unique' => 'Ce code d activite existe deja.',
            'date_fin_prevue.after_or_equal' => 'La date de fin doit etre posterieure ou egale a la date de debut.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user = $this->user();
            $resolver = app(UserScopeResolver::class);

            if ($this->filled('resultat_attendu_id')) {
                $resultatAttenduVisible = ResultatAttendu::query()
                    ->visibleTo($user)
                    ->whereKey($this->integer('resultat_attendu_id'))
                    ->exists();

                if (! $resultatAttenduVisible) {
                    $validator->errors()->add('resultat_attendu_id', 'Le resultat attendu selectionne est hors de votre perimetre autorise.');
                }
            }

            if ($this->filled('direction_id')) {
                $direction = Direction::find($this->integer('direction_id'));

                if (! $direction || ! $resolver->canAccessAttributes($user, departementId: $direction->departement_id, directionId: $direction->id)) {
                    $validator->errors()->add('direction_id', 'La direction selectionnee est hors de votre perimetre autorise.');
                }
            }

            if ($this->filled('service_id')) {
                $service = Service::with('direction')->find($this->integer('service_id'));

                if (! $service || ! $resolver->canAccessAttributes(
                    $user,
                    departementId: $service->direction?->departement_id,
                    directionId: $service->direction_id,
                    serviceId: $service->id,
                )) {
                    $validator->errors()->add('service_id', 'Le service selectionne est hors de votre perimetre autorise.');
                }
            }

            foreach (['responsable_id', 'point_focal_id'] as $field) {
                if (! $this->filled($field)) {
                    continue;
                }

                $assignee = User::find($this->integer($field));

                if (! $assignee || ! $resolver->canAccessAttributes(
                    $user,
                    departementId: $assignee->departement_id ?? $assignee->direction?->departement_id,
                    directionId: $assignee->direction_id,
                    serviceId: $assignee->service_id,
                )) {
                    $validator->errors()->add($field, 'L utilisateur selectionne est hors de votre perimetre autorise.');
                }
            }
        });
    }
}
