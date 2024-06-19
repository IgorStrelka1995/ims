<?php

namespace Tests\Feature\Listeners;

use App\Events\ProductCreated;
use App\Models\Audit;
use App\Models\Stock;
use App\Models\User;
use Database\Factories\ProductWithoutAuditFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HandleCreatedProductToStockTest extends TestCase
{
    use RefreshDatabase;

    public function testAddDataToStockAfterProductCreated(): void
    {
        $product = ProductWithoutAuditFactory::new();

        $productData = $product->count(1)->create();

        $this->assertDatabaseCount('stocks', 0);

        event(new ProductCreated($productData->first()));

        $this->assertDatabaseCount('stocks', 1);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $productData->first()->id,
            'quantity' => $productData->first()->stock,
            'type' => Stock::STOCK_IN,
        ]);
    }
}
