<?php
// app/Http/Controllers/NetworkReportController.php

namespace App\Http\Controllers;

use App\Models\NetworkLog;
use App\Services\ChartRenderService;
use App\Charts\UptimeTrendChart;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NetworkReportController extends Controller
{
    protected ChartRenderService $chartService;

    public function __construct(ChartRenderService $chartService)
    {
        $this->chartService = $chartService;
    }

    /**
     * Generate PDF Laporan Monitoring Jaringan
     */
    public function generatePdf(Request $request)
    {
        try {
            // === 1. AMBIL PARAMETER TANGGAL ===
            $start = $request->input('start', now()->subDays(7)->format('Y-m-d'));
            $end = $request->input('end', now()->format('Y-m-d'));

            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();

            // === 2. AMBIL DATA LOG DARI DATABASE ===
            $logs = NetworkLog::with('device')
                ->whereBetween('checked_at', [$startDate, $endDate])
                ->orderBy('checked_at', 'desc')
                ->get();

            // === 3. HITUNG RINGKASAN (SUMMARY) ===
            $totalLogs = $logs->count();
            $totalUp = $logs->where('status', 'online')->count();
            $totalDown = $logs->where('status', 'offline')->count();
            $uptimePct = $totalLogs > 0 ? ($totalUp / $totalLogs) * 100 : 0;
            $avgResponse = $logs->avg('response_time') ?? 0;

            $summary = [
                'total_logs' => $totalLogs,
                'total_up' => $totalUp,
                'total_down' => $totalDown,
                'uptime_pct' => number_format($uptimePct, 2),
                'avg_response' => number_format($avgResponse, 2),
            ];

            // === 4. GENERATE CHART DATA ===
            $chartImage = $this->generateChartImage($logs, $uptimePct);

            // === 5. RENDER PDF ===
            $pdf = Pdf::loadView('pdf.network-report', compact(
                'logs',
                'summary',
                'start',
                'end',
                'chartImage'
            ));

            // Konfigurasi PDF (Dompdf)
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,  // Untuk gambar base64
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Helvetica',
                'margin_top' => 15,
                'margin_right' => 12,
                'margin_bottom' => 20,
                'margin_left' => 12,
            ]);

            // === 6. OUTPUT PDF ===
            $filename = 'Laporan-Monitoring-' .
                $startDate->format('Y-m-d') .
                '-sd-' .
                $endDate->format('Y-m-d') .
                '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate chart image dari data log
     */
    private function generateChartImage($logs, float $uptimePct): ?string
    {
        // Jika tidak ada data log, gunakan chart dummy
        if ($logs->isEmpty()) {
            return $this->generateDummyChart();
        }

        // Siapkan data untuk chart
        $labels = $logs->pluck('checked_at')
            ->map(fn($date) => $date->format('d/m H:i'))
            ->toArray();

        $uptimeData = $logs->map(function ($log) {
            return $log->status === 'online' ? 100 : 0;
        })->toArray();

        $responseData = $logs->pluck('response_time')
            ->map(fn($val) => $val ?? 0)
            ->toArray();

        // Build Chart.js config
        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Status Uptime (%)',
                        'data' => $uptimeData,
                        'borderColor' => '#22c55e',
                        'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                        'borderWidth' => 2,
                        'pointBackgroundColor' => '#22c55e',
                        'pointRadius' => 4,
                        'fill' => true,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Response Time (ms)',
                        'data' => $responseData,
                        'borderColor' => '#7c3aed',
                        'backgroundColor' => 'rgba(124, 58, 237, 0.1)',
                        'borderWidth' => 2,
                        'pointBackgroundColor' => '#7c3aed',
                        'pointRadius' => 4,
                        'fill' => true,
                        'tension' => 0.3,
                        'yAxisID' => 'y1'
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Tren Uptime & Response Time',
                        'font' => ['size' => 14, 'weight' => 'bold']
                    ],
                    'legend' => [
                        'position' => 'top',
                        'labels' => ['font' => ['size' => 10]]
                    ]
                ],
                'scales' => [
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Waktu Pengecekan'
                        ]
                    ],
                    'y' => [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'left',
                        'min' => 0,
                        'max' => 100,
                        'title' => [
                            'display' => true,
                            'text' => 'Uptime (%)'
                        ]
                    ],
                    'y1' => [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'right',
                        'min' => 0,
                        'title' => [
                            'display' => true,
                            'text' => 'Response (ms)'
                        ],
                        'grid' => ['drawOnChartArea' => false]
                    ]
                ]
            ],
            'width' => 800,
            'height' => 400
        ];

        // Render ke PNG via Node.js
        $chartImage = $this->chartService->renderAndEncode($chartConfig, 'uptime_trend_' . uniqid() . '.png');

        return $chartImage;
    }

    /**
     * Generate chart dummy jika tidak ada data
     */
    private function generateDummyChart(): ?string
    {
        $dummyConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'datasets' => [
                    [
                        'label' => 'Tidak Ada Data',
                        'data' => [0, 0, 0, 0, 0, 0, 0],
                        'backgroundColor' => '#e2e8f0'
                    ]
                ]
            ],
            'options' => [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Tidak ada data monitoring dalam periode ini'
                    ]
                ]
            ],
            'width' => 600,
            'height' => 300
        ];

        return $this->chartService->renderAndEncode($dummyConfig, 'dummy_chart.png');
    }

    /**
     * Preview laporan di browser (tanpa download)
     */
    public function preview(Request $request)
    {
        $start = $request->input('start', now()->subDays(7)->format('Y-m-d'));
        $end = $request->input('end', now()->format('Y-m-d'));

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $logs = NetworkLog::with('device')
            ->whereBetween('checked_at', [$startDate, $endDate])
            ->orderBy('checked_at', 'desc')
            ->get();

        $totalLogs = $logs->count();
        $totalUp = $logs->where('status', 'online')->count();
        $totalDown = $logs->where('status', 'offline')->count();
        $uptimePct = $totalLogs > 0 ? ($totalUp / $totalLogs) * 100 : 0;
        $avgResponse = $logs->avg('response_time') ?? 0;

        $summary = [
            'total_logs' => $totalLogs,
            'total_up' => $totalUp,
            'total_down' => $totalDown,
            'uptime_pct' => number_format($uptimePct, 2),
            'avg_response' => number_format($avgResponse, 2),
        ];

        $chartImage = $this->generateChartImage($logs, $uptimePct);

        return view('pdf.network-report', compact(
            'logs',
            'summary',
            'start',
            'end',
            'chartImage'
        ));
    }
}