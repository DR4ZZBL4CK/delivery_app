<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CamioneroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'documento' => $this->faker->unique()->numerify('########'),
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '2000-01-01'),
            'licencia' => $this->faker->unique()->bothify('?#######'),
            'telefono' => $this->faker->numerify('3########'),
        ];
    }
}
