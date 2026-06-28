<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MonitoringLog;
use App\Models\NotificationLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDevices = Device::active()->count();
        $devicesUp = Device::active()->where('status', 'up')->count();
        $devicesDown = Device::active()->where('status', 'down')->count();
        $devicesUnknown = Device::active()->where('status', 'unknown')->count();
        $downtimeToday = MonitoringLog::whereDate('checked_at', today())->where('status', 'down')->count();

        $recentNotifications = NotificationLog::with('device')
            ->where('is_sent', true)        // ← HANYA YANG SUDAH TERKIRIM
            ->whereNotNull('sent_at')        // ← PASTIKAN ADA TANGGAL
            ->latest('sent_at')              // ← URUT BERDASARKAN TANGGAL KIRIM
            ->limit(10)
            ->get();
        $devices = Device::active()->orderByRaw("FIELD(status,'down','unknown','up')")->get();

        return view('dashboard', compact(
            'totalDevices',
            'devicesUp',
            'devicesDown',
            'devicesUnknown',
            'downtimeToday',
            'recentNotifications',
            'devices'
        ));
    }
}