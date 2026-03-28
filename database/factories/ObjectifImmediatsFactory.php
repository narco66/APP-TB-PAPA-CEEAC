<?php

namespace Database\Factories;

use App\Models\ActionPrioritaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObjectifImmediatsFactory extends Factory
{
    public function definition(): array
    {
        static $n = 0;
        $n++;
        return [
            'action_prioritaire_id' => ActionPrioritaire::factory(),
            'code'    => 'OI-TEST-' . str_pad($n, 3, '0', STR_PAD_LEFT),
            'libelle' => fake()->sentence(5),
            'statut'  => 'planifie',
            'taux_atteinte' => 0,
            'ordre'   => $n,
        ];
    }
}
