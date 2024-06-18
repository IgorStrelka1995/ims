<?php

namespace Tests\Feature\Request;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateProductRequestTest extends TestCase
{
    use RefreshDatabase;

    public function testRequiredFieldsWhileProductUpdate()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $product = Product::factory(1)->create();

        $response = $this->putJson("/api/v1/products/" . $product->first()->id, [
            "sku" => "",
            "name" => "",
            "description" => "",
            "price" => "",
            "stock" => "",
            "user_id" => ""
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->where('message', "The sku field is required. (and 5 more errors)")
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
                        ->has('user_id')
                        ->where('user_id.0', 'The user id field is required.');
                });
        });
    }

    public function testProductSkuIsUniqueWhileProductUpdate()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $product = Product::factory()->create();
        $product2 = Product::factory()->create();

        $sku = $product->first()->sku;

        $response = $this->putJson("/api/v1/products/" . $product->first()->id, [
            "sku" => $sku
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

    public function testUserIsPresentWhileProductUpdated()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $user_id = $user->first()->id + 10;

        $product = Product::factory()->create();

        $response = $this->putJson("/api/v1/products/" . $product->first()->id, [
            "sku" => "new-sku",
            "user_id" => $user_id
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->where('message', "The selected user id is invalid.")
                ->has('errors', function (AssertableJson $json) {
                    $json
                        ->has('user_id')
                        ->where('user_id.0', 'The selected user id is invalid.');
                });
        });
    }
}
