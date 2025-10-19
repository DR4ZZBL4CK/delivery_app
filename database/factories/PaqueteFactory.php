<?php

namespace Database\Factories;

use App\Models\Camionero;
use App\Models\EstadoPaquete;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaqueteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'camioneros_id' => Camionero::factory(),
            'estados_paquetes_id' => EstadoPaquete::factory(),
            'direccion' => $this->faker->address(),
        ];
    }
}
