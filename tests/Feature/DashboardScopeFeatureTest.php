<?php

namespace Tests\Feature;

use App\Models\ActionPrioritaire;
use App\Models\Activite;
use App\Models\Departement;
use App\Models\Direction;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_commissaire_est_limite_au_departement_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('commissaire', ['papa.voir']);

        $departementVisible = Departement::factory()->create(['libelle' => 'Departement Dashboard Visible']);
        $departementMasque = Departement::factory()->create(['libelle' => 'Departement Dashboard Masque']);
        $directionVisible = Direction::factory()->create(['departement_id' => $departementVisible->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create(['statut' => 'en_execution']);

        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
            'taux_realisation' => 25,
            'statut' => 'en_cours',
        ]);
        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementMasque->id,
            'taux_realisation' => 80,
            'statut' => 'en_cours',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('Departement Dashboard Visible')
            ->assertSee('25%')
            ->assertDontSee('80%');
    }

    public function test_dashboard_direction_ne_compte_pas_les_activites_d_une_autre_direction(): void
    {
        $role = $this->creerRoleAvecPermissions('directeur_technique', ['papa.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Dashboard Visible',
            'type_direction' => 'technique',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Dashboard Masquee',
            'type_direction' => 'technique',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create(['statut' => 'en_execution']);

        $actionVisible = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departement->id,
        ]);
        $objectifVisible = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionVisible->id]);
        $resultatVisible = ResultatAttendu::factory()->create(['objectif_immediat_id' => $objectifVisible->id]);
        Activite::factory()->create([
            'resultat_attendu_id' => $resultatVisible->id,
            'direction_id' => $directionVisible->id,
            'statut' => 'en_cours',
            'libelle' => 'Activite Dashboard Visible',
        ]);

        $actionMasquee = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departement->id,
        ]);
        $objectifMasque = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionMasquee->id]);
        $resultatMasque = ResultatAttendu::factory()->create(['objectif_immediat_id' => $objectifMasque->id]);
        Activite::factory()->create([
            'resultat_attendu_id' => $resultatMasque->id,
            'direction_id' => $directionMasquee->id,
            'statut' => 'en_cours',
            'libelle' => 'Activite Dashboard Masquee',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('Direction Dashboard Visible')
            ->assertSee('/ 1 total')
            ->assertDontSee('/ 2 total');
    }
}
