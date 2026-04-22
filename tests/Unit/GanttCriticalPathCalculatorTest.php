<?php

namespace Tests\Unit;

use App\Services\Gantt\GanttCriticalPathCalculator;
use Tests\TestCase;

class GanttCriticalPathCalculatorTest extends TestCase
{
    private GanttCriticalPathCalculator $cpm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cpm = new GanttCriticalPathCalculator();
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function task(int $id, string $start, string $end): array
    {
        return [
            'id'         => $id,
            'start_date' => $start,
            'end_date'   => $end,
            'type'       => 'task',
        ];
    }

    private function link(int $source, int $target, int $lag = 0): array
    {
        return ['id' => $source * 1000 + $target, 'source' => $source, 'target' => $target, 'lag' => $lag];
    }

    // ── Tests ─────────────────────────────────────────────────────────────

    public function test_graphe_vide_retourne_tableau_vide(): void
    {
        $this->assertSame([], $this->cpm->compute([], []));
        $this->assertSame([], $this->cpm->compute([['is_group' => true, 'id' => 1]], []));
    }

    public function test_tache_unique_sans_dependance_est_critique(): void
    {
        $tasks = [$this->task(1, '01-01-2026', '31-01-2026')];
        $ids   = $this->cpm->compute($tasks, []);

        $this->assertContains(1, $ids);
    }

    public function test_graphe_lineaire_toutes_taches_sur_chemin_critique(): void
    {
        // A(30j) → B(20j) → C(10j)  →  chemin unique = critique
        $tasks = [
            $this->task(1, '01-01-2026', '31-01-2026'),  // 30j
            $this->task(2, '01-02-2026', '21-02-2026'),  // 20j
            $this->task(3, '21-02-2026', '03-03-2026'),  // 10j
        ];
        $links = [$this->link(1, 2), $this->link(2, 3)];

        $ids = $this->cpm->compute($tasks, $links);

        $this->assertContains(1, $ids);
        $this->assertContains(2, $ids);
        $this->assertContains(3, $ids);
    }

    public function test_seul_le_chemin_le_plus_long_est_critique(): void
    {
        // A se divise en B (court, 5j) et C (long, 30j), les deux rejoignent D.
        // Chemin critique : A → C → D. B n'est pas critique.
        $tasks = [
            $this->task(1, '01-01-2026', '11-01-2026'),  // A : 10j
            $this->task(2, '11-01-2026', '16-01-2026'),  // B :  5j (court)
            $this->task(3, '11-01-2026', '10-02-2026'),  // C : 30j (long)
            $this->task(4, '10-02-2026', '20-02-2026'),  // D : 10j
        ];
        $links = [
            $this->link(1, 2),
            $this->link(1, 3),
            $this->link(2, 4),
            $this->link(3, 4),
        ];

        $ids = $this->cpm->compute($tasks, $links);

        $this->assertContains(1, $ids, 'A doit être critique');
        $this->assertNotContains(2, $ids, 'B (court) ne doit pas être critique');
        $this->assertContains(3, $ids, 'C (long) doit être critique');
        $this->assertContains(4, $ids, 'D doit être critique');
    }

    public function test_taches_groupe_ignorees(): void
    {
        $tasks = [
            ['id' => 999, 'is_group' => true, 'start_date' => '01-01-2026', 'end_date' => '31-01-2026', 'type' => 'project'],
            $this->task(1, '01-01-2026', '31-01-2026'),
        ];
        $ids = $this->cpm->compute($tasks, []);

        $this->assertNotContains(999, $ids, 'Le groupe ne doit pas apparaître dans le chemin critique');
        $this->assertContains(1, $ids);
    }

    public function test_jalons_ignores(): void
    {
        $tasks = [
            $this->task(1, '01-01-2026', '31-01-2026'),
            ['id' => 2, 'start_date' => '31-01-2026', 'end_date' => '31-01-2026', 'type' => 'milestone'],
        ];
        $ids = $this->cpm->compute($tasks, [$this->link(1, 2)]);

        $this->assertNotContains(2, $ids, 'Le jalon ne doit pas apparaître dans le chemin critique');
    }

    public function test_lag_retarde_le_successeur(): void
    {
        // A(10j) → [lag=20j] → B(10j)
        // Sans lag : float de B = 20j → B non critique
        // Avec lag  : EF(A)+20 = 30, EF(B) = 40 = project finish → B critique
        $tasks = [
            $this->task(1, '01-01-2026', '11-01-2026'),  // A : 10j
            $this->task(2, '01-01-2026', '11-01-2026'),  // B : 10j (dates prévues quelconques, CPM recalcule)
        ];
        $links = [
            ['id' => 12, 'source' => 1, 'target' => 2, 'lag' => 20],
        ];

        $ids = $this->cpm->compute($tasks, $links);

        // A : ES=0, EF=10 ; B : ES=30, EF=40 = project finish → float 0
        $this->assertContains(2, $ids);
    }

    public function test_cycle_retourne_tableau_vide(): void
    {
        $tasks = [
            $this->task(1, '01-01-2026', '11-01-2026'),
            $this->task(2, '11-01-2026', '21-01-2026'),
        ];
        $links = [
            $this->link(1, 2),
            $this->link(2, 1),   // cycle !
        ];

        $ids = $this->cpm->compute($tasks, $links);

        $this->assertSame([], $ids, 'Un cycle doit produire un tableau vide');
    }

    public function test_taches_sans_dates_valides_ignorees(): void
    {
        $tasks = [
            ['id' => 1, 'start_date' => '',             'end_date' => '31-01-2026', 'type' => 'task'],
            ['id' => 2, 'start_date' => '01-01-2026',   'end_date' => '',           'type' => 'task'],
            $this->task(3, '01-01-2026', '31-01-2026'),
        ];

        $ids = $this->cpm->compute($tasks, []);

        $this->assertNotContains(1, $ids);
        $this->assertNotContains(2, $ids);
        $this->assertContains(3, $ids);
    }
}
