@extends('layouts.app')

@section('title', 'Country Dashboard')

@push('styles')
<style>
    .country-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }
    .info-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #f1f3f9;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .info-item:hover {
        background: #f1f5f9;
        border-color: #e2e8f0;
    }
    .info-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .info-icon.blue { background: #e0f2fe; color: #0284c7; }
    .info-icon.green { background: #dcfce7; color: #16a34a; }
    .info-icon.orange { background: #fef3c7; color: #d97706; }
    .info-icon.purple { background: #f3e8ff; color: #7c3aed; }
    
    .info-content {
        display: flex;
        flex-direction: column;
    }
    .info-label {
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .info-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-flag text-primary"></i> Global Country Dashboard
        </h1>
        <p class="text-muted fs-5 mb-0">Pilih negara untuk melihat data lengkap: ekonomi, cuaca, kurs, dan risiko.</p>
        <hr>
    </div>
</div>

<div class="row">
    <!-- Sidebar Selector -->
    <div class="col-lg-4 mb-4 mb-lg-0">
        <div class="card premium-card">
            <div class="premium-card-header">
                <i class="fas fa-search text-primary"></i>
                <span>Pilih Negara</span>
            </div>
            <div class="premium-card-body">
                <select class="form-select form-select-lg py-3 rounded-3" id="countrySelect" style="font-size: 1rem; border-color: #cbd5e1; box-shadow: none;">
                    <option value="">-- Pilih Negara --</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->code }}">{{ $country->name }} ({{ $country->code }})</option>
                    @endforeach
                </select>
                <div id="loadingIndicator" class="mt-4 text-center d-none">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Menghubungi API & database...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Country Detail Viewer -->
    <div class="col-lg-8">
        <div id="countryDetail">
            <div class="card premium-card">
                <div class="premium-card-body text-center text-muted py-5">
                    <i class="fas fa-globe-asia text-secondary mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h5 class="fw-bold text-dark">Detail Negara</h5>
                    <p class="mb-0 text-muted">Silakan pilih negara dari dropdown di samping untuk melihat analisis risiko rantai pasok.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('countrySelect');
    const detailContainer = document.getElementById('countryDetail');
    const loadingIndicator = document.getElementById('loadingIndicator');

    select.addEventListener('change', function() {
        const countryCode = this.value;
        
        if (!countryCode) {
            detailContainer.innerHTML = `
                <div class="card premium-card">
                    <div class="premium-card-body text-center text-muted py-5">
                        <i class="fas fa-globe-asia text-secondary mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                        <h5 class="fw-bold text-dark">Detail Negara</h5>
                        <p class="mb-0 text-muted">Silakan pilih negara dari dropdown di samping untuk melihat analisis risiko rantai pasok.</p>
                    </div>
                </div>
            `;
            return;
        }

        loadingIndicator.classList.remove('d-none');
        detailContainer.innerHTML = '';

        fetch(`/api/country/${countryCode}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Country not found');
                }
                return response.json();
            })
            .then(data => {
                loadingIndicator.classList.add('d-none');
                renderCountryDetail(data);
            })
            .catch(error => {
                loadingIndicator.classList.add('d-none');
                detailContainer.innerHTML = `
                    <div class="card premium-card border border-danger">
                        <div class="premium-card-body text-center text-danger py-5">
                            <i class="fas fa-exclamation-circle mb-3" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold">Gagal Memuat Data</h5>
                            <p class="mb-0 text-muted">${error.message}</p>
                        </div>
                    </div>
                `;
            });
    });

    function renderCountryDetail(data) {
        const population = data.population ? new Intl.NumberFormat('id-ID').format(data.population) : 'N/A';
        
        // Ekonomi
        const economic = data.economic || {};
        const gdp = economic.gdp != null 
            ? new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'USD',
                maximumFractionDigits: 0 
              }).format(economic.gdp) 
            : 'N/A';
        const inflation = economic.inflation != null ? economic.inflation + '%' : 'N/A';
        const year = economic.year || 'N/A';

        // Kurs
        const rate = data.currency_rate?.rate != null 
            ? new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              }).format(data.currency_rate.rate) 
            : 'N/A';

        // Risiko
        const risk = data.risk || {};
        let riskBadge = '';
        if (risk.total != null) {
            const total = risk.total;
            let color = 'success';
            let label = 'Rendah';
            let subClass = 'bg-success-subtle text-success border border-success-subtle';
            if (total > 60) { 
                color = 'danger'; 
                label = 'Tinggi'; 
                subClass = 'bg-danger-subtle text-danger border border-danger-subtle';
            } else if (total > 30) { 
                color = 'warning'; 
                label = 'Sedang'; 
                subClass = 'bg-warning-subtle text-warning border border-warning-subtle';
            }
            riskBadge = `<span class="badge ${subClass} px-3 py-2 rounded-pill fw-bold fs-6 ms-2 align-middle">${label} (${total}%)</span>`;
        }

        // Cuaca
        const weather = data.weather || {};
        const isFavorited = data.is_favorited || false;

        // Render HTML premium card-based layout (no more messy tables)
        detailContainer.innerHTML = `
            <div class="card premium-card">
                <div class="premium-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h3 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        ${data.flag ? `<img src="${data.flag}" alt="${data.name}" style="height:26px; border-radius:4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-right:12px;">` : ''}
                        <span>${data.name} (${data.code})</span>
                        ${riskBadge}
                    </h3>
                    <button id="favoriteBtn" class="btn ${isFavorited ? 'btn-danger' : 'btn-outline-danger'} rounded-pill px-3">
                        <i class="${isFavorited ? 'fas' : 'far'} fa-star me-1"></i>
                        ${isFavorited ? 'Hapus dari Favorit' : 'Tambah ke Favorit'}
                    </button>
                </div>
                <div class="premium-card-body">
                    
                    <!-- Tab 1: Informasi Umum -->
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-building text-primary me-2"></i> Informasi Umum</h5>
                    <div class="country-grid mb-4">
                        <div class="info-item">
                            <div class="info-icon blue"><i class="fas fa-city"></i></div>
                            <div class="info-content">
                                <span class="info-label">Ibukota</span>
                                <span class="info-value">${data.capital || 'N/A'}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon blue"><i class="fas fa-users"></i></div>
                            <div class="info-content">
                                <span class="info-label">Populasi</span>
                                <span class="info-value">${population}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon blue"><i class="fas fa-coins"></i></div>
                            <div class="info-content">
                                <span class="info-label">Mata Uang</span>
                                <span class="info-value">${data.currency || 'N/A'}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon blue"><i class="fas fa-map-marked-alt"></i></div>
                            <div class="info-content">
                                <span class="info-label">Region</span>
                                <span class="info-value">${data.region || 'N/A'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Ekonomi & Kurs -->
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-chart-line text-success me-2"></i> Parameter Ekonomi & Kurs</h5>
                    <div class="country-grid mb-4">
                        <div class="info-item">
                            <div class="info-icon green"><i class="fas fa-dollar-sign"></i></div>
                            <div class="info-content">
                                <span class="info-label">GDP (PDB)</span>
                                <span class="info-value">${gdp}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon green"><i class="fas fa-percentage"></i></div>
                            <div class="info-content">
                                <span class="info-label">Inflasi</span>
                                <span class="info-value">${inflation}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon green"><i class="fas fa-calendar-alt"></i></div>
                            <div class="info-content">
                                <span class="info-label">Tahun Data</span>
                                <span class="info-value">${year}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon green"><i class="fas fa-money-bill-transfer"></i></div>
                            <div class="info-content">
                                <span class="info-label">Nilai Tukar (${data.currency_rate?.base || 'USD'})</span>
                                <span class="info-value">${rate}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Tab 3: Cuaca Terkini -->
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="card bg-light border-0 rounded-4 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-cloud-sun text-warning me-2"></i> Cuaca Terkini</h6>
                                ${weather.temperature != null ? `
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex justify-content-between border-bottom pb-2">
                                            <span class="text-muted">Suhu</span>
                                            <strong class="text-dark">${weather.temperature} °C</strong>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-2">
                                            <span class="text-muted">Kelembaban</span>
                                            <strong class="text-dark">${weather.humidity}%</strong>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-2">
                                            <span class="text-muted">Kecepatan Angin</span>
                                            <strong class="text-dark">${weather.wind_speed} km/h</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Kondisi</span>
                                            <strong class="text-dark text-capitalize">${weather.description || 'N/A'}</strong>
                                        </div>
                                    </div>
                                ` : '<p class="text-muted text-center py-4">Data cuaca tidak tersedia</p>'}
                            </div>
                        </div>

                        <!-- Tab 4: Skor Risiko Detail -->
                        <div class="col-md-6">
                            <div class="card bg-light border-0 rounded-4 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Analisis Risiko Rantai Pasok</h6>
                                ${risk.total != null ? `
                                    <div class="d-flex flex-column gap-3">
                                        <!-- Weather Risk -->
                                        <div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted" style="font-size: 0.85rem;">Risiko Cuaca</span>
                                                <strong class="text-dark" style="font-size: 0.85rem;">${risk.weather}%</strong>
                                            </div>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: ${risk.weather}%"></div>
                                            </div>
                                        </div>
                                        <!-- Inflation Risk -->
                                        <div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted" style="font-size: 0.85rem;">Risiko Inflasi</span>
                                                <strong class="text-dark" style="font-size: 0.85rem;">${risk.inflation}%</strong>
                                            </div>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: ${risk.inflation}%"></div>
                                            </div>
                                        </div>
                                        <!-- Currency Risk -->
                                        <div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted" style="font-size: 0.85rem;">Risiko Kurs</span>
                                                <strong class="text-dark" style="font-size: 0.85rem;">${risk.currency}%</strong>
                                            </div>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: ${risk.currency}%"></div>
                                            </div>
                                        </div>
                                        <!-- Political Risk -->
                                        <div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted" style="font-size: 0.85rem;">Risiko Politik</span>
                                                <strong class="text-dark" style="font-size: 0.85rem;">${risk.political}%</strong>
                                            </div>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: ${risk.political}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                ` : '<p class="text-muted text-center py-4">Data analisis risiko tidak tersedia</p>'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('favoriteBtn')?.addEventListener('click', function() {
            const countryCode = data.code;
            toggleFavorite(countryCode, this);
        });
    }

    function toggleFavorite(countryCode, btnElement) {
        const isFavorited = btnElement.classList.contains('btn-danger');
        const method = isFavorited ? 'DELETE' : 'POST';
        const url = `/api/favorites/${countryCode}`;

        btnElement.disabled = true;

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.favorited === true) {
                btnElement.classList.remove('btn-outline-danger');
                btnElement.classList.add('btn-danger');
                btnElement.innerHTML = '<i class="fas fa-star"></i> Hapus dari Favorit';
            } else if (data.favorited === false) {
                btnElement.classList.remove('btn-danger');
                btnElement.classList.add('btn-outline-danger');
                btnElement.innerHTML = '<i class="far fa-star"></i> Tambah ke Favorit';
            } else {
                alert('Gagal mengubah status favorit. Silakan coba lagi.');
            }
            btnElement.disabled = false;
        })
        .catch(error => {
            console.error('❌ Error:', error);
            alert('Gagal mengubah status favorit. Silakan coba lagi.');
            btnElement.disabled = false;
        });
    }
});
</script>
@endpush