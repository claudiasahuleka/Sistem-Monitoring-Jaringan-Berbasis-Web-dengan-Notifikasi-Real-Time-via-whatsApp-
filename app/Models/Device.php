<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name',
        'ip_address',
        'location',
        'type',
        'status',
        'response_time',
        'last_checked_at',
        'is_active',
        'congestion_threshold_ms',
        'congestion_check_count',
        'description',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'is_active' => 'boolean',
        'congestion_threshold_ms' => 'integer',
        'congestion_check_count' => 'integer',
    ];

    public function monitoringLogs()
    {
        return $this->hasMany(MonitoringLog::class);
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'up' => 'success',
            'down' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'up' => 'Online',
            'down' => 'Offline',
            default => 'Tidak Diketahui',
        };
    }
}