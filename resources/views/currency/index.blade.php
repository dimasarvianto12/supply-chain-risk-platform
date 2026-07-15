@extends('layouts.app')

@section('title', 'Currency Impact Dashboard')

@section('styles')
<style>
    .currency-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .rate-table th {
        background-color: #343a40;
        color: white;
    }
    .rate-table tr:hover {
        background-color: #e9ecef;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-money-bill-wave"></i> Currency Impact Dashboard</h1>
        <p>Pantau nilai tukar mata uang dan pergerakannya.</p>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filter
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="baseCurrency" class="form-label">Base Currency</label>
                    <select id="baseCurrency" class="form-select">
                        @foreach($baseCurrencies as $base)
                            <option value="{{ $base }}" {{ $base == 'USD' ? 'selected' : '' }}>{{ $base }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="targetCurrency" class="form-label">Target Currency</label>
                    <select id="targetCurrency" class="form-select">
                        <option value="">-- Pilih Mata Uang --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->currency }}" data-code="{{ $country->code }}">
                                {{ $country->name }} ({{ $country->currency }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button id="refreshBtn" class="btn btn-primary w-100">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i> Grafik Pergerakan Kurs (7 Hari Terakhir)
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="currencyChart"></canvas>
                </div>
                <div id="chartMessage" class="text-center text-muted mt-2"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i> Daftar Kurs Terkini
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped rate-table" id="rateTable">
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
                            <tr><td colspan="5" class="text-center">
                                <span class="loading-spinner"></span> Memuat data...
                            </td></tr>
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
    // Elemen
    const baseSelect = document.getElementById('baseCurrency');
    const targetSelect = document.getElementById('targetCurrency');
    const refreshBtn = document.getElementById('refreshBtn');
    const tableBody = document.getElementById('rateTableBody');
    const chartCanvas = document.getElementById('currencyChart');
    const chartMessage = document.getElementById('chartMessage');
    let chart = null;

    // ==========================================
    // Fungsi untuk load data kurs terbaru
    // ==========================================
    function loadRates(base) {
        console.log('📥 Memuat kurs untuk base:', base);
        
        // Tampilkan loading
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center">
            <span class="loading-spinner"></span> Memuat data...
        </td></tr>`;

        fetch(`/api/currency/latest/${base}`)
            .then(response => {
                console.log('📡 Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 Data kurs (raw):', data);

                // Jika response berupa object dengan properti 'data', ambil array-nya
                let rates = data;
                if (data && typeof data === 'object' && !Array.isArray(data)) {
                    if (data.data && Array.isArray(data.data)) {
                        rates = data.data;
                    } else if (data.message) {
                        // Jika ada pesan, tampilkan
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-warning">${data.message}</td></tr>`;
                        return;
                    }
                }

                // Pastikan rates adalah array
                if (!Array.isArray(rates)) {
                    rates = [];
                }

                if (rates.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Tidak ada data kurs. 
                        <br><small>Jalankan <code>php artisan app:fetch-rates USD</code> di terminal.</small>
                    </td></tr>`;
                    return;
                }

                let rows = '';
                rates.forEach(item => {
                    const rate = parseFloat(item.rate) || 0;
                    rows += `
                        <tr>
                            <td>${item.country || 'N/A'}</td>
                            <td>${item.code || 'N/A'}</td>
                            <td>${item.currency || 'N/A'}</td>
                            <td>${rate.toFixed(4)}</td>
                            <td>${item.recorded_at || 'N/A'}</td>
                        </tr>
                    `;
                });
                tableBody.innerHTML = rows;
                console.log(`✅ ${rates.length} kurs berhasil dimuat.`);
            })
            .catch(error => {
                console.error('❌ Error loading rates:', error);
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">
                    <i class="fas fa-exclamation-circle"></i> 
                    Gagal memuat data: ${error.message}
                </td></tr>`;
            });
    }

    // ==========================================
    // Fungsi untuk load history dan render chart
    // ==========================================
    function loadHistory(base, target) {
        // Clear chart jika tidak ada target
        if (!target) {
            if (chart) { chart.destroy(); chart = null; }
            chartMessage.textContent = 'Silakan pilih target currency untuk melihat grafik.';
            return;
        }

        chartMessage.textContent = 'Memuat grafik...';

        fetch(`/api/currency/history/${base}/${target}?days=7`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📊 History data:', data);

                // Jika tidak ada history
                if (!data.history || data.history.length === 0) {
                    if (chart) { chart.destroy(); chart = null; }
                    chartMessage.textContent = 'Belum ada data history untuk pasangan mata uang ini.';
                    return;
                }

                const labels = data.history.map(h => h.date);
                const rates = data.history.map(h => parseFloat(h.rate) || 0);

                // Hapus chart lama jika ada
                if (chart) {
                    chart.destroy();
                    chart = null;
                }

                const ctx = chartCanvas.getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: `1 ${base} = ${target}`,
                            data: rates,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0,123,255,0.1)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 4,
                            pointBackgroundColor: '#007bff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y.toFixed(4)}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                ticks: {
                                    callback: function(value) {
                                        return value.toFixed(4);
                                    }
                                }
                            }
                        }
                    }
                });

                chartMessage.textContent = '';
                console.log('✅ Chart berhasil dirender.');
            })
            .catch(error => {
                console.error('❌ Error loading history:', error);
                if (chart) { chart.destroy(); chart = null; }
                chartMessage.textContent = 'Gagal memuat grafik: ' + error.message;
            });
    }

    // ==========================================
    // Fungsi refresh semua data
    // ==========================================
    function refreshAll() {
        const base = baseSelect.value;
        const target = targetSelect.value;
        console.log(`🔄 Refresh: base=${base}, target=${target}`);
        loadRates(base);
        loadHistory(base, target);
    }

    // ==========================================
    // Event listeners
    // ==========================================
    baseSelect.addEventListener('change', refreshAll);
    targetSelect.addEventListener('change', refreshAll);
    refreshBtn.addEventListener('click', refreshAll);

    // ==========================================
    // Inisialisasi pertama
    // ==========================================
    refreshAll();
});
</script>
@endpush