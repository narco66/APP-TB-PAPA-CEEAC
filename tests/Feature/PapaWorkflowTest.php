<?php

namespace Tests\Feature;

use App\Models\Papa;
use App\Models\User;
use App\Services\PapaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PapaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $gestionnaire;
    private User $validateur;

    protected function setUp(): void
    {
        parent::setUp();

        $roleGest = $this->creerRoleAvecPermissions('gestionnaire', [
            'papa.voir', 'papa.creer', 'papa.modifier',
        ]);
        $roleValid = $this->creerRoleAvecPermissions('validateur', [
            'papa.voir', 'papa.valider',
        ]);

        $this->gestionnaire = User::factory()->create(['actif' => true]);
        $this->gestionnaire->assignRole($roleGest);

        $this->validateur = User::factory()->create(['actif' => true]);
        $this->validateur->assignRole($roleValid);
    }

    // ── Accès authentification ────────────────────────────────────────────

    public function test_invite_redirige_vers_login(): void
    {
        $response = $this->get(route('papas.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_utilisateur_inactif_ne_peut_pas_se_connecter(): void
    {
        $user = User::factory()->create(['actif' => false, 'password' => bcrypt('password')]);

        $response = $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    // ── CRUD PAPA ─────────────────────────────────────────────────────────

    public function test_creation_papa(): void
    {
        $response = $this->actingAs($this->gestionnaire)->post(route('papas.store'), [
            'code'        => 'PAPA-TEST-2025',
            'libelle'     => "Plan d'Action Prioritaire 2025",
            'annee'       => 2025,
            'date_debut'  => '2025-01-01',
            'date_fin'    => '2025-12-31',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('papas', ['code' => 'PAPA-TEST-2025', 'statut' => 'brouillon']);
    }

    public function test_liste_papas_accessible(): void
    {
        Papa::factory()->count(3)->create();

        $response = $this->actingAs($this->gestionnaire)->get(route('papas.index'));

        $response->assertOk();
    }

    public function test_show_papa(): void
    {
        $papa = Papa::factory()->create();

        $response = $this->actingAs($this->gestionnaire)->get(route('papas.show', $papa));

        $response->assertOk();
    }

    // ── Workflow de statut ────────────────────────────────────────────────

    public function test_workflow_complet_brouillon_vers_en_execution(): void
    {
        $service = app(PapaService::class);
        $papa    = Papa::factory()->create(['statut' => 'brouillon']);

        // brouillon → soumis
        $service->soumettre($papa, $this->gestionnaire);
        $papa->refresh();
        $this->assertEquals('soumis', $papa->statut);

        // soumis → valide
        $service->valider($papa, $this->validateur);
        $papa->refresh();
        $this->assertEquals('valide', $papa->statut);
        $this->assertEquals($this->validateur->id, $papa->validated_by);
    }

    public function test_papa_valide_peut_etre_rejete(): void
    {
        $service = app(PapaService::class);
        $papa    = Papa::factory()->create(['statut' => 'soumis']);

        $service->rejeter($papa, $this->validateur, 'Périmètre insuffisant');

        $papa->refresh();
        $this->assertEquals('brouillon', $papa->statut);
    }

    public function test_papa_archive_est_verrouille(): void
    {
        $service = app(PapaService::class);
        $papa    = Papa::factory()->create(['statut' => 'cloture']);

        $service->archiver($papa, $this->validateur);

        $papa->refresh();
        $this->assertTrue($papa->estVerrouille());
        $this->assertFalse($papa->estEditable());
    }

    // ── HTTP workflow actions ─────────────────────────────────────────────

    public function test_soumettre_via_http(): void
    {
        $papa = Papa::factory()->create(['statut' => 'brouillon']);

        $response = $this->actingAs($this->gestionnaire)
            ->post(route('papas.soumettre', $papa));

        $response->assertRedirect();
        $papa->refresh();
        $this->assertEquals('soumis', $papa->statut);
    }

    public function test_valider_via_http_necessite_permission(): void
    {
        $papa = Papa::factory()->create(['statut' => 'soumis']);

        // Le gestionnaire n'a pas papa.valider
        $response = $this->actingAs($this->gestionnaire)
            ->post(route('papas.valider', $papa));

        $response->assertForbidden();
        $papa->refresh();
        $this->assertEquals('soumis', $papa->statut);
    }

    public function test_valider_via_http_avec_bonne_permission(): void
    {
        $papa = Papa::factory()->create(['statut' => 'soumis']);

        $response = $this->actingAs($this->validateur)
            ->post(route('papas.valider', $papa));

        $response->assertRedirect();
        $papa->refresh();
        $this->assertEquals('valide', $papa->statut);
    }

    // ── estEditable ───────────────────────────────────────────────────────

    public function test_papa_brouillon_est_editable(): void
    {
        $papa = Papa::factory()->create(['statut' => 'brouillon']);
        $this->assertTrue($papa->estEditable());
    }

    public function test_papa_archive_non_editable(): void
    {
        $papa = Papa::factory()->verrouille()->create();
        $this->assertFalse($papa->estEditable());
    }

    // ── Clonage ───────────────────────────────────────────────────────────

    public function test_cloner_papa_via_http(): void
    {
        $papa = Papa::factory()->create(['annee' => 2024]);

        $response = $this->actingAs($this->gestionnaire)
            ->post(route('papas.cloner', $papa), ['annee' => 2025]);

        $response->assertRedirect();
        $this->assertDatabaseHas('papas', ['clone_de_papa_id' => $papa->id, 'annee' => 2025]);
    }
}
