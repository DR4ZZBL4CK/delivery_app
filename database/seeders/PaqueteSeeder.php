<?php

namespace Database\Seeders;

use App\Models\Camionero;
use App\Models\Camion;
use App\Models\EstadoPaquete;
use App\Models\Paquete;
use App\Models\DetallePaquete;
use App\Models\TipoMercancia;
use Illuminate\Database\Seeder;

class PaqueteSeeder extends Seeder
{
    public function run(): void
    {
        // Crear relaciones camionero-camión
        $camioneros = Camionero::all();
        $camiones = Camion::all();
        
        foreach ($camioneros as $index => $camionero) {
            $camion = $camiones[$index % $camiones->count()];
            $camionero->camiones()->attach($camion->id);
        }

        // Crear paquetes de ejemplo
        $estados = EstadoPaquete::all();
        $tipos = TipoMercancia::all();

        $paquetes = [
            [
                'camioneros_id' => $camioneros[0]->id,
                'estados_paquetes_id' => $estados[0]->id, // Pendiente
                'direccion' => 'Calle 123 #45-67',
                'detalles' => [
                    [
                        'tipo_mercancia_id' => $tipos[0]->id, // Electrodomésticos
                        'dimencion' => '50x30x40 cm',
                        'peso' => '15 kg',
                        'fecha_entrega' => now()->addDays(2)->format('Y-m-d'),
                    ],
                ],
            ],
            [
                'camioneros_id' => $camioneros[1]->id,
                'estados_paquetes_id' => $estados[1]->id, // En tránsito
                'direccion' => 'Carrera 45 #78-90',
                'detalles' => [
                    [
                        'tipo_mercancia_id' => $tipos[1]->id, // Ropa
                        'dimencion' => '30x20x10 cm',
                        'peso' => '2 kg',
                        'fecha_entrega' => now()->addDays(1)->format('Y-m-d'),
                    ],
                ],
            ],
            [
                'camioneros_id' => $camioneros[2]->id,
                'estados_paquetes_id' => $estados[2]->id, // Entregado
                'direccion' => 'Avenida 80 #12-34',
                'detalles' => [
                    [
                        'tipo_mercancia_id' => $tipos[2]->id, // Alimentos
                        'dimencion' => '40x25x15 cm',
                        'peso' => '5 kg',
                        'fecha_entrega' => now()->subDays(1)->format('Y-m-d'),
                    ],
                ],
            ],
        ];

        foreach ($paquetes as $paqueteData) {
            $detalles = $paqueteData['detalles'];
            unset($paqueteData['detalles']);

            $paquete = Paquete::create($paqueteData);

            foreach ($detalles as $detalle) {
                DetallePaquete::create([
                    'paquetes_id' => $paquete->id,
                    'tipo_mercancia_id' => $detalle['tipo_mercancia_id'],
                    'dimencion' => $detalle['dimencion'],
                    'peso' => $detalle['peso'],
                    'fecha_entrega' => $detalle['fecha_entrega'],
                ]);
            }
        }
    }
}
