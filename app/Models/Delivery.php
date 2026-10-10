<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'driver_id',
        'status',
        'delivery_address',
        'delivery_notes',
        'scheduled_at',
        'delivered_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}