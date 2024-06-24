<?php

namespace Tests\Feature\Request;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreProductRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    public function testRequiredFieldsWhileProductStore()
    {
        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            "sku" => "",
            "name" => "",
            "description" => "",
            "price" => "",
            "stock" => "",
            "user_id" => ""
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->where('message', "The sku field is required. (and 4 more errors)")
                ->has('errors', function (AssertableJson $json) {
                    $json
                        ->has('sku')
                        ->where('sku.0', 'The sku field is required.')
                        ->has('name')
                        ->where('name.0', 'The name field is required.')
                        ->has('description')
                        ->where('description.0', 'The description field is required.')
                        ->has('price')
                        ->where('price.0', 'The price field is required.')
                        ->has('stock')
                        ->where('stock.0', 'The stock field is required.')
                        ;
                });
        });
    }

    public function testProductSkuIsUnique()
    {
        $user = User::factory()->withRole(User::ROLE_ADMIN)->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/products', [
            "sku" => "lorem-ipsum",
            "name" => "Lorem Ipsum",
            "description" => "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
            "price" => "10.20",
            "stock" => 110,
            "user_id" => $user->first()->id
        ]);

        $response = $this->postJson('/api/v1/products', [
            "sku" => "lorem-ipsum",
            "name" => "Lorem Ipsum-new",
            "description" => "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
            "price" => "10.20",
            "stock" => 110,
            "user_id" => $user->first()->id
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->where('message', "The sku has already been taken.")
                ->has('errors', function (AssertableJson $json) {
                    $json
                        ->has('sku')
                        ->where('sku.0', 'The sku has already been taken.');
                });
        });
    }
}
