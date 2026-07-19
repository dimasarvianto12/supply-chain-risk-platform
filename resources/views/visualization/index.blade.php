@extends('layouts.app')

@section('title', 'Data Visualization Dashboard')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
    .loading-spinner {
        display: inline-block;
        width: 1.2rem;
        height: 1.2rem;
        border: 2.5px solid #e2e8f0;
        border-top: 2.5px solid #6366f1;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Left border states matching charts */
    .gdp-card { border-left: 4px solid #3b82f6 !important; }
    .inflation-card { border-left: 4px solid #ef4444 !important; }
    .currency-card { border-left: 4px solid #10b981 !important; }
    .risk-card { border-left: 4px solid #f59e0b !important; }
    
    .chart-info-tag {
        font-weight: 700;
        color: #475569;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-chart-pie text-primary"></i> Data Visualization Dashboard
        </h1>
        <p class="text-muted fs-5 mb-0">Grafik tren makroekonomi, nilai tukar kurs, dan analisis risiko rantai pasok.</p>
        <hr>
    </div>
</div>

<!-- Filter Control Panel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card premium-card">
            <div class="premium-card-header">
                <i class="fas fa-filter text-primary"></i>
                <span>Pilih Cakupan Analisis</span>
            </div>
            <div class="premium-card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <label for="countrySelect" class="form-label fw-bold text-secondary mb-2" style="font-size: 0.85rem;">NEGARA TARGET</label>
                        <select id="countrySelect" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                            <option value="">-- Pilih Negara --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->code }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-none d-md-block mb-2">&nbsp;</label>
                        <button id="loadBtn" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">
                            <i class="fas fa-sync-alt me-1"></i> Muat Visualisasi
                        </button>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-none d-md-block mb-2">&nbsp;</label>
                        <div class="text-muted fw-semibold" id="statusText" style="font-size: 0.9rem;">
                            Silakan pilih negara dan muat data.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="row g-4">
    <!-- GDP Chart -->
    <div class="col-md-6">
        <div class="card premium-card gdp-card">
            <div class="premium-card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-chart-bar text-primary"></i> Tren GDP
                </span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded chart-info-tag" id="gdpInfo">
                    Belum ada data
                </span>
            </div>
            <div class="premium-card-body">
                <div class="chart-container">
                    <canvas id="gdpChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Inflation Chart -->
    <div class="col-md-6">
        <div class="card premium-card inflation-card">
            <div class="premium-card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-chart-line text-danger"></i> Tren Inflasi
                </span>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded chart-info-tag" id="inflationInfo">
                    Belum ada data
                </span>
            </div>
            <div class="premium-card-body">
                <div class="chart-container">
                    <canvas id="inflationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <!-- Currency Chart -->
    <div class="col-md-6">
        <div class="card premium-card currency-card">
            <div class="premium-card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-chart-line text-success"></i> Tren Kurs
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded chart-info-tag" id="currencyInfo">
                    Belum ada data
                </span>
            </div>
            <div class="premium-card-body">
                <div class="chart-container">
                    <canvas id="currencyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Trend Chart -->
    <div class="col-md-6">
        <div class="card premium-card risk-card">
            <div class="premium-card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-shield-alt text-warning"></i> Tren Risiko Berkelanjutan
                </span>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 rounded chart-info-tag" id="riskInfo">
                    Belum ada data
                </span>
            </div>
            <div class="premium-card-body">
                <div class="chart-container">
                    <canvas id="riskChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('countrySelect');
    const loadBtn = document.getElementById('loadBtn');
    const statusText = document.getElementById('statusText');

    // Canvas
    const gdpCanvas = document.getElementById('gdpChart');
    const inflationCanvas = document.getElementById('inflationChart');
    const currencyCanvas = document.getElementById('currencyChart');
    const riskCanvas = document.getElementById('riskChart');

    // Info labels
    const gdpInfo = document.getElementById('gdpInfo');
    const inflationInfo = document.getElementById('inflationInfo');
    const currencyInfo = document.getElementById('currencyInfo');
    const riskInfo = document.getElementById('riskInfo');

    // Chart instances
    let gdpChart = null;
    let inflationChart = null;
    let currencyChart = null;
    let riskChart = null;

    // Load Data trigger
    function loadData(countryCode) {
        if (!countryCode) {
            statusText.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Silakan pilih negara target.</span>';
            return;
        }

        statusText.innerHTML = '<span class="loading-spinner"></span> Memuat visualisasi...';

        // Load GDP
        fetch(`/api/visualization/gdp/${countryCode}`)
            .then(r => r.json())
            .then(data => renderGdpChart(data))
            .catch(err => {
                console.error('GDP error:', err);
                gdpInfo.textContent = 'Gagal memuat GDP';
            });

        // Load Inflation
        fetch(`/api/visualization/inflation/${countryCode}`)
            .then(r => r.json())
            .then(data => renderInflationChart(data))
            .catch(err => {
                console.error('Inflation error:', err);
                inflationInfo.textContent = 'Gagal memuat Inflasi';
            });

        // Load Currency
        fetch(`/api/visualization/currency/${countryCode}`)
            .then(r => r.json())
            .then(data => renderCurrencyChart(data))
            .catch(err => {
                console.error('Currency error:', err);
                currencyInfo.textContent = 'Gagal memuat Kurs';
            });

        // Load Risk
        fetch(`/api/visualization/risk/${countryCode}`)
            .then(r => r.json())
            .then(data => renderRiskChart(data))
            .catch(err => {
                console.error('Risk error:', err);
                riskInfo.textContent = 'Gagal memuat Risiko';
            });

        setTimeout(() => {
            const countryName = countrySelect.options[countrySelect.selectedIndex].text;
            statusText.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i> Visualisasi ${countryName} aktif</span>`;
        }, 800);
    }

    // GDP Render (Bar Chart with gradient fill)
    function renderGdpChart(data) {
        if (gdpChart) { gdpChart.destroy(); }
        if (!data.data || data.data.length === 0) {
            gdpInfo.textContent = 'Tidak ada data GDP';
            return;
        }
        const labels = data.data.map(d => d.year);
        const values = data.data.map(d => d.gdp);
        const ctx = gdpCanvas.getContext('2d');

        // Create elegant blue-gradient bar
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, '#3b82f6');
        gradient.addColorStop(1, '#93c5fd');

        gdpChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'GDP (USD)',
                    data: values,
                    backgroundColor: gradient,
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        titleFont: { family: 'Plus Jakarta Sans', weight: '700' },
                        bodyFont: { family: 'Plus Jakarta Sans' },
                        callbacks: {
                            label: function(context) {
                                return ' GDP: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans' } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { family: 'Plus Jakarta Sans' } }
                    }
                }
            }
        });
        gdpInfo.textContent = `${data.country} (${data.data.length} Tahun)`;
    }

    // Inflation Render (Line Chart with custom red gradient)
    function renderInflationChart(data) {
        if (inflationChart) { inflationChart.destroy(); }
        if (!data.data || data.data.length === 0) {
            inflationInfo.textContent = 'Tidak ada data Inflasi';
            return;
        }
        const labels = data.data.map(d => d.year);
        const values = data.data.map(d => d.inflation);
        const ctx = inflationCanvas.getContext('2d');

        // Red-gradient path fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
        gradient.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        inflationChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inflasi (%)',
                    data: values,
                    borderColor: '#ef4444',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        titleFont: { family: 'Plus Jakarta Sans', weight: '700' },
                        bodyFont: { family: 'Plus Jakarta Sans' },
                        callbacks: {
                            label: function(context) {
                                return ' Inflasi: ' + context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans' } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { family: 'Plus Jakarta Sans' } }
                    }
                }
            }
        });
        inflationInfo.textContent = `${data.country} (${data.data.length} Tahun)`;
    }

    // Currency Render (Line Chart with emerald gradient)
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