<?php

namespace Database\Factories;

use App\Models\Departement;
use Illuminate\Database\Eloquent\Factories\Factory;

class DirectionFactory extends Factory
{
    public function definition(): array
    {
        static $n = 0;
        $n++;
        return [
            'departement_id' => Departement::factory(),
            'code'           => 'DIR-' . str_pad($n, 3, '0', STR_PAD_LEFT),
            'libelle'        => fake()->words(5, true),
            'type_direction' => 'technique',
            'actif'          => true,
        ];
    }
}
