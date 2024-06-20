<?php

namespace Tests\Feature\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StockPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    public function testViewAnyStock()
    {
        /********** Unauthenticated *********/

        $response = $this->get('/api/v1/stocks', ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/stocks');

        $response->assertStatus(200);

        /********** ROLE_INVENTORY_MANAGER *********/

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/stocks');

        $response->assertStatus(200);

        /********** ROLE_ADMIN *********/

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/stocks');

        $response->assertStatus(200);
    }

    public function testStockIn()
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/stocks/in/' . $product->id, ["quantity" => 100], ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/stocks/in/' . $product->id, ["quantity" => 100], ['Accept' => 'application/json']);

        $response->assertStatus(403);

        /********** ROLE_INVENTORY_MANAGER *********/

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/stocks/in/' . $product->id, ["quantity" => 100], ['Accept' => 'application/json']);

        $response->assertStatus(201);

        /********** ROLE_ADMIN *********/

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/stocks/in/' . $product->id, ["quantity" => 100], ['Accept' => 'application/json']);

        $response->assertStatus(201);
    }

    public function testStockOut()
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/stocks/out/' . $product->id, ["quantity" => 100], ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/stocks/out/' . $product->id, ["quantity" => 100], ['Accept' => 'application/json']);

        $response->assertStatus(403);

        /********** ROLE_INVENTORY_MANAGER *********/

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/stocks/out/' . $product->id, ["quantity" => 100], ['Accept' => 'application/json']);

        $response->assertStatus(201);

        /********** ROLE_ADMIN *********/

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/stocks/out/' . $product->id, ["quantity" => 100], ['Accept' => 'application/json']);

        $response->assertStatus(201);
    }
}
