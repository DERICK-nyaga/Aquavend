<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'category', 'outstanding_balance', 'notes'];

    public function stockRefills()
    {
        return $this->hasMany(StockRefill::class);
    }
}