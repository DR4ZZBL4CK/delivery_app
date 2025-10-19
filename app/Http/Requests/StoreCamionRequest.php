<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCamionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'placa' => ['required', 'string', 'max:10', 'unique:camiones,placa'],
            'modelo' => ['required', 'string', 'max:10'],
            'camioneros' => ['sometimes', 'array'],
            'camioneros.*' => ['exists:camioneros,id'],
        ];
    }
}
