<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleUpdateProductQuantity
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $product = $event->product;
        $stock = $event->stock;

        $product->update([
            'stock' => $stock->quantity
        ]);
    }
}
