@extends('layouts.app')

@section('title', 'Currency Impact Dashboard')

@push('styles')
<style>
    .rate-table th {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 16px 20px;
    }
    .rate-table td {
        padding: 16px 20px;
        font-size: 0.95rem;
        color: #334155;
        vertical-align: middle;
    }
    .rate-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    .rate-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .chart-container {
        position: relative;
        height: 320px;
        width: 100%;
    }
    .loading-spinner {
        display: inline-block;
        width: 1.2rem;
        height: 1.2rem;
        border: 2.5px solid #e2e8f0;
        border-top: 2.5px solid #8b5cf6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .currency-badge {
        background: #e0e7ff;
        color: #4f46e5;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
    }
    .rate-value {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        font-size: 1.1rem;
        color: #0f172a;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-money-bill-wave text-success"></i> Currency Impact Dashboard
        </h1>
        <p class="text-muted fs-5 mb-0">Pantau nilai tukar mata uang global dan pergerakan tren terkininya.</p>
        <hr>
    </div>
</div>

<div class="row g-4">
    <!-- Filter Card -->
    <div class="col-lg-4">
        <div class="card premium-card h-100">
            <div class="premium-card-header">
                <i class="fas fa-filter text-primary"></i>
                <span>Filter Kurs</span>
            </div>
            <div class="premium-card-body d-flex flex-column justify-content-between" style="min-height: 280px;">
                <div>
                    <div class="mb-3">
                        <label for="baseCurrency" class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">BASE CURRENCY</label>
                        <select id="baseCurrency" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                            @foreach($baseCurrencies as $base)
                                <option value="{{ $base }}" {{ $base == 'USD' ? 'selected' : '' }}>{{ $base }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="targetCurrency" class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">TARGET CURRENCY</label>
                        <select id="targetCurrency" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                            <option value="">-- Pilih Mata Uang --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->currency }}" data-code="{{ $country->code }}">
                                    {{ $country->name }} ({{ $country->currency }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button id="refreshBtn" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold">
                    <i class="fas fa-sync-alt me-1"></i> Refresh Data
                </button>
            </div>
        </div>
    </div>

    <!-- Chart Card -->
    <div class="col-lg-8">
        <div class="card premium-card h-100">
            <div class="premium-card-header">
                <i class="fas fa-chart-line text-primary"></i>
                <span>Grafik Pergerakan Kurs (7 Hari Terakhir)</span>
            </div>
            <div class="premium-card-body d-flex flex-column justify-content-center">
                <div class="chart-container">
                    <canvas id="currencyChart"></canvas>
                </div>
                <div id="chartMessage" class="text-center text-muted mt-2 fw-semibold" style="font-size: 0.9rem;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card premium-card">
            <div class="premium-card-header">
                <i class="fas fa-table text-primary"></i>
                <span>Daftar Kurs Terkini</span>
            </div>
            <div class="premium-card-body p-0">
                <div class="table-responsive">
                    <table class="table rate-table mb-0" id="rateTable">
                        <thead>
                            <tr>
                                <th>Negara</th>
                                <th>Kode</th>
                                <th>Mata Uang</th>
                                <th>Kurs (per 1 Base)</th>
                                <th>Waktu Update</th>
                            </tr>
                        </thead>
                        <tbody id="rateTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <span class="loading-spinner"></span> Menghubungi database kurs...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseSelect = document.getElementById('baseCurrency');
    const targetSelect = document.getElementById('targetCurrency');
    const refreshBtn = document.getElementById('refreshBtn');
    const tableBody = document.getElementById('rateTableBody');
    const chartCanvas = document.getElementById('currencyChart');
    const chartMessage = document.getElementById('chartMessage');
    let chart = null;

    // Load rates table
    function loadRates(base) {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted">
            <span class="loading-spinner"></span> Memuat data kurs terbaru...
        </td></tr>`;

        fetch(`/api/currency/latest/${base}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                return response.json();
            })
            .then(data => {
                let rates = data;
                if (data && typeof data === 'object' && !Array.isArray(data)) {
                    if (data.data && Array.isArray(data.data)) {
                        rates = data.data;
                    } else if (data.message) {
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-warning">${data.message}</td></tr>`;
                        return;
                    }
                }

                if (!Array.isArray(rates)) rates = [];

                if (rates.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted">
                        <i class="fas fa-info-circle me-2"></i> Belum ada data kurs. Silakan jalankan seeder atau penarik kurs.
                    </td></tr>`;
                    return;
                }

                let rows = '';
                rates.forEach(item => {
                    const rate = parseFloat(item.rate) || 0;
                    const date = item.recorded_at ? item.recorded_at.substring(0, 16) : 'N/A';
                    rows += `
                        <tr>
                            <td class="fw-bold text-dark">${item.country || 'N/A'}</td>
                            <td><span class="badge bg-light text-secondary border px-2 py-1.5 rounded">${item.code || 'N/A'}</span></td>
                            <td><span class="currency-badge">${item.currency || 'N/A'}</span></td>
                            <td class="rate-value">${rate.toFixed(4)}</td>
                            <td><span class="text-muted"><i class="far fa-clock me-1"></i> ${date}</span></td>
                        </tr>
                    `;
                });
                tableBody.innerHTML = rows;
            })
            .catch(error => {
                console.error('❌ Error loading rates:', error);
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> Gagal memuat data kurs: ${error.message}
                </td></tr>`;
            });
    }

    // Load history and render premium chart
    function loadHistory(base, target) {
        if (!target) {
            if (chart) { chart.destroy(); chart = null; }
            chartCanvas.style.display = 'none';
            chartMessage.style.display = 'block';
            chartMessage.textContent = 'Silakan pilih target currency untuk melihat grafik tren pergerakan.';
            return;
        }

        chartCanvas.style.display = 'block';
        chartMessage.style.display = 'none';

        fetch(`/api/currency/history/${base}/${target}?days=7`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (!data.history || data.history.length === 0) {
                    if (chart) { chart.destroy(); chart = null; }
                    chartCanvas.style.display = 'none';
                    chartMessage.style.display = 'block';
                    chartMessage.textContent = 'Belum ada data history untuk pasangan mata uang ini.';
                    return;
                }

                const labels = data.history.map(h => h.date);
                const rates = data.history.map(h => parseFloat(h.rate) || 0);

                if (chart) {
                    chart.destroy();
                    chart = null;
                }

                const ctx = chartCanvas.getContext('2d');
                
                // Buat gradient ungu-biru yang menakjubkan
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(139, 92, 246, 0.4)');
                gradient.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: `1 ${base} = ${target}`,
                            data: rates,
                            borderColor: '#8b5cf6', // purple line
                            borderWidth: 3,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#8b5cf6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    font: {
                                        family: 'Plus Jakarta Sans',
                                        weight: '600'
                                    }
                                }
                            },
                            tooltip: {
                                padding: 12,
                                titleFont: { family: 'Plus Jakarta Sans', weight: '700' },
                                bodyFont: { family: 'Plus Jakarta Sans' },
                                callbacks: {
                                    label: function(context) {
                                        return ` Nilai Tukar: ${context.parsed.y.toFixed(4)}`;
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
                                beginAtZero: false,
                                ticks: {
                                    font: { family: 'Plus Jakarta Sans' },
                                    callback: function(value) {
                                        return value.toFixed(4);
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('❌ Error loading history:', error);
                if (chart) { chart.destroy(); chart = null; }
                chartCanvas.style.display = 'none';
                chartMessage.style.display = 'block';
                chartMessage.textContent = 'Gagal memuat grafik: ' + error.message;
            });
    }

    function refreshAll() {
        const base = baseSelect.value;
        const target = targetSelect.value;
        loadRates(base);
        loadHistory(base, target);
    }

    baseSelect.addEventListener('change', refreshAll);
    targetSelect.addEventListener('change', refreshAll);
    refreshBtn.addEventListener('click', refreshAll);

    refreshAll();
});
</script>
@endpush