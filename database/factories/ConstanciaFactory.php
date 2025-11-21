<?php

namespace Database\Factories;

use App\Models\Evento;
use App\Models\Participante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Constancia>
 */
class ConstanciaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'participante_id' => Participante::factory(),
            'evento_id' => Evento::factory(),
            'tipo' => $this->faker->randomElement(['asistente', 'ganador', 'ponente']),
            'archivo_path' => $this->faker->filePath(),
            'codigo_qr' => $this->faker->unique()->md5,
        ];
    }
}
