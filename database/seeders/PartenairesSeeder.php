<?php

namespace Database\Seeders;

use App\Models\Partenaire;
use Illuminate\Database\Seeder;

class PartenairesSeeder extends Seeder
{
    public function run(): void
    {
        $partenaires = [
            ['code' => 'UA', 'libelle' => 'Union Africaine', 'sigle' => 'UA', 'type' => 'multilateral'],
            ['code' => 'UE', 'libelle' => 'Union Européenne', 'sigle' => 'UE', 'type' => 'bilateral'],
            ['code' => 'BAD', 'libelle' => 'Banque Africaine de Développement', 'sigle' => 'BAD', 'type' => 'multilateral'],
            ['code' => 'BM', 'libelle' => 'Banque Mondiale', 'sigle' => 'BM', 'type' => 'multilateral'],
            ['code' => 'PNUD', 'libelle' => 'Programme des Nations Unies pour le Développement', 'sigle' => 'PNUD', 'type' => 'multilateral'],
            ['code' => 'FMI', 'libelle' => 'Fonds Monétaire International', 'sigle' => 'FMI', 'type' => 'multilateral'],
            ['code' => 'AFD', 'libelle' => 'Agence Française de Développement', 'sigle' => 'AFD', 'type' => 'bilateral'],
            ['code' => 'GIZ', 'libelle' => 'Deutsche Gesellschaft für Internationale Zusammenarbeit', 'sigle' => 'GIZ', 'type' => 'bilateral'],
            ['code' => 'BDEAC', 'libelle' => 'Banque de Développement des États de l\'Afrique Centrale', 'sigle' => 'BDEAC', 'type' => 'multilateral'],
            ['code' => 'BEAC', 'libelle' => 'Banque des États de l\'Afrique Centrale', 'sigle' => 'BEAC', 'type' => 'multilateral'],
        ];

        foreach ($partenaires as $p) {
            Partenaire::updateOrCreate(['code' => $p['code']], $p);
        }
    }
}
