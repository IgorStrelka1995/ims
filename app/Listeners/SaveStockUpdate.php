<?php

namespace App\Listeners;

use App\Events\ProductUpdated;
use App\Models\Stock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveStockUpdate
{
    /**
     * Handle the event.
     */
    public function handle(ProductUpdated $event): void
    {
        $product = $event->product;
        $previousStock = $event->previousStock;

        $type = $previousStock < $product->stock ? Stock::STOCK_IN : Stock::STOCK_OUT;

        Stock::create([
            'product_id' => $product->id, 'quantity' => $product->stock, 'type' => $type,
        ]);
    }
}
