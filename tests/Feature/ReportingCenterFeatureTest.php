<?php

namespace Tests\Feature;

use App\Models\Departement;
use App\Models\Decision;
use App\Models\Direction;
use App\Models\GeneratedReport;
use App\Models\NotificationApp;
use App\Models\Rapport;
use App\Models\ReportDefinition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportingCenterFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'reporting_user', 'guard_name' => 'web']);

        foreach ([
            'rapport.voir',
            'rapport.exporter',
            'rapport.dashboard.voir',
            'rapport.bibliotheque.voir',
            'rapport.bibliotheque.telecharger',
        ] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $role->givePermissionTo([
            'rapport.voir',
            'rapport.exporter',
            'rapport.dashboard.voir',
            'rapport.bibliotheque.voir',
            'rapport.bibliotheque.telecharger',
        ]);

        $this->user = User::factory()->create(['actif' => true]);
        $this->user->assignRole($role);
    }

    public function test_reporting_dashboard_est_accessible(): void
    {
        ReportDefinition::create([
            'code' => 'executive_global_papa',
            'libelle' => 'Rapport executif global du PAPA',
            'categorie' => 'Executif',
            'formats' => ['pdf', 'xlsx'],
            'actif' => true,
        ]);

        $this->actingAs($this->user)
            ->get(route('reports.dashboard'))
            ->assertOk()
            ->assertSee('Centre de reporting')
            ->assertSee('Rapport executif global du PAPA');
    }

    public function test_reporting_dashboard_affiche_entree_vers_rapport_narratif_si_permission_creation(): void
    {
        Permission::findOrCreate('rapport.creer', 'web');
        $this->user->givePermissionTo('rapport.creer');

        $this->actingAs($this->user)
            ->get(route('reports.dashboard'))
            ->assertOk()
            ->assertSee('Nouveau rapport narratif')
            ->assertSee(route('rapports.create'), false);
    }

    public function test_reporting_dashboard_affiche_les_rapports_narratifs_recents_du_perimetre(): void
    {
        $departement = Departement::factory()->create();
        $direction = Direction::factory()->create(['departement_id' => $departement->id]);
        $autreDirection = Direction::factory()->create();

        $this->user->update(['direction_id' => $direction->id]);

        $papa = \App\Models\Papa::factory()->create();

        Rapport::factory()->create([
            'papa_id' => $papa->id,
            'direction_id' => $direction->id,
            'departement_id' => $departement->id,
            'redige_par' => $this->user->id,
            'titre' => 'Rapport narratif visible dashboard',
        ]);

        Rapport::factory()->create([
            'papa_id' => $papa->id,
            'direction_id' => $autreDirection->id,
            'departement_id' => $autreDirection->departement_id,
            'redige_par' => User::factory()->create(['actif' => true, 'direction_id' => $autreDirection->id])->id,
            'titre' => 'Rapport narratif hors perimetre dashboard',
        ]);

        $this->actingAs($this->user)
            ->get(route('reports.dashboard'))
            ->assertOk()
            ->assertSee('Rapports narratifs recents')
            ->assertSee('Rapport narratif visible dashboard')
            ->assertDontSee('Rapport narratif hors perimetre dashboard')
            ->assertSee(route('rapports.index'), false);
    }

    public function test_reporting_library_download_journalise_le_telechargement(): void
    {
        Storage::fake('local');

        $definition = ReportDefinition::create([
            'code' => 'financial_global_papa',
            'libelle' => 'Rapport budgetaire global du PAPA',
            'categorie' => 'Financier',
            'formats' => ['pdf'],
            'actif' => true,
        ]);

        Storage::disk('local')->put('reports/demo.pdf', 'dummy pdf');

        $report = GeneratedReport::create([
            'report_definition_id' => $definition->id,
            'user_id' => $this->user->id,
            'titre' => 'Rapport budgetaire global',
            'format' => 'pdf',
            'statut' => 'generated',
            'file_disk' => 'local',
            'file_path' => 'reports/demo.pdf',
            'file_name' => 'demo.pdf',
            'mime_type' => 'application/pdf',
            'generated_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->get(route('reports.library.download', $report))
            ->assertOk();

        $this->assertDatabaseHas('report_download_logs', [
            'generated_report_id' => $report->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_reporting_dashboard_peut_generer_un_rapport_budgetaire(): void
    {
        Storage::fake('local');

        $definition = ReportDefinition::create([
            'code' => 'financial_global_papa',
            'libelle' => 'Rapport budgetaire global du PAPA',
            'categorie' => 'Financier',
            'formats' => ['pdf', 'xlsx', 'csv'],
            'actif' => true,
        ]);

        $papa = \App\Models\Papa::factory()->create();

        $this->actingAs($this->user)
            ->post(route('reports.generate', $definition), [
                'papa_id' => $papa->id,
                'format' => 'csv',
            ])
            ->assertRedirect();

        $generatedReport = GeneratedReport::query()->latest()->first();

        $this->assertNotNull($generatedReport);
        Storage::disk('local')->assertExists($generatedReport->file_path);
        $this->assertDatabaseHas('generated_reports', [
            'report_definition_id' => $definition->id,
            'papa_id' => $papa->id,
            'format' => 'csv',
            'statut' => 'generated',
        ]);
    }

    public function test_reporting_dashboard_peut_generer_un_rapport_decisions(): void
    {
        Storage::fake('local');

        $definition = ReportDefinition::create([
            'code' => 'governance_decisions',
            'libelle' => 'Decisions et arbitrages',
            'categorie' => 'Gouvernance',
            'formats' => ['pdf', 'xlsx', 'csv'],
            'actif' => true,
        ]);

        $papa = \App\Models\Papa::factory()->create();
        Decision::create([
            'reference' => 'DEC-001',
            'titre' => 'Arbitrage budgetaire',
            'description' => 'Arbitrage institutionnel.',
            'type_decision' => 'arbitrage',
            'niveau_decision' => 'sg',
            'statut' => 'validee',
            'date_decision' => now()->toDateString(),
            'papa_id' => $papa->id,
            'prise_par' => $this->user->id,
            'validee_par' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('reports.generate', $definition), [
                'papa_id' => $papa->id,
                'format' => 'csv',
            ])
            ->assertRedirect();

        $generatedReport = GeneratedReport::query()->latest()->first();

        $this->assertNotNull($generatedReport);
        Storage::disk('local')->assertExists($generatedReport->file_path);
        $this->assertStringContainsString('governance_decisions', strtolower($generatedReport->file_path));
    }

    public function test_reporting_dashboard_peut_generer_la_chaine_rbm_consolidee(): void
    {
        Storage::fake('local');

        $definition = ReportDefinition::create([
            'code' => 'rbm_chain_consolidated',
            'libelle' => 'Chaine consolidee des resultats',
            'categorie' => 'RBM',
            'formats' => ['pdf', 'xlsx', 'csv'],
            'actif' => true,
        ]);

        $papa = \App\Models\Papa::factory()->create();
        $action = \App\Models\ActionPrioritaire::factory()->create(['papa_id' => $papa->id]);
        $objectif = \App\Models\ObjectifImmediats::factory()->create(['action_prioritaire_id' => $action->id]);
        $resultat = \App\Models\ResultatAttendu::factory()->create(['objectif_immediat_id' => $objectif->id]);
        $direction = \App\Models\Direction::factory()->create();
        \App\Models\Indicateur::create([
            'action_prioritaire_id' => $action->id,
            'objectif_immediat_id' => $objectif->id,
            'resultat_attendu_id' => $resultat->id,
            'code' => 'IND-RBM-001',
            'libelle' => 'Taux de mise en oeuvre RBM',
            'type_indicateur' => 'quantitatif',
            'unite_mesure' => '%',
            'valeur_baseline' => 10,
            'valeur_cible_annuelle' => 90,
            'frequence_collecte' => 'trimestrielle',
            'source_donnees' => 'Tableau RBM',
            'responsable_id' => $this->user->id,
            'direction_id' => $direction->id,
            'taux_realisation_courant' => 55,
            'actif' => true,
        ]);
        \App\Models\Activite::factory()->create([
            'resultat_attendu_id' => $resultat->id,
            'direction_id' => $direction->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('reports.generate', $definition), [
                'papa_id' => $papa->id,
                'format' => 'csv',
            ])
            ->assertRedirect();

        $generatedReport = GeneratedReport::query()->latest()->first();

        $this->assertNotNull($generatedReport);
        Storage::disk('local')->assertExists($generatedReport->file_path);
        $this->assertStringContainsString('rbm_chain_consolidated', strtolower($generatedReport->file_path));
    }

    public function test_reporting_lourd_est_mis_en_file(): void
    {
        Queue::fake();

        $definition = ReportDefinition::create([
            'code' => 'ged_missing_evidence',
            'libelle' => 'Resultats non prouves',
            'categorie' => 'GED',
            'formats' => ['pdf', 'xlsx', 'csv'],
            'is_async_recommended' => true,
            'actif' => true,
        ]);

        $papa = \App\Models\Papa::factory()->create();

        $this->actingAs($this->user)
            ->post(route('reports.generate', $definition), [
                'papa_id' => $papa->id,
                'format' => 'csv',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('generated_reports', [
            'report_definition_id' => $definition->id,
            'papa_id' => $papa->id,
            'format' => 'csv',
            'statut' => 'queued',
        ]);

        $this->assertDatabaseHas('notifications_app', [
            'user_id' => $this->user->id,
            'type' => 'report_queued',
        ]);
    }

    public function test_reporting_retry_recree_un_job_asynchrone(): void
    {
        Queue::fake();

        $definition = ReportDefinition::create([
            'code' => 'ged_missing_evidence',
            'libelle' => 'Resultats non prouves',
            'categorie' => 'GED',
            'formats' => ['pdf', 'xlsx', 'csv'],
            'is_async_recommended' => true,
            'actif' => true,
        ]);

        $report = GeneratedReport::create([
            'report_definition_id' => $definition->id,
            'user_id' => $this->user->id,
            'titre' => 'Resultats non prouves',
            'format' => 'csv',
            'statut' => 'failed',
            'filters' => ['papa_id' => null, 'format' => 'csv'],
            'error_message' => 'Echec volontaire',
        ]);

        $this->actingAs($this->user)
            ->post(route('reports.library.retry', $report))
            ->assertRedirect();

        $this->assertDatabaseHas('generated_reports', [
            'report_definition_id' => $definition->id,
            'format' => 'csv',
            'statut' => 'queued',
        ]);
    }

    public function test_reporting_genere_notification_en_succes_synchrone(): void
    {
        Storage::fake('local');

        $definition = ReportDefinition::create([
            'code' => 'financial_global_papa',
            'libelle' => 'Rapport budgetaire global du PAPA',
            'categorie' => 'Financier',
            'formats' => ['pdf', 'xlsx', 'csv'],
            'actif' => true,
        ]);

        $papa = \App\Models\Papa::factory()->create();

        $this->actingAs($this->user)
            ->post(route('reports.generate', $definition), [
                'papa_id' => $papa->id,
                'format' => 'csv',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notifications_app', [
            'user_id' => $this->user->id,
            'type' => 'report_generated',
            'niveau' => 'succes',
        ]);
    }

    public function test_reporting_bibliotheque_affiche_action_relancer_sur_echec(): void
    {
        $definition = ReportDefinition::create([
            'code' => 'ged_missing_evidence',
            'libelle' => 'Resultats non prouves',
            'categorie' => 'GED',
            'formats' => ['pdf', 'xlsx', 'csv'],
            'is_async_recommended' => true,
            'actif' => true,
        ]);

        $report = GeneratedReport::create([
            'report_definition_id' => $definition->id,
            'user_id' => $this->user->id,
            'titre' => 'Resultats non prouves',
            'format' => 'csv',
            'statut' => 'failed',
            'filters' => ['format' => 'csv'],
        ]);

        $this->actingAs($this->user)
            ->get(route('reports.library.show', $report))
            ->assertOk()
            ->assertSee('Relancer');
    }

    public function test_reporting_dashboard_ne_montre_que_les_exports_de_l_utilisateur(): void
    {
        $definition = ReportDefinition::create([
            'code' => 'financial_global_papa',
            'libelle' => 'Rapport budgetaire global du PAPA',
            'categorie' => 'Financier',
            'formats' => ['pdf'],
            'actif' => true,
        ]);

        GeneratedReport::create([
            'report_definition_id' => $definition->id,
            'user_id' => $this->user->id,
            'titre' => 'Mon rapport',
            'format' => 'pdf',
            'statut' => 'generated',
        ]);

        $otherUser = User::factory()->create(['actif' => true]);
        GeneratedReport::create([
            'report_definition_id' => $definition->id,
            'user_id' => $otherUser->id,
            'titre' => 'Rapport d un autre utilisateur',
            'format' => 'pdf',
            'statut' => 'generated',
        ]);

        $this->actingAs($this->user)
            ->get(route('reports.dashboard'))
            ->assertOk()
            ->assertSee('Mon rapport')
            ->assertDontSee('Rapport d un autre utilisateur');
    }

    public function test_reporting_bibliotheque_refuse_acces_au_rapport_d_un_autre_utilisateur(): void
    {
        $definition = ReportDefinition::create([
            'code' => 'financial_global_papa',
            'libelle' => 'Rapport budgetaire global du PAPA',
            'categorie' => 'Financier',
            'formats' => ['pdf'],
            'actif' => true,
        ]);

        $otherUser = User::factory()->create(['actif' => true]);
        $report = GeneratedReport::create([
            'report_definition_id' => $definition->id,
            'user_id' => $otherUser->id,
            'titre' => 'Rapport prive',
            'format' => 'pdf',
            'statut' => 'generated',
        ]);

        $this->actingAs($this->user)
            ->get(route('reports.library.show', $report))
            ->assertForbidden();
    }
}
