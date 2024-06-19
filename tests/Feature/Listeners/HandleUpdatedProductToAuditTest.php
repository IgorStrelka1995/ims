<?php

namespace Tests\Feature\Listeners;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Models\Audit;
use App\Models\User;
use Database\Factories\ProductWithoutAuditFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HandleUpdatedProductToAuditTest extends TestCase
{
    use RefreshDatabase;

    public function testAddDataToAuditAfterProductDataUpdated(): void
    {
        $product = ProductWithoutAuditFactory::new();

        $productData = $product->count(1)->create();

        $previousStock = $productData->first()->stock;

        $this->assertDatabaseCount('audit_logs', 0);

        event(new ProductUpdated($productData->first(), $previousStock));

        $this->assertDatabaseCount('audit_logs', 1);

        // Stock is not changed

        $this->assertDatabaseHas('audit_logs', [
            'product_id' => $productData->first()->id,
            'action' => Audit::PRODUCT_UPDATE_ACTION
        ]);
    }

    public function testAddDataToAuditAfterProductStockUpdated(): void
    {
        $product = ProductWithoutAuditFactory::new();

        $productData = $product->count(1)->create();

        $previousStock = $productData->first()->stock + 10;

        $this->assertDatabaseCount('audit_logs', 0);

        event(new ProductUpdated($productData->first(), $previousStock));

        $this->assertDatabaseCount('audit_logs', 1);

        // Stock was changed manually

        $this->assertDatabaseHas('audit_logs', [
            'product_id' => $productData->first()->id,
            'action' => Audit::STOCK_ADJUSTMENT_ACTION
        ]);
    }
}
