<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $previousStock;

    /**
     * Create a new event instance.
     */
    public function __construct($product, $previousStock)
    {
        $this->product = $product;
        $this->previousStock = $previousStock;
    }
}
