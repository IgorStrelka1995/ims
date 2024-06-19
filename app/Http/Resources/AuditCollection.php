<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AuditCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function($audit) {
            return [
                "id" => $audit->id,
                "product_id" => $audit->product_id,
                "action" => $audit->action,
                "created_at" => $audit->created_at,
            ];
        })->all();
    }
}
