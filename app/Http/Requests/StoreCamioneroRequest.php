<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCamioneroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documento' => ['required', 'string', 'max:10', 'unique:camioneros,documento'],
            'nombre' => ['required', 'string', 'max:45'],
            'apellido' => ['required', 'string', 'max:45'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'licencia' => ['required', 'string', 'max:10', 'unique:camioneros,licencia'],
            'telefono' => ['required', 'string', 'max:15'],
            'camiones' => ['sometimes', 'array'],
            'camiones.*' => ['exists:camiones,id'],
        ];
    }
}
