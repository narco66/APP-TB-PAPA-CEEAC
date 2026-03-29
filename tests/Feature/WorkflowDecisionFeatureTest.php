<?php

namespace Tests\Feature;

use App\Models\Decision;
use App\Models\Document;
use App\Models\Papa;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowDecisionFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $initiateur;
    private User $sg;
    private User $createurDecision;
    private User $president;

    protected function setUp(): void
    {
        parent::setUp();

        $roleInitiateur = $this->creerRoleAvecPermissions('initiateur_workflow', [
            'workflow.demarrer',
            'workflow.voir',
        ]);
        $roleSg = $this->creerRoleAvecPermissions('secretaire_general', [
            'workflow.voir',
            'workflow.approuver',
            'workflow.rejeter',
            'workflow.commenter',
        ]);
        $roleCreateurDecision = $this->creerRoleAvecPermissions('gestionnaire_decision', [
            'decision.voir',
            'decision.creer',
        ]);
        $rolePresident = $this->creerRoleAvecPermissions('president', [
            'decision.voir',
            'decision.valider',
            'decision.executer',
        ]);

        $this->initiateur = User::factory()->create(['actif' => true]);
        $this->initiateur->assignRole($roleInitiateur);

        $this->sg = User::factory()->create(['actif' => true]);
        $this->sg->assignRole($roleSg);

        $this->createurDecision = User::factory()->create(['actif' => true]);
        $this->createurDecision->assignRole($roleCreateurDecision);

        $this->president = User::factory()->create(['actif' => true]);
        $this->president->assignRole($rolePresident);
    }

    public function test_workflow_papa_peut_etre_demarre_et_approuve_via_http(): void
    {
        $papa = Papa::factory()->create();

        $definition = WorkflowDefinition::create([
            'code' => 'PAPA_VALIDATION_STANDARD',
            'libelle' => 'Validation standard PAPA',
            'module_cible' => 'papa',
            'type_objet' => Papa::class,
            'actif' => true,
            'version' => 1,
        ]);

        WorkflowStep::create([
            'workflow_definition_id' => $definition->id,
            'code' => 'SG_REVIEW',
            'libelle' => 'Revue SG',
            'ordre' => 1,
            'role_requis' => 'secretaire_general',
            'permission_requise' => 'workflow.approuver',
            'est_etape_initiale' => true,
        ]);

        $this->actingAs($this->initiateur)
            ->post(route('workflows.demarrer-papa', $papa), [
                'commentaire' => 'Soumission du circuit de validation',
            ])
            ->assertRedirect();

        $instance = WorkflowInstance::query()->where('papa_id', $papa->id)->first();

        $this->assertNotNull($instance);
        $this->assertSame('en_cours', $instance->statut);

        $this->actingAs($this->sg)
            ->post(route('workflows.approuver', $instance))
            ->assertRedirect();

        $instance->refresh();
        $this->assertSame('approuve', $instance->statut);

        $this->actingAs($this->sg)
            ->get(route('workflows.show', $instance))
            ->assertOk();

        $this->actingAs($this->sg)
            ->get(route('workflows.audit', $instance))
            ->assertRedirect(route('admin.audit-events', $instance->auditTrailParams()));

        $this->assertSame([
            'auditable_type' => WorkflowInstance::class,
            'auditable_id' => $instance->id,
        ], $instance->auditTrailParams());
        $this->assertStringContainsString('auditable_type=', $instance->auditTrailUrl());
        $this->assertStringContainsString('auditable_id=' . $instance->id, $instance->auditTrailUrl());

        $this->assertDatabaseHas('audit_events', [
            'module' => 'workflow',
            'event_type' => 'workflow_approuve',
        ]);
    }

    public function test_decision_peut_etre_creee_documentee_validee_et_executee(): void
    {
        $papa = Papa::factory()->create();
        $document = Document::factory()->create();

        $this->actingAs($this->createurDecision)
            ->post(route('decisions.store'), [
                'titre' => 'Arbitrage sur le rééchelonnement d’une activité prioritaire',
                'description' => 'Décision de réaffectation partielle pour sécuriser la livraison du jalon semestriel.',
                'type_decision' => 'arbitrage',
                'niveau_decision' => 'presidence',
                'papa_id' => $papa->id,
                'impact_budgetaire' => 250000,
                'impact_calendrier_jours' => 10,
                'mise_en_oeuvre_obligatoire' => 1,
            ])
            ->assertRedirect();

        $decision = Decision::query()->latest('id')->first();

        $this->assertNotNull($decision);
        $this->assertSame('brouillon', $decision->statut);

        $this->actingAs($this->createurDecision)
            ->post(route('decisions.rattacher-document', $decision), [
                'document_id' => $document->id,
                'type_piece' => 'note_justificative',
                'obligatoire' => 1,
            ])
            ->assertRedirect();

        $this->actingAs($this->president)
            ->post(route('decisions.valider', $decision))
            ->assertRedirect();

        $decision->refresh();
        $this->assertSame('validee', $decision->statut);

        $this->actingAs($this->president)
            ->post(route('decisions.executer', $decision))
            ->assertRedirect();

        $decision->refresh();
        $this->assertSame('executee', $decision->statut);

        $this->actingAs($this->president)
            ->get(route('decisions.show', $decision))
            ->assertOk();

        $this->actingAs($this->president)
            ->get(route('decisions.audit', $decision))
            ->assertRedirect(route('admin.audit-events', $decision->auditTrailParams()));

        $this->assertSame([
            'auditable_type' => Decision::class,
            'auditable_id' => $decision->id,
        ], $decision->auditTrailParams());
        $this->assertStringContainsString('auditable_type=', $decision->auditTrailUrl());
        $this->assertStringContainsString('auditable_id=' . $decision->id, $decision->auditTrailUrl());

        $this->assertDatabaseHas('audit_events', [
            'module' => 'decision',
            'event_type' => 'decision_executee',
        ]);
    }

    public function test_acces_audit_workflow_refuse_sans_permission_de_lecture(): void
    {
        $workflow = WorkflowInstance::create([
            'workflow_definition_id' => WorkflowDefinition::create([
                'code' => 'WF_PRIVATE',
                'libelle' => 'Workflow prive',
                'module_cible' => 'papa',
                'type_objet' => Papa::class,
                'actif' => true,
                'version' => 1,
            ])->id,
            'objet_type' => Papa::class,
            'objet_id' => Papa::factory()->create()->id,
            'statut' => 'en_cours',
            'demarre_par' => $this->initiateur->id,
            'date_demarrage' => now(),
        ]);

        $intrus = User::factory()->create(['actif' => true]);

        $this->actingAs($intrus)
            ->get(route('workflows.audit', $workflow))
            ->assertForbidden();
    }

    public function test_acces_audit_decision_refuse_sans_permission_de_lecture(): void
    {
        $decision = Decision::create([
            'reference' => 'DEC-TEST-LOCKED',
            'titre' => 'Decision protegee',
            'description' => 'Decision reservee aux acteurs autorises.',
            'type_decision' => 'arbitrage',
            'niveau_decision' => 'presidence',
            'statut' => 'brouillon',
            'prise_par' => $this->createurDecision->id,
        ]);

        $intrus = User::factory()->create(['actif' => true]);

        $this->actingAs($intrus)
            ->get(route('decisions.audit', $decision))
            ->assertForbidden();
    }
}
