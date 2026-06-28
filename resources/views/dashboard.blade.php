@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    <style>
        /* ── CSS Variables ── */
        :root {
            --bg-primary: #f8fafc;
            --bg-card: #ffffff;
            --bg-sidebar: #ffffff;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --accent-primary: #3b82f6;
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
            --accent-danger: #ef4444;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        /* ── Base ── */
        body {
            background-color: var(--bg-primary) !important;
            color: var(--text-primary);
        }

        /* ── Clean Cards ── */
        .clean-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .clean-card:hover {
            box-shadow: var(--shadow-lg);
        }

        /* ── Stat Cards ── */
        .stat-card-clean {
            position: relative;
            padding-top: 4px;
            overflow: hidden;
        }

        .stat-card-clean::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 12px 12px 0 0;
        }

        .stat-card-clean.primary::before {
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        }

        .stat-card-clean.success::before {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .stat-card-clean.warning::before {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }

        .stat-card-clean.danger::before {
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        .stat-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* ── Pulse Dot ── */
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.3);
            }
        }

        /* ── Status Badges ── */
        .status-badge-clean {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-online {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.15);
        }

        .status-offline {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.15);
        }

        /* ── Quality Badges ── */
        .quality-badge-clean {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .quality-good {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .quality-slow {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .quality-very-slow {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .quality-down {
            background: rgba(107, 114, 128, 0.1);
            color: #64748b;
        }

        /* ── Device Rows ── */
        .device-row-clean {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .device-row-clean:hover {
            background: rgba(59, 130, 246, 0.03);
        }

        .device-row-clean.status-up {
            border-left-color: #10b981;
            background: rgba(16, 185, 129, 0.02);
        }

        .device-row-clean.status-down {
            border-left-color: #ef4444;
            background: rgba(239, 68, 68, 0.02);
        }

        .device-row-clean.status-slow {
            border-left-color: #f59e0b;
            background: rgba(245, 158, 11, 0.02);
        }

        /* ── Alert Feed ── */
        .alert-item-clean {
            animation: slideInAlert 0.4s ease-out;
            border-left: 3px solid #ef4444;
            background: rgba(239, 68, 68, 0.03);
        }

        @keyframes slideInAlert {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ── Empty State ── */
        .empty-state-clean {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        /* ── Custom Scrollbar ── */
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ── Monitor Badge ── */
        .monitoring-badge-clean {
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.15);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        /* ── Table Styling ── */
        .table-clean {
            color: var(--text-primary);
        }

        .table-clean thead th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 12px;
            border: none;
            padding: 12px 16px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-clean tbody td {
            border: none;
            padding: 12px 16px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-light);
        }

        .table-clean tbody tr:last-child td {
            border-bottom: none;
        }

        /* ── Chart Container ── */
        .chart-container-clean {
            position: relative;
            height: 280px;
        }

        /* ── WhatsApp Badge ── */
        .wa-badge-clean {
            font-size: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* ── Alert Ticker ── */
        .alert-ticker-clean {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
        }

        /* ── Override Bootstrap defaults for light theme ── */
        .table-hover tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.03);
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .card-header {
            background: transparent !important;
            border-bottom: 1px solid var(--border-light) !important;
        }

        .list-group-item {
            background: transparent !important;
            border-color: var(--border-light) !important;
            color: var(--text-primary);
        }

        code {
            background: #f1f5f9;
            color: #ec4899;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12px;
            border: 1px solid #e2e8f0;
        }

        /* ── Button Clean ── */
        .btn-clean {
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-clean:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: var(--text-secondary);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .chart-container-clean {
                height: 200px;
            }

            .stat-icon-box {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }
    </style>

    {{-- Alert Ticker (muncul saat ada perangkat DOWN/Slow) --}}
    <div id="alert-ticker" class="alert alert-dismissible d-none mb-4 py-3 alert-ticker-clean" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            <div>
                <strong class="text-danger">ALERT:</strong>
                <span id="alert-ticker-text" class="text-danger"></span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Monitoring
                Real-Time</h4>

        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="monitoring-badge-clean" id="monitor-badge">
                <span class="pulse-dot bg-success me-2"></span>Monitoring Aktif
            </span>
            <small class="text-muted">Cek berikutnya: <span id="countdown" class="fw-bold text-primary">30</span>s</small>
        </div>
    </div>

    {{-- Statistik Utama --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="clean-card stat-card-clean primary">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-muted mb-1 fw-medium">Total Perangkat</div>
                            <div class="fs-2 fw-bold" style="color: #3b82f6;" id="stat-total">{{ $totalDevices }}</div>
                            <div class="mt-2 small text-muted">

                            </div>
                        </div>
                        <div class="stat-icon-box" style="background: rgba(59,130,246,0.1); color: #3b82f6;">
                            <i class="bi bi-hdd-network"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="clean-card stat-card-clean success">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-muted mb-1 fw-medium">Online & Baik</div>
                            <div class="fs-2 fw-bold text-success" id="stat-up">{{ $devicesUp }}</div>
                            <div class="mt-2 small text-muted">

                            </div>
                        </div>
                        <div class="stat-icon-box" style="background: rgba(16,185,129,0.1); color: #10b981;">
                            <i class="bi bi-wifi"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="clean-card stat-card-clean warning">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-muted mb-1 fw-medium">Jaringan Lemah</div>
                            <div class="fs-2 fw-bold text-warning" id="stat-slow">0</div>
                            <div class="mt-2 small text-muted">

                            </div>
                        </div>
                        <div class="stat-icon-box" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="clean-card stat-card-clean danger">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-muted mb-1 fw-medium">Offline</div>
                            <div class="fs-2 fw-bold text-danger" id="stat-down">{{ $devicesDown }}</div>
                            <div class="mt-2 small text-muted">

                            </div>
                        </div>
                        <div class="stat-icon-box" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                            <i class="bi bi-wifi-off"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Tabel Status Real-Time --}}
        <div class="col-lg-8">
            <div class="clean-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-2">
                            <i class="bi bi-list-check text-primary"></i>
                        </div>
                        <div>
                            <strong class="text-dark">Status Perangkat</strong>
                            <div class="small text-muted">Pemantauan perangkat real-time</div>
                        </div>
                    </div>
                    <small class="text-muted">Update: <span id="last-update">{{ now()->format('H:i:s') }}</span></small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-clean" style="font-size:13px">
                            <thead>
                                <tr>
                                    <th class="ps-4">Perangkat</th>
                                    <th>IP</th>
                                    <th>Status</th>
                                    <th>Kualitas</th>
                                    <th>Latency</th>
                                    <th class="pe-4">Cek</th>
                                </tr>
                            </thead>
                            <tbody id="device-table-body">
                                @foreach($devices as $device)
                                    <tr data-device-id="{{ $device->id }}" id="row-{{ $device->id }}"
                                        class="device-row-clean status-{{ $device->status === 'up' ? 'up' : 'down' }}">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-primary bg-opacity-10 p-2 rounded-2">
                                                    <i class="bi bi-router text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $device->name }}</div>
                                                    <small class="text-muted">{{ $device->location }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3"><code>{{ $device->ip_address }}</code></td>
                                        <td class="py-3">
                                            <span
                                                class="status-badge-clean {{ $device->status === 'up' ? 'status-online' : 'status-offline' }}"
                                                id="status-{{ $device->id }}">
                                                <span
                                                    class="pulse-dot {{ $device->status === 'up' ? 'bg-success' : 'bg-danger' }}"></span>
                                                {{ $device->status === 'up' ? 'Online' : 'Offline' }}
                                            </span>
                                        </td>
                                        @php
                                            $responseTime = $device->response_time;
                                            if ($device->status === 'down') {
                                                $qualityLabel = '🔴 Terputus';
                                                $qualityClass = 'quality-down';
                                            } elseif ($responseTime !== null && $responseTime >= 400) {
                                                $qualityLabel = '🟠 Sangat Lemah';
                                                $qualityClass = 'quality-very-slow';
                                            } elseif ($responseTime !== null && $responseTime >= 200) {
                                                $qualityLabel = '🟡 Melemah';
                                                $qualityClass = 'quality-slow';
                                            } else {
                                                $qualityLabel = '✅ Baik';
                                                $qualityClass = 'quality-good';
                                            }
                                        @endphp
                                        <td class="py-3" id="quality-{{ $device->id }}">
                                            <span class="quality-badge-clean {{ $qualityClass }}">{{ $qualityLabel }}</span>
                                        </td>
                                        <td class="py-3 fw-medium" id="rt-{{ $device->id }}">
                                            {{ $device->response_time ? $device->response_time . ' ms' : '—' }}
                                        </td>
                                        <td class="pe-4 py-3 text-muted" style="font-size:11px" id="checked-{{ $device->id }}">
                                            {{ $device->last_checked_at?->format('H:i:s') ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Grafik Response Time 24 Jam --}}
            <div class="clean-card">
                <div class="card-header py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-info bg-opacity-10 p-2 rounded-2">
                            <i class="bi bi-graph-up-arrow text-info"></i>
                        </div>
                        <div>
                            <strong class="text-dark">Grafik Response Time (Real-Time)</strong>
                            <div class="small text-muted">Metrik performa real-time</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container-clean">
                        <canvas id="responseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Kanan: Alert & Notifikasi --}}
        <div class="col-lg-4">
            {{-- Live Alert Feed --}}
            <div class="clean-card mb-4">
                <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-2">
                            <i class="bi bi-bell-fill text-danger"></i>
                        </div>
                        <div>
                            <strong class="text-dark">Alert Live</strong>
                            <div class="small text-muted">Notifikasi aktif</div>
                        </div>
                    </div>
                    <span class="badge bg-danger rounded-pill" id="alert-count">0</span>
                </div>
                <div class="card-body p-0">
                    {{-- Tampilan kosong --}}
                    <div id="alert-empty" class="empty-state-clean">
                        <div class="empty-state-icon">
                            <i class="bi bi-shield-check text-success fs-3"></i>
                        </div>
                        <h6 class="text-muted mb-1">Tidak ada alert saat ini</h6>
                        <small class="text-muted">Semua sistem berjalan normal</small>
                    </div>
                    {{-- Daftar alert (hidden saat kosong) --}}
                    <ul class="list-group list-group-flush d-none custom-scroll" id="alert-feed"
                        style="max-height:220px;overflow-y:auto">
                    </ul>
                </div>
                <div class="card-footer bg-transparent py-3 px-4">
                    <button class="btn btn-clean btn-sm w-100" style="font-size:12px" onclick="clearAlerts()">
                        <i class="bi bi-trash me-1"></i>Bersihkan
                    </button>
                </div>
            </div>

            {{-- Notifikasi WA Terbaru --}}
            <div class="clean-card">
                <div class="card-header py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success bg-opacity-10 p-2 rounded-2">
                            <i class="bi bi-whatsapp text-success"></i>
                        </div>
                        <div>
                            <strong class="text-dark">Notifikasi WA Terkirim</strong>
                            <div class="small text-muted">Alert WhatsApp terbaru</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height:320px;overflow-y:auto">
                    <ul class="list-group list-group-flush custom-scroll" id="notif-list">
                        @forelse($recentNotifications as $notif)
                            <li class="list-group-item py-3 px-4">
                                <div class="d-flex gap-3">
                                    @php
                                        $colors = [
                                            'down' => 'danger',
                                            'recovery' => 'success',
                                            'slow' => 'warning',
                                            'stable' => 'info',
                                            'bandwidth_high' => 'orange',
                                        ];
                                        $labels = [
                                            'down' => 'DOWN',
                                            'recovery' => 'PULIH',
                                            'slow' => 'LEMAH',
                                            'stable' => 'STABIL',
                                            'bandwidth_high' => 'BW ⚠',
                                        ];
                                    @endphp
                                    <div class="mt-1">
                                        <span class="badge bg-{{ $colors[$notif->type] ?? 'secondary' }} wa-badge-clean"
                                            style="height:fit-content">
                                            {{ $labels[$notif->type] ?? $notif->type }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold" style="font-size:13px">{{ $notif->device->name }}</div>
                                        <div class="d-flex justify-content-between align-items-center mt-1"
                                            style="font-size:11px;color:#666">
                                            <span>{{ $notif->sent_at?->format('d/m H:i') }}</span>
                                            <span class="{{ $notif->is_sent ? 'text-success' : 'text-danger' }}">
                                                {{ $notif->is_sent ? '✓ Terkirim' : '✗ Gagal' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted small py-4">
                                <i class="bi bi-inbox fs-4 mb-2 d-block text-muted"></i>
                                Belum ada notifikasi
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const MONITOR_URL = '{{ route("api.monitor.run") }}';
        const NOTIF_URL = '{{ route("api.monitor.notifications") }}';
        const INTERVAL = 30;
        const NOTIF_INTERVAL = 10; // Fetch notifikasi setiap 10 detik

        // ── Data grafik --
        const chartLabels = [];
        const chartData = { avg: [], max: [] };
        const MAX_POINTS = 20;

        // Inisialisasi grafik Chart.js
        const ctx = document.getElementById('responseChart').getContext('2d');

        // Gradient untuk chart
        const gradientAvg = ctx.createLinearGradient(0, 0, 0, 300);
        gradientAvg.addColorStop(0, 'rgba(59,130,246,0.2)');
        gradientAvg.addColorStop(1, 'rgba(59,130,246,0.0)');

        const gradientMax = ctx.createLinearGradient(0, 0, 0, 300);
        gradientMax.addColorStop(0, 'rgba(239,68,68,0.15)');
        gradientMax.addColorStop(1, 'rgba(239,68,68,0.0)');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Avg Response (ms)',
                        data: chartData.avg,
                        borderColor: '#3b82f6',
                        backgroundColor: gradientAvg,
                        tension: 0.4, fill: true, pointRadius: 4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        borderWidth: 3,
                    },
                    {
                        label: 'Max Response (ms)',
                        data: chartData.max,
                        borderColor: '#ef4444',
                        backgroundColor: gradientMax,
                        tension: 0.4, fill: true, borderDash: [5, 5], pointRadius: 3,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 300 },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'ms', color: '#94a3b8' },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: '#64748b' },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' },
                    },
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20, color: '#64748b' }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255,255,255,0.95)',
                        titleColor: '#1e293b',
                        bodyColor: '#64748b',
                        padding: 12,
                        cornerRadius: 8,
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        titleFont: { weight: 'bold' },
                    }
                },
            },
        });

        // ── Badge & label kualitas ──
        const qualityMap = {
            good: { label: '✅ Baik', cls: 'quality-good' },
            slow: { label: '🟡 Melemah', cls: 'quality-slow' },
            very_slow: { label: '🟠 Sangat Lemah', cls: 'quality-very-slow' },
            down: { label: '🔴 Terputus', cls: 'quality-down' },
        };
        const notifLabel = {
            down: 'DOWN', recovery: 'PULIH', slow: 'LEMAH',
            stable: 'STABIL', bandwidth_high: 'BW TINGGI',
        };

        let alertCount = 0;
        let isChecking = false;
        let countdown = INTERVAL;
        let monitorInterval = null;
        let notifInterval = null;

        // Countdown timer
        setInterval(() => {
            countdown--;
            const el = document.getElementById('countdown');
            if (el) el.textContent = countdown;
            if (countdown <= 0) countdown = INTERVAL;
        }, 1000);

        // Jam navbar
        setInterval(() => {
            const el = document.getElementById('nav-last-check');
            if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
        }, 1000);

        function startMonitorInterval() {
            if (monitorInterval) return;
            monitorInterval = setInterval(runMonitoring, INTERVAL * 1000);
        }

        function startNotifInterval() {
            if (notifInterval) return;
            notifInterval = setInterval(fetchRecentNotifications, NOTIF_INTERVAL * 1000);
        }

        function ensureMonitoring() {
            runMonitoring();
            startMonitorInterval();
            fetchRecentNotifications(); // Fetch notifikasi juga saat load
            startNotifInterval();
        }

        // Fungsi bersihkan alert
        function clearAlerts() {
            alertCount = 0;
            document.getElementById('alert-count').textContent = '0';
            const feed = document.getElementById('alert-feed');
            const empty = document.getElementById('alert-empty');
            feed.innerHTML = '';
            feed.classList.add('d-none');
            empty.classList.remove('d-none');
        }

        async function runMonitoring() {
            if (isChecking) return;
            isChecking = true;

            const badge = document.getElementById('monitor-badge');
            if (badge) {
                badge.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memeriksa...';
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const resp = await fetch(MONITOR_URL, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                if (!resp.ok) {
                    const errorText = await resp.text();
                    console.error('Monitoring request failed:', resp.status, resp.statusText, errorText);
                    if (badge) {
                        badge.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Error';
                    }
                    return;
                }

                const data = await resp.json().catch(err => {
                    console.error('Monitoring JSON parse failed:', err);
                    return null;
                });
                if (!data || !Array.isArray(data.devices)) {
                    console.error('Invalid monitoring response:', data);
                    if (badge) {
                        badge.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Error';
                    }
                    return;
                }

                let slowCount = 0, downCount = 0, upCount = 0;
                const alerts = [];
                const rtValues = [];

                data.devices.forEach(d => {
                    // Update baris tabel
                    const row = document.getElementById('row-' + d.id);
                    if (!row) return;

                    // Status badge
                    const sBadge = document.getElementById('status-' + d.id);
                    const isOnline = d.status === 'up';
                    sBadge.className = 'status-badge-clean ' + (isOnline ? 'status-online' : 'status-offline');
                    sBadge.innerHTML = '<span class="pulse-dot ' + (isOnline ? 'bg-success' : 'bg-danger') + '"></span>' +
                        (isOnline ? 'Online' : 'Offline');

                    // Kualitas badge
                    const qEl = document.getElementById('quality-' + d.id);
                    const qInfo = d.status === 'down'
                        ? qualityMap.down
                        : qualityMap[d.quality] || { label: '—', cls: 'quality-down' };
                    qEl.innerHTML = `<span class="quality-badge-clean ${qInfo.cls}">${qInfo.label}</span>`;

                    // Response time
                    document.getElementById('rt-' + d.id).textContent =
                        d.response_time ? d.response_time + ' ms' : '—';

                    // Bandwidth
                    const bwEl = document.getElementById('bw-' + d.id);
                    if (bwEl) {
                        if (d.bandwidth) {
                            const inPct = d.bandwidth.in_util.toFixed(1);
                            const outPct = d.bandwidth.out_util.toFixed(1);
                            bwEl.innerHTML =
                                `<div>↓ ${d.bandwidth.in_mbps} Mbps <small class="text-muted">(${inPct}%)</small></div>` +
                                `<div>↑ ${d.bandwidth.out_mbps} Mbps <small class="text-muted">(${outPct}%)</small></div>` +
                                (d.bw_alert ? `<span class="badge bg-danger" style="font-size:9px">⚠ Tinggi</span>` : '');
                        } else {
                            bwEl.innerHTML = '<span class="text-muted">—</span>';
                        }
                    }

                    // Waktu cek
                    document.getElementById('checked-' + d.id).textContent = data.checked_at;

                    // Warna baris
                    row.className = 'device-row-clean';
                    if (d.status === 'down') {
                        row.classList.add('status-down'); downCount++;
                        alerts.push({ name: d.name, type: 'down', msg: 'Perangkat OFFLINE' });
                    } else if (d.quality === 'very_slow') {
                        row.classList.add('status-slow'); slowCount++;
                        alerts.push({ name: d.name, type: 'slow', msg: 'Jaringan Sangat Lemah (' + d.response_time + 'ms)' });
                    } else if (d.quality === 'slow') {
                        row.classList.add('status-slow'); slowCount++;
                    } else {
                        row.classList.add('status-up'); upCount++;
                    }

                    if (d.bw_alert) {
                        alerts.push({ name: d.name, type: 'bw', msg: 'Bandwidth Tinggi (' + d.bw_util?.toFixed(1) + '%)' });
                    }

                    if (d.response_time) rtValues.push(d.response_time);
                });

                // Update statistik
                document.getElementById('stat-up').textContent = upCount;
                document.getElementById('stat-slow').textContent = slowCount;
                document.getElementById('stat-down').textContent = downCount;

                // Update alert ticker
                const ticker = document.getElementById('alert-ticker');
                if (downCount > 0 || slowCount > 0) {
                    const msgs = alerts.map(a => a.name + ': ' + a.msg).join(' | ');
                    document.getElementById('alert-ticker-text').textContent = msgs;
                    ticker.classList.remove('d-none');
                    ticker.className = ticker.className.replace(/alert-(danger|warning)/, '');
                    ticker.classList.add(downCount > 0 ? 'alert-danger' : 'alert-warning');
                } else {
                    ticker.classList.add('d-none');
                }

                // Update alert feed
                const feed = document.getElementById('alert-feed');
                const empty = document.getElementById('alert-empty');

                if (alerts.length > 0) {
                    empty.classList.add('d-none');
                    feed.classList.remove('d-none');

                    const now = new Date().toLocaleTimeString('id-ID');
                    alerts.forEach(a => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item py-2 px-4 border-0 alert-item-clean';
                        li.innerHTML =
                            `<div class="d-flex align-items-center gap-2" style="font-size:12px">
                                                                            <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                                                            <span class="text-muted">${now}</span>
                                                                            <strong>${a.name}</strong>
                                                                            <span class="text-muted">—</span>
                                                                            <span class="text-danger">${a.msg}</span>
                                                                        </div>`;
                        feed.prepend(li);
                    });
                    alertCount += alerts.length;
                    document.getElementById('alert-count').textContent = alertCount;
                }

                // Update grafik
                const avgRt = rtValues.length ? Math.round(rtValues.reduce((a, b) => a + b, 0) / rtValues.length) : 0;
                const maxRt = rtValues.length ? Math.max(...rtValues) : 0;
                chartLabels.push(data.checked_at);
                chartData.avg.push(avgRt);
                chartData.max.push(maxRt);
                if (chartLabels.length > MAX_POINTS) {
                    chartLabels.shift(); chartData.avg.shift(); chartData.max.shift();
                }
                chart.update('none');
                await fetchRecentNotifications();

                document.getElementById('last-update').textContent = data.checked_at;
                badge.innerHTML = '<span class="pulse-dot bg-success me-2"></span>';

            } catch (e) {
                badge.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Error';
                console.error('Monitoring error:', e);
            } finally {
                isChecking = false;
                countdown = INTERVAL;
            }
        }

        async function fetchRecentNotifications() {
            try {
                const resp = await fetch(NOTIF_URL, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (!resp.ok) {
                    const errorText = await resp.text();
                    console.error('Notification request failed:', resp.status, resp.statusText, errorText);
                    return;
                }

                const data = await resp.json().catch(err => {
                    console.error('Notification JSON parse failed:', err);
                    return null;
                });
                if (!data || !Array.isArray(data.notifications)) {
                    console.error('Invalid notification response:', data);
                    return;
                }

                const notifList = document.getElementById('notif-list');
                if (!notifList) return;

                // Bersihkan list lama
                notifList.innerHTML = '';

                if (data.notifications.length === 0) {
                    notifList.innerHTML = '<li class="list-group-item text-center text-muted small py-4"><i class="bi bi-inbox fs-4 mb-2 d-block text-muted"></i>Belum ada notifikasi</li>';
                    return;
                }

                // Tambahkan notifikasi baru
                data.notifications.forEach(notif => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item py-3 px-4';
                    li.innerHTML = `
                                                                    <div class="d-flex gap-3">
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-${notif.badge_color} wa-badge-clean" style="height:fit-content">
                                                                                ${notif.type_label}
                                                                            </span>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <div class="fw-semibold" style="font-size:13px">${notif.device_name}</div>
                                                                            <div class="d-flex justify-content-between align-items-center mt-1" style="font-size:11px;color:#666">
                                                                                <span>${notif.sent_at}</span>
                                                                                <span class="${notif.status_class}">
                                                                                    ${notif.status_text}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                `;
                    notifList.appendChild(li);
                });

            } catch (e) {
                console.error('Fetch notifications error:', e);
            }
        }

        // Mulai monitoring segera setelah halaman dibuka
        ensureMonitoring();

        // Jika pengguna kembali ke halaman dashboard dari history / cache,
        // jalankan monitoring lagi agar grafik dan data live kembali aktif.
        window.addEventListener('pageshow', () => {
            ensureMonitoring();
            if (chart) {
                chart.resize();
                chart.update();
            }
        });

        // Saat halaman kembali visible, pastikan monitoring berjalan lagi
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                ensureMonitoring();
                if (chart) {
                    chart.resize();
                    chart.update();
                }
            }
        });

        // Jika pengguna kembali ke halaman setelah fokus lost, pastikan monitoring tetap aktif.
        window.addEventListener('focus', () => {
            ensureMonitoring();
        });
    </script>
@endpush