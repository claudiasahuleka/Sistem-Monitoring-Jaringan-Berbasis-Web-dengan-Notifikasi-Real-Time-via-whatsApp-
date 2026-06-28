<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MonitoringLog;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::latest()->paginate(15);
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        $types = ['router', 'switch', 'server', 'access_point', 'firewall', 'lainnya'];
        return view('devices.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:devices,ip_address',
            'location' => 'nullable|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'congestion_threshold_ms' => 'nullable|integer|min:30|max:2000',
            'congestion_check_count' => 'nullable|integer|min:3|max:20',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['congestion_threshold_ms'] = $request->input('congestion_threshold_ms', 150);
        $validated['congestion_check_count'] = $request->input('congestion_check_count', 5);
        Device::create($validated);

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil ditambahkan!');
    }

    public function show(Device $device)
    {
        $logs = $device->monitoringLogs()->latest('checked_at')->paginate(20);
        $uptimePercentage = $this->calcUptime($device);
        return view('devices.show', compact('device', 'logs', 'uptimePercentage'));
    }

    public function edit(Device $device)
    {
        $types = ['router', 'switch', 'server', 'access_point', 'firewall', 'lainnya'];
        return view('devices.edit', compact('device', 'types'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:devices,ip_address,' . $device->id,
            'location' => 'nullable|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'congestion_threshold_ms' => 'nullable|integer|min:30|max:2000',
            'congestion_check_count' => 'nullable|integer|min:3|max:20',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['congestion_threshold_ms'] = $request->input('congestion_threshold_ms', 150);
        $validated['congestion_check_count'] = $request->input('congestion_check_count', 5);
        $device->update($validated);

        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil diperbarui!');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Perangkat berhasil dihapus!');
    }

    private function calcUptime(Device $device): float
    {
        $total = $device->monitoringLogs()->where('checked_at', '>=', now()->subDays(7))->count();
        if ($total === 0)
            return 0;
        $up = $device->monitoringLogs()->where('checked_at', '>=', now()->subDays(7))->where('status', 'up')->count();
        return round(($up / $total) * 100, 2);
    }
}