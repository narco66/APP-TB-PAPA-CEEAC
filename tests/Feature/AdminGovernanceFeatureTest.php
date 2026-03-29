<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\Decision;
use App\Models\NotificationRule;
use App\Models\Papa;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGovernanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = $this->creerRoleAvecPermissions('admin_gouvernance', [
            'admin.audit_log',
            'notification_rule.gerer',
        ]);

        $this->admin = User::factory()->create(['actif' => true]);
        $this->admin->assignRole($role);
    }

    public function test_admin_peut_consulter_audit_metier(): void
    {
        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'workflow',
            'event_type' => 'workflow_demarre',
            'auditable_type' => User::class,
            'auditable_id' => $this->admin->id,
            'acteur_id' => $this->admin->id,
            'action' => 'soumis',
            'description' => 'Workflow demarre en test',
            'niveau' => 'info',
            'horodate_evenement' => now(),
            'checksum' => sha1('workflow-test'),
        ]);

        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'decision',
            'event_type' => 'decision_rejetee',
            'auditable_type' => User::class,
            'auditable_id' => $this->admin->id,
            'acteur_id' => $this->admin->id,
            'action' => 'rejete',
            'description' => 'Decision rejetee en test',
            'niveau' => 'warning',
            'horodate_evenement' => now(),
            'checksum' => sha1('decision-test'),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.audit-events'))
            ->assertOk()
            ->assertSee('Audit')
            ->assertSee('Reinitialiser')
            ->assertSee(route('admin.audit-events', ['module' => 'workflow']), false)
            ->assertSee('workflow_demarre')
            ->assertSee(route('admin.audit-events', ['niveau' => 'warning']), false)
            ->assertSee('Warning');
    }

    public function test_admin_peut_consulter_et_modifier_regles_notification(): void
    {
        $rule = NotificationRule::create([
            'code' => 'TEST_RULE',
            'libelle' => 'Regle de test',
            'event_type' => 'workflow_demarre',
            'canal' => 'in_app',
            'template_message' => 'Message initial',
            'actif' => true,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.notification-rules'))
            ->assertOk()
            ->assertSee('TEST_RULE');

        $this->actingAs($this->admin)
            ->put(route('admin.notification-rules.update', $rule), [
                'libelle' => 'Regle de test mise a jour',
                'event_type' => 'workflow_demarre',
                'canal' => 'email',
                'role_cible' => 'president',
                'permission_cible' => 'workflow.voir',
                'delai_minutes' => 30,
                'template_sujet' => 'Alerte workflow',
                'template_message' => 'Message mis a jour',
                'escalade' => 1,
                'actif' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notification_rules', [
            'id' => $rule->id,
            'libelle' => 'Regle de test mise a jour',
            'canal' => 'email',
            'role_cible' => 'president',
        ]);
    }

    public function test_admin_peut_filtrer_audit_metier_par_objet_audite(): void
    {
        $cible = User::factory()->create(['actif' => true]);
        $autre = User::factory()->create(['actif' => true]);

        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'workflow',
            'event_type' => 'workflow_approuve',
            'auditable_type' => User::class,
            'auditable_id' => $cible->id,
            'acteur_id' => $this->admin->id,
            'action' => 'approuve',
            'description' => 'Evenement cible',
            'niveau' => 'info',
            'horodate_evenement' => now(),
            'checksum' => sha1('audit-target'),
        ]);

        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'decision',
            'event_type' => 'decision_validee',
            'auditable_type' => User::class,
            'auditable_id' => $autre->id,
            'acteur_id' => $this->admin->id,
            'action' => 'valide',
            'description' => 'Evenement hors filtre',
            'niveau' => 'info',
            'horodate_evenement' => now(),
            'checksum' => sha1('audit-other'),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.audit-events', [
                'auditable_type' => User::class,
                'auditable_id' => $cible->id,
            ]))
            ->assertOk()
            ->assertSee('Filtre objet actif')
            ->assertSee('Evenement cible')
            ->assertDontSee('Evenement hors filtre');
    }

    public function test_admin_peut_exporter_audit_metier_en_csv(): void
    {
        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'workflow',
            'event_type' => 'workflow_rejete',
            'auditable_type' => User::class,
            'auditable_id' => $this->admin->id,
            'acteur_id' => $this->admin->id,
            'action' => 'rejete',
            'description' => 'Export de test audit',
            'niveau' => 'warning',
            'horodate_evenement' => now(),
            'checksum' => sha1('audit-export'),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit-events.export', ['module' => 'workflow']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString('workflow_rejete', $content);
        $this->assertStringContainsString('Export de test audit', $content);
    }

    public function test_audit_filtre_sur_decision_propose_un_retour_vers_la_fiche(): void
    {
        $decision = Decision::create([
            'reference' => 'DEC-RET-001',
            'titre' => 'Decision de retour',
            'description' => 'Decision pour test de navigation retour.',
            'type_decision' => 'arbitrage',
            'niveau_decision' => 'presidence',
            'statut' => 'brouillon',
            'prise_par' => $this->admin->id,
        ]);

        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'decision',
            'event_type' => 'decision_validee',
            'auditable_type' => Decision::class,
            'auditable_id' => $decision->id,
            'acteur_id' => $this->admin->id,
            'action' => 'valide',
            'description' => 'Navigation retour test',
            'niveau' => 'info',
            'horodate_evenement' => now(),
            'checksum' => sha1('audit-decision-return'),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.audit-events', [
                'auditable_type' => Decision::class,
                'auditable_id' => $decision->id,
            ]))
            ->assertOk()
            ->assertSee('Retour a la decision')
            ->assertSee(route('decisions.show', $decision), false);
    }

    public function test_liste_audit_affiche_un_lien_vers_l_objet_source(): void
    {
        $decision = Decision::create([
            'reference' => 'DEC-LINK-001',
            'titre' => 'Decision liee',
            'description' => 'Decision pour test de lien source.',
            'type_decision' => 'arbitrage',
            'niveau_decision' => 'presidence',
            'statut' => 'brouillon',
            'prise_par' => $this->admin->id,
        ]);

        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'decision',
            'event_type' => 'decision_creee',
            'auditable_type' => Decision::class,
            'auditable_id' => $decision->id,
            'acteur_id' => $this->admin->id,
            'action' => 'cree',
            'description' => 'Lien source sur liste audit',
            'niveau' => 'info',
            'horodate_evenement' => now(),
            'checksum' => sha1('audit-link-source'),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.audit-events'))
            ->assertOk()
            ->assertSee('Decision #' . $decision->id)
            ->assertSee(route('decisions.show', $decision), false);
    }

    public function test_liste_audit_affiche_un_lien_vers_le_papa_associe(): void
    {
        $papa = Papa::factory()->create();

        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'papa',
            'event_type' => 'papa_soumis',
            'auditable_type' => Papa::class,
            'auditable_id' => $papa->id,
            'papa_id' => $papa->id,
            'acteur_id' => $this->admin->id,
            'action' => 'soumis',
            'description' => 'Lien PAPA dans la liste audit',
            'niveau' => 'info',
            'horodate_evenement' => now(),
            'checksum' => sha1('audit-papa-link'),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.audit-events'))
            ->assertOk()
            ->assertSee($papa->code)
            ->assertSee(route('papas.show', $papa), false);
    }

    public function test_audit_filtre_sur_workflow_propose_un_retour_vers_la_fiche(): void
    {
        $papa = Papa::factory()->create();
        $definition = WorkflowDefinition::create([
            'code' => 'WF-AUDIT-RET',
            'libelle' => 'Workflow audit retour',
            'module_cible' => 'papa',
            'type_objet' => Papa::class,
            'actif' => true,
            'version' => 1,
        ]);

        $workflow = WorkflowInstance::create([
            'workflow_definition_id' => $definition->id,
            'objet_type' => Papa::class,
            'objet_id' => $papa->id,
            'papa_id' => $papa->id,
            'statut' => 'en_cours',
            'demarre_par' => $this->admin->id,
            'date_demarrage' => now(),
        ]);

        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'workflow',
            'event_type' => 'workflow_approuve',
            'auditable_type' => WorkflowInstance::class,
            'auditable_id' => $workflow->id,
            'acteur_id' => $this->admin->id,
            'action' => 'approuve',
            'description' => 'Navigation retour workflow',
            'niveau' => 'info',
            'horodate_evenement' => now(),
            'checksum' => sha1('audit-workflow-return'),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.audit-events', [
                'auditable_type' => WorkflowInstance::class,
                'auditable_id' => $workflow->id,
            ]))
            ->assertOk()
            ->assertSee('Retour au workflow')
            ->assertSee(route('workflows.show', $workflow), false);
    }

    public function test_audit_filtre_sur_papa_propose_un_retour_vers_la_fiche(): void
    {
        $papa = Papa::factory()->create();

        AuditEvent::create([
            'event_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'module' => 'papa',
            'event_type' => 'papa_valide',
            'auditable_type' => Papa::class,
            'auditable_id' => $papa->id,
            'acteur_id' => $this->admin->id,
            'action' => 'valide',
            'description' => 'Navigation retour papa',
            'niveau' => 'info',
            'horodate_evenement' => now(),
            'checksum' => sha1('audit-papa-return'),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.audit-events', [
                'auditable_type' => Papa::class,
                'auditable_id' => $papa->id,
            ]))
            ->assertOk()
            ->assertSee('Retour au PAPA')
            ->assertSee(route('papas.show', $papa), false);
    }
}
