<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'station' => $this->whenLoaded('station', fn () => $this->station?->name),
            'category' => $this->category,
            'description' => $this->description,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'recorded_by' => $this->whenLoaded('recordedBy', fn () => $this->recordedBy?->name),
        ];
    }
}