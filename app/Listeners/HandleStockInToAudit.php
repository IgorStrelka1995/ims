<?php

namespace App\Listeners;

use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleStockInToAudit
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $product = $event->product;

        Audit::create([
            'product_id' => $product->id,
            'action' => Audit::STOCK_IN_ACTION
        ]);
    }
}
