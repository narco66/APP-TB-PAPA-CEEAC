<?php

namespace Tests\Feature;

use App\Models\ActionPrioritaire;
use App\Models\Decision;
use App\Models\Document;
use App\Models\Papa;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowDecisionScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_decision_index_est_limite_au_perimetre_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('decision_scope_reader', ['decision.voir']);

        $departementVisible = \App\Models\Departement::factory()->create(['libelle' => 'Departement Decision Visible']);
        $departementMasque = \App\Models\Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papaVisible = Papa::factory()->create();
        $papaMasque = Papa::factory()->create();
        ActionPrioritaire::factory()->create(['papa_id' => $papaVisible->id, 'departement_id' => $departementVisible->id]);
        ActionPrioritaire::factory()->create(['papa_id' => $papaMasque->id, 'departement_id' => $departementMasque->id]);

        Decision::create([
            'reference' => 'DEC-VISIBLE',
            'titre' => 'Decision visible',
            'description' => 'Visible',
            'type_decision' => 'arbitrage',
            'niveau_decision' => 'direction',
            'statut' => 'brouillon',
            'papa_id' => $papaVisible->id,
            'prise_par' => $user->id,
        ]);
        Decision::create([
            'reference' => 'DEC-MASQUEE',
            'titre' => 'Decision masquee',
            'description' => 'Masquee',
            'type_decision' => 'arbitrage',
            'niveau_decision' => 'direction',
            'statut' => 'brouillon',
            'papa_id' => $papaMasque->id,
        ]);

        $this->actingAs($user)
            ->get(route('decisions.index'))
            ->assertOk()
            ->assertSee('Decision visible')
            ->assertDontSee('Decision masquee')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Departement Decision Visible');
    }

    public function test_creation_decision_refuse_un_papa_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('decision_scope_create', ['decision.creer']);

        $departementVisible = \App\Models\Departement::factory()->create();
        $departementMasque = \App\Models\Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papaVisible = Papa::factory()->create();
        $papaMasque = Papa::factory()->create();
        ActionPrioritaire::factory()->create(['papa_id' => $papaVisible->id, 'departement_id' => $departementVisible->id]);
        ActionPrioritaire::factory()->create(['papa_id' => $papaMasque->id, 'departement_id' => $departementMasque->id]);

        $this->actingAs($user)
            ->from(route('decisions.create'))
            ->post(route('decisions.store'), [
                'titre' => 'Decision forgee',
                'description' => 'Tentative hors scope',
                'type_decision' => 'arbitrage',
                'niveau_decision' => 'direction',
                'papa_id' => $papaMasque->id,
            ])
            ->assertSessionHasErrors('papa_id');
    }

    public function test_rattachement_document_decision_refuse_document_hors_perimetre(): void
    {
        $roleCreateur = $this->creerRoleAvecPermissions('decision_scope_attach', ['decision.voir', 'decision.creer']);
        $roleDocument = $this->creerRoleAvecPermissions('decision_scope_attach_doc', ['document.voir']);

        $departementVisible = \App\Models\Departement::factory()->create();
        $departementMasque = \App\Models\Departement::factory()->create();

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($roleCreateur);
        $user->givePermissionTo('document.voir');

        $papa = Papa::factory()->create();
        ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementVisible->id]);

        $decision = Decision::create([
            'reference' => 'DEC-ATTACH-1',
            'titre' => 'Decision documentee',
            'description' => 'Decision',
            'type_decision' => 'arbitrage',
            'niveau_decision' => 'direction',
            'statut' => 'brouillon',
            'papa_id' => $papa->id,
            'prise_par' => $user->id,
        ]);

        $documentHorsScope = Document::factory()->create(['depose_par' => User::factory()->create(['actif' => true])->id]);

        $this->actingAs($user)
            ->post(route('decisions.rattacher-document', $decision), [
                'document_id' => $documentHorsScope->id,
                'type_piece' => 'note',
            ])
            ->assertForbidden();
    }

    public function test_workflow_index_est_limite_au_perimetre_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('workflow_scope_reader', ['workflow.voir']);

        $departementVisible = \App\Models\Departement::factory()->create(['libelle' => 'Departement Workflow Visible']);
        $departementMasque = \App\Models\Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $definition = WorkflowDefinition::create([
            'code' => 'WF_SCOPE',
            'libelle' => 'Workflow scope',
            'module_cible' => 'papa',
            'type_objet' => Papa::class,
            'actif' => true,
            'version' => 1,
        ]);
        WorkflowStep::create([
            'workflow_definition_id' => $definition->id,
            'code' => 'ETAPE1',
            'libelle' => 'Etape 1',
            'ordre' => 1,
            'est_etape_initiale' => true,
        ]);

        $papaVisible = Papa::factory()->create();
        $papaMasque = Papa::factory()->create();
        ActionPrioritaire::factory()->create(['papa_id' => $papaVisible->id, 'departement_id' => $departementVisible->id]);
        ActionPrioritaire::factory()->create(['papa_id' => $papaMasque->id, 'departement_id' => $departementMasque->id]);

        WorkflowInstance::create([
            'workflow_definition_id' => $definition->id,
            'objet_type' => Papa::class,
            'objet_id' => $papaVisible->id,
            'papa_id' => $papaVisible->id,
            'statut' => 'en_cours',
            'demarre_par' => $user->id,
            'date_demarrage' => now(),
        ]);
        WorkflowInstance::create([
            'workflow_definition_id' => $definition->id,
            'objet_type' => Papa::class,
            'objet_id' => $papaMasque->id,
            'papa_id' => $papaMasque->id,
            'statut' => 'en_cours',
            'demarre_par' => User::factory()->create(['actif' => true])->id,
            'date_demarrage' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('workflows.index'))
            ->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('Papa #' . $papaVisible->id)
            ->assertDontSee('Papa #' . $papaMasque->id);
    }

    public function test_demarrage_workflow_refuse_un_papa_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('workflow_scope_start', ['workflow.demarrer']);

        $departementVisible = \App\Models\Departement::factory()->create();
        $departementMasque = \App\Models\Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $definition = WorkflowDefinition::create([
            'code' => 'PAPA_VALIDATION_STANDARD',
            'libelle' => 'Validation standard',
            'module_cible' => 'papa',
            'type_objet' => Papa::class,
            'actif' => true,
            'version' => 1,
        ]);
        WorkflowStep::create([
            'workflow_definition_id' => $definition->id,
            'code' => 'ETAPE1',
            'libelle' => 'Etape 1',
            'ordre' => 1,
            'est_etape_initiale' => true,
        ]);

        $papaMasque = Papa::factory()->create();
        ActionPrioritaire::factory()->create(['papa_id' => $papaMasque->id, 'departement_id' => $departementMasque->id]);

        $this->actingAs($user)
            ->post(route('workflows.demarrer-papa', $papaMasque))
            ->assertForbidden();
    }
}
