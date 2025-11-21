<?php

namespace Database\Factories;

use App\Models\CriterioEvaluacion;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calificacion>
 */
class CalificacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proyecto_id' => Proyecto::factory(),
            'juez_user_id' => User::factory(),
            'criterio_id' => CriterioEvaluacion::factory(),
            'puntuacion' => $this->faker->numberBetween(0, 100),
        ];
    }
}
