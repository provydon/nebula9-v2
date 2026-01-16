<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferAnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_enclosure_id' => 'required|exists:enclosures,id',
        ];
    }
}
