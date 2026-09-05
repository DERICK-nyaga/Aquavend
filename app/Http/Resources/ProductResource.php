<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'size_value' => $this->size_value,
            'unit' => $this->unit,
            'display_name' => $this->display_name,
            'price' => $this->price,
            'is_active' => $this->is_active,
        ];
    }
}