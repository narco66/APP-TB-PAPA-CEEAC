<?php

namespace Database\Factories;

use App\Models\Departement;
use App\Models\Papa;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionPrioritaireFactory extends Factory
{
    public function definition(): array
    {
        static $n = 0;
        $n++;
        return [
            'papa_id'        => Papa::factory(),
            'departement_id' => Departement::factory(),
            'code'           => 'AP-TEST-' . str_pad($n, 3, '0', STR_PAD_LEFT),
            'libelle'        => fake()->sentence(6),
            'qualification'  => 'technique',
            'statut'         => 'planifie',
            'taux_realisation' => 0,
            'ordre'          => $n,
        ];
    }
}
