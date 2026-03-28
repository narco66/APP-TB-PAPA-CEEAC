<?php

namespace Database\Factories;

use App\Models\ObjectifImmediats;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResultatAttenduFactory extends Factory
{
    public function definition(): array
    {
        static $n = 0;
        $n++;
        return [
            'objectif_immediat_id' => ObjectifImmediats::factory(),
            'code'         => 'RA-TEST-' . str_pad($n, 3, '0', STR_PAD_LEFT),
            'libelle'      => fake()->sentence(5),
            'type_resultat' => 'output',
            'statut'       => 'planifie',
            'taux_atteinte' => 0,
            'ordre'        => $n,
        ];
    }
}
