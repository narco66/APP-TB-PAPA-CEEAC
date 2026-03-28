<?php

namespace Database\Seeders;

use App\Models\Pays;
use Illuminate\Database\Seeder;

class PaysSeeder extends Seeder
{
    public function run(): void
    {
        $pays = [
            ['nom' => 'Angola', 'nom_court' => 'Angola', 'code_iso' => 'AGO'],
            ['nom' => 'Burundi', 'nom_court' => 'Burundi', 'code_iso' => 'BDI'],
            ['nom' => 'Cameroun', 'nom_court' => 'Cameroun', 'code_iso' => 'CMR'],
            ['nom' => 'République Centrafricaine', 'nom_court' => 'RCA', 'code_iso' => 'CAF'],
            ['nom' => 'République du Congo', 'nom_court' => 'Congo', 'code_iso' => 'COG'],
            ['nom' => 'République Démocratique du Congo', 'nom_court' => 'RDC', 'code_iso' => 'COD'],
            ['nom' => 'Guinée Équatoriale', 'nom_court' => 'GE', 'code_iso' => 'GNQ'],
            ['nom' => 'Gabon', 'nom_court' => 'Gabon', 'code_iso' => 'GAB'],
            ['nom' => 'Rwanda', 'nom_court' => 'Rwanda', 'code_iso' => 'RWA'],
            ['nom' => 'Sao Tomé et Príncipe', 'nom_court' => 'STP', 'code_iso' => 'STP'],
            ['nom' => 'Tchad', 'nom_court' => 'Tchad', 'code_iso' => 'TCD'],
        ];

        foreach ($pays as $p) {
            Pays::updateOrCreate(['code_iso' => $p['code_iso']], $p);
        }
    }
}
