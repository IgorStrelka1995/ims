<?php

namespace Tests\Feature\Listeners;

use App\Events\StockIn;
use App\Models\Audit;
use App\Models\Stock;
use App\Models\User;
use Database\Factories\ProductWithoutAuditFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HandleStockInToAuditTest extends TestCase
{
    use RefreshDatabase;

    public function testAddDataToAuditAfterStockIn(): void
    {
        $product = ProductWithoutAuditFactory::new();

        $productData = $product->count(1)->create();

        $stockData = Stock::factory()->create();

        $this->assertDatabaseCount('audit_logs', 0);

        event(new StockIn($productData->first(), $stockData->first()));

        $this->assertDatabaseCount('audit_logs', 1);

        $this->assertDatabaseHas('audit_logs', [
            'product_id' => $productData->first()->id,
            'action' => Audit::STOCK_IN_ACTION
        ]);
    }
}
