@extends('admin.layout')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('admin-content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2rem;">
            <i class="fas fa-user-pen text-primary"></i> {{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
        </h2>
        <p class="text-muted mb-0">Isi data akun pengguna dengan benar untuk mengatur hak akses sistem.</p>
        <hr class="my-3">
    </div>
</div>

<!-- Form Card -->
<div class="card premium-card">
    <div class="premium-card-body p-4">
        <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">NAMA PENGGUNA</label>
                <input type="text" name="name" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('name', $user->name ?? '') }}" required placeholder="Masukkan nama lengkap...">
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">ALAMAT EMAIL</label>
                <input type="email" name="email" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('email', $user->email ?? '') }}" required placeholder="contoh: user@domain.com">
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">KATA SANDI (PASSWORD)</label>
                <input type="password" name="password" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" {{ isset($user) ? '' : 'required' }} placeholder="{{ isset($user) ? 'Masukkan sandi baru jika ingin diubah...' : 'Masukkan kata sandi akun...' }}">
                @if(isset($user))
                    <small class="text-muted"><i class="fas fa-circle-info me-1 mt-1"></i>Kosongkan kolom sandi jika Anda tidak ingin mengganti kata sandi lama.</small>
                @endif
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">HAK AKSES / ROLE</label>
                <select name="is_admin" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                    <option value="0" {{ old('is_admin', $user->is_admin ?? 0) == 0 ? 'selected' : '' }}>User Biasa (Pemantauan Saja)</option>
                    <option value="1" {{ old('is_admin', $user->is_admin ?? 0) == 1 ? 'selected' : '' }}>Administrator (Akses Penuh Kontrol Panel)</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="fas fa-save me-1.5"></i> Simpan Data
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-pill px-4 fw-bold border" style="border-color:#cbd5e1 !important;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection