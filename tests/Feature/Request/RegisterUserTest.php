<?php

namespace Tests\Feature\Request;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    private static $userData = [
        'name' => 'customer',
        'email' => 'customer@mail.com',
        'password' => 'tmp1234!',
        'role' => User::ROLE_VIEWER
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    public function testRegisterUser()
    {
        $response = $this->postJson('/api/v1/user/register', self::$userData);

        $response->assertStatus(401);

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user/register', self::$userData);

        $response->assertStatus(403);

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user/register', self::$userData);

        $response->assertStatus(403);

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user/register', self::$userData);

        $response->assertStatus(201);
    }

    public function testValidationRegister()
    {
        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/user/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'role' => ''
        ]);

        $response->assertStatus(422);

        $userData = self::$userData;
        $userData['role'] = 'undefined-role';

        $response = $this->postJson('/api/v1/user/register', $userData);

        $response->assertStatus(422);
    }
}
