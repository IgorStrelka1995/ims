<?php

namespace Tests\Feature\Listeners;

use App\Events\StockIn;
use App\Events\StockOut;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Database\Factories\ProductWithoutAuditFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HandleUpdateProductQuantityTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateProductStockAfterStockIn(): void
    {
        $product = ProductWithoutAuditFactory::new();

        $productData = $product->count(1)->create(['stock' => 100]);

        $this->assertEquals(100, $productData->first()->stock);

        $stockVal = $productData->first()->stock + 10;

        $stockData = Stock::factory(1)->create(['quantity' => $stockVal]);

        event(new StockIn($productData->first(), $stockData->first()));

        $product = Product::find($productData->first()->id);

        $this->assertEquals(110, $product->first()->stock);
    }

    public function testUpdateProductStockAfterStockOut(): void
    {
        $product = ProductWithoutAuditFactory::new();

        $productData = $product->count(1)->create([
            'stock' => 100
        ]);

        $this->assertEquals(100, $productData->first()->stock);

        $stockVal = $productData->first()->stock - 10;

        $stockData = Stock::factory(1)->create(['quantity' => $stockVal]);

        event(new StockOut($productData->first(), $stockData->first()));

        $product = Product::find($productData->first()->id);

        $this->assertEquals(90, $product->first()->stock);
    }
}
