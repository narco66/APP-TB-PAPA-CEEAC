<?php

namespace Tests\Feature;

use App\Models\CategorieDocument;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentExportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_audit_ged_exclut_les_documents_strictement_confidentiels_sans_permission(): void
    {
        $role = $this->creerRoleAvecPermissions('document_export_user', [
            'document.voir',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'prenom' => 'Jean',
            'name' => 'Export',
        ]);
        $user->assignRole($role);

        $categorie = CategorieDocument::create([
            'code' => 'NOTE',
            'libelle' => 'Note',
            'actif' => true,
        ]);

        Document::create([
            'categorie_id' => $categorie->id,
            'titre' => 'Document interne',
            'reference' => 'DOC-001',
            'chemin_fichier' => 'docs/interne.pdf',
            'nom_fichier_original' => 'interne.pdf',
            'extension' => 'pdf',
            'taille_octets' => 12345,
            'confidentialite' => 'interne',
            'statut' => 'valide',
            'depose_par' => $user->id,
        ]);

        Document::create([
            'categorie_id' => $categorie->id,
            'titre' => 'Document strictement confidentiel',
            'reference' => 'DOC-002',
            'chemin_fichier' => 'docs/secret.pdf',
            'nom_fichier_original' => 'secret.pdf',
            'extension' => 'pdf',
            'taille_octets' => 99999,
            'confidentialite' => 'strictement_confidentiel',
            'statut' => 'valide',
            'depose_par' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('documents.export-audit'));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('Document interne', $content);
        $this->assertStringContainsString('12345', $content);
        $this->assertStringNotContainsString('Document strictement confidentiel', $content);
        $this->assertStringNotContainsString('99999', $content);
    }

    public function test_export_audit_ged_inclut_les_documents_strictement_confidentiels_avec_permission(): void
    {
        $role = $this->creerRoleAvecPermissions('document_export_confidentiel', [
            'document.voir',
            'document.voir_confidentiel',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'prenom' => 'Marie',
            'name' => 'Controle',
        ]);
        $user->assignRole($role);

        $categorie = CategorieDocument::create([
            'code' => 'PV',
            'libelle' => 'Proces verbal',
            'actif' => true,
        ]);

        Document::create([
            'categorie_id' => $categorie->id,
            'titre' => 'Dossier confidentiel',
            'reference' => 'DOC-003',
            'chemin_fichier' => 'docs/confidentiel.pdf',
            'nom_fichier_original' => 'confidentiel.pdf',
            'extension' => 'pdf',
            'taille_octets' => 54321,
            'confidentialite' => 'strictement_confidentiel',
            'statut' => 'valide',
            'depose_par' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('documents.export-audit'));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('Dossier confidentiel', $content);
        $this->assertStringContainsString('54321', $content);
    }
}
