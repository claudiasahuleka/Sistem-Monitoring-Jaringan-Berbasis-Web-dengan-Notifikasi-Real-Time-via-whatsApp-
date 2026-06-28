<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MonitoringLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->get('end_date', now()->format('Y-m-d'));

        $logs = MonitoringLog::with('device')
            ->whereBetween('checked_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->where('status', 'down')
            ->latest('checked_at')
            ->paginate(20);

        $summary = $this->getSummary($start, $end);

        return view('reports.index', compact('logs', 'summary', 'start', 'end'));
    }

    public function downloadPdf(Request $request)
    {
        $start = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->get('end_date', now()->format('Y-m-d'));
        $range = [$start . ' 00:00:00', $end . ' 23:59:59'];

        // 1. AMBIL DATA DEVICE YANG AKTIF
        $devices = Device::active()->get();

        // 2. QUERY HITUNG AKUMULASI HISTORIS (TOTAL LOG & UPTIME) BERDASARKAN RENTANG TANGGAL
        $stats = MonitoringLog::whereBetween('checked_at', $range)
            ->select(
                'device_id',
                DB::raw('COUNT(*) as total_logs'),
                DB::raw('SUM(CASE WHEN status = "up" THEN 1 ELSE 0 END) as up_logs')
            )
            ->groupBy('device_id')
            ->get()
            ->keyBy('device_id');

        // 3. QUERY SUBQUERY UNTUK MENDAPATKAN DATA LOG TERAKHIR (SINKRON DASHBOARD REAL-TIME)
        $latestLogsSub = MonitoringLog::select('device_id', DB::raw('MAX(checked_at) as max_checked_at'))
            ->whereBetween('checked_at', $range)
            ->groupBy('device_id');

        $latestLogs = MonitoringLog::joinSub($latestLogsSub, 'latest_logs', function ($join) {
            $join->on('monitoring_logs.device_id', '=', 'latest_logs.device_id')
                ->on('monitoring_logs.checked_at', '=', 'latest_logs.max_checked_at');
        })
            ->get()
            ->keyBy('device_id');

        // 4. PEMETAAN DATA KE TIAP PERANGKAT
        foreach ($devices as $device) {
            $stat = $stats[$device->id] ?? null;
            $latestLog = $latestLogs[$device->id] ?? null;

            // Hitung persentase uptime historis
            $device->total_logs = $stat ? (int) $stat->total_logs : 0;
            $device->up_logs = $stat ? (int) $stat->up_logs : 0;
            $device->uptime_percent = $device->total_logs > 0
                ? round(($device->up_logs / $device->total_logs) * 100, 2)
                : 0;

            // Ambil kondisi riil terakhir (Kondisi Terkini / Sinkron Dashboard)
            $device->response_time = $latestLog ? (float) $latestLog->response_time : 0;

            // Status mengikuti data status terakhir dari log tersebut
            $device->status = $latestLog ? $latestLog->status : 'down';
        }

        $summary = $this->getSummary($start, $end);

        // 5. GENERATE DOMPDF
        $pdf = Pdf::loadView('reports.pdf', compact('devices', 'summary', 'start', 'end'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 150,
                'fontHeightRatio' => 1.0,
            ]);

        return $pdf->download("Laporan-Monitoring-{$start}-{$end}.pdf");
    }

    private function getSummary(string $start, string $end): array
    {
        $range = [$start . ' 00:00:00', $end . ' 23:59:59'];

        $total = MonitoringLog::whereBetween('checked_at', $range)->count();
        $down = MonitoringLog::whereBetween('checked_at', $range)->where('status', 'down')->count();
        $up = $total - $down;

        $avgResp = MonitoringLog::whereBetween('checked_at', $range)
            ->where('status', 'up')
            ->avg('response_time');

        return [
            'total_logs' => $total,
            'total_down' => $down,
            'total_up' => $up,
            'avg_response' => round($avgResp ?? 0, 2),
            'uptime_pct' => $total > 0 ? round(($up / $total) * 100, 2) : 0,
        ];
    }
}