<?php

namespace Tests\Feature;

use App\Models\Papa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RapportPapaExportAccessFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_excel_papa_visible_est_autorise(): void
    {
        Queue::fake();

        $role = $this->creerRoleAvecPermissions('rapport_export_papa_visible', [
            'rapport.exporter',
            'papa.voir',
        ]);

        $user = User::factory()->create(['actif' => true]);
        $user->assignRole($role);

        $papa = Papa::factory()->create(['statut' => 'brouillon']);

        $this->actingAs($user)
            ->get(route('rapports.export-excel', $papa))
            ->assertOk();
    }

    public function test_export_excel_papa_archive_sans_permission_archive_retourne_403(): void
    {
        Queue::fake();

        $role = $this->creerRoleAvecPermissions('rapport_export_papa_sans_archive', [
            'rapport.exporter',
            'papa.voir',
        ]);

        $user = User::factory()->create(['actif' => true]);
        $user->assignRole($role);

        $papa = Papa::factory()->create(['statut' => 'archive']);

        $this->actingAs($user)
            ->get(route('rapports.export-excel', $papa))
            ->assertForbidden();
    }
}
