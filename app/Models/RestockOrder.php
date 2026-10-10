<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'station_id',
        'received_by',
        'liters_added',
        'total_cost',
        'status',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'liters_added' => 'float',
        'total_cost'   => 'float',
        'received_at'  => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}