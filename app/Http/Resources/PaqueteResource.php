<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaqueteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'direccion' => $this->direccion,
            'camionero' => [
                'id' => $this->camionero->id,
                'nombre' => $this->camionero->nombre,
                'apellido' => $this->camionero->apellido,
            ],
            'estado' => [
                'id' => $this->estado->id,
                'estado' => $this->estado->estado,
            ],
            'detalles' => $this->detalles->map(function ($d) {
                return [
                    'id' => $d->id,
                    'tipo_mercancia' => [
                        'id' => $d->tipoMercancia->id,
                        'tipo' => $d->tipoMercancia->tipo,
                    ],
                    'dimencion' => $d->dimencion,
                    'peso' => $d->peso,
                    'fecha_entrega' => $d->fecha_entrega,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


