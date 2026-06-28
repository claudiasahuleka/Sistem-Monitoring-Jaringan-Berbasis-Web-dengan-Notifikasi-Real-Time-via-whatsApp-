<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $token;
    private string $apiUrl;
    private string $adminNumber;

    public function __construct()
    {
        $this->token = config('app.fonnte_token');
        $this->apiUrl = config('app.fonnte_api_url', 'https://api.fonnte.com/send');
        $this->adminNumber = config('app.admin_wa_number');
    }

    public function send(string $phone, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                        'target' => $phone,
                        'message' => $message,
                    ]);

            return [
                'success' => $response->successful(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return ['success' => false, 'response' => ['error' => $e->getMessage()]];
        }
    }

    public function sendDownAlert(string $deviceName, string $ip, string $location): array
    {
        $message = "🔴 *ALERT - Perangkat DOWN!*\n\n"
            . "📌 Perangkat : *{$deviceName}*\n"
            . "🌐 IP Address: {$ip}\n"
            . "📍 Lokasi    : {$location}\n"
            . "⏰ Waktu     : " . now()->format('d/m/Y H:i:s') . "\n\n"
            . "⚠️ Mohon segera periksa perangkat!\n"
            . "_- Sistem Monitoring DISNAKERTRANS_";

        return $this->send($this->adminNumber, $message);
    }

    public function sendRecoveryAlert(string $deviceName, string $ip, string $location): array
    {
        $message = "🟢 *RECOVERY - Perangkat Kembali Online!*\n\n"
            . "📌 Perangkat : *{$deviceName}*\n"
            . "🌐 IP Address: {$ip}\n"
            . "📍 Lokasi    : {$location}\n"
            . "⏰ Waktu     : " . now()->format('d/m/Y H:i:s') . "\n\n"
            . "Perangkat sudah kembali online.\n"
            . "_- Sistem Monitoring DISNAKERTRANS_";

        return $this->send($this->adminNumber, $message);
    }

    public function sendSlowAlert(string $deviceName, string $ip, string $location, int $responseTime, int $packetLoss): array
    {
        // Format level to match thresholds: >=400 => SANGAT LEMAH, >=200 => MELEMAH
        if ($responseTime >= 400) {
            $level = '🟠 *SANGAT LEMAH*';
        } elseif ($responseTime >= 200) {
            $level = '🟡 *MELEMAH*';
        } else {
            $level = '✅ *BAIK*';
        }
        $message = "{$level} *Kualitas Jaringan Menurun!*\n\n"
            . "📌 Perangkat  : *{$deviceName}*\n"
            . "🌐 IP Address : {$ip}\n"
            . "📍 Lokasi     : {$location}\n"
            . "⏰ Response   : *{$responseTime} ms*\n"
            . "📦 Packet Loss: {$packetLoss}%\n"
            . "⏰ Waktu      : " . now()->format('d/m/Y H:i:s') . "\n\n"
            . "⚠️ Jaringan mengalami perlambatan. Pantau penggunaan bandwidth!\n"
            . "_- Sistem Monitoring DISNAKERTRANS_";

        return $this->send($this->adminNumber, $message);
    }

    public function sendStableAlert(string $deviceName, string $ip, string $location, int $responseTime): array
    {
        $message = "✅ *Jaringan Kembali Stabil!*\n\n"
            . "📌 Perangkat  : *{$deviceName}*\n"
            . "🌐 IP Address : {$ip}\n"
            . "📍 Lokasi     : {$location}\n"
            . "⏰ Response   : *{$responseTime} ms*\n"
            . "⏰ Waktu      : " . now()->format('d/m/Y H:i:s') . "\n\n"
            . "Kualitas koneksi sudah kembali normal.\n"
            . "_- Sistem Monitoring DISNAKERTRANS_";

        return $this->send($this->adminNumber, $message);
    }

    /**
     * Notifikasi jaringan padat (VERSI BARU DENGAN FORMAT BOLD)
     */
    public function sendCongestionAlert(
        string $deviceName,
        string $ip,
        string $location,
        float $avgResponse,
        int $threshold,
        string $level = 'high'
    ): array {
        $emoji = $level === 'very_high' ? '🔴' : '🟠';
        $tingkat = $level === 'very_high' ? 'SANGAT PADAT' : 'PADAT';

        $message = "{$emoji} *ALERT - Jaringan {$tingkat}!*\n\n"
            . "Kemungkinan pemakaian bandwidth berlebihan\n\n"
            . "📌 Perangkat    : *{$deviceName}*\n"
            . "🌐 IP Address   : {$ip}\n"
            . "📍 Lokasi       : {$location}\n"
            . "⏰ Avg Response : *{$avgResponse} ms* (batas: {$threshold} ms)\n"
            . "⏰ Waktu        : " . now()->format('d/m/Y H:i:s') . "\n\n"
            . "Jaringan mengalami perlambatan signifikan.\n"
            . "Kemungkinan penyebab:\n"
            . "• Banyak pengguna streaming/download bersamaan\n"
            . "• Ada perangkat yang mengonsumsi bandwidth besar\n"
            . "• Gangguan dari ISP\n\n"
            . "Silakan periksa kondisi jaringan di lokasi tersebut.\n"
            . "_- Sistem Monitoring DISNAKERTRANS_";

        return $this->send($this->adminNumber, $message);
    }

    /**
     * Notifikasi jaringan normal kembali (VERSI BARU DENGAN FORMAT BOLD)
     */
    public function sendCongestionResolvedAlert(
        string $deviceName,
        string $ip,
        string $location,
        float $avgResponse
    ): array {
        $message = "✅ *Jaringan Kembali Normal!*\n\n"
            . "📌 Perangkat  : *{$deviceName}*\n"
            . "🌐 IP Address : {$ip}\n"
            . "📍 Lokasi     : {$location}\n"
            . "⏰ Avg Response saat ini: *{$avgResponse} ms*\n"
            . "⏰ Waktu      : " . now()->format('d/m/Y H:i:s') . "\n\n"
            . "⚠️Kepadatan jaringan sudah mereda. Koneksi kembali lancar.\n"
            . "_- Sistem Monitoring DISNAKERTRANS_";

        return $this->send($this->adminNumber, $message);
    }
}