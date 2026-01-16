<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string',
            'specie' => 'sometimes|string',
            'preferred_environment' => 'sometimes|string',
            'enclosure_id' => 'sometimes|exists:enclosures,id',
        ];
    }
}
