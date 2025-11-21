<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Carrera>
 */
class CarreraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement(['Ingeniería en Sistemas Computacionales', 'Arquitectura', 'Ingeniería Industrial', 'Diseño Gráfico', 'Derecho']),
            'clave' => $this->faker->unique()->bothify('??###'),
        ];
    }
}
