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
            'placa' => ['sometimes', 'required', 'string', 'max:10', 'min:5', 'regex:/^[A-Z0-9]+$/', Rule::unique('camiones', 'placa')->ignore($this->route('camion'))],
            'modelo' => ['sometimes', 'required', 'string', 'max:10', 'min:2'],
            'camioneros' => ['sometimes', 'array'],
            'camioneros.*' => ['integer', 'exists:camioneros,id', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'placa.required' => 'La placa es obligatoria.',
            'placa.unique' => 'Esta placa ya está registrada.',
            'placa.regex' => 'La placa solo puede contener letras mayúsculas y números.',
            'placa.max' => 'La placa no puede exceder 10 caracteres.',
            'placa.min' => 'La placa debe tener al menos 5 caracteres.',
            'modelo.required' => 'El modelo es obligatorio.',
            'modelo.max' => 'El modelo no puede exceder 10 caracteres.',
            'modelo.min' => 'El modelo debe tener al menos 2 caracteres.',
            'camioneros.*.exists' => 'Uno o más camioneros seleccionados no existen.',
            'camioneros.*.distinct' => 'No se pueden asignar camioneros duplicados.',
        ];
    }
}
