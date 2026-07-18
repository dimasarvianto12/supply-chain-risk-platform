@extends('admin.layout')

@section('title', isset($port) ? 'Edit Pelabuhan' : 'Tambah Pelabuhan')

@section('admin-content')
<h2>{{ isset($port) ? 'Edit Pelabuhan' : 'Tambah Pelabuhan' }}</h2>
<hr>

<form method="POST" action="{{ isset($port) ? route('admin.ports.update', $port->id) : route('admin.ports.store') }}">
    @csrf
    @if(isset($port)) @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Nama Pelabuhan</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $port->name ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Negara</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $port->country ?? '') }}" required>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Latitude</label>
                <input type="number" step="0.000001" name="latitude" class="form-control" value="{{ old('latitude', $port->latitude ?? '') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Longitude</label>
                <input type="number" step="0.000001" name="longitude" class="form-control" value="{{ old('longitude', $port->longitude ?? '') }}" required>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" {{ old('status', $port->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $port->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.ports.index') }}" class="btn btn-secondary">Batal</a>
</form>
@endsection