<?php

namespace Tests\Unit;

use App\Models\Activite;
use App\Models\Direction;
use App\Models\ResultatAttendu;
use App\Services\Gantt\GanttTreeBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GanttTreeBuilderTest extends TestCase
{
    use RefreshDatabase;

    private GanttTreeBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new GanttTreeBuilder();
    }

    // ── Helper : faux callable toTask ────────────────────────────────────

    private function toTask(): callable
    {
        return fn(Activite $a) => [
            'id'         => $a->id,
            'text'       => $a->libelle,
            'start_date' => $a->date_debut_prevue?->format('d-m-Y'),
            'end_date'   => $a->date_fin_prevue?->format('d-m-Y'),
            'progress'   => (float) $a->taux_realisation / 100,
            'type'       => 'task',
        ];
    }

    // ── Tests ─────────────────────────────────────────────────────────────

    public function test_collection_vide_retourne_tableau_vide(): void
    {
        $result = $this->builder->build(new Collection(), $this->toTask());

        $this->assertSame([], $result);
    }

    public function test_cree_un_groupe_par_resultat_attendu(): void
    {
        $ra1 = ResultatAttendu::factory()->create(['code' => 'RA-001', 'libelle' => 'Premier résultat']);
        $ra2 = ResultatAttendu::factory()->create(['code' => 'RA-002', 'libelle' => 'Deuxième résultat']);
        $dir = Direction::factory()->create();

        Activite::factory()->create(['resultat_attendu_id' => $ra1->id, 'direction_id' => $dir->id, 'date_debut_prevue' => '2026-01-01', 'date_fin_prevue' => '2026-02-01']);
        Activite::factory()->create(['resultat_attendu_id' => $ra2->id, 'direction_id' => $dir->id, 'date_debut_prevue' => '2026-02-01', 'date_fin_prevue' => '2026-03-01']);

        $activites = Activite::with('resultatAttendu')->get();
        $nodes     = $this->builder->build($activites, $this->toTask());

        $groups = array_filter($nodes, fn($n) => !empty($n['is_group']));
        $this->assertCount(2, $groups, 'Doit créer 2 groupes pour 2 résultats attendus');
    }

    public function test_id_groupe_utilise_offset_million(): void
    {
        $ra  = ResultatAttendu::factory()->create();
        $dir = Direction::factory()->create();
        Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'direction_id' => $dir->id]);

        $activites = Activite::with('resultatAttendu')->get();
        $nodes     = $this->builder->build($activites, $this->toTask());

        $group = current(array_filter($nodes, fn($n) => !empty($n['is_group'])));
        $this->assertEquals(1_000_000 + $ra->id, $group['id'], "L'ID du groupe doit valoir 1_000_000 + ra_id");
    }

    public function test_activites_assignees_au_bon_groupe_parent(): void
    {
        $ra  = ResultatAttendu::factory()->create();
        $dir = Direction::factory()->create();
        $a   = Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'direction_id' => $dir->id]);

        $activites = Activite::with('resultatAttendu')->get();
        $nodes     = $this->builder->build($activites, $this->toTask());

        $taskNode = current(array_filter($nodes, fn($n) => empty($n['is_group']) && $n['id'] === $a->id));
        $this->assertNotFalse($taskNode);
        $this->assertEquals(1_000_000 + $ra->id, $taskNode['parent']);
    }

    public function test_plage_dates_groupe_couvre_toutes_les_activites_enfants(): void
    {
        $ra  = ResultatAttendu::factory()->create();
        $dir = Direction::factory()->create();
        Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'direction_id' => $dir->id, 'date_debut_prevue' => '2026-03-01', 'date_fin_prevue' => '2026-04-01']);
        Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'direction_id' => $dir->id, 'date_debut_prevue' => '2026-01-01', 'date_fin_prevue' => '2026-06-30']);

        $activites = Activite::with('resultatAttendu')->get();
        $nodes     = $this->builder->build($activites, $this->toTask());

        $group = current(array_filter($nodes, fn($n) => !empty($n['is_group'])));
        $this->assertEquals('01-01-2026', $group['start_date'], 'La date de début du groupe doit être la plus ancienne');
        $this->assertEquals('30-06-2026', $group['end_date'],   'La date de fin du groupe doit être la plus tardive');
    }

    public function test_avancement_groupe_est_moyenne_des_enfants(): void
    {
        $ra  = ResultatAttendu::factory()->create();
        $dir = Direction::factory()->create();
        Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'direction_id' => $dir->id, 'taux_realisation' => 40]);
        Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'direction_id' => $dir->id, 'taux_realisation' => 60]);

        $activites = Activite::with('resultatAttendu')->get();
        $nodes     = $this->builder->build($activites, $this->toTask());

        $group = current(array_filter($nodes, fn($n) => !empty($n['is_group'])));
        // Moyenne = (40 + 60) / 2 / 100 = 0.50
        $this->assertEquals(0.50, $group['progress']);
    }

    public function test_groupe_est_de_type_project(): void
    {
        $ra  = ResultatAttendu::factory()->create();
        $dir = Direction::factory()->create();
        Activite::factory()->create(['resultat_attendu_id' => $ra->id, 'direction_id' => $dir->id]);

        $activites = Activite::with('resultatAttendu')->get();
        $nodes     = $this->builder->build($activites, $this->toTask());

        $group = current(array_filter($nodes, fn($n) => !empty($n['is_group'])));
        $this->assertEquals('project', $group['type']);
    }
}
