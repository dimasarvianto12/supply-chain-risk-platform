@extends('admin.layout')

@section('title', isset($port) ? 'Edit Pelabuhan' : 'Tambah Pelabuhan')

@section('admin-content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2rem;">
            <i class="fas fa-ship text-primary"></i> {{ isset($port) ? 'Edit Pelabuhan' : 'Tambah Pelabuhan Baru' }}
        </h2>
        <p class="text-muted mb-0">Isi data koordinat dan informasi tingkat kepadatan operasional pelabuhan logistik.</p>
        <hr class="my-3">
    </div>
</div>

<!-- Form Card -->
<div class="card premium-card">
    <div class="premium-card-body p-4">
        <form method="POST" action="{{ isset($port) ? route('admin.ports.update', $port->id) : route('admin.ports.store') }}">
            @csrf
            @if(isset($port)) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">NAMA PELABUHAN</label>
                    <input type="text" name="name" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('name', $port->name ?? '') }}" required placeholder="Masukkan nama pelabuhan...">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">NEGARA KEDUDUKAN</label>
                    <input type="text" name="country" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('country', $port->country ?? '') }}" required placeholder="Masukkan nama negara asal...">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">GARIS LINTANG (LATITUDE)</label>
                    <input type="number" step="0.000001" name="latitude" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('latitude', $port->latitude ?? '') }}" required placeholder="contoh: -37.8136">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">GARIS BUJUR (LONGITUDE)</label>
                    <input type="number" step="0.000001" name="longitude" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('longitude', $port->longitude ?? '') }}" required placeholder="contoh: 144.9631">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">STATUS OPERASIONAL</label>
                    <select name="status" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                        <option value="active" {{ old('status', $port->status ?? '') == 'active' ? 'selected' : '' }}>Aktif (Active)</option>
                        <option value="inactive" {{ old('status', $port->status ?? '') == 'inactive' ? 'selected' : '' }}>Non-aktif (Inactive)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">TINGKAT KEMACETAN</label>
                    <select name="congestion_level" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                        <option value="low" {{ old('congestion_level', $port->congestion_level ?? '') == 'low' ? 'selected' : '' }}>LOW (Lancar)</option>
                        <option value="medium" {{ old('congestion_level', $port->congestion_level ?? '') == 'medium' ? 'selected' : '' }}>MEDIUM (Sedang)</option>
                        <option value="high" {{ old('congestion_level', $port->congestion_level ?? '') == 'high' ? 'selected' : '' }}>HIGH (Macet)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-4">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">DELAY KETERLAMBATAN (HARI)</label>
                    <input type="number" name="delay_days" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('delay_days', $port->delay_days ?? 0) }}" required min="0" placeholder="Jumlah hari delay...">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="fas fa-save me-1.5"></i> Simpan Pelabuhan
                </button>
                <a href="{{ route('admin.ports.index') }}" class="btn btn-light rounded-pill px-4 fw-bold border" style="border-color:#cbd5e1 !important;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection