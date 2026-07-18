@extends('admin.layout')

@section('title', 'Manage Ports')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-ship"></i> Manage Ports</h2>
    <a href="{{ route('admin.ports.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Pelabuhan
    </a>
</div>
<hr>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Negara</th>
            <th>Koordinat</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ports as $port)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $port->name }}</td>
            <td>{{ $port->country }}</td>
            <td>{{ $port->latitude }}, {{ $port->longitude }}</td>
            <td>
                <span class="badge {{ $port->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $port->status }}
                </span>
            </td>
            <td>
                <a href="{{ route('admin.ports.edit', $port->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.ports.destroy', $port->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pelabuhan ini?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">Belum ada pelabuhan.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $ports->links() }}
@endsection