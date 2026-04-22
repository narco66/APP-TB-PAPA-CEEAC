<?php

namespace Tests\Feature;

use App\Models\ActionPrioritaire;
use App\Models\Activite;
use App\Models\Departement;
use App\Models\Direction;
use App\Models\Indicateur;
use App\Models\ObjectifImmediats;
use App\Models\Papa;
use App\Models\ResultatAttendu;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_liste_actions_prioritaires_est_limitee_au_departement_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_departement', ['papa.voir']);

        $departementVisible = Departement::factory()->create(['libelle' => 'Departement Visible']);
        $departementMasque = Departement::factory()->create(['libelle' => 'Departement Masque']);
        $papa = Papa::factory()->create();

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
            'libelle' => 'AP Visible',
        ]);
        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementMasque->id,
            'libelle' => 'AP Masquee',
        ]);

        $this->actingAs($user)
            ->get(route('actions-prioritaires.index'))
            ->assertOk()
            ->assertSee('AP Visible')
            ->assertDontSee('AP Masquee')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Departement Visible');
    }

    public function test_liste_activites_est_limitee_a_la_direction_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_activite', ['activite.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Masquee',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        Activite::factory()->create([
            'direction_id' => $directionVisible->id,
            'libelle' => 'Activite Visible',
        ]);
        Activite::factory()->create([
            'direction_id' => $directionMasquee->id,
            'libelle' => 'Activite Masquee',
        ]);

        $this->actingAs($user)
            ->get(route('activites.index'))
            ->assertOk()
            ->assertSee('Activite Visible')
            ->assertDontSee('Activite Masquee')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Direction Visible');
    }

    public function test_fiche_activite_hors_perimetre_est_refusee(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_activite_show', ['activite.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $activite = Activite::factory()->create([
            'direction_id' => $directionMasquee->id,
            'libelle' => 'Activite Hors Perimetre',
        ]);

        $this->actingAs($user)
            ->get(route('activites.show', $activite))
            ->assertForbidden();
    }

    public function test_liste_indicateurs_est_limitee_a_la_direction_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_indicateur', ['indicateur.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Indicateur Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Indicateur Masquee',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        Indicateur::factory()->create([
            'direction_id' => $directionVisible->id,
            'libelle' => 'Indicateur Visible',
        ]);
        Indicateur::factory()->create([
            'direction_id' => $directionMasquee->id,
            'libelle' => 'Indicateur Masque',
        ]);

        $this->actingAs($user)
            ->get(route('indicateurs.index'))
            ->assertOk()
            ->assertSee('Indicateur Visible')
            ->assertDontSee('Indicateur Masque')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Direction Indicateur Visible');
    }

    public function test_fiche_indicateur_hors_perimetre_est_refusee(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_indicateur_show', ['indicateur.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $indicateur = Indicateur::factory()->create([
            'direction_id' => $directionMasquee->id,
            'libelle' => 'Indicateur Hors Perimetre',
        ]);

        $this->actingAs($user)
            ->get(route('indicateurs.show', $indicateur))
            ->assertForbidden();
    }

    public function test_fiches_activite_et_indicateur_affichent_le_perimetre_courant(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_show_labels', ['activite.voir', 'indicateur.voir']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Detail Visible',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $activite = Activite::factory()->create([
            'direction_id' => $directionVisible->id,
            'libelle' => 'Activite Detail Visible',
        ]);
        $indicateur = Indicateur::factory()->create([
            'direction_id' => $directionVisible->id,
            'libelle' => 'Indicateur Detail Visible',
        ]);

        $this->actingAs($user)
            ->get(route('activites.show', $activite))
            ->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('Direction Detail Visible');

        $this->actingAs($user)
            ->get(route('indicateurs.show', $indicateur))
            ->assertOk()
            ->assertSee('Perimetre de donnees')
            ->assertSee('Direction Detail Visible');
    }

    public function test_formulaire_creation_activite_ne_propose_que_les_directions_du_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_activite_form', ['activite.creer']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Form Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Form Masquee',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('activites.create'))
            ->assertOk()
            ->assertSee('Direction Form Visible')
            ->assertDontSee('Direction Form Masquee')
            ->assertSee('Perimetre de donnees');
    }

    public function test_creation_activite_refuse_direction_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_activite_store', ['activite.creer']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);
        $resultatAttendu = ResultatAttendu::factory()->create();

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->from(route('activites.create'))
            ->post(route('activites.store'), [
                'resultat_attendu_id' => $resultatAttendu->id,
                'direction_id' => $directionMasquee->id,
                'code' => 'ACT-SCOPE-001',
                'libelle' => 'Activite forgee hors scope',
                'priorite' => 'normale',
            ])
            ->assertSessionHasErrors('direction_id');
    }

    public function test_formulaire_creation_activite_ne_propose_que_les_resultats_attendus_du_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_activite_ra_form', ['activite.creer']);

        $departement = Departement::factory()->create();
        $departementMasque = Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Form Visible',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        $actionVisible = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departement->id,
        ]);
        $actionMasquee = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementMasque->id,
        ]);
        $objectifVisible = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionVisible->id]);
        $objectifMasque = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionMasquee->id]);

        ResultatAttendu::factory()->create([
            'objectif_immediat_id' => $objectifVisible->id,
            'libelle' => 'RA Form Visible',
        ]);
        ResultatAttendu::factory()->create([
            'objectif_immediat_id' => $objectifMasque->id,
            'libelle' => 'RA Form Masque',
        ]);

        $this->actingAs($user)
            ->get(route('activites.create'))
            ->assertOk()
            ->assertSee('RA Form Visible')
            ->assertDontSee('RA Form Masque');
    }

    public function test_creation_activite_refuse_resultat_attendu_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_activite_ra_store', ['activite.creer']);

        $departement = Departement::factory()->create();
        $departementMasque = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        $actionVisible = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departement->id]);
        $actionMasquee = ActionPrioritaire::factory()->create(['papa_id' => $papa->id, 'departement_id' => $departementMasque->id]);
        $objectifVisible = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionVisible->id]);
        $objectifMasque = ObjectifImmediats::factory()->create(['action_prioritaire_id' => $actionMasquee->id]);
        ResultatAttendu::factory()->create(['objectif_immediat_id' => $objectifVisible->id]);
        $resultatMasque = ResultatAttendu::factory()->create(['objectif_immediat_id' => $objectifMasque->id]);

        $this->actingAs($user)
            ->from(route('activites.create'))
            ->post(route('activites.store'), [
                'resultat_attendu_id' => $resultatMasque->id,
                'direction_id' => $directionVisible->id,
                'code' => 'ACT-SCOPE-002',
                'libelle' => 'Activite forgee avec RA hors scope',
                'priorite' => 'normale',
            ])
            ->assertSessionHasErrors('resultat_attendu_id');
    }

    public function test_formulaire_creation_indicateur_ne_propose_que_les_directions_du_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_indicateur_form', ['indicateur.creer']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Indicateur Form Visible',
        ]);
        $directionMasquee = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction Indicateur Form Masquee',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('indicateurs.create'))
            ->assertOk()
            ->assertSee('Direction Indicateur Form Visible')
            ->assertDontSee('Direction Indicateur Form Masquee')
            ->assertSee('Perimetre de donnees');
    }

    public function test_creation_indicateur_refuse_direction_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_indicateur_store', ['indicateur.creer']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departement->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->from(route('indicateurs.create'))
            ->post(route('indicateurs.store'), [
                'code' => 'IND-SCOPE-001',
                'libelle' => 'Indicateur forge hors scope',
                'type_indicateur' => 'quantitatif',
                'frequence_collecte' => 'trimestrielle',
                'direction_id' => $directionMasquee->id,
            ])
            ->assertSessionHasErrors('direction_id');
    }

    public function test_api_papa_limite_actions_et_objectifs_au_perimetre_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_departement_api_papa', ['papa.voir']);

        $departementVisible = Departement::factory()->create();
        $departementMasque = Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();

        $actionVisible = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
            'code' => 'AP-VISIBLE',
            'libelle' => 'Action API Visible',
        ]);
        $actionMasquee = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementMasque->id,
            'code' => 'AP-MASQUEE',
            'libelle' => 'Action API Masquee',
        ]);

        ObjectifImmediats::factory()->create([
            'action_prioritaire_id' => $actionVisible->id,
            'code' => 'OI-VISIBLE',
            'libelle' => 'Objectif API Visible',
        ]);
        ObjectifImmediats::factory()->create([
            'action_prioritaire_id' => $actionMasquee->id,
            'code' => 'OI-MASQUE',
            'libelle' => 'Objectif API Masque',
        ]);

        $this->actingAs($user)
            ->get(route('api.papa.actions-prioritaires', $papa))
            ->assertOk()
            ->assertSee('AP-VISIBLE')
            ->assertDontSee('AP-MASQUEE');

        $this->actingAs($user)
            ->get(route('api.papa.objectifs-immediats', $papa))
            ->assertOk()
            ->assertSee('OI-VISIBLE')
            ->assertDontSee('OI-MASQUE');
    }

    public function test_api_direction_services_retourne_uniquement_les_services_visibles(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_api_services', ['activite.creer']);

        $departement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create([
            'departement_id' => $departement->id,
            'libelle' => 'Direction API Visible',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        Service::create([
            'direction_id' => $directionVisible->id,
            'code' => 'SRV-VISIBLE',
            'libelle' => 'Service Visible',
            'libelle_court' => 'SRV V',
            'ordre_affichage' => 1,
            'actif' => true,
        ]);

        $this->actingAs($user)
            ->get(route('api.direction.services', $directionVisible))
            ->assertOk()
            ->assertSee('Service Visible');
    }

    public function test_api_direction_services_refuse_une_direction_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_direction_api_services_forbidden', ['activite.creer']);

        $departement = Departement::factory()->create();
        $autreDepartement = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departement->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $autreDepartement->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        Service::create([
            'direction_id' => $directionMasquee->id,
            'code' => 'SRV-MASQUE',
            'libelle' => 'Service Masque',
            'libelle_court' => 'SRV M',
            'ordre_affichage' => 1,
            'actif' => true,
        ]);

        $this->actingAs($user)
            ->get(route('api.direction.services', $directionMasquee))
            ->assertForbidden();
    }

    public function test_formulaire_creation_action_prioritaire_ne_propose_que_les_departements_du_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_departement_ap_form', ['papa.modifier']);

        $departementVisible = Departement::factory()->create(['libelle' => 'Departement AP Visible']);
        $departementMasque = Departement::factory()->create(['libelle' => 'Departement AP Masque']);
        $papa = Papa::factory()->create();

        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('actions-prioritaires.create', ['papa_id' => $papa->id]))
            ->assertOk()
            ->assertSee('Departement AP Visible')
            ->assertDontSee('Departement AP Masque')
            ->assertSee('Perimetre de donnees');
    }

    public function test_creation_action_prioritaire_refuse_un_departement_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_departement_ap_store', ['papa.modifier']);

        $departementVisible = Departement::factory()->create();
        $departementMasque = Departement::factory()->create();
        $papa = Papa::factory()->create();

        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->from(route('actions-prioritaires.create', ['papa_id' => $papa->id]))
            ->post(route('actions-prioritaires.store'), [
                'papa_id' => $papa->id,
                'departement_id' => $departementMasque->id,
                'code' => 'AP-SCOPE-002',
                'libelle' => 'Action forgee hors scope',
                'qualification' => 'technique',
                'priorite' => 'normale',
            ])
            ->assertSessionHasErrors('departement_id');
    }

    public function test_fiche_action_prioritaire_hors_perimetre_est_refusee(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_departement_ap_show', ['papa.voir']);

        $departementVisible = Departement::factory()->create();
        $departementMasque = Departement::factory()->create();
        $papa = Papa::factory()->create();
        $ap = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementMasque->id,
            'libelle' => 'Action hors perimetre',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('actions-prioritaires.show', $ap))
            ->assertForbidden();
    }

    public function test_fiche_papa_n_affiche_que_les_actions_prioritaires_du_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_departement_papa_show', ['papa.voir']);

        $departementVisible = Departement::factory()->create(['libelle' => 'Departement PAPA Visible']);
        $departementMasque = Departement::factory()->create(['libelle' => 'Departement PAPA Masque']);
        $papa = Papa::factory()->create();

        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
            'libelle' => 'AP PAPA Visible',
        ]);
        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementMasque->id,
            'libelle' => 'AP PAPA Masquee',
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('papas.show', $papa))
            ->assertOk()
            ->assertSee('AP PAPA Visible')
            ->assertDontSee('AP PAPA Masquee');
    }
}
