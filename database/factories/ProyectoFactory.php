<?php

namespace Database\Factories;

use App\Models\Equipo;
use App\Models\Evento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proyecto>
 */
class ProyectoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'equipo_id' => Equipo::factory(),
            'evento_id' => Evento::factory(),
            'nombre' => $this->faker->sentence(4),
            'descripcion' => $this->faker->paragraph,
            'repositorio_url' => $this->faker->url,
        ];
    }
}
