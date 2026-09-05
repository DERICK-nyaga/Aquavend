<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockRefillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'station_id' => $this->station_id,
            'liters_added' => $this->liters_added,
            'supplier' => $this->supplier,
            'cost' => $this->cost,
            'created_at' => $this->created_at,
        ];
    }
}