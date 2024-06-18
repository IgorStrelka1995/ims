<?php

namespace Tests\Feature\Listeners;

use App\Events\ProductUpdated;
use App\Models\Audit;
use App\Models\Stock;
use App\Models\User;
use Database\Factories\ProductWithoutAuditFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HandleUpdatedProductToStockTest extends TestCase
{
    use RefreshDatabase;

    public function testAddDataToStockOutAfterProductDataUpdated(): void
    {
        $user = User::factory(1)->create();

        $product = ProductWithoutAuditFactory::new();
        $productData = $product->count(1)->create();

        $previousStock = $productData->first()->stock - 10;

        $this->assertDatabaseCount('stocks', 0);

        event(new ProductUpdated($productData->first(), $previousStock, $user->first()->id));

        $this->assertDatabaseCount('stocks', 1);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $productData->first()->id,
            'quantity' => $productData->first()->stock,
            'type' => Stock::STOCK_OUT,
        ]);
    }

    public function testAddDataToStockInAfterProductDataUpdated(): void
    {
        $user = User::factory(1)->create();

        $product = ProductWithoutAuditFactory::new();
        $productData = $product->count(1)->create();

        $previousStock = $productData->first()->stock + 10;

        $this->assertDatabaseCount('stocks', 0);

        event(new ProductUpdated($productData->first(), $previousStock, $user->first()->id));

        $this->assertDatabaseCount('stocks', 1);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $productData->first()->id,
            'quantity' => $productData->first()->stock,
            'type' => Stock::STOCK_IN,
        ]);
    }
}
