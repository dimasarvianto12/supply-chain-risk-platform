@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('admin-content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2rem;">
            <i class="fas fa-chart-line text-primary"></i> Control Center Dashboard
        </h2>
        <p class="text-muted mb-0">Overview status dan manajemen operasional sistem Supply Chain Risk Platform.</p>
        <hr class="my-3">
    </div>
</div>

<!-- Metrics Cards -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="card premium-card metric-card gradient-purple p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="metric-title">TOTAL USERS</div>
                    <div class="metric-value">{{ $totalUsers }}</div>
                </div>
                <div class="metric-icon-wrapper">
                    <i class="fas fa-users text-white"></i>
                </div>
            </div>
            <div class="metric-desc">Akun pengguna terdaftar dalam sistem</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card premium-card metric-card gradient-blue p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="metric-title">TOTAL PORTS</div>
                    <div class="metric-value">{{ $totalPorts }}</div>
                </div>
                <div class="metric-icon-wrapper">
                    <i class="fas fa-ship text-white"></i>
                </div>
            </div>
            <div class="metric-desc">Pelabuhan pengiriman logistik terpantau</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card premium-card metric-card gradient-orange p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="metric-title">TOTAL ARTICLES</div>
                    <div class="metric-value">{{ $totalArticles }}</div>
                </div>
                <div class="metric-icon-wrapper">
                    <i class="fas fa-newspaper text-white"></i>
                </div>
            </div>
            <div class="metric-desc">Artikel berita yang dipublikasikan</div>
        </div>
    </div>
</div>

<!-- Detailed Data Tables & Feeds -->
<div class="row g-4 mt-3">
    <!-- Left Column: Tables -->
    <div class="col-lg-8">
        <!-- Recent Users Table -->
        <div class="card premium-card mb-4">
            <div class="premium-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-user-clock text-primary"></i>
                    <span>Pendaftaran Pengguna Baru</span>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold text-primary">
                    Kelola Pengguna
                </a>
            </div>
            <div class="premium-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Nama</th>
                                <th class="border-0 py-3">Email</th>
                                <th class="border-0 py-3">Role</th>
                                <th class="border-0 px-4 py-3">Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $user)
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <span class="fw-bold text-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td><code>{{ $user->email }}</code></td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 fw-bold">Admin</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 fw-bold">User</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-muted">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada pengguna terdaftar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Congested Ports Table -->
        <div class="card premium-card">
            <div class="premium-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-danger"></i>
                    <span>Tingkat Keterlambatan Pelabuhan Tertinggi</span>
                </div>
                <a href="{{ route('admin.ports.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold text-primary">
                    Kelola Pelabuhan
                </a>
            </div>
            <div class="premium-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Nama Pelabuhan</th>
                                <th class="border-0 py-3">Negara</th>
                                <th class="border-0 py-3">Tingkat Kemacetan</th>
                                <th class="border-0 px-4 py-3">Keterlambatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($congestedPorts as $port)
                                @php
                                    $congestion = strtolower($port->congestion_level ?? 'lancar');
                                    $badgeClass = match($congestion) {
                                        'high', 'macet' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        'medium', 'sedang' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                        default => 'bg-success-subtle text-success border border-success-subtle'
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 fw-bold text-dark">{{ $port->name }}</td>
                                    <td>{{ $port->country }}</td>
                                    <td>
                                        <span class="badge {{ $badgeClass }} rounded-pill px-2.5 py-1 fw-bold">
                                            {{ strtoupper($port->congestion_level ?? 'lancar') }}
                                        </span>
                                    </td>
                                    <td class="px-4 fw-extrabold text-danger">{{ $port->delay_days ?? 0 }} Hari</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Data pelabuhan kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Actions & Feed -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card premium-card mb-4">
            <div class="premium-card-header">
                <i class="fas fa-gears text-primary"></i>
                <span>Tindakan Cepat</span>
            </div>
            <div class="premium-card-body d-flex flex-column gap-2.5">
                <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary text-start d-flex align-items-center gap-2 rounded-3 py-2 fw-bold w-100">
                    <i class="fas fa-user-plus"></i> Tambah Pengguna Baru
                </a>
                <a href="{{ route('admin.ports.create') }}" class="btn btn-outline-success text-start d-flex align-items-center gap-2 rounded-3 py-2 fw-bold w-100">
                    <i class="fas fa-circle-plus"></i> Tambah Pelabuhan Baru
                </a>
                <a href="{{ route('admin.articles.create') }}" class="btn btn-outline-warning text-start d-flex align-items-center gap-2 rounded-3 py-2 fw-bold w-100 text-dark">
                    <i class="fas fa-pen-nib"></i> Tulis Artikel Berita Baru
                </a>
            </div>
        </div>

        <!-- Latest Articles Feed -->
        <div class="card premium-card">
            <div class="premium-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-rss text-primary"></i>
                    <span>Berita Terbit Terbaru</span>
                </div>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-sm btn-light rounded-pill px-2.5 py-0.5 fw-bold" style="font-size:0.75rem;">
                    Lihat Semua
                </a>
            </div>
            <div class="premium-card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($recentArticles as $article)
                        @php
                            $sentiment = strtolower($article->sentiment ?? 'neutral');
                            $borderLeft = match($sentiment) {
                                'positive' => 'border-left: 3px solid #10b981;',
                                'negative' => 'border-left: 3px solid #ef4444;',
                                default => 'border-left: 3px solid #64748b;'
                            };
                        @endphp
                        <div class="p-3 bg-light rounded-3 d-flex flex-column gap-1" style="{{ $borderLeft }}">
                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $article->title }}" style="font-size:0.9rem;">
                                {{ $article->title }}
                            </h6>
                            <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                <span class="fw-bold"><i class="far fa-user me-1"></i>{{ $article->author ?? 'System' }}</span>
                                <span><i class="far fa-calendar me-1"></i>{{ $article->created_at ? $article->created_at->format('d M Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-3 text-muted mb-0" style="font-size: 0.9rem;">Belum ada artikel diterbitkan</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection