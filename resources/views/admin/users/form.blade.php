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

@if(isset($user))
<div class="card premium-card mt-4">
    <div class="premium-card-header bg-light border-bottom">
        <i class="fas fa-star text-warning"></i>
        <span class="text-dark fw-bold">Daftar Negara Favorit Pengguna</span>
    </div>
    <div class="card-body p-0">
        @if($user->watchlists->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted" style="font-size: 0.85rem;">NEGARA</th>
                        <th class="px-4 py-3 text-muted" style="font-size: 0.85rem;">DITAMBAHKAN PADA</th>
                        <th class="px-4 py-3 text-muted" style="font-size: 0.85rem;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->watchlists as $watchlist)
                    <tr>
                        <td class="px-4 py-3 fw-bold">
                            <span class="badge bg-info text-dark fw-bold px-3 py-2 rounded-pill">
                                <i class="fas fa-flag me-1"></i> {{ $watchlist->country->name ?? 'Negara Dihapus' }} ({{ $watchlist->country->code ?? 'N/A' }})
                            </span>
                        </td>
                        <td class="px-4 py-3 text-muted" style="font-size: 0.9rem;">
                            <i class="far fa-clock me-1"></i> {{ $watchlist->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.favorites.destroy', $watchlist->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hak pantau negara ini dari favorit pengguna?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus dari daftar pantau pengguna">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-folder-open mb-3" style="font-size: 2.5rem; opacity: 0.5;"></i>
            <h6 class="fw-bold">Belum Ada Favorit</h6>
            <p class="mb-0" style="font-size: 0.9rem;">Pengguna ini belum menambahkan satu pun negara ke dalam daftar pantauannya.</p>
        </div>
        @endif
    </div>
</div>
@endif
@endsection