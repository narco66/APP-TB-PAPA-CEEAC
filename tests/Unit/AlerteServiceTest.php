<?php

namespace Tests\Unit;

use App\Models\Activite;
use App\Models\Alerte;
use App\Models\ActionPrioritaire;
use App\Models\BudgetPapa;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\User;
use App\Services\AlerteService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlerteServiceTest extends TestCase
{
    use RefreshDatabase;

    private AlerteService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AlerteService();
    }

    private function creerChaine(Papa $papa): ResultatAttendu
    {
        $ap = ActionPrioritaire::factory()->create(['papa_id' => $papa->id]);
        $oi = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $ap->id]);
        return ResultatAttendu::factory()->create(['objectif_immediat_id' => $oi->id]);
    }

    // ── genererAlertesPapa ────────────────────────────────────────────────

    public function test_aucune_alerte_sans_retard(): void
    {
        $papa = Papa::factory()->create();
        $ra   = $this->creerChaine($papa);

        // Activité avec date_fin dans le futur — pas en retard
        Activite::factory()->create([
            'resultat_attendu_id' => $ra->id,
            'statut'              => 'en_cours',
            'date_fin_prevue'     => now()->addDays(10)->toDateString(),
        ]);

        $alertes = $this->service->genererAlertesPapa($papa);

        $this->assertCount(0, $alertes);
        $this->assertDatabaseCount('alertes', 0);
    }

    public function test_genere_alerte_pour_activite_en_retard(): void
    {
        $papa = Papa::factory()->create();
        $ra   = $this->creerChaine($papa);

        Activite::factory()->enRetard()->create(['resultat_attendu_id' => $ra->id]);

        $alertes = $this->service->genererAlertesPapa($papa);

        $this->assertCount(1, $alertes);
        $this->assertEquals('retard_activite', $alertes->first()->type_alerte);
        $this->assertEquals('attention', $alertes->first()->niveau);
    }

    public function test_ne_duplique_pas_alerte_existante(): void
    {
        $papa = Papa::factory()->create();
        $ra   = $this->creerChaine($papa);
        $act  = Activite::factory()->enRetard()->create(['resultat_attendu_id' => $ra->id]);

        // Première passe
        $this->service->genererAlertesPapa($papa);

        // Deuxième passe — ne doit pas créer de doublon
        $alertes = $this->service->genererAlertesPapa($papa);

        $this->assertCount(0, $alertes);
        $this->assertDatabaseCount('alertes', 1);
    }

    public function test_activite_terminee_ne_genere_pas_alerte(): void
    {
        $papa = Papa::factory()->create();
        $ra   = $this->creerChaine($papa);

        Activite::factory()->terminee()->create([
            'resultat_attendu_id' => $ra->id,
            'date_fin_prevue'     => now()->subDays(5)->toDateString(),
        ]);

        $alertes = $this->service->genererAlertesPapa($papa);

        $this->assertCount(0, $alertes);
    }

    public function test_genere_alerte_critique_taux_faible_apres_juin(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 8, 1)); // Août = après juin

        $papa = Papa::factory()->create(['taux_execution_physique' => 15]);
        $this->creerChaine($papa); // besoin d'au moins une AP pour le contexte

        $alertes = $this->service->genererAlertesPapa($papa);

        $critique = $alertes->firstWhere('type_alerte', 'taux_realisation_faible');
        $this->assertNotNull($critique);
        $this->assertEquals('critique', $critique->niveau);

        Carbon::setTestNow();
    }

    public function test_pas_alerte_taux_faible_avant_juin(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 4, 15)); // Avril = avant juin

        $papa = Papa::factory()->create(['taux_execution_physique' => 5]);

        $alertes = $this->service->genererAlertesPapa($papa);

        $this->assertNull($alertes->firstWhere('type_alerte', 'taux_realisation_faible'));

        Carbon::setTestNow();
    }

    public function test_genere_alerte_pour_depassement_budgetaire(): void
    {
        $papa = Papa::factory()->create();

        BudgetPapa::create([
            'papa_id' => $papa->id,
            'source_financement' => 'budget_ceeac',
            'montant_prevu' => 100000,
            'montant_engage' => 125000,
            'montant_decaisse' => 0,
            'montant_solde' => -25000,
            'libelle_ligne' => 'Mission de coordination',
            'annee_budgetaire' => (int) $papa->annee,
            'devise' => 'XAF',
        ]);

        $alertes = $this->service->genererAlertesPapa($papa);

        $budgetAlerte = $alertes->firstWhere('type_alerte', 'budget_depasse');

        $this->assertNotNull($budgetAlerte);
        $this->assertEquals('critique', $budgetAlerte->niveau);
    }

    // ── marquerVue ────────────────────────────────────────────────────────

    public function test_marquer_vue_change_statut(): void
    {
        $papa   = Papa::factory()->create();
        $alerte = Alerte::factory()->create([
            'papa_id'        => $papa->id,
            'alertable_type' => Papa::class,
            'alertable_id'   => $papa->id,
            'statut'         => 'nouvelle',
        ]);

        $this->service->marquerVue($alerte);

        $alerte->refresh();
        $this->assertEquals('vue', $alerte->statut);
        $this->assertNotNull($alerte->lue_le);
    }

    // ── resoudre ──────────────────────────────────────────────────────────

    public function test_resoudre_alerte(): void
    {
        $papa   = Papa::factory()->create();
        $user   = User::factory()->create();
        $alerte = Alerte::factory()->create([
            'papa_id'        => $papa->id,
            'alertable_type' => Papa::class,
            'alertable_id'   => $papa->id,
            'statut'         => 'nouvelle',
        ]);

        $this->service->resoudre($alerte, $user->id, 'Mesure corrective appliquée');

        $alerte->refresh();
        $this->assertEquals('resolue', $alerte->statut);
        $this->assertEquals('Mesure corrective appliquée', $alerte->resolution);
        $this->assertEquals($user->id, $alerte->traitee_par);
    }

    // ── compterParNiveau ──────────────────────────────────────────────────

    public function test_compter_par_niveau(): void
    {
        $papa = Papa::factory()->create();

        Alerte::factory()->count(3)->create([
            'papa_id'        => $papa->id,
            'alertable_type' => Papa::class,
            'alertable_id'   => $papa->id,
            'niveau'         => 'critique',
            'statut'         => 'nouvelle',
        ]);
        Alerte::factory()->count(2)->create([
            'papa_id'        => $papa->id,
            'alertable_type' => Papa::class,
            'alertable_id'   => $papa->id,
            'niveau'         => 'attention',
            'statut'         => 'vue',
        ]);
        // Une alerte résolue ne doit pas être comptée
        Alerte::factory()->create([
            'papa_id'        => $papa->id,
            'alertable_type' => Papa::class,
            'alertable_id'   => $papa->id,
            'niveau'         => 'critique',
            'statut'         => 'resolue',
        ]);

        $comptage = $this->service->compterParNiveau($papa);

        $this->assertEquals(3, $comptage['critique']);
        $this->assertEquals(2, $comptage['attention']);
    }
}
