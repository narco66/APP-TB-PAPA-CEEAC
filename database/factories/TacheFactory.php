<?php

namespace Database\Factories;

use App\Models\Activite;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tache>
 */
class TacheFactory extends Factory
{
    protected $model = Tache::class;

    public function definition(): array
    {
        $start = now()->subDays(fake()->numberBetween(5, 40));
        $end = (clone $start)->addDays(fake()->numberBetween(5, 20));

        return [
            'activite_id' => Activite::factory(),
            'parent_tache_id' => null,
            'code' => 'TCH-' . fake()->unique()->numerify('####'),
            'libelle' => fake()->randomElement([
                'Préparer le dossier technique détaillé',
                'Coordonner la collecte des contributions',
                'Consolider les preuves et notes de suivi',
            ]),
            'description' => 'Tâche générée pour les tests et la démo.',
            'ordre' => fake()->numberBetween(1, 4),
            'date_debut_prevue' => $start,
            'date_fin_prevue' => $end,
            'date_debut_reelle' => $start->copy()->addDay(),
            'date_fin_reelle' => null,
            'statut' => fake()->randomElement(['a_faire', 'en_cours', 'en_revue']),
            'taux_realisation' => fake()->numberBetween(5, 80),
            'assignee_id' => User::factory(),
            'notes' => 'Tâche générée automatiquement.',
        ];
    }
}
