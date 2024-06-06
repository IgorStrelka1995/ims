<?php

namespace App\Listeners;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveProductQuantity
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $product = $event->product;
        $stock = $event->stock;

        $productData = [
            'stock' => $stock->quantity
        ];

        $product->update($productData);
    }
}
