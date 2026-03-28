<?php

namespace Database\Factories;

use App\Models\Papa;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlerteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'papa_id'        => Papa::factory(),
            'alertable_type' => Papa::class,
            'alertable_id'   => 1, // overridden in tests
            'type_alerte'    => 'retard_activite',
            'niveau'         => 'attention',
            'titre'          => fake()->sentence(4),
            'message'        => fake()->sentence(10),
            'statut'         => 'nouvelle',
            'auto_generee'   => false,
            'escaladee'      => false,
        ];
    }
}
