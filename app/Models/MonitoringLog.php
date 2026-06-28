<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringLog extends Model
{
    protected $fillable = [
        'device_id',
        'status',
        'quality',
        'packet_loss',
        'response_time',
        'ip_address',
        'checked_at',
        'notes',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}