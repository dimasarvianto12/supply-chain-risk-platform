@extends('admin.layout')

@section('title', isset($article) ? 'Edit Artikel' : 'Tambah Artikel')

@section('admin-content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2rem;">
            <i class="fas fa-pen-to-square text-primary"></i> {{ isset($article) ? 'Edit Artikel' : 'Tulis Artikel Baru' }}
        </h2>
        <p class="text-muted mb-0">Tulis dan terbitkan artikel berita internal untuk membagikan pembaruan rantai pasokan logistik.</p>
        <hr class="my-3">
    </div>
</div>

<!-- Form Card -->
<div class="card premium-card">
    <div class="premium-card-body p-4">
        <form method="POST" action="{{ isset($article) ? route('admin.articles.update', $article->id) : route('admin.articles.store') }}">
            @csrf
            @if(isset($article)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">JUDUL ARTIKEL</label>
                <input type="text" name="title" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('title', $article->title ?? '') }}" required placeholder="Masukkan judul berita utama...">
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">NAMA PENULIS (AUTHOR)</label>
                <input type="text" name="author" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" value="{{ old('author', $article->author ?? '') }}" placeholder="contoh: Nama Admin, Departemen Riset (Opsional)">
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">KONTEN ARTIKEL</label>
                <textarea name="content" class="form-control py-2 rounded-3" rows="8" style="border-color: #cbd5e1; box-shadow: none;" required placeholder="Tulis isi berita selengkapnya disini...">{{ old('content', $article->content ?? '') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="fas fa-save me-1.5"></i> Terbitkan Artikel
                </button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-light rounded-pill px-4 fw-bold border" style="border-color:#cbd5e1 !important;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection