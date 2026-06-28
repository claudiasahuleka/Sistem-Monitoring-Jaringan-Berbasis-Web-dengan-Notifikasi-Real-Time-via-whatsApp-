<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MonitoringLog;
use App\Models\NotificationLog;
use Illuminate\Http\Request;

class LogHistorisController extends Controller
{
    public function index(Request $request)
    {
        $deviceId  = $request->get('device_id');
        $status    = $request->get('status');
        $date      = $request->get('date', today()->format('Y-m-d'));

        $query = MonitoringLog::with('device')
            ->whereDate('checked_at', $date)
            ->latest('checked_at');

        if ($deviceId) $query->where('device_id', $deviceId);
        if ($status)   $query->where('status', $status);

        $logs    = $query->paginate(30)->withQueryString();
        $devices = Device::active()->get();

        $summary = [
            'total' => MonitoringLog::whereDate('checked_at', $date)->count(),
            'up'    => MonitoringLog::whereDate('checked_at', $date)->where('status', 'up')->count(),
            'down'  => MonitoringLog::whereDate('checked_at', $date)->where('status', 'down')->count(),
        ];

        $notifications = NotificationLog::with('device')
            ->whereDate('sent_at', $date)
            ->latest('sent_at')
            ->take(20)
            ->get();

        return view('logs.index', compact('logs', 'devices', 'summary', 'notifications', 'date', 'deviceId', 'status'));
    }
}