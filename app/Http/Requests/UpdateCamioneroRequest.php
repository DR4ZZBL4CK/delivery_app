<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCamioneroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documento' => ['sometimes', 'required', 'string', 'max:10', Rule::unique('camioneros', 'documento')->ignore($this->route('camionero'))],
            'nombre' => ['sometimes', 'required', 'string', 'max:45'],
            'apellido' => ['sometimes', 'required', 'string', 'max:45'],
            'fecha_nacimiento' => ['sometimes', 'required', 'date', 'before:today'],
            'licencia' => ['sometimes', 'required', 'string', 'max:10', Rule::unique('camioneros', 'licencia')->ignore($this->route('camionero'))],
            'telefono' => ['sometimes', 'required', 'string', 'max:15'],
            'camiones' => ['sometimes', 'array'],
            'camiones.*' => ['exists:camiones,id'],
        ];
    }
}
