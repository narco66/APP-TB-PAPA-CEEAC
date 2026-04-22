<?php

namespace Tests\Unit;

use App\Models\Departement;
use App\Models\Direction;
use App\Models\Service;
use App\Models\TransversalScope;
use App\Models\User;
use App\Services\Security\UserScopeResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserScopeResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resout_le_perimetre_depuis_service_direction_et_departement(): void
    {
        $departement = Departement::factory()->create();
        $direction = Direction::factory()->create(['departement_id' => $departement->id]);
        $service = Service::create([
            'direction_id' => $direction->id,
            'code' => 'SRV-001',
            'libelle' => 'Service test',
            'actif' => true,
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'scope_level' => 'service',
        ]);

        $scope = app(UserScopeResolver::class)->resolve($user);

        $this->assertSame('service', $scope->level);
        $this->assertSame([$departement->id], $scope->departementIds);
        $this->assertSame([$direction->id], $scope->directionIds);
        $this->assertSame([$service->id], $scope->serviceIds);
        $this->assertFalse($scope->isGlobal);
    }

    public function test_ajoute_les_scopes_transversaux_actifs(): void
    {
        $departement = Departement::factory()->create();
        $direction = Direction::factory()->create(['departement_id' => $departement->id]);
        $service = Service::create([
            'direction_id' => $direction->id,
            'code' => 'SRV-002',
            'libelle' => 'Service test 2',
            'actif' => true,
        ]);

        $user = User::factory()->create([
            'actif' => true,
            'departement_id' => $departement->id,
            'direction_id' => $direction->id,
            'service_id' => $service->id,
            'scope_level' => 'service',
        ]);

        $autreDepartement = Departement::factory()->create();

        TransversalScope::create([
            'user_id' => $user->id,
            'scope_type' => 'departement',
            'departement_id' => $autreDepartement->id,
            'can_view' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $scope = app(UserScopeResolver::class)->resolve($user);

        $this->assertTrue($scope->isTransversal);
        $this->assertContains($autreDepartement->id, $scope->departementIds);
    }
}
