<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StructureOrganisationnelleSeeder::class,
        ]);
    }
}
