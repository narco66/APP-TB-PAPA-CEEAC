<?php

namespace Tests\Feature;

use App\Models\ActionPrioritaire;
use App\Models\Activite;
use App\Models\Direction;
use App\Models\Indicateur;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbmHierarchyScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_liste_objectifs_immediats_est_limitee_au_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('rbm_scope_oi_reader', ['papa.voir']);

        $departementVisible = \App\Models\Departement::factory()->create(['libelle' => 'Departement OI Visible']);
        $departementMasque = \App\Models\Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        $actionVisible = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementVisible->id]);
        $actionMasquee = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementMasque->id]);

        ObjectifImmediats::factory()->create([
            'action_prioritaire_id' => $actionVisible->id,
            'libelle' => 'OI Visible',
        ]);
        ObjectifImmediats::factory()->create([
            'action_prioritaire_id' => $actionMasquee->id,
            'libelle' => 'OI Masque',
        ]);

        $this->actingAs($user)
            ->get(route('objectifs-immediats.index'))
            ->assertOk()
            ->assertSee('OI Visible')
            ->assertDontSee('OI Masque')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Departement OI Visible');
    }

    public function test_creation_objectif_immediat_refuse_action_prioritaire_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('rbm_scope_oi_create', ['papa.modifier']);

        $departementVisible = \App\Models\Departement::factory()->create();
        $departementMasque = \App\Models\Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementVisible->id]);
        $actionMasquee = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementMasque->id]);

        $this->actingAs($user)
            ->post(route('objectifs-immediats.store'), [
                'action_prioritaire_id' => $actionMasquee->id,
                'code' => 'OI-SCOPE-001',
                'libelle' => 'Objectif forge',
            ])
            ->assertForbidden();
    }

    public function test_fiche_resultat_attendu_hors_perimetre_est_refusee(): void
    {
        $role = $this->creerRoleAvecPermissions('rbm_scope_ra_reader', ['papa.voir']);

        $departementVisible = \App\Models\Departement::factory()->create();
        $departementMasque = \App\Models\Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        $actionVisible = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementVisible->id]);
        $actionMasquee = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementMasque->id]);
        $objectifVisible = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionVisible->id]);
        $objectifMasque = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionMasquee->id]);

        $raVisible = ResultatAttendu::factory()->create(['objectif_immediat_id' => $objectifVisible->id, 'libelle' => 'RA Visible']);
        $raMasque = ResultatAttendu::factory()->create(['objectif_immediat_id' => $objectifMasque->id, 'libelle' => 'RA Masque']);

        $this->actingAs($user)
            ->get(route('resultats-attendus.index'))
            ->assertOk()
            ->assertSee('RA Visible')
            ->assertDontSee('RA Masque')
            ->assertSee('Perimetre de donnees');

        $this->actingAs($user)
            ->get(route('resultats-attendus.show', $raMasque))
            ->assertForbidden();
    }

    public function test_creation_resultat_attendu_refuse_objectif_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('rbm_scope_ra_create', ['papa.modifier']);

        $departementVisible = \App\Models\Departement::factory()->create();
        $departementMasque = \App\Models\Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        $actionVisible = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementVisible->id]);
        $actionMasquee = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementMasque->id]);
        ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionVisible->id]);
        $objectifMasque = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionMasquee->id]);

        $this->actingAs($user)
            ->post(route('resultats-attendus.store'), [
                'objectif_immediat_id' => $objectifMasque->id,
                'code' => 'RA-SCOPE-001',
                'libelle' => 'Resultat forge',
                'type_resultat' => 'output',
            ])
            ->assertForbidden();
    }

    public function test_fiche_objectif_immediat_ne_montre_pas_indicateur_hors_direction(): void
    {
        $role = $this->creerRoleAvecPermissions('rbm_scope_oi_show_nested', ['papa.voir']);

        $departement = \App\Models\Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction OI Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction OI Masquee',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        $action = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departement->id,
        ]);
        $objectif = ObjectifImmediats::factory()->create([
            'action_prioritaire_id' => $action->id,
            'libelle' => 'OI Scope Nested',
        ]);

        Indicateur::factory()->create([
            'objectif_immediat_id' => $objectif->id,
            'action_prioritaire_id' => $action->id,
            'direction_id' => $directionVisible->id,
            'libelle' => 'Indicateur OI Visible',
        ]);
        Indicateur::factory()->create([
            'objectif_immediat_id' => $objectif->id,
            'action_prioritaire_id' => $action->id,
            'direction_id' => $directionMasquee->id,
            'libelle' => 'Indicateur OI Masque',
        ]);

        $this->actingAs($user)
            ->get(route('objectifs-immediats.show', $objectif))
            ->assertOk()
            ->assertSee('Indicateur OI Visible')
            ->assertDontSee('Indicateur OI Masque')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Direction OI Visible');
    }

    public function test_fiche_resultat_attendu_ne_montre_pas_activite_hors_direction(): void
    {
        $role = $this->creerRoleAvecPermissions('rbm_scope_ra_show_nested', ['papa.voir']);

        $departement = \App\Models\Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction RA Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction RA Masquee',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        $action = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departement->id,
        ]);
        $objectif = ObjectifImmediats::factory()->create([
            'action_prioritaire_id' => $action->id,
        ]);
        $resultat = ResultatAttendu::factory()->create([
            'objectif_immediat_id' => $objectif->id,
            'libelle' => 'RA Scope Nested',
        ]);

        Activite::factory()->create([
            'resultat_attendu_id' => $resultat->id,
            'direction_id' => $directionVisible->id,
            'libelle' => 'Activite RA Visible',
        ]);
        Activite::factory()->create([
            'resultat_attendu_id' => $resultat->id,
            'direction_id' => $directionMasquee->id,
            'libelle' => 'Activite RA Masquee',
        ]);

        $this->actingAs($user)
            ->get(route('resultats-attendus.show', $resultat))
            ->assertOk()
            ->assertSee('Activite RA Visible')
            ->assertDontSee('Activite RA Masquee')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Direction RA Visible');
    }

    public function test_fiche_action_prioritaire_affiche_le_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('rbm_scope_ap_show_label', ['papa.voir']);

        $departementVisible = \App\Models\Departement::factory()->create(['libelle' => 'Departement AP Scope']);
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        $action = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
            'libelle' => 'AP Scope Label',
        ]);

        $this->actingAs($user)
            ->get(route('actions-prioritaires.show', $action))
            ->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('Departement AP Scope');
    }
}
