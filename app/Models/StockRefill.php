<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRefill extends Model
{
    use HasFactory;

    protected $fillable = ['station_id', 
    'supplier_id', 
    'liters_added', 
    'supplier', 
    'cost'];

    public function supplierRecord()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}