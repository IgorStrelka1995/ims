<?php

namespace App\Listeners;

use App\Models\Stock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleCreatedProductToStock
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $product = $event->product;

        Stock::create([
            'product_id' => $product->id,
            'quantity' => $product->stock,
            'type' => Stock::STOCK_IN,
        ]);
    }
}
