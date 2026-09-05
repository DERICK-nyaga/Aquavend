<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    /** @use HasFactory<\Database\Factories\StationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'status',
        'tank_capacity_liters',
        'current_level_liters',
    ];


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function stockRefills()
    {
        return $this->hasMany(StockRefill::class);
    }
}
