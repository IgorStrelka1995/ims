<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuditPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    public function testViewAnyAudit()
    {
        /********** Unauthenticated *********/

        $response = $this->get('/api/v1/audits', ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/audits');

        $response->assertStatus(200);

        /********** ROLE_INVENTORY_MANAGER *********/

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/audits');

        $response->assertStatus(200);

        /********** ROLE_ADMIN *********/

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/audits');

        $response->assertStatus(200);
    }
}
