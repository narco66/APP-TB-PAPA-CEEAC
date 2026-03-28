<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PapaFactory extends Factory
{
    public function definition(): array
    {
        $annee = fake()->numberBetween(2024, 2030);
        return [
            'code'        => 'PAPA-' . $annee . '-' . fake()->unique()->numerify('##'),
            'libelle'     => "Plan d'Action Prioritaire Annuel {$annee}",
            'annee'       => $annee,
            'date_debut'  => "{$annee}-01-01",
            'date_fin'    => "{$annee}-12-31",
            'statut'      => 'brouillon',
            'est_verrouille' => false,
            'taux_execution_physique'   => 0,
            'taux_execution_financiere' => 0,
        ];
    }

    public function enExecution(): static
    {
        return $this->state(['statut' => 'en_execution']);
    }

    public function verrouille(): static
    {
        return $this->state(['statut' => 'archive', 'est_verrouille' => true]);
    }
}
