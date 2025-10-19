<?php

namespace Database\Seeders;

use App\Models\EstadoPaquete;
use Illuminate\Database\Seeder;

class EstadoPaqueteSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['estado' => 'Pendiente'],
            ['estado' => 'En tránsito'],
            ['estado' => 'Entregado'],
            ['estado' => 'Devuelto'],
        ];

        foreach ($estados as $estado) {
            EstadoPaquete::create($estado);
        }
    }
}
