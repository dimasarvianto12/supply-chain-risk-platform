@extends('layouts.app')

@section('title', 'Data Visualization Dashboard')

@section('styles')
<style>
    .chart-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 20px;
        margin-bottom: 20px;
    }
    .chart-card .chart-header {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .loading-spinner {
        display: inline-block;
        width: 1.5rem;
        height: 1.5rem;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .empty-state {
        text-align: center;
        padding: 30px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 2rem;
        color: #dee2e6;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-chart-pie"></i> Data Visualization Dashboard</h1>
        <p>Grafik tren ekonomi, kurs, dan risiko rantai pasok.</p>
        <hr>
    </div>
</div>

<!-- Filter -->
<div class="row">
    <div class="col-12">
        <div class="filter-section">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="countrySelect" class="form-label">Pilih Negara</label>
                    <select id="countrySelect" class="form-select">
                        <option value="">-- Pilih Negara --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->code }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button id="loadBtn" class="btn btn-primary w-100">
                        <i class="fas fa-sync-alt"></i> Muat Data
                    </button>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small" id="statusText">Pilih negara dan klik tombol untuk memuat data.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-header"><i class="fas fa-chart-line text-primary"></i> Tren GDP</div>
            <div class="chart-container">
                <canvas id="gdpChart"></canvas>
            </div>
            <div class="text-muted small text-center mt-2" id="gdpInfo">Belum ada data</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-header"><i class="fas fa-chart-line text-danger"></i> Tren Inflasi</div>
            <div class="chart-container">
                <canvas id="inflationChart"></canvas>
            </div>
            <div class="text-muted small text-center mt-2" id="inflationInfo">Belum ada data</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-header"><i class="fas fa-chart-line text-success"></i> Tren Kurs</div>
            <div class="chart-container">
                <canvas id="currencyChart"></canvas>
            </div>
            <div class="text-muted small text-center mt-2" id="currencyInfo">Belum ada data</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-header"><i class="fas fa-chart-line text-warning"></i> Tren Risiko</div>
            <div class="chart-container">
                <canvas id="riskChart"></canvas>
            </div>
            <div class="text-muted small text-center mt-2" id="riskInfo">Belum ada data</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // 1. ELEMEN
    // ==========================================
    const countrySelect = document.getElementById('countrySelect');
    const loadBtn = document.getElementById('loadBtn');
    const statusText = document.getElementById('statusText');

    // Canvas
    const gdpCanvas = document.getElementById('gdpChart');
    const inflationCanvas = document.getElementById('inflationChart');
    const currencyCanvas = document.getElementById('currencyChart');
    const riskCanvas = document.getElementById('riskChart');

    // Info texts
    const gdpInfo = document.getElementById('gdpInfo');
    const inflationInfo = document.getElementById('inflationInfo');
    const currencyInfo = document.getElementById('currencyInfo');
    const riskInfo = document.getElementById('riskInfo');

    // Chart instances
    let gdpChart = null;
    let inflationChart = null;
    let currencyChart = null;
    let riskChart = null;

    // ==========================================
    // 2. FUNGSI LOAD DATA
    // ==========================================
    function loadData(countryCode) {
        if (!countryCode) {
            statusText.textContent = '⚠️ Silakan pilih negara terlebih dahulu.';
            return;
        }

        statusText.innerHTML = '<span class="loading-spinner"></span> Memuat data...';

        // Load GDP
        fetch(`/api/visualization/gdp/${countryCode}`)
            .then(r => r.json())
            .then(data => renderGdpChart(data))
            .catch(err => {
                console.error('GDP error:', err);
                gdpInfo.textContent = '❌ Gagal memuat data GDP';
            });

        // Load Inflation
        fetch(`/api/visualization/inflation/${countryCode}`)
            .then(r => r.json())
            .then(data => renderInflationChart(data))
            .catch(err => {
                console.error('Inflation error:', err);
                inflationInfo.textContent = '❌ Gagal memuat data Inflasi';
            });

        // Load Currency
        fetch(`/api/visualization/currency/${countryCode}`)
            .then(r => r.json())
            .then(data => renderCurrencyChart(data))
            .catch(err => {
                console.error('Currency error:', err);
                currencyInfo.textContent = '❌ Gagal memuat data Kurs';
            });

        // Load Risk
        fetch(`/api/visualization/risk/${countryCode}`)
            .then(r => r.json())
            .then(data => renderRiskChart(data))
            .catch(err => {
                console.error('Risk error:', err);
                riskInfo.textContent = '❌ Gagal memuat data Risiko';
            });

        statusText.textContent = `✅ Data untuk ${countrySelect.options[countrySelect.selectedIndex].text} dimuat.`;
    }

    // ==========================================
    // 3. RENDER FUNCTIONS
    // ==========================================
    function renderGdpChart(data) {
        if (gdpChart) { gdpChart.destroy(); }
        if (!data.data || data.data.length === 0) {
            gdpInfo.textContent = 'Tidak ada data GDP';
            return;
        }
        const labels = data.data.map(d => d.year);
        const values = data.data.map(d => d.gdp);
        const ctx = gdpCanvas.getContext('2d');
        gdpChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'GDP (USD)',
                    data: values,
                    backgroundColor: 'rgba(52,152,219,0.6)',
                    borderColor: '#3498db',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'GDP: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        gdpInfo.textContent = `GDP ${data.country} (${data.data.length} tahun)`;
    }

    function renderInflationChart(data) {
        if (inflationChart) { inflationChart.destroy(); }
        if (!data.data || data.data.length === 0) {
            inflationInfo.textContent = 'Tidak ada data Inflasi';
            return;
        }
        const labels = data.data.map(d => d.year);
        const values = data.data.map(d => d.inflation);
        const ctx = inflationCanvas.getContext('2d');
        inflationChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inflasi (%)',
                    data: values,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231,76,60,0.1)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
        inflationInfo.textContent = `Inflasi ${data.country} (${data.data.length} tahun)`;
    }

    function renderCurrencyChart(data) {
        if (currencyChart) { currencyChart.destroy(); }
        if (!data.data || data.data.length === 0) {
            currencyInfo.textContent = 'Tidak ada data Kurs';
            return;
        }
        const labels = data.data.map(d => d.date);
        const values = data.data.map(d => d.rate);
        const ctx = currencyCanvas.getContext('2d');
        currencyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: `Kurs (${data.base}/${data.target})`,
                    data: values,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46,204,113,0.1)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
        currencyInfo.textContent = `Kurs ${data.country} (${data.base}/${data.target})`;
    }

    function renderRiskChart(data) {
        if (riskChart) { riskChart.destroy(); }
        if (!data.data || data.data.length === 0) {
            riskInfo.textContent = 'Tidak ada data Risiko';
            return;
        }
        const labels = data.data.map(d => d.date);
        const ctx = riskCanvas.getContext('2d');
        riskChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Risiko',
                        data: data.data.map(d => d.total),
                        borderColor: '#f39c12',
                        backgroundColor: 'rgba(243,156,18,0.1)',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Cuaca',
                        data: data.data.map(d => d.weather),
                        borderColor: '#3498db',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3,
                    },
                    {
                        label: 'Inflasi',
                        data: data.data.map(d => d.inflation),
                        borderColor: '#e74c3c',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3,
                    },
                    {
                        label: 'Politik',
                        data: data.data.map(d => d.political),
                        borderColor: '#9b59b6',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 10 } }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: { callback: function(v) { return v + '%'; } }
                    }
                }
            }
        });
        riskInfo.textContent = `Risiko ${data.country} (${data.data.length} hari)`;
    }

    // ==========================================
    // 4. EVENT LISTENERS
    // ==========================================
    loadBtn.addEventListener('click', function() {
        const code = countrySelect.value;
        loadData(code);
    });

    // Enter key pada dropdown
    countrySelect.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            loadData(this.value);
        }
    });

    // ==========================================
    // 5. INISIALISASI
    // ==========================================
    statusText.textContent = 'Pilih negara dan klik tombol untuk memuat data.';
});
</script>
@endpush