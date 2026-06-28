<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'device_id', 'wa_number', 'message',
        'type', 'is_sent', 'response', 'sent_at',
    ];

    protected $casts = [
        'is_sent'  => 'boolean',
        'sent_at'  => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}