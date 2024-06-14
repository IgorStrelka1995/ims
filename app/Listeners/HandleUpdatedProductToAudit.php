<?php

namespace App\Listeners;

use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleUpdatedProductToAudit
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $product = $event->product;
        $previousStock = $event->previousStock;
        $user_id = $event->user_id;

        $action = $product->stock !== $previousStock ? Audit::STOCK_ADJUSTMENT_ACTION : Audit::PRODUCT_UPDATE_ACTION;

        Audit::create([
            'user_id' => $user_id,
            'product_id' => $product->id,
            'action' => $action
        ]);
    }
}
