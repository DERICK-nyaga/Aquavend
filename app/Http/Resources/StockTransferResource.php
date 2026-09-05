<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from_station' => $this->whenLoaded('fromStation', fn () => $this->fromStation?->name),
            'to_station' => $this->whenLoaded('toStation', fn () => $this->toStation?->name),
            'liters_transferred' => $this->liters_transferred,
            'status' => $this->status,
            'notes' => $this->notes,
            'initiated_by' => $this->whenLoaded('initiatedBy', fn () => $this->initiatedBy?->name),
            'created_at' => $this->created_at,
        ];
    }
}