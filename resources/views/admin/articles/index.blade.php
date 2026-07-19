@extends('admin.layout')

@section('title', 'Manage Articles')

@section('admin-content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2rem;">
                <i class="fas fa-newspaper text-primary"></i> Kelola Artikel
            </h2>
            <p class="text-muted mb-0">Daftar artikel berita internal yang diterbitkan oleh tim administrator.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-pen-nib"></i> Tulis Artikel
        </a>
    </div>
    <div class="col-12">
        <hr class="my-3">
    </div>
</div>

<!-- Table Card -->
<div class="card premium-card">
    <div class="premium-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px; width: 60px;">#</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px;">Judul Artikel</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px; width: 180px;">Penulis (Author)</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px; width: 180px;">Tanggal Terbit</th>
                        <th class="border-0 px-4 py-3 text-secondary text-uppercase fw-bold text-end" style="font-size:0.75rem; letter-spacing:0.5px; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                    <tr>
                        <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark text-truncate" style="max-width: 320px;" title="{{ $article->title }}">
                                {{ $article->title }}
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold"><i class="far fa-user me-1 text-secondary"></i>{{ $article->author ?? 'System' }}</span>
                        </td>
                        <td class="text-secondary">
                            <i class="far fa-calendar-alt me-1"></i>{{ $article->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 text-end">
                            <div class="d-inline-flex gap-1.5">
                                <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-sm btn-outline-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Artikel">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="d-inline mb-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="return confirm('Hapus artikel ini?')" title="Hapus Artikel">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-newspaper mb-2" style="font-size:2.5rem; color:#dee2e6;"></i>
                            <h5>Belum ada artikel diterbitkan</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $articles->links() }}
</div>
@endsection