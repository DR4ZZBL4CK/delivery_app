<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EstadoPaqueteFactory extends Factory
{
    public function definition(): array
    {
        $estados = ['Pendiente', 'En tránsito', 'Entregado', 'Devuelto'];
        
        return [
            'estado' => $this->faker->randomElement($estados),
        ];
    }
}
