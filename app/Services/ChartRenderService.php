<?php
// app/Services/ChartRenderService.php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChartRenderService
{
    /**
     * Render Chart.js config ke PNG menggunakan Node.js + Puppeteer
     * 
     * @param array $chartData Data untuk chart
     * @param string $outputFilename Nama file output
     * @return string|null Path ke file PNG atau null jika gagal
     */
    public function renderToPng(array $chartData, string $outputFilename = 'chart.png'): ?string
    {
        $outputPath = storage_path('app/charts/' . $outputFilename);

        // Pastikan direktori ada
        $chartDir = dirname($outputPath);
        if (!is_dir($chartDir)) {
            mkdir($chartDir, 0755, true);
        }

        // Simpan config chart sebagai JSON temporary
        $configPath = storage_path('app/temp/chart_config_' . uniqid() . '.json');
        $configDir = dirname($configPath);
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }

        file_put_contents($configPath, json_encode($chartData, JSON_PRETTY_PRINT));

        // Jalankan script Node.js
        $scriptPath = base_path('scripts/render-chart.js');
        $command = sprintf(
            'node %s %s %s 2>&1',
            escapeshellarg($scriptPath),
            escapeshellarg($configPath),
            escapeshellarg($outputPath)
        );

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        // Bersihkan file temporary
        if (file_exists($configPath)) {
            unlink($configPath);
        }

        if ($returnCode !== 0) {
            Log::error('Chart render failed', [
                'command' => $command,
                'output' => implode("\n", $output),
                'return_code' => $returnCode
            ]);
            return null;
        }

        return file_exists($outputPath) ? $outputPath : null;
    }

    /**
     * Encode file PNG ke base64 untuk Dompdf
     */
    public function encodeToBase64(string $filePath): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $imageData = file_get_contents($filePath);
        if ($imageData === false) {
            return null;
        }

        return base64_encode($imageData);
    }

    /**
     * Render dan encode dalam satu langkah
     */
    public function renderAndEncode(array $chartData, string $outputFilename = 'chart.png'): ?string
    {
        $pngPath = $this->renderToPng($chartData, $outputFilename);

        if (!$pngPath) {
            return null;
        }

        return $this->encodeToBase64($pngPath);
    }
}