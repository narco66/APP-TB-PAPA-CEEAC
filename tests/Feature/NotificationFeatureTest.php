<?php

namespace Tests\Feature;

use App\Models\NotificationApp;
use App\Models\Decision;
use App\Models\Departement;
use App\Models\NotificationRule;
use App\Models\Papa;
use App\Models\ActionPrioritaire;
use App\Models\User;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $initiateur;
    private User $sg;
    private Departement $departement;

    protected function setUp(): void
    {
        parent::setUp();

        $roleInitiateur = $this->creerRoleAvecPermissions('initiateur_notifications', [
            'workflow.demarrer',
            'workflow.voir',
        ]);
        $roleSg = $this->creerRoleAvecPermissions('secretaire_general', [
            'workflow.voir',
            'workflow.approuver',
        ]);

        $this->departement = Departement::factory()->create();

        $this->initiateur = User::factory()->create([
            'actif' => true,
            'departement_id' => $this->departement->id,
            'scope_level' => 'departement',
        ]);
        $this->initiateur->assignRole($roleInitiateur);

        $this->sg = User::factory()->create(['actif' => true]);
        $this->sg->assignRole($roleSg);
    }

    public function test_workflow_demarre_genere_notification_in_app(): void
    {
        NotificationRule::create([
            'code' => 'WF_TEST_SG',
            'libelle' => 'Notification SG test',
            'event_type' => 'workflow_demarre',
            'canal' => 'in_app',
            'role_cible' => 'secretaire_general',
            'template_sujet' => 'Nouvelle soumission à examiner',
            'template_message' => 'Un workflow a été démarré.',
            'actif' => true,
        ]);

        $definition = WorkflowDefinition::create([
            'code' => 'WF_TEST',
            'libelle' => 'Workflow test',
            'module_cible' => 'papa',
            'type_objet' => Papa::class,
            'actif' => true,
            'version' => 1,
        ]);

        WorkflowStep::create([
            'workflow_definition_id' => $definition->id,
            'code' => 'validation_sg',
            'libelle' => 'Validation SG',
            'ordre' => 1,
            'role_requis' => 'secretaire_general',
            'permission_requise' => 'workflow.approuver',
            'est_etape_initiale' => true,
        ]);

        $papa = Papa::factory()->create();
        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $this->departement->id,
        ]);

        $this->actingAs($this->initiateur)
            ->post(route('workflows.demarrer-papa', $papa), ['workflow_code' => 'WF_TEST'])
            ->assertRedirect();

        $this->assertDatabaseHas('notifications_app', [
            'user_id' => $this->sg->id,
            'type' => 'workflow_demarre',
            'titre' => 'Nouvelle soumission à examiner',
        ]);
    }

    public function test_utilisateur_peut_voir_et_lire_ses_notifications(): void
    {
        $notification = NotificationApp::create([
            'user_id' => $this->sg->id,
            'type' => 'workflow_demarre',
            'titre' => 'Notification test',
            'message' => 'Message test',
            'lien' => route('dashboard'),
            'niveau' => 'info',
            'notifiable_type' => WorkflowInstance::class,
            'notifiable_id' => 1,
        ]);

        $this->actingAs($this->sg)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notification test');

        $this->actingAs($this->sg)
            ->get(route('notifications.read', $notification))
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull($notification->fresh()->lue_le);
    }

    public function test_utilisateur_peut_filtrer_et_marquer_tout_comme_lu(): void
    {
        NotificationApp::create([
            'user_id' => $this->sg->id,
            'type' => 'workflow_demarre',
            'titre' => 'Notification non lue',
            'message' => 'A traiter',
            'niveau' => 'info',
        ]);

        NotificationApp::create([
            'user_id' => $this->sg->id,
            'type' => 'workflow_demarre',
            'titre' => 'Notification lue',
            'message' => 'Déjà lue',
            'niveau' => 'info',
            'lue_le' => now(),
        ]);

        $this->actingAs($this->sg)
            ->get(route('notifications.index', ['statut' => 'non_lues']))
            ->assertOk()
            ->assertSee('Tout marquer comme lu')
            ->assertSee('Filtrer')
            ->assertSee('Notification non lue')
            ->assertDontSee('Notification lue');

        $this->actingAs($this->sg)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(
            0,
            NotificationApp::query()->where('user_id', $this->sg->id)->whereNull('lue_le')->count()
        );
    }

    public function test_utilisateur_obtient_un_resume_coherent_de_ses_notifications(): void
    {
        NotificationApp::create([
            'user_id' => $this->sg->id,
            'type' => 'workflow_demarre',
            'titre' => 'Notification A',
            'message' => 'A',
            'niveau' => 'info',
        ]);

        NotificationApp::create([
            'user_id' => $this->sg->id,
            'type' => 'workflow_demarre',
            'titre' => 'Notification B',
            'message' => 'B',
            'niveau' => 'info',
            'lue_le' => now(),
        ]);

        NotificationApp::create([
            'user_id' => $this->sg->id,
            'type' => 'workflow_demarre',
            'titre' => 'Notification C',
            'message' => 'C',
            'niveau' => 'info',
        ]);

        $summary = $this->sg->fresh()->notificationSummary();

        $this->assertSame([
            'total' => 3,
            'non_lues' => 2,
            'lues' => 1,
        ], $summary);
    }

    public function test_notification_expose_un_lien_source_metier(): void
    {
        $decision = Decision::create([
            'reference' => 'DEC-NOTIF-001',
            'titre' => 'Decision source notification',
            'description' => 'Decision rattachee a une notification.',
            'type_decision' => 'arbitrage',
            'niveau_decision' => 'presidence',
            'statut' => 'brouillon',
            'prise_par' => $this->sg->id,
        ]);

        $notification = NotificationApp::create([
            'user_id' => $this->sg->id,
            'type' => 'decision_validee',
            'titre' => 'Decision validee',
            'message' => 'Une decision attend votre attention.',
            'niveau' => 'succes',
            'notifiable_type' => Decision::class,
            'notifiable_id' => $decision->id,
        ]);

        $this->assertSame('Decision #' . $decision->id, $notification->sourceLabel());
        $this->assertSame(route('decisions.show', $decision), $notification->sourceUrl());
    }
}
