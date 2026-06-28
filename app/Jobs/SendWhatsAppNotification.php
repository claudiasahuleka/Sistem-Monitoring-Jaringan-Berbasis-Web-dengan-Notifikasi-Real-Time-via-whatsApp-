<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\NotificationLog;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [10, 30, 60];
    public $maxExceptions = 3;

    public function __construct(
        public int $logId,        // ← TAMBAH INI (ID notifikasi di database)
        public int $deviceId,
        public string $type,
        public string $messageType,
        public array $messageData = []
    ) {
    }

    public function middleware(): array
    {
        return [
            new RateLimited('whatsapp'),
        ];
    }

    public function handle(WhatsAppService $waService): void
    {
        // Cari log berdasarkan ID (PASTI TEPAT, tidak mungkin salah)
        $log = NotificationLog::find($this->logId);

        if (!$log) {
            Log::warning("NotificationLog not found: {$this->logId}");
            return;
        }

        $device = Device::find($this->deviceId);

        if (!$device) {
            Log::warning("Device not found: {$this->deviceId}");
            // Update log sebagai gagal
            $log->update([
                'is_sent' => false,
                'response' => json_encode(['error' => 'Device not found']),
            ]);
            return;
        }

        try {
            // Kirim notifikasi sesuai tipe
            $result = match ($this->messageType) {
                'down' => $waService->sendDownAlert(
                    $device->name,
                    $device->ip_address,
                    $device->location ?? '-'
                ),
                'recovery' => $waService->sendRecoveryAlert(
                    $device->name,
                    $device->ip_address,
                    $device->location ?? '-'
                ),
                'slow' => $waService->sendSlowAlert(
                    $device->name,
                    $device->ip_address,
                    $device->location ?? '-',
                    $this->messageData['response_time'] ?? 0,
                    $this->messageData['packet_loss'] ?? 0
                ),
                'stable' => $waService->sendStableAlert(
                    $device->name,
                    $device->ip_address,
                    $device->location ?? '-',
                    $this->messageData['response_time'] ?? 0
                ),
                'bandwidth_high' => $waService->sendCongestionAlert(
                    $device->name,
                    $device->ip_address,
                    $device->location ?? '-',
                    $this->messageData['avg_response'] ?? 0,
                    $this->messageData['threshold'] ?? 0,
                    $this->messageData['level'] ?? 'normal'
                ),
                'bandwidth_resolved' => $waService->sendCongestionResolvedAlert(
                    $device->name,
                    $device->ip_address,
                    $device->location ?? '-',
                    $this->messageData['response_time'] ?? 0
                ),
                default => ['success' => false, 'response' => ['error' => 'Unknown message type']],
            };

            // Update NotificationLog berdasarkan ID (PASTI TEPAT)
            if ($result['success'] ?? false) {
                // ✅ BERHASIL
                $log->update([
                    'is_sent' => true,
                    'sent_at' => now(),
                    'response' => json_encode($result['response'] ?? []),
                ]);
            } else {
                // ❌ GAGAL dari API Fonnte
                $log->update([
                    'is_sent' => false,
                    'response' => json_encode($result['response'] ?? ['error' => 'Unknown error']),
                ]);
                throw new \Exception('WhatsApp send failed: ' . json_encode($result['response'] ?? []));
            }

            Log::info("WhatsApp notification sent successfully", [
                'log_id' => $this->logId,
                'device_id' => $device->id,
                'type' => $this->type,
            ]);

        } catch (\Exception $e) {
            Log::error("WhatsApp send error: {$e->getMessage()}", [
                'log_id' => $this->logId,
                'device_id' => $device->id,
                'type' => $this->type,
                'attempt' => $this->attempts(),
            ]);

            // Pastikan log di-update sebagai gagal kalau belum terupdate
            if ($log->is_sent === false && $log->sent_at === null) {
                $log->update([
                    'response' => json_encode(['exception' => $e->getMessage()]),
                ]);
            }

            if ($this->attempts() >= $this->tries) {
                Log::error("WhatsApp notification failed after {$this->tries} attempts", [
                    'log_id' => $this->logId,
                    'device_id' => $device->id,
                    'type' => $this->type,
                ]);
            } else {
                throw $e; // Retry
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendWhatsAppNotification job failed permanently", [
            'log_id' => $this->logId,
            'device_id' => $this->deviceId,
            'type' => $this->type,
            'error' => $exception->getMessage(),
        ]);
    }
}