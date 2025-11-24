<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaqueteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'camioneros_id' => ['sometimes', 'required', 'integer', 'exists:camioneros,id'],
            'estados_paquetes_id' => ['sometimes', 'required', 'integer', 'exists:estados_paquetes,id'],
            'direccion' => ['sometimes', 'required', 'string', 'max:25', 'min:5'],
            'detalles' => ['sometimes', 'array', 'min:1'],
            'detalles.*.id' => ['sometimes', 'integer', 'exists:detalles_paquetes,id'],
            'detalles.*.tipo_mercancia_id' => ['required_with:detalles', 'integer', 'exists:tipo_mercancia,id'],
            'detalles.*.dimencion' => ['required_with:detalles', 'string', 'max:45', 'min:3'],
            'detalles.*.peso' => ['required_with:detalles', 'string', 'max:45', 'min:2'],
            'detalles.*.fecha_entrega' => ['required_with:detalles', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'camioneros_id.required' => 'El camionero es obligatorio.',
            'camioneros_id.exists' => 'El camionero seleccionado no existe.',
            'estados_paquetes_id.required' => 'El estado del paquete es obligatorio.',
            'estados_paquetes_id.exists' => 'El estado seleccionado no existe.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección no puede exceder 25 caracteres.',
            'direccion.min' => 'La dirección debe tener al menos 5 caracteres.',
            'detalles.min' => 'Debe incluir al menos un detalle del paquete.',
            'detalles.*.tipo_mercancia_id.required_with' => 'El tipo de mercancía es obligatorio para cada detalle.',
            'detalles.*.tipo_mercancia_id.exists' => 'El tipo de mercancía seleccionado no existe.',
            'detalles.*.dimencion.required_with' => 'La dimensión es obligatoria para cada detalle.',
            'detalles.*.dimencion.max' => 'La dimensión no puede exceder 45 caracteres.',
            'detalles.*.peso.required_with' => 'El peso es obligatorio para cada detalle.',
            'detalles.*.peso.max' => 'El peso no puede exceder 45 caracteres.',
            'detalles.*.fecha_entrega.required_with' => 'La fecha de entrega es obligatoria para cada detalle.',
            'detalles.*.fecha_entrega.date' => 'La fecha de entrega debe ser una fecha válida.',
            'detalles.*.fecha_entrega.after' => 'La fecha de entrega debe ser posterior a hoy.',
        ];
    }
}


