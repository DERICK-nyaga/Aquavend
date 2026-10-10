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
        'low_stock_threshold',
    ];

    /* =========================================================================
     | Helper & Capacity Methods
     | ========================================================================= */

    /**
     * Remaining liters the tank can hold.
     */
    public function availableLiters(): float
    {
        return max(
            0,
            ($this->tank_capacity_liters ?? 0) - ($this->current_level_liters ?? 0)
        );
    }

    /**
     * Can this station accept the given volume?
     */
    public function canFit(float $liters): bool
    {
        return $liters <= $this->availableLiters();
    }

    /**
     * Is the tank effectively full (within a small tolerance)?
     */
    public function isFull(float $tolerance = 0.01): bool
    {
        return $this->availableLiters() <= $tolerance;
    }

    /**
     * Human-readable capacity summary.
     */
    public function capacitySummary(): string
    {
        $capacity  = $this->tank_capacity_liters ?? 0;
        $current   = $this->current_level_liters ?? 0;
        $available = $this->availableLiters();

        return "Capacity: {$capacity}L, Current: {$current}L, Available: {$available}L";
    }

    /**
     * Check if current stock level is below the defined warning threshold.
     */
    public function isBelowThreshold(): bool
    {
        return $this->current_level_liters < $this->low_stock_threshold;
    }

    /* =========================================================================
     | Relationships
     | ========================================================================= */

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function stockRefills()
    {
        return $this->hasMany(StockRefill::class);
    }

    public function productStock()
    {
        return $this->hasMany(StationProductStock::class);
    }

    public function staff()
    {
        return $this->hasMany(User::class)->where('role', 'staff');
    }

    public function motorbikes()
    {
        return $this->hasMany(Motorbike::class);
    }

    public function stocks()
    {
        return $this->hasMany(StationProductStock::class);
    }
}