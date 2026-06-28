@extends('layouts.app')
@section('title', 'Laporan Monitoring')

@section('content')
    <div class="container-fluid px-0">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
            </div>

            </span>
        </div>

        <!-- Filter -->
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm rounded-3"
                            value="{{ $start }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">Sampai</label>
                        <input type="date" name="end_date" class="form-control form-control-sm rounded-3"
                            value="{{ $end }}">
                    </div>
                    <div class="col-md-auto col-sm-12">
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('reports.pdf', ['start_date' => $start, 'end_date' => $end]) }}"
                            class="btn btn-danger btn-sm rounded-3 px-3" target="_blank">
                            <i class="bi bi-file-pdf"></i> PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-2 mb-3">
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100"
                        style="height:3px;background:linear-gradient(90deg,#0ea5e9,#6366f1)"></div>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                        style="width:40px;height:40px;background:rgba(14,165,233,0.1)">
                        <i class="bi bi-check-circle text-primary fs-5"></i>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ $summary['total_logs'] }}</div>
                    <small class="text-secondary">Total Cek</small>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100"
                        style="height:3px;background:linear-gradient(90deg,#22c55e,#16a34a)"></div>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                        style="width:40px;height:40px;background:rgba(34,197,94,0.1)">
                        <i class="bi bi-wifi text-success fs-5"></i>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ $summary['total_up'] }}</div>
                    <small class="text-secondary">Online</small>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100"
                        style="height:3px;background:linear-gradient(90deg,#ef4444,#dc2626)"></div>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                        style="width:40px;height:40px;background:rgba(239,68,68,0.1)">
                        <i class="bi bi-wifi-off text-danger fs-5"></i>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ $summary['total_down'] }}</div>
                    <small class="text-secondary">Downtime</small>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100"
                        style="height:3px;background:linear-gradient(90deg,#f59e0b,#d97706)"></div>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                        style="width:40px;height:40px;background:rgba(245,158,11,0.1)">
                        <i class="bi bi-activity text-warning fs-5"></i>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ $summary['uptime_pct'] }}%</div>
                    <small class="text-secondary">Uptime</small>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100"
                        style="height:3px;background:linear-gradient(90deg,#8b5cf6,#7c3aed)"></div>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                        style="width:40px;height:40px;background:rgba(139,92,246,0.1)">
                        <i class="bi bi-lightning-charge text-info fs-5"></i>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ $summary['avg_response'] }}<small
                            class="fs-6 fw-normal">ms</small></div>
                    <small class="text-secondary">Avg Latency</small>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-3 mb-3">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">

                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div
                class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Daftar Kejadian
                    Downtime</h6>
                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">{{ $summary['total_down'] }}
                    Kejadian</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold">#</th>
                                <th class="text-secondary small fw-bold">Perangkat</th>
                                <th class="text-secondary small fw-bold">IP</th>
                                <th class="text-secondary small fw-bold">Lokasi</th>
                                <th class="text-secondary small fw-bold">Status</th>
                                <th class="text-secondary small fw-bold">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td class="ps-3 fw-bold text-secondary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-2"
                                                style="width:32px;height:32px;background:rgba(239,68,68,0.1)">
                                                <i class="bi bi-router text-danger"></i>
                                            </span>
                                            <span class="fw-semibold">{{ $log->device->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-danger bg-danger bg-opacity-10 px-2 py-1 rounded-2"
                                            style="font-size:0.8rem">{{ $log->ip_address }}</code>
                                    </td>
                                    <td>
                                        <span class="text-secondary small"><i
                                                class="bi bi-geo-alt text-primary me-1"></i>{{ $log->device->location ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                            <span class="d-inline-block rounded-circle bg-danger me-1"
                                                style="width:5px;height:5px"></span>
                                            DOWN
                                        </span>
                                    </td>
                                    <td class="text-secondary small">
                                        <i
                                            class="bi bi-clock-history me-1 text-secondary"></i>{{ $log->checked_at->format('d/m/Y H:i:s') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2 opacity-50"></i>
                                        Tidak ada downtime dalam periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $logs->links() }}
            </div>
        </div>

    </div>

    <style>
        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Uptime Chart
        const ctxUptime = document.getElementById('uptimeChart').getContext('2d');
        new Chart(ctxUptime, {
            type: 'line',
            data: {
                labels: ['21/05', '22/05', '23/05', '24/05', '25/05', '26/05', '27/05'],
                datasets: [{
                    label: 'Uptime (%)',
                    data: [98.2, 97.5, 99.1, 96.8, 98.5, 95.2, {{ $summary['uptime_pct'] ?? 95.95 }}],
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0ea5e9',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 90,
                        max: 100,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    }
                }
            }
        });

        // Status Chart
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Online', 'Downtime'],
                datasets: [{
                    data: [{{ $summary['total_up'] ?? 142 }}, {{ $summary['total_down'] ?? 6 }}],
                    backgroundColor: ['#22c55e', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 11 }, padding: 15, usePointStyle: true }
                    }
                }
            }
        });
    </script>
@endsection