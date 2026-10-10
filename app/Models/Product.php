<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'size_value', 'unit', 'price', 'is_active',
    ];

    public const CATEGORIES = ['water', 'oil', 'milk', 'yoghurt', 'eggs', 'bottles'];

    public const UNITS = ['ml', 'l', 'pcs', 'dozen', 'tray'];

    // Units that make sense per category — used to drive the form dropdown
    public const UNITS_BY_CATEGORY = [
        'water' => ['ml', 'l'],
        'oil' => ['ml', 'l'],
        'milk' => ['ml', 'l'],
        'yoghurt' => ['ml', 'l'],
        'bottles' => ['ml', 'l', 'pcs'],
        'eggs' => ['pcs', 'dozen', 'tray'],
    ];

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Convenience accessor: "Water — 500ml" or "Eggs — 1 tray"
    public function getDisplayNameAttribute(): string
    {
        $size = rtrim(rtrim((string) $this->size_value, '0'), '.');
        return "{$this->name} — {$size}{$this->unit}";
    }

    public function stationStock()
    {
        return $this->hasMany(StationProductStock::class);
    }

    public function toStockUnits(int $quantitysold)
    {
        if ($this->unit === 'l') {
            return $quantitysold * $this->size_value; // liters
        } elseif ($this->unit === 'ml') {
            return ($this->size_value/1000) * $quantitysold; // convert ml to liters
        }

        return $quantitysold; // pcs, dozen, tray — no conversion
    }

    public function isLiquid(): bool
    {
        return in_array($this->unit, ['ml', 'l']);
    }

    public function stockUnitLabel(): string
    {
        return $this->isLiquid() ? 'liters' : ucfirst($this->unit);
    }
}