<?php

namespace Tests\Feature;

use App\Models\Departement;
use App\Models\Direction;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $scopedAdmin;
    private Departement $departementVisible;
    private Departement $departementMasque;

    protected function setUp(): void
    {
        parent::setUp();

        $role = $this->creerRoleAvecPermissions('admin_scope_local', [
            'admin.utilisateurs',
            'parametres.droits.voir',
            'parametres.droits.modifier',
        ]);

        $this->departementVisible = Departement::factory()->create([
            'libelle' => 'Departement Admin Visible',
        ]);
        $this->departementMasque = Departement::factory()->create([
            'libelle' => 'Departement Admin Masque',
        ]);

        $this->scopedAdmin = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $this->scopedAdmin->assignRole($role);
    }

    public function test_admin_scope_sur_structure_limite_les_departements_et_directions(): void
    {
        $directionVisible = Direction::factory()->create([
            'departement_id' => $this->departementVisible->id,
            'libelle' => 'Direction Admin Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $this->departementMasque->id,
            'libelle' => 'Direction Admin Masquee',
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.structure.departements'))
            ->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('Departement Admin Visible')
            ->assertDontSee('Departement Admin Masque');

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.structure.directions'))
            ->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('Direction Admin Visible')
            ->assertDontSee('Direction Admin Masquee');
    }

    public function test_admin_scope_ne_peut_pas_editer_une_direction_hors_perimetre(): void
    {
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $this->departementMasque->id,
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.structure.directions.edit', $directionMasquee))
            ->assertForbidden();
    }

    public function test_parametres_droits_affiche_seulement_les_utilisateurs_du_perimetre(): void
    {
        $roleMetier = Role::create(['name' => 'lecteur_scope_test', 'guard_name' => 'web']);

        $visible = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementVisible->id,
            'scope_level' => 'departement',
            'name' => 'Visible User',
        ]);
        $visible->assignRole($roleMetier);

        $masque = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementMasque->id,
            'scope_level' => 'departement',
            'name' => 'Masked User',
        ]);
        $masque->assignRole($roleMetier);

        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.droits.roles.show', $roleMetier))
            ->assertOk()
            ->assertSee('Visible User')
            ->assertDontSee('Masked User');
    }

    public function test_parametres_droits_refuse_toggle_user_hors_perimetre(): void
    {
        $target = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementMasque->id,
            'scope_level' => 'departement',
        ]);

        $this->actingAs($this->scopedAdmin)
            ->post(route('parametres.droits.users.toggle', $target))
            ->assertForbidden();
    }

    public function test_parametres_droits_index_affiche_un_compte_utilisateur_scope_par_role(): void
    {
        $roleMetier = Role::create(['name' => 'lecteur_scope_index', 'guard_name' => 'web']);

        $visible = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $visible->assignRole($roleMetier);

        $masque = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementMasque->id,
            'scope_level' => 'departement',
        ]);
        $masque->assignRole($roleMetier);

        $response = $this->actingAs($this->scopedAdmin)->get(route('parametres.droits.index'));

        $response->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('lecteur_scope_index')
            ->assertSee('utilisateurs');

        $this->assertStringContainsString('>1<', $response->getContent());
    }

    public function test_parametres_droits_matrice_affiche_le_perimetre(): void
    {
        $this->actingAs($this->scopedAdmin)
            ->get(route('parametres.droits.matrice'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');
    }

    public function test_admin_utilisateurs_n_affiche_que_les_comptes_du_perimetre(): void
    {
        $directionVisible = Direction::factory()->create([
            'departement_id' => $this->departementVisible->id,
            'libelle' => 'Direction Scope Utilisateur Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $this->departementMasque->id,
            'libelle' => 'Direction Scope Utilisateur Masquee',
        ]);

        User::factory()->create([
            'actif' => true,
            'name' => 'Visible Admin User',
            'departement_id' => $this->departementVisible->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);

        User::factory()->create([
            'actif' => true,
            'name' => 'Masked Admin User',
            'departement_id' => $this->departementMasque->id,
            'direction_id' => $directionMasquee->id,
            'scope_level' => 'direction',
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.utilisateurs.index'))
            ->assertOk()
            ->assertSee('Visible Admin User')
            ->assertDontSee('Masked Admin User')
            ->assertSee('Perimetre de donnees');
    }

    public function test_admin_utilisateurs_refuse_edition_hors_perimetre(): void
    {
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $this->departementMasque->id,
        ]);

        $target = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementMasque->id,
            'direction_id' => $directionMasquee->id,
            'scope_level' => 'direction',
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.utilisateurs.edit', $target))
            ->assertForbidden();
    }

    public function test_admin_utilisateurs_limite_les_directions_proposees_et_refuse_un_rattachement_hors_perimetre(): void
    {
        $directionVisible = Direction::factory()->create([
            'departement_id' => $this->departementVisible->id,
            'libelle' => 'Direction Form Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $this->departementMasque->id,
            'libelle' => 'Direction Form Masquee',
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.utilisateurs.create'))
            ->assertOk()
            ->assertSee('Direction Form Visible')
            ->assertDontSee('Direction Form Masquee')
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->post(route('admin.utilisateurs.store'), [
                'name' => 'Utilisateur Hors Scope',
                'prenom' => 'Test',
                'email' => 'hors-scope@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'direction_id' => $directionMasquee->id,
                'role' => $this->scopedAdmin->roles->first()->name,
                'actif' => 1,
            ])
            ->assertNotFound();
    }

    public function test_admin_utilisateurs_refuse_toggle_archivage_et_restauration_hors_perimetre(): void
    {
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $this->departementMasque->id,
        ]);

        $target = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departementMasque->id,
            'direction_id' => $directionMasquee->id,
            'scope_level' => 'direction',
        ]);

        $this->actingAs($this->scopedAdmin)
            ->post(route('admin.utilisateurs.toggle-actif', $target))
            ->assertForbidden();

        $target->delete();

        $this->actingAs($this->scopedAdmin)
            ->post(route('admin.utilisateurs.restore', $target->id))
            ->assertForbidden();
    }

    public function test_formulaires_structure_affichent_le_perimetre(): void
    {
        $directionVisible = Direction::factory()->create([
            'departement_id' => $this->departementVisible->id,
            'libelle' => 'Direction Form Scope',
        ]);

        Service::create([
            'direction_id' => $directionVisible->id,
            'code' => 'SCOPE-FORM',
            'libelle' => 'Service Form Scope',
            'actif' => true,
        ]);

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.structure.directions.create'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($this->scopedAdmin)
            ->get(route('admin.structure.services.create'))
            ->assertOk()
            ->assertSee('Perimetre de donnees');
    }
}
