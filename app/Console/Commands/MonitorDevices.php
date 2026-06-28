<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\MonitoringLog;
use App\Models\NotificationLog;
use App\Services\PingService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorDevices extends Command
{
    protected $signature   = 'monitor:devices';
    protected $description = 'Ping semua perangkat aktif dan simpan hasilnya';

    public function handle(PingService $pingService, WhatsAppService $waService): int
    {
        $this->info('Memulai monitoring perangkat...');

        $devices = Device::active()->get();

        if ($devices->isEmpty()) {
            $this->warn('Tidak ada perangkat aktif untuk dimonitor.');
            return self::SUCCESS;
        }

        foreach ($devices as $device) {
            $this->processDevice($device, $pingService, $waService);
        }

        $this->info("Selesai monitoring {$devices->count()} perangkat.");
        return self::SUCCESS;
    }

    private function processDevice(Device $device, PingService $pingService, WhatsAppService $waService): void
    {
        $previousStatus = $device->status;

        // Lakukan ping
        $result = $pingService->ping($device->ip_address);

        // Simpan log monitoring
        MonitoringLog::create([
            'device_id'     => $device->id,
            'status'        => $result['status'],
            'response_time' => $result['response_time'],
            'ip_address'    => $device->ip_address,
            'checked_at'    => now(),
        ]);

        // Update status perangkat
        $device->update([
            'status'          => $result['status'],
            'response_time'   => $result['response_time'],
            'last_checked_at' => now(),
        ]);

        $this->line("  [{$result['status']}] {$device->name} ({$device->ip_address}) - {$result['response_time']}ms");

        // Kirim notifikasi WhatsApp
        $this->handleNotification($device, $previousStatus, $result['status'], $waService);
    }

    private function handleNotification(Device $device, string $previousStatus, string $currentStatus, WhatsAppService $waService): void
    {
        // Kirim alert jika perangkat baru down
        if ($currentStatus === 'down' && $previousStatus !== 'down') {
            $this->warn("  ⚠ {$device->name} DOWN! Mengirim notifikasi WhatsApp...");

            $waResult = $waService->sendDownAlert(
                $device->name,
                $device->ip_address,
                $device->location ?? 'Tidak diketahui'
            );

            NotificationLog::create([
                'device_id' => $device->id,
                'wa_number' => config('app.admin_wa_number'),
                'message'   => "Perangkat {$device->name} DOWN",
                'type'      => 'down',
                'is_sent'   => $waResult['success'],
                'response'  => json_encode($waResult['response']),
                'sent_at'   => now(),
            ]);
        }

        // Kirim notifikasi recovery jika perangkat kembali online
        if ($currentStatus === 'up' && $previousStatus === 'down') {
            $this->info("  ✓ {$device->name} kembali ONLINE! Mengirim notifikasi recovery...");

            $waResult = $waService->sendRecoveryAlert(
                $device->name,
                $device->ip_address,
                $device->location ?? 'Tidak diketahui'
            );

            NotificationLog::create([
                'device_id' => $device->id,
                'wa_number' => config('app.admin_wa_number'),
                'message'   => "Perangkat {$device->name} kembali ONLINE",
                'type'      => 'recovery',
                'is_sent'   => $waResult['success'],
                'response'  => json_encode($waResult['response']),
                'sent_at'   => now(),
            ]);
        }
    }
}