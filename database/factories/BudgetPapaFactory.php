<?php

namespace Database\Factories;

use App\Models\ActionPrioritaire;
use App\Models\Activite;
use App\Models\BudgetPapa;
use App\Models\Papa;
use App\Models\Partenaire;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BudgetPapa>
 */
class BudgetPapaFactory extends Factory
{
    protected $model = BudgetPapa::class;

    public function definition(): array
    {
        $montantPrevu = fake()->numberBetween(15000000, 180000000);
        $montantEngage = (int) round($montantPrevu * fake()->randomFloat(2, 0.35, 0.95));
        $montantDecaisse = (int) round($montantEngage * fake()->randomFloat(2, 0.3, 0.9));

        return [
            'papa_id' => Papa::factory(),
            'action_prioritaire_id' => ActionPrioritaire::factory(),
            'activite_id' => Activite::factory(),
            'partenaire_id' => null,
            'source_financement' => 'budget_ceeac',
            'libelle_ligne' => 'Ligne budgétaire opérationnelle',
            'annee_budgetaire' => fake()->numberBetween(2024, 2027),
            'devise' => 'XAF',
            'montant_prevu' => $montantPrevu,
            'montant_engage' => $montantEngage,
            'montant_decaisse' => $montantDecaisse,
            'montant_solde' => max(0, $montantPrevu - $montantEngage),
            'notes' => 'Ligne budgétaire générée par factory.',
            'created_by' => User::factory(),
        ];
    }

    public function partnerFunded(): static
    {
        return $this->state(fn () => [
            'source_financement' => 'partenaire_technique_financier',
            'partenaire_id' => Partenaire::query()->inRandomOrder()->value('id') ?? Partenaire::factory(),
        ]);
    }
}
