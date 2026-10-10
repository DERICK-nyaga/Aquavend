<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'customer_id',
        'delivery_staff_id',
        'motorbike_registration',
        'total_amount',
        'payment_method',
        'status',
        'dispatched_at',
        'delivered_at',
        'delivery_time_minutes',
        'delivery_score',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_address_note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
    public function hasDeliveryLocation(): bool
    {
        return $this->delivery_latitude !== null && $this->delivery_longitude !== null;
    }

    public function googleMapsUrl(): ?string
    {
        if (!$this->hasDeliveryLocation()) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->delivery_latitude},{$this->delivery_longitude}";
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_staff_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'transaction_items')
            ->withPivot('quantity', 'unit_price', 'subtotal', 'liters');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByStation($query, $stationId)
    {
        return $query->where('station_id', $stationId);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByPaymentMethod($query, $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
}