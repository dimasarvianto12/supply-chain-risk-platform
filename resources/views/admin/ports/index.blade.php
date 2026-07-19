@extends('admin.layout')

@section('title', 'Manage Ports')

@section('admin-content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2rem;">
                <i class="fas fa-ship text-primary"></i> Kelola Pelabuhan
            </h2>
            <p class="text-muted mb-0">Manajemen pelabuhan pengiriman logistik global dalam pantauan sistem.</p>
        </div>
        <a href="{{ route('admin.ports.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-circle-plus"></i> Tambah Pelabuhan
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
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px;">Nama Pelabuhan</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px;">Negara</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px;">Koordinat</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px; width: 140px;">Kemacetan</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px; width: 100px;">Delay</th>
                        <th class="border-0 py-3 text-secondary text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing:0.5px; width: 100px;">Status</th>
                        <th class="border-0 px-4 py-3 text-secondary text-uppercase fw-bold text-end" style="font-size:0.75rem; letter-spacing:0.5px; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ports as $port)
                    @php
                        $congestion = strtolower($port->congestion_level ?? 'lancar');
                        $congClass = match($congestion) {
                            'high', 'macet' => 'bg-danger-subtle text-danger border border-danger-subtle',
                            'medium', 'sedang' => 'bg-warning-subtle text-warning border border-warning-subtle',
                            default => 'bg-success-subtle text-success border border-success-subtle'
                        };
                    @endphp
                    <tr>
                        <td class="px-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $port->name }}</td>
                        <td>{{ $port->country }}</td>
                        <td><span class="font-monospace text-secondary" style="font-size:0.85rem;">{{ number_format($port->latitude, 4) }}, {{ number_format($port->longitude, 4) }}</span></td>
                        <td>
                            <span class="badge {{ $congClass }} rounded-pill px-2.5 py-1 fw-bold">
                                {{ strtoupper($port->congestion_level ?? 'lancar') }}
                            </span>
                        </td>
                        <td class="fw-extrabold text-danger">{{ $port->delay_days ?? 0 }} Hari</td>
                        <td>
                            @if($port->status == 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold">Aktif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold">Non-aktif</span>
                            @endif
                        </td>
                        <td class="px-4 text-end">
                            <div class="d-inline-flex gap-1.5">
                                <a href="{{ route('admin.ports.edit', $port->id) }}" class="btn btn-sm btn-outline-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Pelabuhan">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.ports.destroy', $port->id) }}" method="POST" class="d-inline mb-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="return confirm('Hapus pelabuhan ini?')" title="Hapus Pelabuhan">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-ship mb-2" style="font-size:2.5rem; color:#dee2e6;"></i>
                            <h5>Belum ada pelabuhan terdaftar</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $ports->links() }}
</div>
@endsection