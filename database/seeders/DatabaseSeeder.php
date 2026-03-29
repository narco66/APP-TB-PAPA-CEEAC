<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        fake()->seed(20260328);

        $this->call([
            ReferenceSeeder::class,
            OrganizationSeeder::class,
            RolesPermissionsSeeder::class,
            ReportDefinitionSeeder::class,
            UserSeeder::class,
            WorkflowDefinitionSeeder::class,
            NotificationRuleSeeder::class,
            ParametreSeeder::class,
            DemoScenarioSeeder::class,
        ]);
    }
}
