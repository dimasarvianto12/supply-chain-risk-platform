@extends('admin.layout')

@section('title', 'Kelola Favorit Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800 d-flex align-items-center gap-2">
            <i class="fas fa-star text-primary"></i> Kelola Favorit Pengguna
        </h2>
        <p class="text-muted mb-0 mt-1">Daftar negara yang dipantau oleh pengguna sistem platform.</p>
    </div>
</div>

<div class="card premium-card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted" style="font-size: 0.85rem; letter-spacing: 1px;">#</th>
                        <th class="px-4 py-3 text-muted" style="font-size: 0.85rem; letter-spacing: 1px;">PENGGUNA</th>
                        <th class="px-4 py-3 text-muted" style="font-size: 0.85rem; letter-spacing: 1px;">NEGARA FAVORIT</th>
                        <th class="px-4 py-3 text-muted" style="font-size: 0.85rem; letter-spacing: 1px;">DITAMBAHKAN PADA</th>
                        <th class="px-4 py-3 text-muted" style="font-size: 0.85rem; letter-spacing: 1px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($favorites as $index => $favorite)
                    <tr>
                        <td class="px-4 py-3 text-muted fw-bold">{{ $favorites->firstItem() + $index }}</td>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-weight: bold;">
                                    {{ strtoupper(substr($favorite->user->name ?? '?', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $favorite->user->name ?? 'User Dihapus' }}</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">{{ $favorite->user->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-info text-dark fw-bold px-3 py-2 rounded-pill">
                                <i class="fas fa-flag me-1"></i> {{ $favorite->country->name ?? 'Negara Dihapus' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-muted" style="font-size: 0.9rem;">
                            <i class="far fa-clock me-1"></i> {{ $favorite->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.favorites.destroy', $favorite->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data favorit pengguna ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="fw-bold">Belum ada data favorit</h5>
                            <p>Saat ini belum ada pengguna yang menambahkan negara ke daftar favorit.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($favorites->hasPages())
    <div class="card-footer bg-white border-0 py-3 px-4">
        {{ $favorites->links() }}
    </div>
    @endif
</div>
@endsection
