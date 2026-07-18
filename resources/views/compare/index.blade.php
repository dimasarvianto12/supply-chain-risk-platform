@extends('layouts.app')

@section('title', 'Country Comparison Engine')

@section('styles')
<style>
    .compare-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 20px;
        margin-bottom: 20px;
    }
    .compare-card .card-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    .compare-card .flag-img {
        height: 40px;
        width: auto;
        margin-right: 10px;
    }
    .country-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    .country-header h3 {
        margin: 0;
    }
    .compare-table {
        width: 100%;
        border-collapse: collapse;
    }
    .compare-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #eee;
    }
    .compare-table .label {
        font-weight: 600;
        color: #555;
        width: 30%;
    }
    .compare-table .value-a {
        width: 35%;
        background: #f0f7ff;
        padding: 8px 12px;
    }
    .compare-table .value-b {
        width: 35%;
        background: #f0fff4;
        padding: 8px 12px;
    }
    .badge-compare {
        font-size: 0.9rem;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .winner {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .loser {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
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
        padding: 40px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-balance-scale"></i> Country Comparison Engine</h1>
        <p>Bandingkan dua negara berdasarkan data ekonomi, cuaca, dan risiko.</p>
        <hr>
    </div>
</div>

<!-- Filter -->
<div class="row">
    <div class="col-12">
        <div class="filter-section">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="countryA" class="form-label">Negara A</label>
                    <select id="countryA" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->code }}" {{ $country->code == 'ID' ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="countryB" class="form-label">Negara B</label>
                    <select id="countryB" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->code }}" {{ $country->code == 'US' ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button id="compareBtn" class="btn btn-primary w-100">
                        <i class="fas fa-arrows-left-right"></i> Bandingkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hasil Perbandingan -->
