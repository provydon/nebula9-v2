<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'specie' => 'required|string',
            'preferred_environment' => 'required|string',
            'enclosure_id' => 'nullable|exists:enclosures,id',
        ];
    }
}
