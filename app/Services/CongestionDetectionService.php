<?php

namespace App\Services;

use App\Models\MonitoringLog;
use App\Models\Device;

class CongestionDetectionService
{
    /**
     * Periksa apakah perangkat sedang mengalami congestion
     * berdasarkan tren response time dari N cek terakhir
     */
    public function isCongested(Device $device, int $currentResponseTime = null): array
    {
        $checkCount = $device->congestion_check_count ?? 5;
        $threshold  = $device->congestion_threshold_ms ?? 150;

        // Ambil N log terbaru dari database
        $recentLogs = MonitoringLog::where('device_id', $device->id)
            ->where('status', 'up')
            ->whereNotNull('response_time')
            ->orderByDesc('checked_at')
            ->take($checkCount)
            ->pluck('response_time')
            ->toArray();

        // Masukkan data saat ini jika tersedia
        if ($currentResponseTime !== null) {
            array_unshift($recentLogs, $currentResponseTime);
            $recentLogs = array_slice($recentLogs, 0, $checkCount);
        }

        // Belum cukup data untuk analisis
        if (count($recentLogs) < $checkCount) {
            return [
                'congested'    => false,
                'reason'       => 'Data belum cukup (' . count($recentLogs) . '/' . $checkCount . ' cek)',
                'avg_response' => null,
                'data_count'   => count($recentLogs),
            ];
        }

        $avgResponse  = round(array_sum($recentLogs) / count($recentLogs), 1);
        $maxResponse  = max($recentLogs);
        $minResponse  = min($recentLogs);

        // Congestion = rata-rata SEMUA cek terakhir di atas threshold
        $allAbove = array_filter($recentLogs, fn($rt) => $rt > $threshold);
        $isCongested = count($allAbove) === $checkCount;

        // Hitung level kepadatan
        $level = 'normal';
        if ($avgResponse > $threshold * 2) {
            $level = 'very_high'; // Sangat padat
        } elseif ($avgResponse > $threshold) {
            $level = 'high';      // Padat
        } elseif ($avgResponse > $threshold * 0.7) {
            $level = 'moderate'; // Mulai padat
        }

        // PERBAIKAN DI SINI: Menggunakan count($recentLogs) sebagai pengganti $count
        return [
            'congested'    => $isCongested,
            'level'        => $level,
            'avg_response' => $avgResponse,
            'max_response' => $maxResponse,
            'min_response' => $minResponse,
            'threshold'    => $threshold,
            'check_count'  => count($recentLogs),
            'reason'       => $isCongested
                ? count($recentLogs) . " cek berturut-turut di atas {$threshold}ms (avg: {$avgResponse}ms)"
                : "Avg response {$avgResponse}ms masih dalam batas normal",
        ];
    }

    /**
     * Cek apakah kondisi sudah kembali normal setelah sebelumnya congested
     */
    public function isRecoveredFromCongestion(Device $device): bool
    {
        $threshold  = $device->congestion_threshold_ms ?? 150;
        $checkCount = $device->congestion_check_count ?? 5;

        $recentLogs = MonitoringLog::where('device_id', $device->id)
            ->where('status', 'up')
            ->whereNotNull('response_time')
            ->orderByDesc('checked_at')
            ->take($checkCount)
            ->pluck('response_time')
            ->toArray();

        if (count($recentLogs) < 3) return false;

        // Normal = minimal 3 cek terakhir di bawah threshold
        $belowThreshold = array_filter(
            array_slice($recentLogs, 0, 3),
            fn($rt) => $rt <= $threshold
        );

        return count($belowThreshold) >= 3;
    }
}