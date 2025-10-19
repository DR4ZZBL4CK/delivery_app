<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CamioneroResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'documento' => $this->documento,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'licencia' => $this->licencia,
            'telefono' => $this->telefono,
            'camiones' => $this->whenLoaded('camiones', function () {
                return $this->camiones->map(function ($camion) {
                    return [
                        'id' => $camion->id,
                        'placa' => $camion->placa,
                        'modelo' => $camion->modelo,
                    ];
                });
            }),
            'paquetes_count' => $this->when(isset($this->paquetes_count), $this->paquetes_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
