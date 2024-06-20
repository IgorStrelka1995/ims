<?php

namespace Tests\Feature\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    private static $productData = [
        "sku" => "lorem-ipsum",
        "name" => "Lorem Ipsum",
        "description" => "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
        "price" => "10.20",
        "stock" => 110,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    /**
     * A basic feature test example.
     */
    public function testViewAnyProducts(): void
    {
        /********** Unauthenticated *********/

        $response = $this->get('/api/v1/products', ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/products');

        $response->assertStatus(200);

        /********** ROLE_INVENTORY_MANAGER *********/

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/products');

        $response->assertStatus(200);

        /********** ROLE_ADMIN *********/

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/products');

        $response->assertStatus(200);
    }

    public function testCreateProduct()
    {
        /********** Unauthenticated *********/

        $response = $this->postJson('/api/v1/products', self::$productData, ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', self::$productData);

        $response->assertStatus(403);

        /********** ROLE_INVENTORY_MANAGER *********/

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', self::$productData);

        $response->assertStatus(403);

        /********** ROLE_ADMIN *********/

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', self::$productData);

        $response->assertStatus(201);
    }

    public function testUpdateProduct()
    {
        $product = Product::factory()->create();

        /********** Unauthenticated *********/

        $response = $this->putJson("/api/v1/products/" . $product->first()->id, [
            "name" => "lorem-ipsum",
        ], ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/products/" . $product->first()->id, [
            "name" => "lorem-ipsum",
        ], ['Accept' => 'application/json']);

        $response->assertStatus(403);

        /********** ROLE_INVENTORY_MANAGER *********/

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/products/" . $product->first()->id, [
            "name" => "lorem-ipsum",
        ], ['Accept' => 'application/json']);

        $response->assertStatus(403);

        /********** ROLE_ADMIN *********/

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/products/" . $product->first()->id, [
            "name" => "lorem-ipsum",
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200);
    }

    public function testDestroyProduct()
    {
        $product = Product::factory()->create();

        /********** Unauthenticated *********/

        $response = $this->delete('/api/v1/products/' . $product->first()->id, [], ['Accept' => 'application/json']);

        $response->assertStatus(401);

        /********** ROLE_VIEWER *********/

        $product = Product::factory()->create();

        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Sanctum::actingAs($user);

        $response = $this->delete('/api/v1/products/' . $product->first()->id);

        $response->assertStatus(403);

        /********** ROLE_INVENTORY_MANAGER *********/

        $product = Product::factory()->create();

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->delete('/api/v1/products/' . $product->first()->id);

        $response->assertStatus(403);

        /********** ROLE_ADMIN *********/

        $product = Product::factory()->create();

        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->delete('/api/v1/products/' . $product->first()->id);

        $response->assertStatus(204);
    }
}
