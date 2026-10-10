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
            'delivery_latitude' => $this->delivery_latitude,
            'delivery_longitude' => $this->delivery_longitude,
            'delivery_address_note' => $this->delivery_address_note,
            'delivery_maps_url' => $this->googleMapsUrl(),
            'created_at' => $this->created_at,
            'gross_amount' => $this->gross_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
        ];
    }
}