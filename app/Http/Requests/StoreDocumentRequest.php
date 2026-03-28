<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
