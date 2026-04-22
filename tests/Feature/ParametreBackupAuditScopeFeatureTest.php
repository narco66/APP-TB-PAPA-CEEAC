<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParametreBackupAuditScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $scopedAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = $this->creerRoleAvecPermissions('param_backup_scope_local', [
            'parametres.sauvegardes.voir',
            'parametres.sauvegardes.exporter',
            'parametres.sauvegardes.importer',
            'admin.audit_log',
        ]);

        $this->scopedAdmin = User::factory()->create([
            'actif' => true,
            'departement_id' => \App\Models\Departement::factory()->create()->id,
            'scope_level' => 'departement',
        ]);
        $this->scopedAdmin->assignRole($role);
    }

    public function test_admin_local_peut_voir_la_page_sauvegardes_mais_pas_exporter_ni_importer(): void
    {
        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.sauvegardes.index'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.sauvegardes.exporter', 'parametres'))
            ->assertForbidden();

        $file = tmpfile();
        fwrite($file, json_encode(['meta' => ['exporte_le' => now()->toIso8601String()]]));
        $path = stream_get_meta_data($file)['uri'];

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.sauvegardes.importer'), [
                'fichier' => new \Illuminate\Http\UploadedFile($path, 'import.json', 'application/json', null, true),
                'confirmation' => 'IMPORTER',
            ])
            ->assertForbidden();

        fclose($file);
    }

    public function test_admin_local_ne_peut_pas_consulter_l_audit_log_global(): void
    {
        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.audit-log'))
            ->assertForbidden();
    }

    public function test_admin_local_ne_peut_pas_consulter_ni_exporter_l_audit_metier_global(): void
    {
        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.audit-events'))
            ->assertForbidden();

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.audit-events.export'))
            ->assertForbidden();
    }
}
