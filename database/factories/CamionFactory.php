<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CamionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'placa' => $this->faker->unique()->bothify('???###'),
            'modelo' => $this->faker->year(),
        ];
    }
}
