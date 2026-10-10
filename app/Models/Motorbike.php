<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Motorbike extends Model
{
    protected $fillable = [
        'station_id',
        'registration_number',
        'model',
        'status',
    ];

    public function assignedStaff(): HasOne
    {
        return $this->hasOne(User::class, 'assigned_motorbike_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}