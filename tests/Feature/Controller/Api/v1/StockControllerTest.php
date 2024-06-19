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

class StockControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    public function testReceiveStocks()
    {
        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Product::factory(Product::ITEMS_PER_PAGE)->create();

        Stock::factory(Stock::ITEMS_PER_PAGE)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/stocks');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['data', 'links', 'meta'])
                ->has('data', Stock::ITEMS_PER_PAGE)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll([
                        'id', 'product_id', 'quantity', 'type', 'created_at'
                    ]);
                })
            ;
        });
    }

    public function testReceiveStocksWithPagination()
    {
        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Product::factory(Product::ITEMS_PER_PAGE)->create();

        Stock::factory( Stock::ITEMS_PER_PAGE + 30)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/stocks?page=2');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['data', 'links', 'meta'])
                ->has('data', 30)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll([
                        'id', 'product_id', 'quantity', 'type', 'created_at'
                    ]);
                })
            ;
        });
    }

    public function testReceiveStock()
    {
        Product::factory()->create();

        $stock = Stock::factory()->create();

        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/stocks/' . $stock->first()->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll([
                    'id', 'product', 'quantity', 'type', 'created_at'
                ])->has('product', function (AssertableJson $json) {
                    $json->hasAll(['id', 'sku', 'name', 'description', 'price', 'stock', 'created_at']);
                });
            });
        });
    }

    public function testStockIn()
    {
        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/stocks/in/' . $product->id, [
            "quantity" => 100,
            "user_id" => $user->id
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll(['id', 'product', 'quantity', 'type', 'created_at'])
                    ->where('quantity', 100)
                    ->where('type', Stock::STOCK_IN)
                ;
            });
        });
    }

    public function testStockOut()
    {
        $user = User::factory()->withRole(User::ROLE_INVENTORY_MANAGER)->create();

        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/stocks/out/' . $product->first()->id, [
            "quantity" => 99,
            "user_id" => $user->id
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll(['id', 'product', 'quantity', 'type', 'created_at'])
                    ->where('quantity', 99)
                    ->where('type', Stock::STOCK_OUT)
                ;
            });
        });
    }
}
