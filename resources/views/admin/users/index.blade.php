@extends('admin.layout')

@section('title', 'Manage Users')

@section('admin-content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2rem;">
                <i class="fas fa-users-gear text-primary"></i> Kelola Pengguna
            </h2>
            <p class="text-muted mb-0">Daftar pengguna terdaftar di dalam sistem platform.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-user-plus"></i> Tambah Pengguna
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
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px;">Nama Pengguna</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px;">Email</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px; width: 120px;">Role</th>
                        <th class="border-0 px-4 py-3 text-secondary text-uppercase fw-bold text-end" style="font-size:0.75rem; letter-spacing:0.5px; width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-extrabold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td><code>{{ $user->email }}</code></td>
                        <td>
                            @if($user->is_admin)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold">Admin</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold">User</span>
                            @endif
                        </td>
                        <td class="px-4 text-end">
                            <div class="d-inline-flex gap-1.5">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Pengguna">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline mb-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="return confirm('Hapus pengguna ini dari database?')" title="Hapus Pengguna">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-users mb-2" style="font-size:2.5rem; color:#dee2e6;"></i>
                            <h5>Belum ada pengguna terdaftar</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $users->links() }}
</div>
@endsection