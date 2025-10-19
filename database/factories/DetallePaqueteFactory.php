<?php

namespace Database\Factories;

use App\Models\Paquete;
use App\Models\TipoMercancia;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetallePaqueteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paquetes_id' => Paquete::factory(),
            'tipo_mercancia_id' => TipoMercancia::factory(),
            'dimencion' => $this->faker->numerify('##x##x## cm'),
            'peso' => $this->faker->numerify('## kg'),
            'fecha_entrega' => $this->faker->dateTimeBetween('now', '+30 days'),
        ];
    }
}
