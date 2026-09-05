<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['station_id', 'category', 'description', 'amount', 'expense_date', 'recorded_by'];

    public const CATEGORIES = ['fuel', 'wages', 'maintenance', 'rent', 'utilities', 'supplies', 'other'];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}