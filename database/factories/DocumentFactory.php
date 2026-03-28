<?php

namespace Database\Factories;

use App\Models\CategorieDocument;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $name = fake()->slug();

        return [
            'categorie_id' => CategorieDocument::query()->inRandomOrder()->value('id') ?? null,
            'documentable_type' => null,
            'documentable_id' => null,
            'titre' => fake()->randomElement([
                'Note de cadrage institutionnelle',
                'Rapport de mission technique',
                'Fiche de preuve consolidée',
            ]),
            'description' => 'Document de démonstration généré par factory.',
            'reference' => 'DOC/' . fake()->numerify('###/2026'),
            'date_document' => now()->subDays(fake()->numberBetween(5, 180)),
            'chemin_fichier' => "ged/factory/{$name}.pdf",
            'nom_fichier_original' => "{$name}.pdf",
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'taille_octets' => fake()->numberBetween(120000, 1400000),
            'version' => '1.0',
            'version_precedente_id' => null,
            'confidentialite' => fake()->randomElement(['public', 'interne', 'confidentiel']),
            'statut' => fake()->randomElement(['brouillon', 'soumis', 'valide']),
            'depose_par' => User::factory(),
            'valide_par' => null,
            'valide_le' => null,
            'est_archive' => false,
            'archive_le' => null,
            'hash_sha256' => hash('sha256', $name),
        ];
    }
}
