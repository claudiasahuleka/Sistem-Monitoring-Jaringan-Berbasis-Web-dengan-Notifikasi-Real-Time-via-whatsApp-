<?php

namespace App\Services;

class NetworkQualityService
{
    // Threshold response time (ms)
    // Thresholds adjusted to match controller logic:
    // 0-199 ms  = good (Baik)
    // 200-399 ms = slow (Melemah)
    // 400+ ms   = very_slow (Sangat Lemah)
    const GOOD_MAX = 199;   // <= 199ms  = Baik
    const SLOW_MAX = 399;   // <= 399ms = Melemah
    // > 399ms = Sangat Lemah, timeout = Down

    // Jumlah ping untuk ukur packet loss
    const PING_COUNT = 4;

    /**
     * Cek kualitas koneksi dengan multi-ping
     */
    public function checkQuality(string $ipAddress): array
    {
        $times = [];
        $success = 0;

        for ($i = 0; $i < self::PING_COUNT; $i++) {
            $result = $this->singlePing($ipAddress);
            if ($result['success']) {
                $times[] = $result['time'];
                $success++;
            }
        }

        $packetLoss = (int) round(((self::PING_COUNT - $success) / self::PING_COUNT) * 100);
        $avgTime = $success > 0 ? (int) round(array_sum($times) / $success) : null;

        // Tentukan kualitas
        if ($packetLoss === 100) {
            $quality = 'down';
        } elseif ($packetLoss >= 50 || ($avgTime !== null && $avgTime > self::SLOW_MAX)) {
            $quality = 'very_slow';
        } elseif ($packetLoss > 0 || ($avgTime !== null && $avgTime > self::GOOD_MAX)) {
            $quality = 'slow';
        } else {
            $quality = 'good';
        }

        return [
            'status' => $packetLoss < 100 ? 'up' : 'down',
            'quality' => $quality,
            'response_time' => $avgTime,
            'packet_loss' => $packetLoss,
        ];
    }

    /**
     * Ping sekali dan ukur waktu respons
     */
    private function singlePing(string $ip): array
    {
        $start = microtime(true);
        $cmd = PHP_OS_FAMILY === 'Windows'
            ? "ping -n 1 -w 2000 {$ip}"
            : "ping -c 1 -W 2 {$ip}";

        exec($cmd . ' 2>&1', $output, $code);
        $elapsed = round((microtime(true) - $start) * 1000);

        if ($code !== 0) {
            return ['success' => false, 'time' => null];
        }

        $time = $elapsed;
        foreach ($output as $line) {
            if (preg_match('/time[<=](\d+\.?\d*)/i', $line, $m)) {
                $time = (int) round((float) $m[1]);
                break;
            }
        }

        return ['success' => true, 'time' => $time];
    }

    /**
     * Label kualitas untuk tampilan
     */
    public static function qualityLabel(string $quality): string
    {
        return match ($quality) {
            'good' => '✅ Baik',
            'slow' => '🟡 Melemah',
            'very_slow' => '🟠 Sangat Lemah',
            'down' => '🔴 Terputus',
            default => '⚪ Tidak Diketahui',
        };
    }

    /**
     * Warna badge Bootstrap berdasarkan kualitas
     */
    public static function qualityBadge(string $quality): string
    {
        return match ($quality) {
            'good' => 'success',
            'slow' => 'warning',
            'very_slow' => 'orange',
            'down' => 'danger',
            default => 'secondary',
        };
    }
}