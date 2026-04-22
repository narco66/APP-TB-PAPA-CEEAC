<?php

namespace Tests\Feature;

use App\Models\ActionPrioritaire;
use App\Models\BudgetPapa;
use App\Models\Departement;
use App\Models\Direction;
use App\Models\Papa;
use App\Models\Risque;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetRisqueScopeFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_index_est_limite_au_perimetre_utilisateur(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_budget_index', ['budget.voir']);

        $departementVisible = Departement::factory()->create(['libelle' => 'Departement Budget Visible']);
        $departementMasque = Departement::factory()->create(['libelle' => 'Departement Budget Masque']);
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
            'libelle' => 'AP Budget Visible',
        ]);
        $actionMasquee = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementMasque->id,
            'libelle' => 'AP Budget Masquee',
        ]);

        BudgetPapa::factory()->create([
            'papa_id' => $papa->id,
            'action_prioritaire_id' => $actionVisible->id,
            'libelle_ligne' => 'Budget visible',
        ]);
        BudgetPapa::factory()->create([
            'papa_id' => $papa->id,
            'action_prioritaire_id' => $actionMasquee->id,
            'libelle_ligne' => 'Budget masque',
        ]);

        $this->actingAs($user)
            ->get(route('budgets.index', $papa))
            ->assertOk()
            ->assertSee('Budget visible')
            ->assertDontSee('Budget masque')
            ->assertSee('Perimetre de donnees')
            ->assertSee('Departement Budget Visible');
    }

    public function test_creation_budget_refuse_action_prioritaire_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_budget_store', ['budget.creer']);

        $departementVisible = Departement::factory()->create();
        $departementMasque = Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papa = Papa::factory()->create();
        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
        ]);
        $actionMasquee = ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementMasque->id,
        ]);

        $this->actingAs($user)
            ->from(route('budgets.create', $papa))
            ->post(route('budgets.store', $papa), [
                'source_financement' => 'budget_ceeac',
                'annee_budgetaire' => (int) $papa->annee,
                'montant_prevu' => 100000,
                'action_prioritaire_id' => $actionMasquee->id,
            ])
            ->assertSessionHasErrors('action_prioritaire_id');
    }

    public function test_registre_risques_refuse_un_papa_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_risque_index', ['risque.voir']);

        $departementVisible = Departement::factory()->create();
        $departementMasque = Departement::factory()->create();
        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'scope_level' => 'departement',
        ]);
        $user->assignRole($role);

        $papaVisible = Papa::factory()->create();
        ActionPrioritaire::factory()->create([
            'papa_id' => $papaVisible->id,
            'departement_id' => $departementVisible->id,
        ]);

        $papaMasque = Papa::factory()->create();
        ActionPrioritaire::factory()->create([
            'papa_id' => $papaMasque->id,
            'departement_id' => $departementMasque->id,
        ]);

        $this->actingAs($user)
            ->get(route('risques.index', $papaVisible))
            ->assertOk()
            ->assertSee('Perimetre de donnees');

        $this->actingAs($user)
            ->get(route('risques.index', $papaMasque))
            ->assertForbidden();
    }

    public function test_creation_risque_refuse_responsable_hors_perimetre(): void
    {
        $role = $this->creerRoleAvecPermissions('scope_risque_store', ['risque.creer']);

        $departementVisible = Departement::factory()->create();
        $departementMasque = Departement::factory()->create();
        $directionVisible = Direction::factory()->create(['departement_id' => $departementVisible->id]);
        $directionMasquee = Direction::factory()->create(['departement_id' => $departementMasque->id]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $user->assignRole($role);

        $responsableVisible = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementVisible->id,
            'direction_id' => $directionVisible->id,
            'scope_level' => 'direction',
        ]);
        $responsableMasque = User::factory()->create([
            'actif' => true,
            'departement_id' => $departementMasque->id,
            'direction_id' => $directionMasquee->id,
            'scope_level' => 'direction',
        ]);

        $papa = Papa::factory()->create();
        ActionPrioritaire::factory()->create([
            'papa_id' => $papa->id,
            'departement_id' => $departementVisible->id,
        ]);

        $this->actingAs($user)
            ->get(route('risques.create', $papa))
            ->assertOk()
            ->assertSee($responsableVisible->name)
            ->assertDontSee($responsableMasque->name);

        $this->actingAs($user)
            ->from(route('risques.create', $papa))
            ->post(route('risques.store', $papa), [
                'code' => 'RSQ-SCOPE-001',
                'libelle' => 'Risque forge hors scope',
                'categorie' => 'operationnel',
                'probabilite' => 'moyenne',
                'impact' => 'majeur',
                'responsable_id' => $responsableMasque->id,
            ])
            ->assertSessionHasErrors('responsable_id');
    }
}
