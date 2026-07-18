@extends('admin.layout')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('admin-content')
<h2>{{ isset($user) ? 'Edit User' : 'Tambah User' }}</h2>
<hr>

<form method="POST" action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
    @csrf
    @if(isset($user)) @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
        @if(isset($user))
            <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
        @endif
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="is_admin" class="form-check-input" id="isAdmin" value="1" {{ old('is_admin', $user->is_admin ?? 0) ? 'checked' : '' }}>
        <label class="form-check-label" for="isAdmin">Admin</label>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
</form>
@endsection