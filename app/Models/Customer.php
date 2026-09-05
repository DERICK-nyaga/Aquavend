<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    /** @use HasApiTokens */
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 
        'phone', 
        'email', 
        'address', 
        'wallet_balance', 
        'credit_limit', 
        'credit_balance', 
        'password', 
        'otp_code', 
        'otp_expires_at', 
        'phone_verified_at'
    ];

    protected $hidden = [
        'password', 
        'remember_token', 
        'otp_code', 
        'otp_expires_at', 
        'phone_verified_at'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'wallet_balance' => 'float',
            'credit_limit' => 'float',
            'credit_balance' => 'float',
            'otp_expires_at' => 'datetime',
            'phone_verified_at' => 'datetime',
        ];
    }

    public function availableCredit(): float
    {
        return $this->credit_limit - $this->credit_balance;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
