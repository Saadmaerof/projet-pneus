<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetbyTrierPneuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'vehicule_id'     => 'nullable|integer|exists:vehicules,id',
        'saison'          => 'nullable|string|in:Été,Hiver,4 saisons',
        'marque'          => 'nullable|string|max:255',
        'largeur'         => 'nullable|integer',
        'hauteur'         => 'nullable|integer',
        'diametre_pouces' => 'nullable|integer',
        ];
    }
}
