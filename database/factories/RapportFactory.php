<?php

namespace Database\Factories;

use App\Models\Departement;
use App\Models\Direction;
use App\Models\Papa;
use App\Models\Rapport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rapport>
 */
class RapportFactory extends Factory
{
    protected $model = Rapport::class;

    public function definition(): array
    {
        return [
            'papa_id' => Papa::factory(),
            'direction_id' => Direction::factory(),
            'departement_id' => Departement::factory(),
            'titre' => fake()->randomElement([
                'Rapport trimestriel d’exécution',
                'Rapport flash de supervision',
                'Rapport sectoriel consolidé',
            ]),
            'type_rapport' => fake()->randomElement(['trimestriel', 'flash', 'ad_hoc']),
            'periode_couverte' => 'T1-' . fake()->numberBetween(2024, 2027),
            'annee' => fake()->numberBetween(2024, 2027),
            'numero_periode' => fake()->numberBetween(1, 4),
            'taux_execution_physique' => fake()->numberBetween(20, 95),
            'taux_execution_financiere' => fake()->numberBetween(15, 90),
            'faits_saillants' => 'Progression des activités prioritaires et arbitrages obtenus.',
            'difficultes_rencontrees' => 'Délais de remontée et contraintes de coordination.',
            'recommandations' => 'Renforcer la discipline de reporting et sécuriser les justificatifs.',
            'perspectives' => 'Poursuite des activités structurantes sur la prochaine période.',
            'statut' => fake()->randomElement(['brouillon', 'soumis', 'valide', 'publie']),
            'redige_par' => User::factory(),
            'valide_par' => null,
            'valide_le' => null,
            'publie_le' => null,
        ];
    }
}
