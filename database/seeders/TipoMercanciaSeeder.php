<?php

namespace Database\Seeders;

use App\Models\TipoMercancia;
use Illuminate\Database\Seeder;

class TipoMercanciaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['tipo' => 'Electrodomésticos'],
            ['tipo' => 'Ropa'],
            ['tipo' => 'Alimentos'],
            ['tipo' => 'Electrónicos'],
            ['tipo' => 'Muebles'],
        ];

        foreach ($tipos as $tipo) {
            TipoMercancia::create($tipo);
        }
    }
}
