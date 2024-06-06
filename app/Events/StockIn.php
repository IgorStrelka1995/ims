<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $stock;
    public $actions;

    /**
     * Create a new event instance.
     */
    public function __construct($product, $stock, $actions)
    {
        $this->product = $product;
        $this->stock = $stock;
        $this->actions = $actions;
    }
}
