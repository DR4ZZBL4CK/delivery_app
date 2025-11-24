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
            'documento' => ['sometimes', 'required', 'string', 'max:10', 'min:5', 'regex:/^[0-9]+$/', Rule::unique('camioneros', 'documento')->ignore($this->route('camionero'))],
            'nombre' => ['sometimes', 'required', 'string', 'max:45', 'min:2', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'apellido' => ['sometimes', 'required', 'string', 'max:45', 'min:2', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'fecha_nacimiento' => ['sometimes', 'required', 'date', 'before:today', 'before:-18 years'],
            'licencia' => ['sometimes', 'required', 'string', 'max:10', 'min:5', 'regex:/^[A-Z0-9]+$/', Rule::unique('camioneros', 'licencia')->ignore($this->route('camionero'))],
            'telefono' => ['sometimes', 'required', 'string', 'max:15', 'min:7', 'regex:/^[0-9\s\-\+\(\)]+$/'],
            'camiones' => ['sometimes', 'array'],
            'camiones.*' => ['integer', 'exists:camiones,id', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'documento.required' => 'El documento es obligatorio.',
            'documento.unique' => 'Este documento ya está registrado.',
            'documento.regex' => 'El documento solo puede contener números.',
            'documento.max' => 'El documento no puede exceder 10 caracteres.',
            'documento.min' => 'El documento debe tener al menos 5 caracteres.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre.max' => 'El nombre no puede exceder 45 caracteres.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'apellido.max' => 'El apellido no puede exceder 45 caracteres.',
            'apellido.min' => 'El apellido debe tener al menos 2 caracteres.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy y el camionero debe ser mayor de 18 años.',
            'licencia.required' => 'La licencia es obligatoria.',
            'licencia.unique' => 'Esta licencia ya está registrada.',
            'licencia.regex' => 'La licencia solo puede contener letras mayúsculas y números.',
            'licencia.max' => 'La licencia no puede exceder 10 caracteres.',
            'licencia.min' => 'La licencia debe tener al menos 5 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono tiene un formato inválido.',
            'telefono.max' => 'El teléfono no puede exceder 15 caracteres.',
            'telefono.min' => 'El teléfono debe tener al menos 7 caracteres.',
            'camiones.*.exists' => 'Uno o más camiones seleccionados no existen.',
            'camiones.*.distinct' => 'No se pueden asignar camiones duplicados.',
        ];
    }
}
