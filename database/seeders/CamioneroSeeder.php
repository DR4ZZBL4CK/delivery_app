<?php

namespace Database\Seeders;

use App\Models\Camionero;
use Illuminate\Database\Seeder;

class CamioneroSeeder extends Seeder
{
    public function run(): void
    {
        $camioneros = [
            [
                'documento' => '12345678',
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'fecha_nacimiento' => '1985-03-15',
                'licencia' => 'A123456',
                'telefono' => '3001234567',
            ],
            [
                'documento' => '87654321',
                'nombre' => 'María',
                'apellido' => 'González',
                'fecha_nacimiento' => '1990-07-22',
                'licencia' => 'B789012',
                'telefono' => '3007654321',
            ],
            [
                'documento' => '11223344',
                'nombre' => 'Carlos',
                'apellido' => 'López',
                'fecha_nacimiento' => '1988-11-08',
                'licencia' => 'C345678',
                'telefono' => '3009876543',
            ],
        ];

        foreach ($camioneros as $camionero) {
            Camionero::create($camionero);
        }
    }
}
