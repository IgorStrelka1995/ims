<?php

namespace Tests\Feature\Controller\Api\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    public function testLoginUser()
    {
        User::factory()->create([
            'email' => 'customer@mail.com',
            'password' => Hash::make('tmp1234#')
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'guest@mail.com',
            'password' => 'tmp1234#'
        ]);

        $response->assertStatus(401);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'customer@mail.com',
            'password' => 'tmp1234#'
        ]);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->has('token');
            });
        });
    }

    public function testLogoutUser()
    {
        $user = User::factory()->create([
            'email' => 'customer@mail.com',
            'password' => Hash::make('tmp1234#')
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'customer@mail.com',
            'password' => 'tmp1234#'
        ]);

        $response->assertStatus(200);

        $token = $response->json('data.token');

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/v1/logout', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }

    public function testRegisterUser()
    {
        $response = $this->postJson('/api/v1/register', [
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
