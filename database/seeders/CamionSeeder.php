<?php

namespace Database\Seeders;

use App\Models\Camion;
use Illuminate\Database\Seeder;

class CamionSeeder extends Seeder
{
    public function run(): void
    {
        $camiones = [
            [
                'placa' => 'ABC123',
                'modelo' => '2020',
            ],
            [
                'placa' => 'DEF456',
                'modelo' => '2021',
            ],
            [
                'placa' => 'GHI789',
                'modelo' => '2019',
            ],
        ];

        foreach ($camiones as $camion) {
            Camion::create($camion);
        }
    }
}
