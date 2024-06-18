<?php

namespace App\Listeners;

use App\Models\Stock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleUpdatedProductToStock
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $product = $event->product;
        $previousStock = $event->previousStock;

        if ($previousStock !== $product->stock) {
            $type = $previousStock < $product->stock ? Stock::STOCK_OUT : Stock::STOCK_IN;

            Stock::create([
                'product_id' => $product->id,
                'quantity' => $product->stock,
                'type' => $type,
            ]);
        }
    }
}
