@extends('layouts.app')
@section('title', 'Daftar Perangkat')

@section('content')
    <div class="container-fluid px-0">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
            </div>
            <div class="d-flex gap-2">

                <a href="{{ route('devices.create') }}" class="btn btn-primary btn-sm rounded-3 px-3">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Perangkat
                </a>
            </div>
        </div>

        <!-- Stats Cards -->


        <!-- Table Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div
                class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-list-ul text-primary me-2"></i>Data Perangkat
                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary ms-2">{{ $devices->count() }}
                        perangkat</span>
                </h6>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm rounded-3" placeholder="Cari perangkat..."
                        style="width:200px" onkeyup="filterTable(this.value)">
                    <button class="btn btn-outline-secondary btn-sm rounded-3" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="deviceTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 text-secondary small fw-bold" style="width:50px">#</th>
                                <th class="text-secondary small fw-bold">Nama</th>
                                <th class="text-secondary small fw-bold">IP Address</th>
                                <th class="text-secondary small fw-bold">Lokasi</th>
                                <th class="text-secondary small fw-bold">Jenis</th>
                                <th class="text-secondary small fw-bold">Status</th>
                                <th class="text-secondary small fw-bold text-end">Latency</th>
                                <th class="text-secondary small fw-bold text-center">Aktif</th>
                                <th class="text-secondary small fw-bold text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devices as $device)
                                <tr id="device-row-{{ $device->id }}">
                                    <td class="ps-3 fw-bold text-secondary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-2"
                                                style="width:32px;height:32px;background:rgba(14,165,233,0.1)">
                                                <i class="bi bi-hdd-network text-primary"></i>
                                            </span>
                                            <span class="fw-semibold">{{ $device->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <code
                                            class="text-danger bg-danger bg-opacity-10 px-2 py-1 rounded-2 small">{{ $device->ip_address }}</code>
                                    </td>
                                    <td>
                                        <span class="text-secondary small"><i
                                                class="bi bi-geo-alt text-primary me-1"></i>{{ $device->location ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">{{ $device->type }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge rounded-pill bg-{{ $device->status_badge }} bg-opacity-10 text-{{ $device->status_badge }} border border-{{ $device->status_badge }} border-opacity-25"
                                            id="badge-{{ $device->id }}">
                                            <span class="d-inline-block rounded-circle bg-{{ $device->status_badge }} me-1"
                                                style="width:5px;height:5px"></span>
                                            {{ $device->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end small fw-semibold" id="rt-{{ $device->id }}">
                                        {{ $device->response_time ? $device->response_time . ' ms' : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill bg-{{ $device->is_active ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $device->is_active ? 'success' : 'secondary' }}">
                                            {{ $device->is_active ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center gap-1">
                                            {{-- Ping Sekarang --}}
                                            <button class="btn btn-info btn-sm btn-ping rounded-2 text-white"
                                                data-id="{{ $device->id }}" data-url="{{ route('api.devices.ping', $device) }}"
                                                title="Ping sekarang" style="padding:0.25rem 0.5rem">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            {{-- Detail --}}
                                            <a href="{{ route('devices.show', $device) }}"
                                                class="btn btn-outline-primary btn-sm rounded-2" title="Detail"
                                                style="padding:0.25rem 0.5rem">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            {{-- Edit --}}
                                            <a href="{{ route('devices.edit', $device) }}"
                                                class="btn btn-outline-warning btn-sm rounded-2" title="Edit"
                                                style="padding:0.25rem 0.5rem">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            {{-- Hapus --}}
                                            <form action="{{ route('devices.destroy', $device) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Hapus perangkat {{ $device->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-2"
                                                    title="Hapus" style="padding:0.25rem 0.5rem">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-between align-items-center">
                <span class="text-secondary small">Menampilkan {{ $devices->count() }} dari {{ $devices->total() }}
                    perangkat</span>
                {{ $devices->links() }}
            </div>
        </div>

    </div>

    {{-- Toast notifikasi ping --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="pingToast" class="toast" role="alert">
            <div class="toast-header">
                <strong class="me-auto">Hasil Ping</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="pingToastBody"></div>
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

@push('scripts')
    <script>
        const toast = new bootstrap.Toast(document.getElementById('pingToast'));
        const toastBody = document.getElementById('pingToastBody');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // Filter table
        function filterTable(query) {
            const rows = document.querySelectorAll('#deviceTable tbody tr');
            query = query.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        document.querySelectorAll('.btn-ping').forEach(btn => {
            btn.addEventListener('click', async function () {
                const id = this.dataset.id;
                const url = this.dataset.url;

                if (!url) {
                    toastBody.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>URL ping tidak tersedia</span>';
                    toast.show();
                    return;
                }

                // Tampilkan loading
                this.disabled = true;
                const originalHtml = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                try {
                    console.log('Ping ke:', url);
                    const resp = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        }
                    });

                    console.log('Response status:', resp.status);

                    if (!resp.ok) {
                        const errorText = await resp.text();
                        console.error('Error response:', errorText);
                        throw new Error('HTTP ' + resp.status + ': ' + errorText);
                    }

                    const data = await resp.json();
                    console.log('Response data:', data);

                    // Update badge dan response time di baris
                    const badge = document.getElementById('badge-' + id);
                    const rt = document.getElementById('rt-' + id);
                    const row = document.getElementById('device-row-' + id);

                    if (badge) {
                        badge.className = 'badge rounded-pill bg-' + data.badge + ' bg-opacity-10 text-' + data.badge + ' border border-' + data.badge + ' border-opacity-25';
                        badge.innerHTML = '<span class="d-inline-block rounded-circle bg-' + data.badge + ' me-1" style="width:5px;height:5px"></span>' + (data.status === 'up' ? 'Online' : 'Offline');
                    }
                    if (rt) rt.textContent = data.response_time ? data.response_time + ' ms' : '-';
                    if (row) row.className = data.status === 'down' ? 'table-danger' : '';

                    // Toast
                    const icon = data.status === 'up' ? '<i class="bi bi-check-circle text-success me-1"></i>' : '<i class="bi bi-exclamation-triangle text-warning me-1"></i>';
                    toastBody.innerHTML = icon + (data.message || 'Ping selesai') +
                        (data.response_time ? ' (' + data.response_time + ' ms)' : '');
                    toast.show();

                } catch (e) {
                    console.error('Ping error:', e);
                    toastBody.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Gagal ping: ' + e.message + '</span>';
                    toast.show();
                }

                // Kembalikan tombol
                this.disabled = false;
                this.innerHTML = originalHtml;
            });
        });
    </script>
@endpush