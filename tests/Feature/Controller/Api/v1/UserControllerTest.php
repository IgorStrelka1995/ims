<?php

namespace Tests\Feature\Controller\Api\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    public function testRegisterUser()
    {
        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user/register', [
            'name' => 'Customer',
            'email' => 'customer@mail.com',
            'password' => 'tmp1234#',
            'role' => User::ROLE_INVENTORY_MANAGER
        ]);

        $response->assertStatus(201);

        $userId = $response->json('data.user.id');

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->has('user');
            });
        });

        $roles = User::find($userId)->getRoleNames();

        $this->assertEquals('inventory-manager', $roles->first());
    }
}
