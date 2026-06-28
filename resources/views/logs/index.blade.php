@extends('layouts.app')
@section('title', 'Log Historis ')

@section('content')
    <div class="container-fluid px-0">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
            </div>
        </div>

        <!-- Filter -->
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">Tanggal</label>
                        <input type="date" name="date" class="form-control form-control-sm rounded-3" value="{{ $date }}">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">Perangkat</label>
                        <select name="device_id" class="form-select form-select-sm rounded-3" style="min-width:160px">
                            <option value="">Semua Perangkat</option>
                            @foreach($devices as $d)
                                <option value="{{ $d->id }}" {{ $deviceId == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm rounded-3">
                            <option value="">Semua Status</option>
                            <option value="up" {{ $status === 'up' ? 'selected' : '' }}>Online</option>
                            <option value="down" {{ $status === 'down' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                    <div class="col-md-auto col-sm-12">
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100"
                        style="height:3px;background:linear-gradient(90deg,#0ea5e9,#6366f1)"></div>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                        style="width:44px;height:44px;background:rgba(14,165,233,0.1)">
                        <i class="bi bi-check-circle text-primary fs-4"></i>
                    </div>
                    <div class="fs-3 fw-bold text-dark">{{ $summary['total'] }}</div>
                    <small class="text-secondary">Total Cek</small>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100"
                        style="height:3px;background:linear-gradient(90deg,#22c55e,#16a34a)"></div>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                        style="width:44px;height:44px;background:rgba(34,197,94,0.1)">
                        <i class="bi bi-wifi text-success fs-4"></i>
                    </div>
                    <div class="fs-3 fw-bold text-dark">{{ $summary['up'] }}</div>
                    <small class="text-secondary">Online</small>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100"
                        style="height:3px;background:linear-gradient(90deg,#ef4444,#dc2626)"></div>
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
                        style="width:44px;height:44px;background:rgba(239,68,68,0.1)">
                        <i class="bi bi-wifi-off text-danger fs-4"></i>
                    </div>
                    <div class="fs-3 fw-bold text-dark">{{ $summary['down'] }}</div>
                    <small class="text-secondary">Downtime</small>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row g-3">
            <!-- Log Table -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div
                        class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pengecekan
                            <span
                                class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary ms-2">{{ $logs->total() }}
                                data</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 text-secondary small fw-bold">Waktu</th>
                                        <th class="text-secondary small fw-bold">Perangkat</th>
                                        <th class="text-secondary small fw-bold">IP</th>
                                        <th class="text-secondary small fw-bold">Status</th>
                                        <th class="text-secondary small fw-bold">Kualitas</th>
                                        <th class="text-secondary small fw-bold text-end">Response</th>
                                        <th class="text-secondary small fw-bold text-end pe-3">Packet Loss</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        @php
                                            // ============================================
                                            // LOGIKA KUALITAS & PACKET LOSS - REAL TIME
                                            // Berdasarkan response time (ms)
                                            // ============================================

                                            $responseTime = $log->response_time ?? 0;
                                            $status = $log->status;

                                            // Threshold
                                            $thresholdSlow = 200;      // 200ms+ = Melemah
                                            $thresholdVerySlow = 400;  // 400ms+ = Sangat Lemah

                                            // Jika offline
                                            if ($status === 'down' || $status === 'Offline') {
                                                $qualityLabel = ['label' => '🔴 Terputus', 'cls' => 'danger'];
                                                $packetLoss = 100;
                                                $packetLossClass = 'text-danger fw-bold';
                                                $packetLossLabel = '100%';
                                                $packetLossNote = '(timeout)';
                                                $responseTimeDisplay = '—';
                                                $rowClass = 'table-danger';
                                            }
                                            // Jika online - hitung berdasarkan response time
                                            else {
                                                // --- KUALITAS ---
                                                if ($responseTime >= $thresholdVerySlow) {
                                                    $qualityLabel = ['label' => '🟠 Sangat Lemah', 'cls' => 'danger'];
                                                    $rowClass = 'table-danger';
                                                } elseif ($responseTime >= $thresholdSlow) {
                                                    $qualityLabel = ['label' => '🟡 Melemah', 'cls' => 'warning'];
                                                    $rowClass = 'table-warning';
                                                } else {
                                                    $qualityLabel = ['label' => '✅ Baik', 'cls' => 'success'];
                                                    $rowClass = '';
                                                }

                                                // --- PACKET LOSS (dihitung dari response time) ---
                                                if ($responseTime >= 400) {
                                                    $packetLoss = min(25, 10 + round(($responseTime - 400) / 10));
                                                    $packetLossClass = 'text-danger fw-bold';
                                                } elseif ($responseTime >= 300) {
                                                    $packetLoss = min(10, 5 + round(($responseTime - 300) / 20));
                                                    $packetLossClass = 'text-warning fw-bold';
                                                } elseif ($responseTime >= 200) {
                                                    $packetLoss = min(5, 1 + round(($responseTime - 200) / 25));
                                                    $packetLossClass = 'text-warning';
                                                } else {
                                                    $packetLoss = 0;
                                                    $packetLossClass = 'text-success';
                                                }

                                                $packetLossLabel = $packetLoss . '%';
                                                $packetLossNote = '';
                                                $responseTimeDisplay = $responseTime . ' ms';
                                            }
                                        @endphp

                                        <tr class="{{ $rowClass }}">
                                            <td class="ps-3 text-secondary small">
                                                <i class="bi bi-clock me-1 opacity-50"></i>
                                                {{ $log->checked_at->format('H:i:s') }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="d-inline-flex align-items-center justify-content-center rounded-2"
                                                        style="width:28px;height:28px;background:rgba(14,165,233,0.1)">
                                                        <i class="bi bi-router text-primary small"></i>
                                                    </span>
                                                    <span class="fw-semibold small">{{ $log->device->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <code
                                                    class="text-danger bg-danger bg-opacity-10 px-2 py-1 rounded-2 small">{{ $log->ip_address }}</code>
                                            </td>
                                            <td>
                                                @if($log->status === 'up')
                                                    <span
                                                        class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                        <span class="d-inline-block rounded-circle bg-success me-1"
                                                            style="width:5px;height:5px"></span>Online
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                        <span class="d-inline-block rounded-circle bg-danger me-1"
                                                            style="width:5px;height:5px"></span>Offline
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $qualityLabel['cls'] }} bg-opacity-10 text-{{ $qualityLabel['cls'] }} rounded-pill"
                                                    style="font-size:0.75rem">
                                                    {{ $qualityLabel['label'] }}
                                                </span>
                                            </td>
                                            <td
                                                class="text-end small fw-semibold {{ $responseTime > 300 ? 'text-warning' : 'text-dark' }}">
                                                {{ $responseTimeDisplay }}
                                            </td>
                                            <td class="text-end small pe-3">
                                                <span class="{{ $packetLossClass }}">
                                                    {{ $packetLossLabel }}
                                                    @if($packetLossNote)
                                                        <small class="text-muted"
                                                            style="font-size:0.65rem">{{ $packetLossNote }}</small>
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-secondary">
                                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                                Tidak ada data
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 py-3">
                        {{ $logs->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>

            <!-- Notifikasi WA -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-whatsapp text-success me-2"></i>Notifikasi WA
                            <span class="badge rounded-pill bg-secondary ms-1">{{ $date }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0" style="max-height:500px;overflow-y:auto">
                        <div class="list-group list-group-flush">
                            @forelse($notifications as $notif)
                                <div class="list-group-item py-2 px-3 border-0">
                                    <div class="d-flex gap-2">
                                        <span class="mt-1">
                                            @if($notif->is_sent)
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                            @endif
                                        </span>
                                        <div class="flex-fill">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-{{ $notif->type === 'down' ? 'danger' : 'success' }}"
                                                    style="font-size:0.65rem">{{ $notif->type === 'down' ? 'DOWN' : 'OK' }}</span>
                                                <span class="fw-semibold small">{{ $notif->device->name }}</span>
                                            </div>
                                            <div class="text-secondary mt-1" style="font-size:0.75rem">
                                                <i class="bi bi-clock me-1"></i>{{ $notif->sent_at?->format('H:i:s') }}
                                                @if($notif->is_sent)
                                                    <span class="text-success ms-1"><i class="bi bi-check me-1"></i>Terkirim</span>
                                                @else
                                                    <span class="text-danger ms-1"><i class="bi bi-x me-1"></i>Gagal</span>
                                                @endif
                                            </div>
                                            <div class="text-secondary" style="font-size:0.7rem">ke: {{ $notif->wa_number }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted py-4">
                                    <i class="bi bi-chat-square-text fs-1 d-block mb-2 opacity-50"></i>
                                    Belum ada notifikasi pada tanggal ini
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
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