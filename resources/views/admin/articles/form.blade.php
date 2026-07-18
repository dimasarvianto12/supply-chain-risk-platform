@extends('admin.layout')

@section('title', isset($article) ? 'Edit Artikel' : 'Tambah Artikel')

@section('admin-content')
<h2>{{ isset($article) ? 'Edit Artikel' : 'Tambah Artikel' }}</h2>
<hr>

<form method="POST" action="{{ isset($article) ? route('admin.articles.update', $article->id) : route('admin.articles.store') }}">
    @csrf
    @if(isset($article)) @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Judul</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $article->title ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Author</label>
        <input type="text" name="author" class="form-control" value="{{ old('author', $article->author ?? '') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Konten</label>
        <textarea name="content" class="form-control" rows="6" required>{{ old('content', $article->content ?? '') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Batal</a>
</form>
@endsection