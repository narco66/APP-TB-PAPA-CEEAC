<?php

namespace Tests\Feature;

use App\Models\Departement;
use App\Models\Direction;
use App\Models\Papa;
use App\Models\Rapport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RapportAccessFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_liste_rapports_est_limitee_au_perimetre_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('rapport_lecteur', ['rapport.voir']);

        $departement = Departement::factory()->create();
        $direction = Direction::factory()->create(['departement_id' => $departement->id]);
        $autreDirection = Direction::factory()->create();

        $user = User::factory()->create([
            'actif' => true,
            'direction_id' => $direction->id,
        ]);
        $user->assignRole($role);

        $redacteurMemeDirection = User::factory()->create([
            'actif' => true,
            'direction_id' => $direction->id,
        ]);
        $redacteurAutreDirection = User::factory()->create([
            'actif' => true,
            'direction_id' => $autreDirection->id,
        ]);

        $papa = Papa::factory()->create();

        $rapportVisibleParAuteur = Rapport::factory()->create([
            'papa_id' => $papa->id,
            'direction_id' => $direction->id,
            'departement_id' => $departement->id,
            'redige_par' => $user->id,
            'titre' => 'Rapport visible auteur',
        ]);

        Rapport::factory()->create([
            'papa_id' => $papa->id,
            'direction_id' => $direction->id,
            'departement_id' => $departement->id,
            'redige_par' => $redacteurMemeDirection->id,
            'titre' => 'Rapport visible direction',
        ]);

        Rapport::factory()->create([
            'papa_id' => $papa->id,
            'direction_id' => $autreDirection->id,
            'departement_id' => $autreDirection->departement_id,
            'redige_par' => $redacteurAutreDirection->id,
            'titre' => 'Rapport hors perimetre',
        ]);

        $this->actingAs($user)
            ->get(route('rapports.index'))
            ->assertOk()
            ->assertSee($rapportVisibleParAuteur->titre)
            ->assertSee('Rapport visible direction')
            ->assertDontSee('Rapport hors perimetre');
    }

    public function test_fiche_rapport_hors_perimetre_retourne_403(): void
    {
        $role = $this->creerRoleAvecPermissions('rapport_lecteur_show', ['rapport.voir']);

        $user = User::factory()->create([
            'actif' => true,
            'direction_id' => Direction::factory()->create()->id,
        ]);
        $user->assignRole($role);

        $autreDirection = Direction::factory()->create();
        $rapport = Rapport::factory()->create([
            'direction_id' => $autreDirection->id,
            'departement_id' => $autreDirection->departement_id,
        ]);

        $this->actingAs($user)
            ->get(route('rapports.show', $rapport))
            ->assertForbidden();
    }

    public function test_export_pdf_rapport_hors_perimetre_retourne_403(): void
    {
        $role = $this->creerRoleAvecPermissions('rapport_exporteur', ['rapport.voir', 'rapport.exporter']);

        $user = User::factory()->create([
            'actif' => true,
            'direction_id' => Direction::factory()->create()->id,
        ]);
        $user->assignRole($role);

        $autreDirection = Direction::factory()->create();
        $rapport = Rapport::factory()->create([
            'direction_id' => $autreDirection->id,
            'departement_id' => $autreDirection->departement_id,
        ]);

        $this->actingAs($user)
            ->get(route('rapports.export-pdf', $rapport))
            ->assertForbidden();
    }

    public function test_fiche_rapport_masque_actions_sensibles_hors_permissions_et_hors_visibilite_papa(): void
    {
        $role = $this->creerRoleAvecPermissions('rapport_lecteur_ui', ['rapport.voir']);

        $direction = Direction::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'direction_id' => $direction->id,
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create(['statut' => 'archive']);
        $rapport = Rapport::factory()->create([
            'papa_id' => $papa->id,
            'direction_id' => $direction->id,
            'departement_id' => $direction->departement_id,
            'redige_par' => $user->id,
            'titre' => 'Rapport archive UI',
        ]);

        $this->actingAs($user)
            ->get(route('rapports.show', $rapport))
            ->assertOk()
            ->assertDontSee('Export PDF')
            ->assertDontSee(route('papas.show', $papa), false)
            ->assertDontSee(route('rapports.export-papa-pdf', $papa), false)
            ->assertDontSee(route('rapports.export-excel', $papa), false);
    }

    public function test_vues_rapports_historiques_affichent_la_passserelle_vers_le_nouveau_reporting(): void
    {
        $role = $this->creerRoleAvecPermissions('rapport_lecteur_bridge', ['rapport.voir']);

        $direction = Direction::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'direction_id' => $direction->id,
        ]);
        $user->assignRole($role);

        $rapport = Rapport::factory()->create([
            'direction_id' => $direction->id,
            'departement_id' => $direction->departement_id,
            'redige_par' => $user->id,
            'titre' => 'Rapport passerelle',
        ]);

        $this->actingAs($user)
            ->get(route('rapports.index'))
            ->assertOk()
            ->assertSee('Centre de reporting')
            ->assertSee('Historique')
            ->assertSee(route('reports.dashboard'), false)
            ->assertSee(route('reports.library.index'), false);

        $this->actingAs($user)
            ->get(route('rapports.show', $rapport))
            ->assertOk()
            ->assertSee('Centre de reporting')
            ->assertSee(route('reports.dashboard'), false)
            ->assertSee(route('reports.library.index'), false);
    }
}
