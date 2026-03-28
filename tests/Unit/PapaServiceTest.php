<?php

namespace Tests\Unit;

use App\Models\ActionPrioritaire;
use App\Models\Activite;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\User;
use App\Models\ValidationWorkflow;
use App\Services\PapaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PapaServiceTest extends TestCase
{
    use RefreshDatabase;

    private PapaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PapaService();
    }

    // ── recalculerTaux ────────────────────────────────────────────────────

    public function test_recalculer_taux_avec_aucune_activite(): void
    {
        $papa = Papa::factory()->create();
        $ap   = ActionPrioritaire::factory()->create(['papa_id' => $papa->id]);
        $oi   = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $ap->id]);
        ResultatAttendu::factory()->create(['objectif_immediat_id' => $oi->id]);

        $this->service->recalculerTaux($papa);

        $papa->refresh();
        $this->assertEquals(0, $papa->taux_execution_physique);
    }

    public function test_recalculer_taux_propage_activites_vers_papa(): void
    {
        $papa = Papa::factory()->create();
        $ap   = ActionPrioritaire::factory()->create(['papa_id' => $papa->id]);
        $oi   = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $ap->id]);
        $ra   = ResultatAttendu::factory()->create(['objectif_immediat_id' => $oi->id]);

        // 2 activités : taux 60 et 100 → moyenne = 80
        Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'taux_realisation' => 60]);
        Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'taux_realisation' => 100]);

        $this->service->recalculerTaux($papa);

        $papa->refresh();
        $this->assertEquals(80, $papa->taux_execution_physique);
    }

    public function test_recalculer_taux_avec_plusieurs_ap(): void
    {
        $papa = Papa::factory()->create();

        foreach ([0, 100] as $taux) {
            $ap = ActionPrioritaire::factory()->create(['papa_id' => $papa->id]);
            $oi = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $ap->id]);
            $ra = ResultatAttendu::factory()->create(['objectif_immediat_id' => $oi->id]);
            Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'taux_realisation' => $taux]);
        }

        $this->service->recalculerTaux($papa);

        $papa->refresh();
        // Moyenne des 2 AP : (0 + 100) / 2 = 50
        $this->assertEquals(50, $papa->taux_execution_physique);
    }

    public function test_recalculer_taux_consolide_ra_puis_oi_puis_ap(): void
    {
        $papa = Papa::factory()->create();
        $ap   = ActionPrioritaire::factory()->create(['papa_id' => $papa->id]);
        $oi   = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $ap->id]);

        $ra1 = ResultatAttendu::factory()->create(['objectif_immediat_id' => $oi->id]);
        $ra2 = ResultatAttendu::factory()->create(['objectif_immediat_id' => $oi->id]);

        Activite::factory()->create(['resultat_attendu_id' => $ra1->id, 'taux_realisation' => 40]);
        Activite::factory()->create(['resultat_attendu_id' => $ra2->id, 'taux_realisation' => 80]);

        $this->service->recalculerTaux($papa);

        // RA1 = 40, RA2 = 80 → OI = 60 → AP = 60 → PAPA = 60
        $ap->refresh();
        $this->assertEquals(60, $ap->taux_realisation);

        $papa->refresh();
        $this->assertEquals(60, $papa->taux_execution_physique);
    }

    // ── soumettre ─────────────────────────────────────────────────────────

    public function test_soumettre_change_statut_en_soumis(): void
    {
        $papa = Papa::factory()->create(['statut' => 'brouillon']);
        $user = User::factory()->create();

        $this->service->soumettre($papa, $user, 'Soumis pour validation');

        $papa->refresh();
        $this->assertEquals('soumis', $papa->statut);
    }

    public function test_soumettre_cree_entree_workflow(): void
    {
        $papa = Papa::factory()->create(['statut' => 'brouillon']);
        $user = User::factory()->create();

        $this->service->soumettre($papa, $user, 'Mon commentaire');

        $this->assertDatabaseHas('validations_workflow', [
            'papa_id'      => $papa->id,
            'action'       => 'soumis',
            'acteur_id'    => $user->id,
            'statut_avant' => 'brouillon',
            'statut_apres' => 'soumis',
        ]);
    }

    // ── valider ───────────────────────────────────────────────────────────

    public function test_valider_change_statut_en_valide(): void
    {
        $papa = Papa::factory()->create(['statut' => 'soumis']);
        $user = User::factory()->create();

        $this->service->valider($papa, $user);

        $papa->refresh();
        $this->assertEquals('valide', $papa->statut);
        $this->assertEquals($user->id, $papa->validated_by);
        $this->assertNotNull($papa->validated_at);
    }

    public function test_valider_cree_entree_workflow(): void
    {
        $papa = Papa::factory()->create(['statut' => 'soumis']);
        $user = User::factory()->create();

        $this->service->valider($papa, $user, 'Approuvé en réunion');

        $this->assertDatabaseHas('validations_workflow', [
            'papa_id'      => $papa->id,
            'action'       => 'approuve',
            'acteur_id'    => $user->id,
            'statut_apres' => 'valide',
        ]);
    }

    // ── rejeter ───────────────────────────────────────────────────────────

    public function test_rejeter_remet_en_brouillon(): void
    {
        $papa = Papa::factory()->create(['statut' => 'soumis']);
        $user = User::factory()->create();

        $this->service->rejeter($papa, $user, 'Données incomplètes');

        $papa->refresh();
        $this->assertEquals('brouillon', $papa->statut);
    }

    public function test_rejeter_enregistre_motif_dans_workflow(): void
    {
        $papa = Papa::factory()->create(['statut' => 'soumis']);
        $user = User::factory()->create();

        $this->service->rejeter($papa, $user, 'Données incomplètes');

        $this->assertDatabaseHas('validations_workflow', [
            'papa_id'     => $papa->id,
            'action'      => 'rejete',
            'motif_rejet' => 'Données incomplètes',
        ]);
    }

    // ── archiver ──────────────────────────────────────────────────────────

    public function test_archiver_verrouille_le_papa(): void
    {
        $papa = Papa::factory()->create(['statut' => 'cloture']);
        $user = User::factory()->create();

        $this->service->archiver($papa, $user, 'Exercice 2024 clôturé');

        $papa->refresh();
        $this->assertEquals('archive', $papa->statut);
        $this->assertTrue($papa->est_verrouille);
        $this->assertEquals($user->id, $papa->archived_by);
    }

    // ── cloner ────────────────────────────────────────────────────────────

    public function test_cloner_cree_nouveau_papa_avec_nouvelle_annee(): void
    {
        $papa = Papa::factory()->create(['annee' => 2024]);
        $user = User::factory()->create();

        $clone = $this->service->cloner($papa, 2025, $user);

        $this->assertDatabaseHas('papas', [
            'code'              => 'PAPA-2025',
            'annee'             => 2025,
            'statut'            => 'brouillon',
            'est_verrouille'    => false,
            'clone_de_papa_id'  => $papa->id,
        ]);
        $this->assertEquals(0, $clone->taux_execution_physique);
    }
}
