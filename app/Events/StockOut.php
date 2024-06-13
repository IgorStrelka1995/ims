<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockOut
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $stock;

    /**
     * Create a new event instance.
     */
    public function __construct($product, $stock)
    {
        $this->product = $product;
        $this->stock = $stock;
    }
}
