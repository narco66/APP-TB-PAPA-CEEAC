<?php

namespace Tests\Feature;

use App\Models\Activite;
use App\Models\Alerte;
use App\Models\CategorieDocument;
use App\Models\Departement;
use App\Models\Direction;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentAlerteScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_liste_documents_est_limitee_au_perimetre_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_document_lecteur', ['document.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);
        $categorie = CategorieDocument::create(['code' => 'GED', 'libelle' => 'GED', 'actif' => true]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $activiteVisible = Activite::factory()->create(['direction_id' => $directionVisible->id, 'libelle' => 'Support visible']);
        $activiteMasquee = Activite::factory()->create(['direction_id' => $directionMasquee->id, 'libelle' => 'Support masque']);

        Document::factory()->create([
            'categorie_id' => $categorie->id,
            'documentable_type' => Activite::class,
            'documentable_id' => $activiteVisible->id,
            'titre' => 'Document Visible',
        ]);
        Document::factory()->create([
            'categorie_id' => $categorie->id,
            'documentable_type' => Activite::class,
            'documentable_id' => $activiteMasquee->id,
            'titre' => 'Document Masque',
        ]);

        $this->actingAs($user)
            ->get(route('documents.index'))
            ->assertOk()
            ->assertSee('Document Visible')
            ->assertDontSee('Document Masque')
            ->assertSee('Perimetre de donnees');
    }

    public function test_fiche_document_hors_perimetre_est_refusee(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_document_show', ['document.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);
        $categorie = CategorieDocument::create(['code' => 'GED2', 'libelle' => 'GED 2', 'actif' => true]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $activiteMasquee = Activite::factory()->create(['direction_id' => $directionMasquee->id]);
        $document = Document::factory()->create([
            'categorie_id' => $categorie->id,
            'documentable_type' => Activite::class,
            'documentable_id' => $activiteMasquee->id,
            'titre' => 'Document Hors Scope',
        ]);

        $this->actingAs($user)
            ->get(route('documents.show', $document))
            ->assertForbidden();
    }

    public function test_export_audit_documents_exclut_les_documents_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_document_export', ['document.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);
        $categorie = CategorieDocument::create(['code' => 'GED3', 'libelle' => 'GED 3', 'actif' => true]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $activiteVisible = Activite::factory()->create(['direction_id' => $directionVisible->id]);
        $activiteMasquee = Activite::factory()->create(['direction_id' => $directionMasquee->id]);

        Document::factory()->create([
            'categorie_id' => $categorie->id,
            'documentable_type' => Activite::class,
            'documentable_id' => $activiteVisible->id,
            'titre' => 'Document Export Visible',
        ]);
        Document::factory()->create([
            'categorie_id' => $categorie->id,
            'documentable_type' => Activite::class,
            'documentable_id' => $activiteMasquee->id,
            'titre' => 'Document Export Masque',
        ]);

        $response = $this->actingAs($user)
            ->get(route('documents.export-audit'))
            ->assertOk();

        $csv = $response->streamedContent();

        $this->assertStringContainsString('Document Export Visible', $csv);
        $this->assertStringNotContainsString('Document Export Masque', $csv);
    }

    public function test_liste_alertes_est_limitee_au_perimetre_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_alerte_lecteur', ['alerte.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        Alerte::factory()->create([
            'direction_id' => $directionVisible->id,
            'destinataire_id' => null,
            'titre' => 'Alerte Visible',
        ]);
        Alerte::factory()->create([
            'direction_id' => $directionMasquee->id,
            'destinataire_id' => null,
            'titre' => 'Alerte Masquee',
        ]);

        $this->actingAs($user)
            ->get(route('alertes.index'))
            ->assertOk()
            ->assertSee('Alerte Visible')
            ->assertDontSee('Alerte Masquee')
            ->assertSee('Perimetre de donnees');
    }

    public function test_fiche_alerte_hors_perimetre_est_refusee(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_alerte_show', ['alerte.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $alerte = Alerte::factory()->create([
            'direction_id' => $directionMasquee->id,
            'destinataire_id' => null,
            'titre' => 'Alerte Hors Scope',
        ]);

        $this->actingAs($user)
            ->get(route('alertes.show', $alerte))
            ->assertForbidden();
    }
}
