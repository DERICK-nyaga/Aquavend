<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'status' => $this->status,
            'tank_capacity_liters' => $this->tank_capacity_liters,
            'current_level_liters' => $this->current_level_liters,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}