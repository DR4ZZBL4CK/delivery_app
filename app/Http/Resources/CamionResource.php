<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CamionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'placa' => $this->placa,
            'modelo' => $this->modelo,
            'camioneros' => $this->whenLoaded('camioneros', function () {
                return $this->camioneros->map(function ($camionero) {
                    return [
                        'id' => $camionero->id,
                        'documento' => $camionero->documento,
                        'nombre' => $camionero->nombre,
                        'apellido' => $camionero->apellido,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
