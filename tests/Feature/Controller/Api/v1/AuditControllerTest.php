<?php

namespace Tests\Feature\Controller\Api\v1;

use App\Models\Audit;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Database\Factories\AuditProductStockFactory;
use Database\Factories\AuditProductUpdateFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuditControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupRolesToPermissions();
    }

    /**
     * A basic feature test example.
     */
    public function testReceiveAudits(): void
    {
        $user = User::factory()->withRole(User::ROLE_VIEWER)->create();

        Product::factory(Product::ITEMS_PER_PAGE)->create();

        Stock::factory(Stock::ITEMS_PER_PAGE)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/audits');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['data', 'links', 'meta'])
                ->has('data', 50)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll([
                        'id', 'product_id', 'action', 'created_at'
                    ]);
                })
            ;
        });
    }
}
