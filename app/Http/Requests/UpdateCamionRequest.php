<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCamionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'placa' => ['sometimes', 'required', 'string', 'max:10', Rule::unique('camiones', 'placa')->ignore($this->route('camion'))],
            'modelo' => ['sometimes', 'required', 'string', 'max:10'],
            'camioneros' => ['sometimes', 'array'],
            'camioneros.*' => ['exists:camioneros,id'],
        ];
    }
}
