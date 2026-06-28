<?php

namespace App\Services;

class PingService
{
    /**
     * Ping sebuah IP address dan kembalikan hasilnya
     */
    public function ping(string $ipAddress, int $timeout = 3): array
    {
        $startTime = microtime(true);

        if (PHP_OS_FAMILY === 'Windows') {
            $command = "ping -n 1 -w {$timeout}000 {$ipAddress}";
        } else {
            $command = "ping -c 1 -W {$timeout} {$ipAddress}";
        }

        exec($command . ' 2>&1', $output, $returnCode);

        $endTime   = microtime(true);
        $elapsed   = round(($endTime - $startTime) * 1000); // ms

        $isUp = ($returnCode === 0);

        // Parse response time dari output ping
        $responseTime = null;
        if ($isUp) {
            foreach ($output as $line) {
                if (preg_match('/time[<=](\d+\.?\d*)/i', $line, $matches)) {
                    $responseTime = (int) round((float) $matches[1]);
                    break;
                }
            }
            if ($responseTime === null) {
                $responseTime = $elapsed;
            }
        }

        return [
            'status'        => $isUp ? 'up' : 'down',
            'response_time' => $responseTime,
            'output'        => implode("\n", $output),
        ];
    }

    /**
     * Ping beberapa IP sekaligus
     */
    public function pingMultiple(array $ipAddresses, int $timeout = 3): array
    {
        $results = [];
        foreach ($ipAddresses as $ip) {
            $results[$ip] = $this->ping($ip, $timeout);
        }
        return $results;
    }
}