<?php

namespace Tests\Feature;

use App\Models\Activite;
use App\Models\Direction;
use App\Models\ResultatAttendu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GanttFeatureTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ─────────────────────────────────────────────────────────

    private function userAvecPermission(string $roleName, array $permissions, array $attrs = []): User
    {
        $role = $this->creerRoleAvecPermissions($roleName, $permissions);
        $user = User::factory()->create(array_merge(['actif' => true], $attrs));
        $user->assignRole($role);
        return $user;
    }

    private function creerActiviteVisible(User $user): Activite
    {
        $dir = Direction::factory()->create();

        // Associer l'utilisateur à cette direction
        $user->update(['direction_id' => $dir->id, 'scope_level' => 'direction']);

        $ra = ResultatAttendu::factory()->create();

        return Activite::factory()->create([
            'direction_id'      => $dir->id,
            'resultat_attendu_id' => $ra->id,
            'date_debut_prevue' => now()->subDays(10)->toDateString(),
            'date_fin_prevue'   => now()->addDays(30)->toDateString(),
            'statut'            => 'en_cours',
        ]);
    }

    // ─── Index ───────────────────────────────────────────────────────────

    public function test_index_redirige_si_non_authentifie(): void
    {
        $this->get(route('activites.gantt'))
            ->assertRedirect(route('login'));
    }

    public function test_index_retourne_403_sans_permission(): void
    {
        $role = $this->creerRoleAvecPermissions('gantt_sans_perm', []);
        $user = User::factory()->create(['actif' => true]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('activites.gantt'))
            ->assertForbidden();
    }

    public function test_index_retourne_200_avec_permission(): void
    {
        $user = $this->userAvecPermission('gantt_viewer', ['activite.voir']);

        $this->actingAs($user)
            ->get(route('activites.gantt'))
            ->assertOk()
            ->assertSee('Diagramme Gantt')
            ->assertSee($user->scopeLabel());
    }

    // ─── Endpoint JSON /data ─────────────────────────────────────────────

    public function test_data_retourne_json_bien_structure(): void
    {
        $user = $this->userAvecPermission('gantt_data', ['activite.voir']);
        $this->creerActiviteVisible($user);

        $response = $this->actingAs($user)
            ->getJson(route('gantt.data'));

        $response->assertOk()
            ->assertJsonStructure([
                'data'        => [['id', 'text', 'start_date', 'end_date', 'progress', 'type']],
                'links',
                'scope_label',
                'total',
            ]);
    }

    public function test_data_total_correspond_au_nombre_activites_visibles(): void
    {
        $user = $this->userAvecPermission('gantt_total', ['activite.voir']);
        $dir  = Direction::factory()->create();
        $user->update(['direction_id' => $dir->id, 'scope_level' => 'direction']);
        $ra   = ResultatAttendu::factory()->create();

        // Créer 2 activités dans la même direction, dans la fenêtre glissante
        foreach (range(1, 2) as $i) {
            Activite::factory()->create([
                'direction_id'       => $dir->id,
                'resultat_attendu_id' => $ra->id,
                'date_debut_prevue'  => now()->subDays(10)->toDateString(),
                'date_fin_prevue'    => now()->addDays(30)->toDateString(),
            ]);
        }

        $response = $this->actingAs($user)->getJson(route('gantt.data'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(2, $response->json('total'));
    }

    public function test_data_respecte_le_scope_utilisateur(): void
    {
        $dirVisible  = Direction::factory()->create();
        $dirMasquee  = Direction::factory()->create();

        $user = $this->userAvecPermission('gantt_scope', ['activite.voir'], [
            'direction_id' => $dirVisible->id,
            'scope_level'  => 'direction',
        ]);

        $raVisible  = ResultatAttendu::factory()->create();
        $raMasquee  = ResultatAttendu::factory()->create();

        $actVisible  = Activite::factory()->create([
            'direction_id'       => $dirVisible->id,
            'resultat_attendu_id' => $raVisible->id,
            'libelle'            => 'Activite visible scope',
            'date_debut_prevue'  => now()->subDays(5)->toDateString(),
            'date_fin_prevue'    => now()->addDays(30)->toDateString(),
        ]);
        $actMasquee  = Activite::factory()->create([
            'direction_id'       => $dirMasquee->id,
            'resultat_attendu_id' => $raMasquee->id,
            'libelle'            => 'Activite masquee scope',
            'date_debut_prevue'  => now()->subDays(5)->toDateString(),
            'date_fin_prevue'    => now()->addDays(30)->toDateString(),
        ]);

        $response = $this->actingAs($user)->getJson(route('gantt.data'));

        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($actVisible->id, $ids,    'L\'activité visible doit apparaître');
        $this->assertNotContains($actMasquee->id, $ids, 'L\'activité hors périmètre ne doit pas apparaître');
    }

    public function test_data_filtre_par_statut(): void
    {
        $user = $this->userAvecPermission('gantt_filtre_statut', ['activite.voir']);
        $dir  = Direction::factory()->create();
        $user->update(['direction_id' => $dir->id, 'scope_level' => 'direction']);
        $ra   = ResultatAttendu::factory()->create();

        Activite::factory()->create([
            'direction_id' => $dir->id, 'resultat_attendu_id' => $ra->id,
            'statut' => 'en_cours', 'libelle' => 'En cours test',
            'date_debut_prevue' => now()->subDays(5)->toDateString(),
            'date_fin_prevue'   => now()->addDays(30)->toDateString(),
        ]);
        Activite::factory()->create([
            'direction_id' => $dir->id, 'resultat_attendu_id' => $ra->id,
            'statut' => 'planifiee', 'libelle' => 'Planifiée test',
            'date_debut_prevue' => now()->subDays(5)->toDateString(),
            'date_fin_prevue'   => now()->addDays(30)->toDateString(),
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('gantt.data') . '?statut[]=en_cours');

        $statuts = collect($response->json('data'))
            ->filter(fn($t) => empty($t['is_group']))
            ->pluck('statut')
            ->unique()
            ->values()
            ->all();

        $this->assertContains('en_cours', $statuts);
        $this->assertNotContains('planifiee', $statuts);
    }

    // ─── Endpoint /detail ────────────────────────────────────────────────

    public function test_detail_retourne_fiche_activite_complete(): void
    {
        $user     = $this->userAvecPermission('gantt_detail', ['activite.voir']);
        $activite = $this->creerActiviteVisible($user);

        $response = $this->actingAs($user)
            ->getJson(route('gantt.detail', $activite->id));

        $response->assertOk()
            ->assertJsonStructure([
                'id', 'code', 'libelle', 'statut', 'priorite',
                'taux_realisation',
                'date_debut_prevue', 'date_fin_prevue',
                'budget_prevu', 'budget_consomme',
                'direction', 'responsable',
                'rbm' => ['resultat_attendu', 'objectif_immediat', 'action_prioritaire'],
                'alertes', 'documents', 'url_detail',
            ])
            ->assertJsonPath('id', $activite->id);
    }

    public function test_detail_refuse_activite_hors_perimetre(): void
    {
        $user        = $this->userAvecPermission('gantt_detail_scope', ['activite.voir']);
        $dirMasquee  = Direction::factory()->create();
        $raMasquee   = ResultatAttendu::factory()->create();

        $activiteMasquee = Activite::factory()->create([
            'direction_id'       => $dirMasquee->id,
            'resultat_attendu_id' => $raMasquee->id,
        ]);

        $this->actingAs($user)
            ->getJson(route('gantt.detail', $activiteMasquee->id))
            ->assertStatus(404);
    }

    // ─── Cache ───────────────────────────────────────────────────────────

    public function test_cache_invalide_apres_modification_activite(): void
    {
        Cache::put('gantt.version', 5, 60);

        $user = $this->userAvecPermission('gantt_cache', ['activite.voir']);
        $dir  = Direction::factory()->create();
        $user->update(['direction_id' => $dir->id, 'scope_level' => 'direction']);
        $ra   = ResultatAttendu::factory()->create();

        $activite = Activite::factory()->create([
            'direction_id' => $dir->id, 'resultat_attendu_id' => $ra->id,
        ]);

        $activite->update(['libelle' => 'Libellé modifié']);

        $this->assertGreaterThan(5, Cache::get('gantt.version'), 'La version du cache doit être incrémentée');
    }

    // ─── Export ──────────────────────────────────────────────────────────

    public function test_export_excel_telecharge_un_fichier_xlsx(): void
    {
        $user = $this->userAvecPermission('gantt_export_excel', ['activite.voir']);
        $this->creerActiviteVisible($user);

        $this->actingAs($user)
            ->get(route('gantt.export.excel'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_export_pdf_telecharge_un_fichier_pdf(): void
    {
        $user = $this->userAvecPermission('gantt_export_pdf', ['activite.voir']);
        $this->creerActiviteVisible($user);

        $this->actingAs($user)
            ->get(route('gantt.export.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_excel_interdit_sans_authentification(): void
    {
        $this->get(route('gantt.export.excel'))->assertRedirect(route('login'));
    }

    public function test_export_pdf_interdit_sans_authentification(): void
    {
        $this->get(route('gantt.export.pdf'))->assertRedirect(route('login'));
    }
}
