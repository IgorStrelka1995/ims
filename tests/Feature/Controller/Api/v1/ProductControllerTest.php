<?php

namespace Tests\Feature\Controller\Api\v1;

use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testReceiveProducts()
    {
        User::factory(3)->create();
        Product::factory(Product::ITEMS_PER_PAGE)->create();

        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/api/v1/products');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json)  {
            $json->hasAll(['data', 'links', 'meta'])
                ->has('data', Product::ITEMS_PER_PAGE)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll([
                        'id', 'sku', 'name', 'price', 'stock',
                        'description', 'created_at'
                    ]);
                })
            ;
        });
    }

    public function testReceiveProductsWithPagination()
    {
        User::factory(3)->create();
        Product::factory(Product::ITEMS_PER_PAGE + 10)->create();

        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/api/v1/products?page=2');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['data', 'links', 'meta'])
                ->has('data', 10)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll([
                        'id', 'sku', 'name', 'price', 'stock',
                        'description', 'created_at'
                    ]);
                })
            ;
        });
    }

    public function testReceiveProductsIncludeTransactions()
    {
        User::factory(3)->create();
        Product::factory(Product::ITEMS_PER_PAGE)->create();
        Stock::factory(100)->create();

        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/api/v1/products?include=stocks');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['data', 'links', 'meta'])
                ->has('data', Product::ITEMS_PER_PAGE)
                ->has('data.0', function (AssertableJson $json) {
                    $json->hasAll([
                        'id', 'sku', 'name', 'price', 'stock',
                        'description', 'transactions', 'created_at'
                    ])->has('transactions.0', function (AssertableJson $json) {
                        $json->hasAll(['id', 'product', 'quantity', 'type', 'created_at']);
                    });
                })
            ;
        });
    }

    public function testReceiveProduct()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/api/v1/products/' . $product->first()->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll([
                    'id', 'sku', 'name', 'price',
                    'stock', 'description', 'created_at'
                ]);
            });
        });
    }

    public function testStoreProduct()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/v1/products', [
            "sku" => "lorem-ipsum",
            "name" => "Lorem Ipsum",
            "description" => "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
            "price" => "10.20",
            "stock" => 110,
            "user_id" => $user->first()->id
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll([
                    'id', 'sku', 'name', 'price',
                    'stock', 'description', 'created_at'
                ])->has('id')
                    ->where('sku', 'lorem-ipsum')
                    ->where('name', 'Lorem Ipsum')
                    ->where('description', 'Voluptatum sequi odio sint dolorem consectetur nihil quasi.')
                    ->where('price', '10.20')
                    ->where('stock', 110)
                    ->etc();
            });
        });
    }

    public function testUpdateProduct()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $product = Product::factory(1)->create();

        $response = $this->putJson("/api/v1/products/" . $product->first()->id, [
            "name" => "lorem-ipsum",
            "user_id" => $user->first()->id
        ]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll([
                    'id', 'sku', 'name', 'price',
                    'stock', 'description', 'created_at'
                ])->where('name', 'lorem-ipsum')->etc();
            });
        });
    }

    public function testDestroyProduct()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $product = Product::factory(1)->create();

        $response = $this->get('/api/v1/products/' . $product->first()->id);

        $response->assertStatus(200);

        $response = $this->delete('/api/v1/products/' . $product->first()->id);

        $response->assertStatus(204);

        $response = $this->get('/api/v1/products/' . $product->first()->id);

        $response->assertStatus(404);
    }
}
