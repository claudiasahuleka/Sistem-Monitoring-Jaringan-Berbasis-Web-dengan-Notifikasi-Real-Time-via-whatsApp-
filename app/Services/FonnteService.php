<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    private string $token;
    private string $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    public function send(string $target, string $message): bool
    {
        if (empty($this->token)) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                        'target' => $target,
                        'message' => $message,
                    ]);

            $data = $response->json();
            return isset($data['status']) && $data['status'] === true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function buildAlertMessage(string $deviceName, string $ip, string $status, ?int $latency): string
    {
        $statusLabel = $status === 'offline' ? '🔴 OFFLINE' : '🟡 WARNING';
        $latencyText = $latency !== null ? "Latency: {$latency}ms" : 'Tidak merespons';
        $time = now()->setTimezone('Asia/Jayapura')->format('d/m/Y H:i:s');

        return implode("\n", [
            '⚠️ *ALERT JARINGAN - DISNAKERTRANS Prov. Maluku*',
            '',
            "Perangkat: *{$deviceName}*",
            "IP Address: {$ip}",
            "Status: {$statusLabel}",
            $latencyText,
            "Waktu: {$time} WIT",
            '',
            'Segera periksa perangkat tersebut.',
        ]);
    }
}