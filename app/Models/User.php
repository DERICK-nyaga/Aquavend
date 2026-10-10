<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'station_id',
        'phone',
        'assigned_motorbike_id',
        'employment_status',
        'approved_by',
        'approved_at',
        'status_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships & Helpers
     */

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function motorbike(): BelongsTo
    {
        return $this->belongsTo(Motorbike::class, 'assigned_motorbike_id');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function currentLeave():  HasOne
    {
        return $this->hasOne(Leave::class)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now());
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDirector(): bool
    {
        return $this->role === 'director';
    }
}