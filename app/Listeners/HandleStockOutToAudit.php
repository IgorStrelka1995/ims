<?php

namespace App\Listeners;

use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleStockOutToAudit
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $product = $event->product;
        $user_id = $event->user_id;

        Audit::create([
            'user_id' => $user_id,
            'product_id' => $product->id,
            'action' => Audit::STOCK_OUT_ACTION
        ]);
    }
}
