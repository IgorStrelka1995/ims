<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private static $userData = [
        'name' => 'Customer',
        'email' => 'customer@mail.com',
        'password' => 'tmp1234#',
        'role' => User::ROLE_INVENTORY_MANAGER
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    /**
     * A basic feature test example.
     */
    public function testCreateUser(): void
    {
        /********** Unauthenticated *********/

        $response = $this->postJson('/api/v1/user/register', self::$userData, ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user/register', self::$userData);

        $response->assertStatus(403);

        /********** ROLE_INVENTORY_MANAGER *********/

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user/register', self::$userData);

        $response->assertStatus(403);

        /********** ROLE_ADMIN *********/

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user/register', self::$userData);

        $response->assertStatus(201);
    }
}
