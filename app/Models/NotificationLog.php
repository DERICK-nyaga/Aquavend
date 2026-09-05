<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notifications_log';

    protected $fillable = ['customer_id', 'channel', 'event', 'message', 'status'];

    public const CHANNELS = ['sms', 'whatsapp', 'email'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}