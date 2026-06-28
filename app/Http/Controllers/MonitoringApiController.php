<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Device;
use App\Models\MonitoringLog;
use App\Models\NotificationLog;
use App\Services\PingService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MonitoringApiController extends Controller
{
    public function __construct(
        private PingService $pingService,
        private WhatsAppService $waService
    ) {
    }

    /**
     * ============================================
     * HELPER: Hitung kualitas berdasarkan response time
     * ============================================
     * 
     * Logika:
     * - 0-199 ms  = good (Baik)
     * - 200-399 ms = slow (Melemah)  
     * - 400+ ms   = very_slow (Sangat Lemah)
     */
    private function calculateQuality(?float $responseTime): string
    {
        if ($responseTime === null || $responseTime === 0) {
            return 'good';
        }
        if ($responseTime >= 400) {
            return 'very_slow';
        }
        if ($responseTime >= 200) {
            return 'slow';
        }
        return 'good';
    }

    /**
     * ============================================
     * HELPER: Hitung packet loss berdasarkan response time
     * ============================================
     * 
     * Logika:
     * - 0-199 ms   = 0% (tidak ada packet loss)
     * - 200-299 ms = 1-5% (sedikit packet loss)
     * - 300-399 ms = 5-10% (packet loss meningkat)
     * - 400+ ms    = 10-25% (packet loss tinggi)
     * - Offline    = 100% (total packet loss)
     */
    private function calculatePacketLoss(?float $responseTime): int
    {
        if ($responseTime === null || $responseTime === 0) {
            return 0;
        }

        // 400ms+ = 10-25% packet loss
        if ($responseTime >= 400) {
            return min(25, 10 + round(($responseTime - 400) / 10));
        }

        // 300-399ms = 5-10% packet loss
        if ($responseTime >= 300) {
            return min(10, 5 + round(($responseTime - 300) / 20));
        }

        // 200-299ms = 1-5% packet loss
        if ($responseTime >= 200) {
            return min(5, 1 + round(($responseTime - 200) / 25));
        }

        // 0-199ms = 0% packet loss
        return 0;
    }

    /**
     * ===============================================
     * HELPER: Hitung kualitas & packet loss sekaligus
     * ===============================================
     */
    private function calculateNetworkMetrics(?float $responseTime, string $status): array
    {
        if ($status === 'down' || $status === 'Offline') {
            return [
                'quality' => 'down',
                'packet_loss' => 100,
            ];
        }

        return [
            'quality' => $this->calculateQuality($responseTime),
            'packet_loss' => $this->calculatePacketLoss($responseTime),
        ];
    }

    /**
     * =============================================
     * Endpoint: Pengecekan otomatis setiap 30 detik
     * =============================================
     */
    public function runCheck(): JsonResponse
    {
        $qualityService = app(\App\Services\NetworkQualityService::class);
        $devices = Device::active()->get();
        $results = [];

        foreach ($devices as $device) {
            try {
                $previousStatus = $device->status;
                $previousResponseTime = $device->response_time;
                $previousQuality = $previousStatus === 'down'
                    ? 'down'
                    : $this->calculateQuality($previousResponseTime);

                // Cek kualitas dengan multi-ping (NetworkQualityService)
                $result = $qualityService->checkQuality($device->ip_address);

                $responseTime = $result['response_time'] ?? 0;
                $status = $result['status'] ?? 'unknown';

                // Fallback: jika NetworkQualityService tidak return quality/packet_loss
                $metrics = $this->calculateNetworkMetrics($responseTime, $status);

                $quality = $result['quality'] ?? $metrics['quality'];
                $packetLoss = $result['packet_loss'] ?? $metrics['packet_loss'];

                // Simpan log dengan data kualitas
                MonitoringLog::create([
                    'device_id' => $device->id,
                    'status' => $status,
                    'quality' => $quality,
                    'packet_loss' => $packetLoss,
                    'response_time' => $responseTime,
                    'ip_address' => $device->ip_address,
                    'checked_at' => now(),
                ]);

                // Update device
                $device->update([
                    'status' => $status,
                    'response_time' => $responseTime,
                    'last_checked_at' => now(),
                ]);

                $notifSent = null;
                $qualityChanged = $status === 'up' && $quality !== $previousQuality;
                $responseChanged = $status === 'up' && $quality === $previousQuality && $previousResponseTime !== null && abs($responseTime - $previousResponseTime) >= 20;

                // ── Notifikasi berdasarkan perubahan status & kualitas ──

                // 1. Perangkat DOWN
                if ($status === 'down' && $previousStatus !== 'down') {
                    $this->sendAndLog(
                        $device,
                        'down',
                        'down'
                    );
                    $notifSent = 'down';
                }

                // 2. Perangkat kembali UP (Recovery)
                if ($status === 'up' && $previousStatus === 'down') {
                    $this->sendAndLog(
                        $device,
                        'recovery',
                        'recovery'
                    );
                    $notifSent = 'recovery';
                }

                // 3. Kualitas atau response time berubah saat UP
                if ($status === 'up' && ($qualityChanged || $responseChanged)) {
                    $notificationType = $quality === 'good' ? 'stable' : $quality;
                    $shouldSendAgain = $this->shouldSendRepeatNotification($device, $notificationType);

                    if ($shouldSendAgain) {
                        if ($quality === 'good') {
                            $this->sendAndLog(
                                $device,
                                'stable',
                                'stable',
                                [
                                    'response_time' => $responseTime,
                                ]
                            );
                            $notifSent = 'stable';
                        } else {
                            $this->sendAndLog(
                                $device,
                                $quality,
                                'slow',
                                [
                                    'response_time' => $responseTime,
                                    'packet_loss' => $packetLoss,
                                ]
                            );
                            $notifSent = $quality;
                        }
                    }
                }

                // ── Deteksi Kepadatan Jaringan (ICMP Trend Analysis) ──
                $congestionInfo = ['detected' => false, 'level' => 'normal'];

                if ($status === 'up' && $responseTime !== null) {
                    $congestionService = app(\App\Services\CongestionDetectionService::class);
                    $congestionData = $congestionService->isCongested($device, $responseTime);

                    if ($congestionData['congested']) {
                        // Jaringan padat — kirim alert (max 1x per jam)
                        $recentCongestionAlert = NotificationLog::where('device_id', $device->id)
                            ->where('type', 'bandwidth_high')
                            ->where('created_at', '>=', now()->subHour())
                            ->exists();

                        if (!$recentCongestionAlert) {
                            $this->sendAndLog(
                                $device,
                                'bandwidth_high',
                                'bandwidth_high',
                                [
                                    'avg_response' => $congestionData['avg_response'] ?? 0,
                                    'threshold' => $congestionData['threshold'] ?? 0,
                                    'level' => $congestionData['level'] ?? 'high',
                                ]
                            );
                            $notifSent = 'bandwidth_high';
                        }
                        $congestionInfo = [
                            'detected' => true,
                            'level' => $congestionData['level'],
                            'avg_response' => $congestionData['avg_response'],
                        ];
                    } else {
                        // Cek apakah baru pulih dari congestion
                        $hadCongestionRecently = NotificationLog::where('device_id', $device->id)
                            ->where('type', 'bandwidth_high')
                            ->where('created_at', '>=', now()->subHours(2))
                            ->exists();

                        if ($hadCongestionRecently) {
                            $alreadySentResolved = NotificationLog::where('device_id', $device->id)
                                ->where('type', 'stable')
                                ->where('created_at', '>=', now()->subHour())
                                ->exists();

                            if (!$alreadySentResolved && $congestionService->isRecoveredFromCongestion($device)) {
                                $this->sendAndLog(
                                    $device,
                                    'stable',
                                    'bandwidth_resolved',
                                    [
                                        'avg_response' => $congestionData['avg_response'] ?? $responseTime,
                                    ]
                                );
                            }
                        }
                    }
                }

                $results[] = [
                    'id' => $device->id,
                    'name' => $device->name,
                    'ip' => $device->ip_address,
                    'status' => $status,
                    'quality' => $quality,
                    'response_time' => $responseTime,
                    'packet_loss' => $packetLoss,
                    'notif_sent' => $notifSent,
                    'congestion' => $congestionInfo,
                ];
            } catch (\Throwable $e) {
                Log::error('Device monitoring failed', [
                    'device_id' => $device->id,
                    'device_name' => $device->name,
                    'error' => $e->getMessage(),
                ]);

                $results[] = [
                    'id' => $device->id,
                    'name' => $device->name,
                    'ip' => $device->ip_address,
                    'status' => 'unknown',
                    'quality' => 'unknown',
                    'response_time' => null,
                    'packet_loss' => null,
                    'notif_sent' => null,
                    'congestion' => ['detected' => false, 'level' => 'unknown'],
                ];
            }

        }

        return response()->json([
            'checked_at' => now()->format('H:i:s'),
            'total' => count($results),
            'devices' => $results,
        ]);
    }

    /**
     * ============================================
     * Ping satu perangkat secara manual
     * ============================================
     */
    public function pingOne(Device $device): JsonResponse
    {
        $result = $this->pingService->ping($device->ip_address);

        $responseTime = $result['response_time'] ?? 0;
        $status = $result['status'] ?? 'unknown';

        // Hitung quality dan packet loss berdasarkan response time
        $metrics = $this->calculateNetworkMetrics($responseTime, $status);
        $quality = $metrics['quality'];
        $packetLoss = $metrics['packet_loss'];

        MonitoringLog::create([
            'device_id' => $device->id,
            'status' => $status,
            'quality' => $quality,
            'packet_loss' => $packetLoss,
            'response_time' => $responseTime,
            'ip_address' => $device->ip_address,
            'checked_at' => now(),
            'notes' => 'Manual check',
        ]);

        $device->update([
            'status' => $status,
            'response_time' => $responseTime,
            'last_checked_at' => now(),
        ]);

        return response()->json([
            'status' => $status,
            'response_time' => $responseTime,
            'quality' => $quality,
            'packet_loss' => $packetLoss,
            'message' => $status === 'up' ? '✓ Perangkat Online' : '✗ Perangkat Offline',
            'badge' => $device->status_badge,
        ]);
    }

    /**
     * ============================================
     * Helper: simpan notif log dan queue pengiriman WA
     * ============================================
     * Notifikasi diproses melalui queue untuk:
     * 1. Tidak memblokir response HTTP
     * 2. Auto-retry jika gagal
     * 3. Rate limiting untuk mencegah spam API
     */
    private function sendAndLog(
        Device $device,
        string $type,
        string $messageType,
        array $messageData = []
    ): void {
        // Simpan log dengan status pending
        $log = NotificationLog::create([
            'device_id' => $device->id,
            'wa_number' => config('app.admin_wa_number'),
            'message' => "[$type] {$device->name}",
            'type' => $type,
            'is_sent' => false,
            'response' => json_encode(['status' => 'queued']),
            'sent_at' => null,
        ]);

        // Kirim job WA ke queue agar monitoring tidak tertahan.
        // Bila queue worker belum berjalan, job akan ditaruh di database.
        try {
            SendWhatsAppNotification::dispatch(
                $log->id,
                $device->id,
                $type,
                $messageType,
                $messageData
            );
        } catch (\Throwable $e) {
            Log::error('Failed to enqueue WhatsApp notification job', [
                'device_id' => $device->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }
    /**
     * ============================================
     * Helper: menentukan apakah notifikasi dengan type yang sama
     * harus dikirim ulang atau cukup satu saja untuk kualitas tetap sama
     * ============================================
     */
    private function shouldSendRepeatNotification(Device $device, string $type): bool
    {
        $recentSameType = NotificationLog::where('device_id', $device->id)
            ->where('type', $type)
            ->where('created_at', '>=', now()->subHour())
            ->exists();

        return !$recentSameType;
    }

    /**
     * ============================================
     * Ambil status semua perangkat (tanpa ping ulang)
     * ============================================
     */
    public function getStatus(): JsonResponse
    {
        $devices = Device::active()->get()->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'ip' => $d->ip_address,
            'location' => $d->location,
            'status' => $d->status,
            'response_time' => $d->response_time,
            'last_checked' => $d->last_checked_at?->format('H:i:s'),
            'badge' => $d->status_badge,
            'label' => $d->status_label,
        ]);

        $stats = [
            'total' => Device::active()->count(),
            'up' => Device::active()->where('status', 'up')->count(),
            'down' => Device::active()->where('status', 'down')->count(),
            'unknown' => Device::active()->where('status', 'unknown')->count(),
        ];

        return response()->json([
            'checked_at' => now()->format('H:i:s'),
            'stats' => $stats,
            'devices' => $devices,
        ]);
    }

    /**
     * ============================================
     * Fetch notifikasi WA terbaru untuk real-time update
     * ============================================
     */
    public function getRecentNotifications(): JsonResponse
    {
        $notifications = NotificationLog::with('device')
            ->latest('created_at')
            ->limit(15)
            ->get()
            ->map(function ($notif) {
                $colors = [
                    'down' => 'danger',
                    'recovery' => 'success',
                    'slow' => 'warning',
                    'very_slow' => 'danger',
                    'stable' => 'info',
                    'bandwidth_high' => 'danger',
                ];
                $labels = [
                    'down' => 'DOWN',
                    'recovery' => 'PULIH',
                    'slow' => 'LEMAH',
                    'very_slow' => 'SANGAT LEMAH',
                    'stable' => 'STABIL',
                    'bandwidth_high' => 'BW ⚠',
                ];

                return [
                    'id' => $notif->id,
                    'device_name' => $notif->device->name,
                    'type' => $notif->type,
                    'type_label' => $labels[$notif->type] ?? $notif->type,
                    'badge_color' => $colors[$notif->type] ?? 'secondary',
                    'is_sent' => $notif->is_sent,
                    'status_text' => $notif->is_sent
                        ? '✓ Terkirim'
                        : ($notif->sent_at ? '✗ Gagal' : '⏳ Pending'),
                    'status_class' => $notif->is_sent
                        ? 'text-success'
                        : ($notif->sent_at ? 'text-danger' : 'text-warning'),
                    'sent_at' => $notif->sent_at?->format('d/m/Y H:i') ?? 'Menunggu',
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'updated_at' => now()->format('H:i:s'),
        ]);
    }
}