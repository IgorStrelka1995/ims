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

        if ($previousStock !== $product->stock) {
            $type = $previousStock < $product->stock ? Stock::STOCK_IN : Stock::STOCK_OUT;

            $stock = [
                'product_id' => $product->id,
                'quantity' => $product->stock,
                'type' => $type,
            ];

            Stock::create($stock);
        }
    }
}
