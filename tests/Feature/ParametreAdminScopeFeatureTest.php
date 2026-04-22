<?php

namespace Tests\Feature;

use App\Models\ActionPrioritaire;
use App\Models\Departement;
use App\Models\Papa;
use App\Models\Referentiel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParametreAdminScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $scopedAdmin;
    private Departement $departementVisible;
    private Departement $departementMasque;

    protected function setUp(): void
    {
        parent::setUp();

        $role = $this->creerRoleAvecPermissions('param_scope_local', [
            'parametres.papa.voir',
            'parametres.papa.modifier',
            'parametres.papa.archiver',
            'parametres.referentiels.voir',
            'parametres.referentiels.gerer',
        ]);

        $this->departementVisible = Departement::factory()->create([
            'libelle' => 'Departement Param Visible',
        ]);
        $this->departementMasque = Departement::factory()->create([
            'libelle' => 'Departement Param Masque',
        ]);

        $this->scopedAdmin = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $this->scopedAdmin->assignRole($role);
    }

    public function test_parametres_papa_n_affiche_que_les_papa_du_perimetre(): void
    {
        $papaVisible = Papa::factory()->create([
            'code' => 'PAPA-SCOPE-VISIBLE',
            'statut' => 'valide',
        ]);
        $papaMasque = Papa::factory()->create([
            'code' => 'PAPA-SCOPE-MASQUE',
            'statut' => 'valide',
        ]);

        ActionPrioritaire::factory()->create([
            'papa_id' => $papaVisible->id,
            'departement_id' => $this->departementVisible->id,
        ]);
        ActionPrioritaire::factory()->create([
            'papa_id' => $papaMasque->id,
            'departement_id' => $this->departementMasque->id,
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.papa.index'))
            ->assertOk()
            ->assertSee('PAPA-SCOPE-VISIBLE')
            ->assertDontSee('PAPA-SCOPE-MASQUE')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Departement Param Visible');
    }

    public function test_parametres_papa_refuse_action_hors_perimetre(): void
    {
        $papaMasque = Papa::factory()->create([
            'code' => 'PAPA-HORS-PERIMETRE',
            'statut' => 'valide',
        ]);

        ActionPrioritaire::factory()->create([
            'papa_id' => $papaMasque->id,
            'departement_id' => $this->departementMasque->id,
        ]);

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.papa.activer', $papaMasque))
            ->assertForbidden();
    }

    public function test_parametres_referentiels_reste_visible_mais_non_gerable_pour_un_admin_local(): void
    {
        Referentiel::create([
            'type' => 'type_risque',
            'code' => 'OPER',
            'libelle' => 'Operationnel',
            'ordre' => 1,
            'actif' => true,
            'est_systeme' => false,
            'cree_par' => $this->scopedAdmin->id,
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.referentiels.index'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.referentiels.store', 'type_risque'), [
                'code' => 'FIN',
                'libelle' => 'Financier',
                'ordre' => 2,
            ])
            ->assertForbidden();
    }
}
