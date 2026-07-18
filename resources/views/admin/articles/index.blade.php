@extends('admin.layout')

@section('title', 'Manage Articles')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-newspaper"></i> Manage Articles</h2>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Artikel
    </a>
</div>
<hr>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Judul</th>
            <th>Author</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($articles as $article)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $article->title }}</td>
            <td>{{ $article->author ?? '-' }}</td>
            <td>{{ $article->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus artikel ini?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">Belum ada artikel.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $articles->links() }}
@endsection