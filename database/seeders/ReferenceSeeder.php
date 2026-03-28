<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PaysSeeder::class,
            CategoriesDocumentsSeeder::class,
            PartenairesSeeder::class,
        ]);
    }
}
