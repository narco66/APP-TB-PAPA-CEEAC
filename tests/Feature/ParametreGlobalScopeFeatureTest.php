<?php

namespace Tests\Feature;

use App\Models\NotificationRule;
use App\Models\Papa;
use App\Models\User;
use App\Models\WorkflowDefinition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParametreGlobalScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $scopedAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = $this->creerRoleAvecPermissions('param_global_scope_local', [
            'parametres.generaux.voir',
            'parametres.generaux.modifier',
            'parametres.technique.voir',
            'parametres.technique.modifier',
            'parametres.workflows.voir',
            'parametres.workflows.modifier',
            'parametres.alertes.voir',
            'parametres.alertes.modifier',
            'parametres.rbm.voir',
            'parametres.rbm.modifier',
            'parametres.journal.voir',
        ]);

        $this->scopedAdmin = User::factory()->create([
            'actif' => true,
            'departement_id' => \App\Models\Departement::factory()->create()->id,
            'scope_level' => 'departement',
        ]);
        $this->scopedAdmin->assignRole($role);
    }

    public function test_admin_local_peut_consulter_les_pages_mais_pas_modifier_les_parametres_globaux(): void
    {
        $workflow = WorkflowDefinition::create([
            'code' => 'WF-SCOPE-001',
            'libelle' => 'Workflow scope test',
            'module_cible' => 'papa',
            'type_objet' => 'papa',
            'actif' => true,
            'version' => 1,
        ]);

        $rule = NotificationRule::create([
            'code' => 'RULE_SCOPE_TEST',
            'libelle' => 'Regle scope test',
            'event_type' => 'workflow_demarre',
            'canal' => 'in_app',
            'template_message' => 'Message',
            'actif' => true,
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.generaux'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.technique.index'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.workflows.index'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.alertes.index'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.rbm.index'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.journal'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.generaux.save'), [
                'app_nom' => 'TB-PAPA-CEEAC',
                'app_sigle' => 'TBPAPA',
                'app_organisation' => 'CEEAC',
                'app_langue_defaut' => 'fr',
                'app_fuseau_horaire' => 'Africa/Libreville',
                'app_devise' => 'XAF',
                'app_format_date' => 'd/m/Y',
                'app_annee_reference' => 2026,
            ])
            ->assertForbidden();

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.technique.save'), [
                'session_duree_minutes' => 60,
                'upload_taille_max_mo' => 10,
                'upload_formats_autorises' => 'pdf,docx',
                'pagination_items' => 25,
                'export_format_defaut' => 'pdf',
            ])
            ->assertForbidden();

        $this->actingAs($this->scopedAdmin)
            ->put(route('parametres.workflows.update', $workflow), [
                'libelle' => 'Workflow scope blocked',
                'description' => 'blocked',
                'actif' => 1,
            ])
            ->assertForbidden();

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.alertes.seuils.save'), [
                'alerte_seuil_retard_jours' => 7,
                'alerte_seuil_budget_pct' => 10,
            ])
            ->assertForbidden();

        $this->actingAs($this->scopedAdmin)
            ->put(route('parametres.alertes.rules.update', $rule), [
                'libelle' => 'Updated',
                'delai_minutes' => 15,
                'template_sujet' => 'Sujet',
                'template_message' => 'Message',
                'actif' => 1,
            ])
            ->assertForbidden();

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.rbm.save'), [
                'rbm_seuil_atteint' => 80,
                'rbm_seuil_risque' => 50,
                'rbm_seuil_non_atteint' => 30,
                'rbm_prefixe_ap' => 'AP',
                'rbm_prefixe_oi' => 'OI',
                'rbm_prefixe_ra' => 'RA',
            ])
            ->assertForbidden();
    }

    public function test_hub_parametres_masque_les_agregats_globaux_pour_un_admin_local(): void
    {
        Papa::factory()->create([
            'code' => 'PAPA-SCOPE-HUB',
            'statut' => 'en_execution',
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.hub'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');
    }
}
