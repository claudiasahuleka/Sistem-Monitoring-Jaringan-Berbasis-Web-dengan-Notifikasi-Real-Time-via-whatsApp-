@extends('layouts.app')
@section('title','Edit Perangkat')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4><i class="bi bi-pencil"></i> Edit Perangkat</h4>
    <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('devices.update', $device) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Perangkat <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $device->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">IP Address <span class="text-danger">*</span></label>
                <input type="text" name="ip_address"
                    class="form-control @error('ip_address') is-invalid @enderror"
                    value="{{ old('ip_address', $device->ip_address) }}" required>
                @error('ip_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Lokasi</label>
                <input type="text" name="location" class="form-control"
                    value="{{ old('location', $device->location) }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Perangkat <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type',$device->type)===$type?'selected':'' }}>
                            {{ ucfirst(str_replace('_',' ',$type)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $device->description) }}</textarea>
            </div>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    id="is_active" {{ $device->is_active?'checked':'' }}>
                <label class="form-check-label" for="is_active">Aktif (masuk monitoring)</label>
            </div>

            {{-- Tambahan Pengaturan Deteksi Kepadatan Jaringan --}}
            <hr>
            <h6 class="text-muted mb-3">
                <i class="bi bi-graph-up-arrow"></i> Pengaturan Deteksi Kepadatan Jaringan
            </h6>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Threshold Response Time (ms)</label>
                    <input type="number" name="congestion_threshold_ms"
                        class="form-control form-control-sm"
                        value="{{ old('congestion_threshold_ms', $device->congestion_threshold_ms ?? 150) }}"
                        min="30" max="2000">
                    <div class="form-text">
                        Alert dikirim jika response time melebihi nilai ini secara konsisten.
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Jumlah Cek Berturut-turut</label>
                    <input type="number" name="congestion_check_count"
                        class="form-control form-control-sm"
                        value="{{ old('congestion_check_count', $device->congestion_check_count ?? 5) }}"
                        min="3" max="20">
                    <div class="form-text">
                        Minimal N cek berturut-turut sebelum alert dikirim.
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection