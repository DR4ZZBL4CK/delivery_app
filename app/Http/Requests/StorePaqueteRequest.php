<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaqueteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'camioneros_id' => ['required', 'exists:camioneros,id'],
            'estados_paquetes_id' => ['required', 'exists:estados_paquetes,id'],
            'direccion' => ['required', 'string', 'max:25'],
            'detalles' => ['array'],
            'detalles.*.tipo_mercancia_id' => ['required_with:detalles', 'exists:tipo_mercancia,id'],
            'detalles.*.dimencion' => ['required_with:detalles', 'string', 'max:45'],
            'detalles.*.peso' => ['required_with:detalles', 'string', 'max:45'],
            'detalles.*.fecha_entrega' => ['required_with:detalles', 'date'],
        ];
    }
}


