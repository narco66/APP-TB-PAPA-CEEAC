<?php

namespace Database\Factories;

use App\Models\Direction;
use App\Models\ResultatAttendu;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActiviteFactory extends Factory
{
    public function definition(): array
    {
        static $n = 0;
        $n++;
        return [
            'resultat_attendu_id' => ResultatAttendu::factory(),
            'direction_id'        => Direction::factory(),
            'code'                => 'ACT-TEST-' . str_pad($n, 3, '0', STR_PAD_LEFT),
            'libelle'             => fake()->sentence(5),
            'statut'              => 'planifiee',
            'taux_realisation'    => 0,
            'date_debut_prevue'   => now()->toDateString(),
            'date_fin_prevue'     => now()->addDays(30)->toDateString(),
            'ordre'               => $n,
        ];
    }

    public function enRetard(): static
    {
        return $this->state([
            'statut'           => 'en_cours',
            'date_fin_prevue'  => now()->subDays(5)->toDateString(),
        ]);
    }

    public function terminee(): static
    {
        return $this->state([
            'statut'           => 'terminee',
            'taux_realisation' => 100,
            'date_fin_reelle'  => now()->toDateString(),
        ]);
    }
}
