<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationProductStock extends Model
{
    protected $table = 'station_product_stock';

    protected $fillable = ['station_id', 'product_id', 'quantity_available', 'low_stock_threshold'];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity_available <= $this->low_stock_threshold;
    }
}