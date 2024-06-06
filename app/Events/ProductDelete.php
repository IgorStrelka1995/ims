<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDelete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct($product, $action)
    {
        $this->product = $product;
        $this->action = $action;
    }
}
