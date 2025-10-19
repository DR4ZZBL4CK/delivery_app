<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TipoMercanciaFactory extends Factory
{
    public function definition(): array
    {
        $tipos = ['Electrodomésticos', 'Ropa', 'Alimentos', 'Electrónicos', 'Muebles'];
        
        return [
            'tipo' => $this->faker->randomElement($tipos),
        ];
    }
}
