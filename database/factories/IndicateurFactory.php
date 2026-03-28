<?php

namespace Database\Factories;

use App\Models\ActionPrioritaire;
use App\Models\Direction;
use App\Models\Indicateur;
use App\Models\ObjectifImmediats;
use App\Models\ResultatAttendu;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Indicateur>
 */
class IndicateurFactory extends Factory
{
    protected $model = Indicateur::class;

    public function definition(): array
    {
        $target = fake()->numberBetween(10, 95);

        return [
            'resultat_attendu_id' => ResultatAttendu::factory(),
            'objectif_immediat_id' => ObjectifImmediats::factory(),
            'action_prioritaire_id' => ActionPrioritaire::factory(),
            'code' => 'IND-' . fake()->unique()->numerify('####'),
            'libelle' => 'Taux de mise en oeuvre conforme',
            'definition' => 'Mesure la progression effective des livrables validés sur la période.',
            'unite_mesure' => '%',
            'type_indicateur' => 'quantitatif',
            'valeur_baseline' => fake()->numberBetween(5, 30),
            'valeur_cible_annuelle' => $target,
            'methode_calcul' => '(valeur réalisée / cible annuelle) x 100',
            'frequence_collecte' => 'trimestrielle',
            'source_donnees' => 'Rapports directionnels et fiches de suivi',
            'outil_collecte' => 'Tableau RBM',
            'responsable_id' => User::factory(),
            'direction_id' => Direction::factory(),
            'seuil_alerte_rouge' => 45,
            'seuil_alerte_orange' => 70,
            'seuil_alerte_vert' => 90,
            'taux_realisation_courant' => fake()->numberBetween(20, 100),
            'tendance' => fake()->randomElement(['hausse', 'stable', 'baisse']),
            'actif' => true,
            'notes' => 'Indicateur de démonstration.',
        ];
    }
}
