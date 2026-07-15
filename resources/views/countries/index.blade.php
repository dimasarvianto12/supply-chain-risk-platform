@extends('layouts.app')

@section('title', 'Country Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-flag"></i> Global Country Dashboard</h1>
        <p>Pilih negara untuk melihat data lengkap: ekonomi, cuaca, kurs, dan risiko.</p>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-search"></i> Pilih Negara
            </div>
            <div class="card-body">
                <select class="form-select form-select-lg" id="countrySelect">
                    <option value="">-- Pilih Negara --</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->code }}">{{ $country->name }} ({{ $country->code }})</option>
                    @endforeach
                </select>
                <div id="loadingIndicator" class="mt-3 text-center d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Memuat data...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="countryDetail">
            <div class="card">
                <div class="card-body text-center text-muted">
                    <i class="fas fa-globe-asia" style="font-size: 3rem;"></i>
                    <p class="mt-3">Silakan pilih negara dari dropdown di samping.</p>
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
                <div class="card">
                    <div class="card-body text-center text-muted">
                        <i class="fas fa-globe-asia" style="font-size: 3rem;"></i>
                        <p class="mt-3">Silakan pilih negara dari dropdown di samping.</p>
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
                    <div class="card border-danger">
                        <div class="card-body text-center text-danger">
                            <i class="fas fa-exclamation-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2">Gagal memuat data: ${error.message}</p>
                        </div>
                    </div>
                `;
            });
    });

    function renderCountryDetail(data) {
        // --- PERBAIKAN: Gunakan null coalescing untuk semua data ---
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
            if (total > 60) { color = 'danger'; label = 'Tinggi'; }
            else if (total > 30) { color = 'warning'; label = 'Sedang'; }
            riskBadge = `<span class="badge bg-${color} fs-6">${label} (${total}%)</span>`;
        }

        // Cuaca
        const weather = data.weather || {};

        detailContainer.innerHTML = `
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        ${data.flag ? `<img src="${data.flag}" alt="${data.name}" style="height:30px; margin-right:10px;">` : ''}
                        ${data.name} (${data.code})
                        ${riskBadge}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header"><i class="fas fa-building"></i> Informasi Umum</div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td><strong>Ibukota</strong></td><td>${data.capital || 'N/A'}</td></tr>
                                        <tr><td><strong>Populasi</strong></td><td>${population}</td></tr>
                                        <tr><td><strong>Mata Uang</strong></td><td>${data.currency || 'N/A'}</td></tr>
                                        <tr><td><strong>Region</strong></td><td>${data.region || 'N/A'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header"><i class="fas fa-chart-line"></i> Ekonomi</div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td><strong>GDP</strong></td><td>${gdp}</td></tr>
                                        <tr><td><strong>Inflasi</strong></td><td>${inflation}</td></tr>
                                        <tr><td><strong>Tahun Data</strong></td><td>${year}</td></tr>
                                        <tr><td><strong>Nilai Tukar (${data.currency_rate?.base || 'USD'})</strong></td><td>${rate}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header"><i class="fas fa-cloud-sun"></i> Cuaca Terkini</div>
                                <div class="card-body">
                                    ${weather.temperature != null ? `
                                        <table class="table table-sm table-borderless">
                                            <tr><td><strong>Suhu</strong></td><td>${weather.temperature ?? 'N/A'} °C</td></tr>
                                            <tr><td><strong>Kelembaban</strong></td><td>${weather.humidity ?? 'N/A'}%</td></tr>
                                            <tr><td><strong>Kecepatan Angin</strong></td><td>${weather.wind_speed ?? 'N/A'} km/h</td></tr>
                                            <tr><td><strong>Kondisi</strong></td><td>${weather.description || 'N/A'}</td></tr>
                                        </table>
                                    ` : '<p class="text-muted text-center">Data cuaca tidak tersedia</p>'}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-header"><i class="fas fa-exclamation-triangle"></i> Skor Risiko</div>
                                <div class="card-body">
                                    ${risk.total != null ? `
                                        <table class="table table-sm table-borderless">
                                            <tr><td><strong>Risiko Cuaca</strong></td><td>${risk.weather ?? 0}%</td></tr>
                                            <tr><td><strong>Risiko Inflasi</strong></td><td>${risk.inflation ?? 0}%</td></tr>
                                            <tr><td><strong>Risiko Kurs</strong></td><td>${risk.currency ?? 0}%</td></tr>
                                            <tr><td><strong>Risiko Politik</strong></td><td>${risk.political ?? 0}%</td></tr>
                                            <tr class="table-primary"><td><strong>Total</strong></td><td><strong>${risk.total}%</strong></td></tr>
                                        </table>
                                    ` : '<p class="text-muted text-center">Data risiko tidak tersedia</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
});
</script>
@endpush