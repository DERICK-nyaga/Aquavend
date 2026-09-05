<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'station' => new StationResource($this->whenLoaded('station')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'total_amount' => $this->total_amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'items' => TransactionItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
        ];
    }
}