<div id="resultContainer">
    <div class="empty-state">
        <i class="fas fa-exchange-alt"></i>
        <h5>Pilih dua negara dan klik Bandingkan</h5>
        <p class="text-muted">Data akan ditampilkan di sini.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // 1. ELEMEN
    // ==========================================
    const countryA = document.getElementById('countryA');
    const countryB = document.getElementById('countryB');
    const compareBtn = document.getElementById('compareBtn');
    const resultContainer = document.getElementById('resultContainer');

    let chart = null;

    // ==========================================
    // 2. FUNGSI LOAD COMPARISON
    // ==========================================
    function loadComparison(codeA, codeB) {
        if (!codeA || !codeB) {
            alert('Silakan pilih kedua negara.');
            return;
        }

        resultContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="loading-spinner"></div>
                <p class="mt-2">Memuat data perbandingan...</p>
            </div>
        `;

        fetch(`/api/compare/${codeA}/${codeB}`)
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data');
                return response.json();
            })
            .then(data => {
                renderComparison(data);
            })
            .catch(error => {
                console.error('❌ Error:', error);
                resultContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Gagal memuat data: ${error.message}
                    </div>
                `;
            });
    }

    // ==========================================
    // 3. RENDER PERBANDINGAN
    // ==========================================
    function renderComparison(data) {
        const c1 = data.country1;
        const c2 = data.country2;

        // ==========================================
        // EKSTRAK DATA DENGAN PARSING NUMERIK
        // ==========================================
        const pop1 = c1.population ? c1.population.toLocaleString() : 'N/A';
        const pop2 = c2.population ? c2.population.toLocaleString() : 'N/A';

        const gdp1 = c1.economic?.gdp ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(c1.economic.gdp) : 'N/A';
        const gdp2 = c2.economic?.gdp ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(c2.economic.gdp) : 'N/A';

        const inf1 = c1.economic?.inflation !== undefined && c1.economic.inflation !== null ? parseFloat(c1.economic.inflation).toFixed(1) + '%' : 'N/A';
        const inf2 = c2.economic?.inflation !== undefined && c2.economic.inflation !== null ? parseFloat(c2.economic.inflation).toFixed(1) + '%' : 'N/A';

        // ✅ PERBAIKAN: parsing rate dengan parseFloat
        const rate1 = c1.currency_rate?.rate !== undefined && c1.currency_rate?.rate !== null ? parseFloat(c1.currency_rate.rate).toFixed(4) : 'N/A';
        const rate2 = c2.currency_rate?.rate !== undefined && c2.currency_rate?.rate !== null ? parseFloat(c2.currency_rate.rate).toFixed(4) : 'N/A';

        const temp1 = c1.weather?.temperature !== undefined && c1.weather?.temperature !== null ? parseFloat(c1.weather.temperature).toFixed(1) : 'N/A';
        const temp2 = c2.weather?.temperature !== undefined && c2.weather?.temperature !== null ? parseFloat(c2.weather.temperature).toFixed(1) : 'N/A';

        const risk1 = c1.risk?.total !== undefined && c1.risk?.total !== null ? parseFloat(c1.risk.total).toFixed(1) : 'N/A';
        const risk2 = c2.risk?.total !== undefined && c2.risk?.total !== null ? parseFloat(c2.risk.total).toFixed(1) : 'N/A';

        // Tentukan pemenang (gunakan nilai numerik, bukan string)
        const gdpNum1 = c1.economic?.gdp || 0;
        const gdpNum2 = c2.economic?.gdp || 0;
        const infNum1 = c1.economic?.inflation || 0;
        const infNum2 = c2.economic?.inflation || 0;
        const riskNum1 = c1.risk?.total || 0;
        const riskNum2 = c2.risk?.total || 0;

        const betterGdp = gdpNum1 > gdpNum2 ? c1.name : (gdpNum2 > gdpNum1 ? c2.name : 'Sama');
        const betterInf = infNum1 < infNum2 ? c1.name : (infNum2 < infNum1 ? c2.name : 'Sama');
        const betterRisk = riskNum1 < riskNum2 ? c1.name : (riskNum2 < riskNum1 ? c2.name : 'Sama');

        // ==========================================
        // RENDER HTML
        // ==========================================
        resultContainer.innerHTML = `
            <div class="row">
                <div class="col-12">
                    <div class="compare-card">
                        <div class="card-title"><i class="fas fa-table"></i> Perbandingan Parameter</div>
                        <div class="table-responsive">
                            <table class="compare-table">
                                <thead>
                                    <tr>
                                        <th class="label">Parameter</th>
                                        <th class="value-a text-center">${c1.flag ? `<img src="${c1.flag}" class="flag-img">` : ''} ${c1.name} (${c1.code})</th>
                                        <th class="value-b text-center">${c2.flag ? `<img src="${c2.flag}" class="flag-img">` : ''} ${c2.name} (${c2.code})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="label">Ibukota</td>
                                        <td class="value-a text-center">${c1.capital || 'N/A'}</td>
                                        <td class="value-b text-center">${c2.capital || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Populasi</td>
                                        <td class="value-a text-center">${pop1}</td>
                                        <td class="value-b text-center">${pop2}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Mata Uang</td>
                                        <td class="value-a text-center">${c1.currency || 'N/A'}</td>
                                        <td class="value-b text-center">${c2.currency || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">GDP (USD)</td>
                                        <td class="value-a text-center">${gdp1}</td>
                                        <td class="value-b text-center">${gdp2}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Inflasi</td>
                                        <td class="value-a text-center">${inf1}</td>
                                        <td class="value-b text-center">${inf2}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Nilai Tukar (USD)</td>
                                        <td class="value-a text-center">${rate1}</td>
                                        <td class="value-b text-center">${rate2}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Suhu (°C)</td>
                                        <td class="value-a text-center">${temp1}</td>
                                        <td class="value-b text-center">${temp2}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Skor Risiko</td>
                                        <td class="value-a text-center">${risk1}</td>
                                        <td class="value-b text-center">${risk2}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success badge-compare">🏆 GDP terbesar: ${betterGdp}</span>
                            <span class="badge bg-info badge-compare">📉 Inflasi terendah: ${betterInf}</span>
                            <span class="badge bg-warning badge-compare text-dark">🛡️ Risiko terendah: ${betterRisk}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="compare-card">
                        <div class="card-title"><i class="fas fa-chart-bar"></i> Grafik Perbandingan</div>
                        <div class="chart-container">
                            <canvas id="compareChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Render Chart
        renderChart(c1, c2);
    }

    // ==========================================
    // 4. RENDER CHART
    // ==========================================
    function renderChart(c1, c2) {
        const canvas = document.getElementById('compareChart');
        if (!canvas) return;

        if (chart) { chart.destroy(); }

        const ctx = canvas.getContext('2d');

        // Ambil nilai numerik
        const gdp1 = parseFloat(c1.economic?.gdp) || 0;
        const gdp2 = parseFloat(c2.economic?.gdp) || 0;
        const inf1 = parseFloat(c1.economic?.inflation) || 0;
        const inf2 = parseFloat(c2.economic?.inflation) || 0;
        const rate1 = parseFloat(c1.currency_rate?.rate) || 0;
        const rate2 = parseFloat(c2.currency_rate?.rate) || 0;
        const risk1 = parseFloat(c1.risk?.total) || 0;
        const risk2 = parseFloat(c2.risk?.total) || 0;

        // Cari nilai maksimum untuk normalisasi
        const maxGdp = Math.max(gdp1, gdp2) || 1;
        const maxInf = Math.max(inf1, inf2) || 1;
        const maxRate = Math.max(rate1, rate2) || 1;
        const maxRisk = Math.max(risk1, risk2) || 1;

        // Normalisasi ke 0-100
        const data1 = [
            (gdp1 / maxGdp) * 100,
            (inf1 / maxInf) * 100,
            (rate1 / maxRate) * 100,
            (risk1 / maxRisk) * 100,
        ];
        const data2 = [
            (gdp2 / maxGdp) * 100,
            (inf2 / maxInf) * 100,
            (rate2 / maxRate) * 100,
            (risk2 / maxRisk) * 100,
        ];

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['GDP', 'Inflasi', 'Kurs', 'Risiko'],
                datasets: [
                    {
                        label: c1.name,
                        data: data1,
                        backgroundColor: 'rgba(52, 152, 219, 0.6)',
                        borderColor: '#3498db',
                        borderWidth: 2,
                    },
                    {
                        label: c2.name,
                        data: data2,
                        backgroundColor: 'rgba(46, 204, 113, 0.6)',
                        borderColor: '#2ecc71',
                        borderWidth: 2,
                    }
                ]
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
                                const index = context.dataIndex;
                                const label = context.dataset.label;
                                let value = '';
                                if (index === 0) {
                                    value = label === c1.name ? gdp1.toLocaleString() : gdp2.toLocaleString();
                                    return `GDP: $${value}`;
                                } else if (index === 1) {
                                    value = label === c1.name ? inf1 : inf2;
                                    return `Inflasi: ${value.toFixed(1)}%`;
                                } else if (index === 2) {
                                    value = label === c1.name ? rate1 : rate2;
                                    return `Kurs: ${value.toFixed(4)}`;
                                } else if (index === 3) {
                                    value = label === c1.name ? risk1 : risk2;
                                    return `Risiko: ${value.toFixed(1)}%`;
                                }
                                return `${context.parsed.y.toFixed(0)}%`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    }
                }
            }
        });
    }

    // ==========================================
    // 5. EVENT LISTENERS
    // ==========================================
    compareBtn.addEventListener('click', function() {
        const a = countryA.value;
        const b = countryB.value;
        loadComparison(a, b);
    });

    countryA.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') compareBtn.click();
    });
    countryB.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') compareBtn.click();
    });

    // ==========================================
    // 6. INISIALISASI (Opsional)
    // ==========================================
    // Uncomment jika ingin otomatis membandingkan saat halaman dimuat
    // setTimeout(() => compareBtn.click(), 500);
});
</script>
@endpush