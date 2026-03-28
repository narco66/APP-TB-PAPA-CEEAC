<?php

namespace Database\Seeders;

use App\Models\CategorieDocument;
use Illuminate\Database\Seeder;

class CategoriesDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'PLAN', 'libelle' => 'Plans et stratégies', 'icone' => '📋'],
            ['code' => 'RAPP', 'libelle' => 'Rapports d\'avancement', 'icone' => '📊'],
            ['code' => 'PROT', 'libelle' => 'Procès-verbaux et comptes rendus', 'icone' => '📝'],
            ['code' => 'CONT', 'libelle' => 'Contrats et conventions', 'icone' => '📄'],
            ['code' => 'FACT', 'libelle' => 'Factures et pièces justificatives', 'icone' => '🧾'],
            ['code' => 'PHOT', 'libelle' => 'Photos et preuves visuelles', 'icone' => '📸'],
            ['code' => 'FORM', 'libelle' => 'Formulaires officiels', 'icone' => '📃'],
            ['code' => 'DECI', 'libelle' => 'Décisions et actes officiels', 'icone' => '⚖️'],
            ['code' => 'CORR', 'libelle' => 'Correspondances officielles', 'icone' => '✉️'],
            ['code' => 'TECH', 'libelle' => 'Documents techniques', 'icone' => '🔧'],
            ['code' => 'AUTR', 'libelle' => 'Autres documents', 'icone' => '📎'],
        ];

        foreach ($categories as $c) {
            CategorieDocument::updateOrCreate(['code' => $c['code']], $c);
        }
    }
}
