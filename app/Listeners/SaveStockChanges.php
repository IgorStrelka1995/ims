<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Models\Stock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveStockChanges
{
    /**
     * Handle the event.
     */
    public function handle(ProductCreated $event): void
    {
        $product = $event->product;

        Stock::create([
            'product_id' => $product->id, 'quantity' => $product->stock, 'type' => Stock::STOCK_IN,
        ]);
    }
}
