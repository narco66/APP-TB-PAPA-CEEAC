<?php

namespace App\Http\Requests;

use App\Models\Activite;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('document.deposer');
    }

    public function rules(): array
    {
        return [
            'fichier'          => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar',
            'titre'            => 'required|string|max:400',
            'description'      => 'nullable|string|max:2000',
            'reference'        => 'nullable|string|max:100',
            'date_document'    => 'nullable|date',
            'categorie_id'     => 'nullable|exists:categories_documents,id',
            'confidentialite'  => 'required|in:public,interne,confidentiel,strictement_confidentiel',
            'documentable_type' => 'nullable|string',
            'documentable_id'  => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'fichier.required' => 'Le fichier est obligatoire.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 50 Mo.',
            'fichier.mimes'    => 'Format non autorisé. Formats acceptés : PDF, Word, Excel, PowerPoint, Images, ZIP.',
            'titre.required'   => 'Le titre du document est obligatoire.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('documentable_type') || ! $this->filled('documentable_id')) {
                return;
            }

            $class = $this->input('documentable_type');
            $id = (int) $this->input('documentable_id');
            $allowed = [
                Activite::class,
                ResultatAttendu::class,
                Papa::class,
            ];

            if (! in_array($class, $allowed, true) || ! class_exists($class)) {
                $validator->errors()->add('documentable_type', 'Le type de rattachement est invalide.');
                return;
            }

            $entity = $class::find($id);

            if (! $entity) {
                $validator->errors()->add('documentable_id', 'L entite selectionnee est introuvable.');
                return;
            }

            $user = $this->user();

            $allowedEntity = match ($class) {
                Activite::class => $user->can('voir', $entity),
                ResultatAttendu::class => $entity->activites()->visibleTo($user)->exists()
                    || $entity->objectifImmediats?->actionPrioritaire?->canBeAccessedBy($user),
                Papa::class => $entity->actionsPrioritaires()->visibleTo($user)->exists(),
                default => false,
            };

            if (! $allowedEntity) {
                $validator->errors()->add('documentable_id', 'L entite selectionnee est hors de votre perimetre autorise.');
            }
        });
    }
}
