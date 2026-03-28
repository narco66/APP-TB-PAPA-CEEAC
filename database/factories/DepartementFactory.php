<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepartementFactory extends Factory
{
    public function definition(): array
    {
        static $n = 0;
        $n++;
        return [
            'code'    => 'DEP-' . str_pad($n, 3, '0', STR_PAD_LEFT),
            'libelle' => fake()->words(4, true),
            'type'    => 'technique',
            'actif'   => true,
        ];
    }
}
