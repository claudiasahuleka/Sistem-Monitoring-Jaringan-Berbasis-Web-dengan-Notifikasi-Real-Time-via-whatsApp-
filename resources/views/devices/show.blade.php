@extends('layouts.app')
@section('title', 'Detail Perangkat')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4><i class="bi bi-hdd-network"></i> Detail: {{ $device->name }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('devices.edit', $device) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            {{-- HAPUS IKON PANAH - hanya teks --}}
            <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary btn-sm">
                Kembali
            </a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Info Perangkat</h6>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted">IP Address</td>
                            <td><code>{{ $device->ip_address }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Lokasi</td>
                            <td>{{ $device->location ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis</td>
                            <td>{{ $device->type }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-{{ $device->status_badge }}">{{ $device->status_label }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Response</td>
                            <td>{{ $device->response_time ? $device->response_time . ' ms' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Uptime 7 hari</td>
                            <td><strong>{{ $uptimePercentage }}%</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-2">
                    <strong>Riwayat Monitoring Terbaru</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Response</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->checked_at->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->status === 'up' ? 'success' : 'danger' }}">
                                            {{ $log->status === 'up' ? 'Online' : 'Offline' }}
                                        </span>
                                    </td>
                                    <td>{{ $log->response_time ? $log->response_time . ' ms' : '-' }}</td>
                                    <td class="text-muted">{{ $log->notes ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- GUNAKAN PAGINATION CUSTOM TANPA IKON --}}
                <div class="card-footer bg-white">{{ $logs->links('vendor.pagination.custom') }}</div>
            </div>
        </div>
    </div>
@endsection