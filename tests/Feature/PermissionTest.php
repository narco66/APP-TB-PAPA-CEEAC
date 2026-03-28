<?php

namespace Tests\Feature;

use App\Models\ActionPrioritaire;
use App\Models\Papa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    private User $lecteur;
    private User $gestionnaire;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $roleLecteur = $this->creerRoleAvecPermissions('lecteur', ['papa.voir']);
        $roleGest    = $this->creerRoleAvecPermissions('gestionnaire', [
            'papa.voir', 'papa.creer', 'papa.modifier',
            'activite.voir', 'activite.creer', 'activite.modifier',
            'indicateur.voir',
            'document.voir',
            'alerte.voir',
        ]);
        $roleAdmin = $this->creerRoleAvecPermissions('admin', [
            'papa.voir', 'papa.creer', 'papa.modifier', 'papa.valider', 'papa.supprimer',
            'admin.utilisateurs',
        ]);

        $this->lecteur      = User::factory()->create(['actif' => true]);
        $this->lecteur->assignRole($roleLecteur);

        $this->gestionnaire = User::factory()->create(['actif' => true]);
        $this->gestionnaire->assignRole($roleGest);

        $this->admin = User::factory()->create(['actif' => true]);
        $this->admin->assignRole($roleAdmin);
    }

    // ── Pages PAPA ────────────────────────────────────────────────────────

    public function test_lecteur_peut_voir_liste_papas(): void
    {
        $this->actingAs($this->lecteur)
            ->get(route('papas.index'))
            ->assertOk();
    }

    public function test_lecteur_ne_peut_pas_creer_papa(): void
    {
        $this->actingAs($this->lecteur)
            ->post(route('papas.store'), [
                'code'       => 'PAPA-LECTEUR',
                'libelle'    => 'Test',
                'annee'      => 2025,
                'date_debut' => '2025-01-01',
                'date_fin'   => '2025-12-31',
            ])
            ->assertForbidden();
    }

    public function test_gestionnaire_peut_creer_papa(): void
    {
        $this->actingAs($this->gestionnaire)
            ->post(route('papas.store'), [
                'code'       => 'PAPA-GEST-2025',
                'libelle'    => 'Test Gestionnaire',
                'annee'      => 2025,
                'date_debut' => '2025-01-01',
                'date_fin'   => '2025-12-31',
            ])
            ->assertRedirect();
    }

    public function test_gestionnaire_ne_peut_pas_valider_papa(): void
    {
        $papa = Papa::factory()->create(['statut' => 'soumis']);

        $this->actingAs($this->gestionnaire)
            ->post(route('papas.valider', $papa))
            ->assertForbidden();
    }

    public function test_admin_peut_valider_papa(): void
    {
        $papa = Papa::factory()->create(['statut' => 'soumis']);

        $this->actingAs($this->admin)
            ->post(route('papas.valider', $papa))
            ->assertRedirect();

        $papa->refresh();
        $this->assertEquals('valide', $papa->statut);
    }

    // ── Actions sur PAPA verrouillé ───────────────────────────────────────

    public function test_modification_papa_verrouille_retourne_403(): void
    {
        $papa = Papa::factory()->verrouille()->create();

        $this->actingAs($this->gestionnaire)
            ->put(route('papas.update', $papa), [
                'code'       => $papa->code,
                'libelle'    => 'Tentative modification',
                'annee'      => $papa->annee,
                'date_debut' => $papa->date_debut,
                'date_fin'   => $papa->date_fin,
            ])
            ->assertForbidden();
    }

    // ── Actions Prioritaires ──────────────────────────────────────────────

    public function test_lecteur_peut_voir_actions_prioritaires(): void
    {
        $this->actingAs($this->lecteur)
            ->get(route('actions-prioritaires.index'))
            ->assertOk();
    }

    public function test_lecteur_ne_peut_pas_creer_action_prioritaire(): void
    {
        $papa = Papa::factory()->create();

        $this->actingAs($this->lecteur)
            ->post(route('actions-prioritaires.store'), [
                'papa_id'    => $papa->id,
                'code'       => 'AP-TEST',
                'libelle'    => 'Test',
            ])
            ->assertForbidden();
    }

    // ── Dashboard ─────────────────────────────────────────────────────────

    public function test_utilisateur_authentifie_peut_acceder_dashboard(): void
    {
        $this->actingAs($this->lecteur)
            ->get(route('dashboard'))
            ->assertOk();
    }

    // ── Alertes ───────────────────────────────────────────────────────────

    public function test_lecteur_sans_permission_alerte_ne_voit_pas_alertes(): void
    {
        // Le lecteur a uniquement 'papa.voir', pas 'alerte.voir'
        $this->actingAs($this->lecteur)
            ->get(route('alertes.index'))
            ->assertForbidden();
    }

    public function test_gestionnaire_avec_permission_alerte_voit_alertes(): void
    {
        $this->actingAs($this->gestionnaire)
            ->get(route('alertes.index'))
            ->assertOk();
    }

    // ── Rapports ─────────────────────────────────────────────────────────

    public function test_lecteur_peut_voir_liste_rapports(): void
    {
        $this->actingAs($this->lecteur)
            ->get(route('rapports.index'))
            ->assertOk();
    }

    public function test_lecteur_ne_peut_pas_creer_rapport(): void
    {
        $papa = Papa::factory()->create();

        $this->actingAs($this->lecteur)
            ->post(route('rapports.store'), [
                'papa_id'         => $papa->id,
                'titre'           => 'Test rapport',
                'type_rapport'    => 'mensuel',
                'periode_couverte'=> 'Janvier 2025',
                'annee'           => 2025,
            ])
            ->assertForbidden();
    }
}
