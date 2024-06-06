<?php

namespace App\Listeners;

use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveStockAuditLogs
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $product = $event->product;
        $actions = $event->actions;

        $audit = [
            'user_id' => 1,
            'product_id' => $product->id
        ];

        foreach ($actions as $action) {
            $audit['action'] = $action;

            Audit::create($audit);
        }
    }
}
