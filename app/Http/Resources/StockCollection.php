<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StockCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function($stock) {
            return [
                'id' => $stock->id,
                'product_id' => $stock->product_id,
                'quantity' => $stock->quantity,
                'type' => $stock->type,
                'created_at' => $stock->created_at,
            ];
        })->all();
    }
}
