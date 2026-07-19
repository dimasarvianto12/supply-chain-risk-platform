@extends('layouts.app')

@section('title', 'Country Comparison Engine')

@push('styles')
<style>
    .flag-img {
        max-height: 44px !important;
        width: auto !important;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .compare-table {
        width: 100%;
        border-collapse: collapse;
    }
    .compare-table th {
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 16px;
    }
    .compare-table td {
        padding: 14px 16px;
        font-size: 0.95rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .compare-table tbody tr {
        transition: all 0.2s ease;
    }
    .compare-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .compare-table .label {
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
        width: 25%;
    }
    .compare-table .value-a, .compare-table .value-b {
        width: 37.5%;
        text-align: center;
    }
    .chart-container {
        position: relative;
        height: 320px;
        width: 100%;
    }
    .loading-spinner {
        display: inline-block;
        width: 1.5rem;
        height: 1.5rem;
        border: 3px solid #e2e8f0;
        border-top: 3px solid #6366f1;
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
        padding: 60px 20px;
        color: #64748b;
    }
    .empty-state i {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 16px;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-balance-scale text-primary"></i> Country Comparison Engine
        </h1>
        <p class="text-muted fs-5 mb-0">Bandingkan dua negara secara berdampingan berdasarkan data makroekonomi, cuaca, dan tingkat risiko.</p>
        <hr>
    </div>
</div>

<!-- Filter Selection Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card premium-card">
            <div class="premium-card-header">
                <i class="fas fa-filter text-primary"></i>
                <span>Tentukan Negara Perbandingan</span>
            </div>
            <div class="premium-card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="countryA" class="form-label fw-bold text-secondary mb-2" style="font-size: 0.85rem;">NEGARA A (BASE)</label>
                        <select id="countryA" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                            <option value="">-- Pilih --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->code }}" {{ $country->code == 'ID' ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="countryB" class="form-label fw-bold text-secondary mb-2" style="font-size: 0.85rem;">NEGARA B (KOMPARATOR)</label>
                        <select id="countryB" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                            <option value="">-- Pilih --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->code }}" {{ $country->code == 'US' ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button id="compareBtn" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold">
                            <i class="fas fa-arrows-left-right me-1"></i> Bandingkan Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comparison Results -->
<div id="resultContainer">
    <div class="card premium-card py-5">
        <div class="empty-state">
            <i class="fas fa-exchange-alt"></i>
            <h5 class="fw-bold">Pilih dua negara target di atas</h5>
            <p class="text-muted mb-0">Klik tombol Bandingkan untuk memuat analisis parameter komparatif.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countryA = document.getElementById('countryA');
    const countryB = document.getElementById('countryB');
    const compareBtn = document.getElementById('compareBtn');
    const resultContainer = document.getElementById('resultContainer');

    let chart = null;

    function loadComparison(codeA, codeB) {
        if (!codeA || !codeB) {
            alert('Silakan pilih kedua negara.');
            return;
        }

        resultContainer.innerHTML = `
            <div class="card premium-card py-5 text-center text-muted">
                <div class="loading-spinner"></div>
                <p class="mt-2 mb-0">Menganalisis perbandingan parameter negara...</p>
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
                    <div class="card premium-card py-5 text-center text-danger">
                        <i class="fas fa-exclamation-circle text-danger mb-2" style="font-size: 2.5rem;"></i>
                        <h5 class="fw-bold">Gagal Memuat Analisis</h5>
                        <p class="text-muted mb-0">${error.message}</p>
                    </div>
                `;
            });
    }

    function renderComparison(data) {
        const c1 = data.country1;
        const c2 = data.country2;

        const pop1 = c1.population ? c1.population.toLocaleString() : 'N/A';
        const pop2 = c2.population ? c2.population.toLocaleString() : 'N/A';

        const gdp1 = c1.economic?.gdp ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(c1.economic.gdp) : 'N/A';
        const gdp2 = c2.economic?.gdp ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(c2.economic.gdp) : 'N/A';

        const inf1 = c1.economic?.inflation !== undefined && c1.economic.inflation !== null ? parseFloat(c1.economic.inflation).toFixed(1) + '%' : 'N/A';
        const inf2 = c2.economic?.inflation !== undefined && c2.economic.inflation !== null ? parseFloat(c2.economic.inflation).toFixed(1) + '%' : 'N/A';

        const rate1 = c1.currency_rate?.rate !== undefined && c1.currency_rate?.rate !== null ? parseFloat(c1.currency_rate.rate).toFixed(4) : 'N/A';
        const rate2 = c2.currency_rate?.rate !== undefined && c2.currency_rate?.rate !== null ? parseFloat(c2.currency_rate.rate).toFixed(4) : 'N/A';

        const temp1 = c1.weather?.temperature !== undefined && c1.weather?.temperature !== null ? parseFloat(c1.weather.temperature).toFixed(1) : 'N/A';
        const temp2 = c2.weather?.temperature !== undefined && c2.weather?.temperature !== null ? parseFloat(c2.weather.temperature).toFixed(1) : 'N/A';

        const risk1 = c1.risk?.total !== undefined && c1.risk?.total !== null ? parseFloat(c1.risk.total).toFixed(1) : 'N/A';
        const risk2 = c2.risk?.total !== undefined && c2.risk?.total !== null ? parseFloat(c2.risk.total).toFixed(1) : 'N/A';

        const gdpNum1 = c1.economic?.gdp || 0;
        const gdpNum2 = c2.economic?.gdp || 0;
        const infNum1 = c1.economic?.inflation || 0;
        const infNum2 = c2.economic?.inflation || 0;
        const riskNum1 = c1.risk?.total || 0;
        const riskNum2 = c2.risk?.total || 0;

        const betterGdp = gdpNum1 > gdpNum2 ? c1.name : (gdpNum2 > gdpNum1 ? c2.name : 'Sama');
        const betterInf = infNum1 < infNum2 ? c1.name : (infNum2 < infNum1 ? c2.name : 'Sama');
        const betterRisk = riskNum1 < riskNum2 ? c1.name : (riskNum2 < riskNum1 ? c2.name : 'Sama');

        resultContainer.innerHTML = `
            <div class="row g-4">
                <div class="col-12">
                    <div class="card premium-card">
                        <div class="premium-card-header">
                            <i class="fas fa-table text-primary"></i>
                            <span>Perbandingan Parameter</span>
                        </div>
                        <div class="premium-card-body p-0">
                            <div class="table-responsive">
                                <table class="compare-table">
                                    <thead>
                                        <tr>
                                            <th class="label text-start">Parameter</th>
                                            <th class="value-a text-center">
                                                ${c1.flag ? `<img src="${c1.flag}" class="flag-img" alt="${c1.name}">` : ''} 
                                                <span class="fw-extrabold text-dark d-block mt-1">${c1.name} (${c1.code})</span>
                                            </th>
                                            <th class="value-b text-center">
                                                ${c2.flag ? `<img src="${c2.flag}" class="flag-img" alt="${c2.name}">` : ''} 
                                                <span class="fw-extrabold text-dark d-block mt-1">${c2.name} (${c2.code})</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="label">Ibukota</td>
                                            <td class="value-a fw-semibold text-center">${c1.capital || 'N/A'}</td>
                                            <td class="value-b fw-semibold text-center">${c2.capital || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Populasi</td>
                                            <td class="value-a text-center">${pop1}</td>
                                            <td class="value-b text-center">${pop2}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Mata Uang</td>
                                            <td class="value-a text-center"><span class="badge bg-light text-dark border px-3 py-1.5 rounded">${c1.currency || 'N/A'}</span></td>
                                            <td class="value-b text-center"><span class="badge bg-light text-dark border px-3 py-1.5 rounded">${c2.currency || 'N/A'}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="label">GDP (USD)</td>
                                            <td class="value-a fw-bold text-center">${gdp1}</td>
                                            <td class="value-b fw-bold text-center">${gdp2}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Inflasi</td>
                                            <td class="value-a text-danger fw-semibold text-center">${inf1}</td>
                                            <td class="value-b text-danger fw-semibold text-center">${inf2}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Nilai Tukar (per 1 USD)</td>
                                            <td class="value-a font-monospace fw-bold text-center">${rate1}</td>
                                            <td class="value-b font-monospace fw-bold text-center">${rate2}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Suhu Rata-rata (°C)</td>
                                            <td class="value-a text-info fw-semibold text-center">${temp1} °C</td>
                                            <td class="value-b text-info fw-semibold text-center">${temp2} °C</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Skor Risiko Rantai Pasok</td>
                                            <td class="value-a text-center"><span class="badge rounded-pill px-3 py-1.5 border fw-bold bg-secondary-subtle text-secondary">${risk1}</span></td>
                                            <td class="value-b text-center"><span class="badge rounded-pill px-3 py-1.5 border fw-bold bg-secondary-subtle text-secondary">${risk2}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Highlight Badges -->
                            <div class="d-flex flex-wrap gap-3 justify-content-center py-4 bg-light border-top">
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold fs-7">
                                    <i class="fas fa-trophy text-warning me-1"></i> GDP Terbesar: <strong>${betterGdp}</strong>
                                </div>
                                <div class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill fw-bold fs-7">
                                    <i class="fas fa-arrow-trend-down text-info me-1"></i> Inflasi Terendah: <strong>${betterInf}</strong>
                                </div>
                                <div class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-bold fs-7" style="color:#854d0e !important;">
                                    <i class="fas fa-shield-alt text-warning me-1"></i> Risiko Terendah: <strong>${betterRisk}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mt-1">
                <div class="col-12">
                    <div class="card premium-card">
                        <div class="premium-card-header">
                            <i class="fas fa-chart-bar text-primary"></i>
                            <span>Matriks Normalisasi Perbandingan</span>
                        </div>
                        <div class="premium-card-body">
                            <div class="chart-container">
                                <canvas id="compareChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        renderChart(c1, c2);
    }

    function renderChart(c1, c2) {
        const canvas = document.getElementById('compareChart');
        if (!canvas) return;

        if (chart) { chart.destroy(); }

        const ctx = canvas.getContext('2d');

        const gdp1 = parseFloat(c1.economic?.gdp) || 0;
        const gdp2 = parseFloat(c2.economic?.gdp) || 0;
        const inf1 = parseFloat(c1.economic?.inflation) || 0;
        const inf2 = parseFloat(c2.economic?.inflation) || 0;
        const rate1 = parseFloat(c1.currency_rate?.rate) || 0;
        const rate2 = parseFloat(c2.currency_rate?.rate) || 0;
        const risk1 = parseFloat(c1.risk?.total) || 0;
        const risk2 = parseFloat(c2.risk?.total) || 0;

        const maxGdp = Math.max(gdp1, gdp2) || 1;
        const maxInf = Math.max(inf1, inf2) || 1;
        const maxRate = Math.max(rate1, rate2) || 1;
        const maxRisk = Math.max(risk1, risk2) || 1;

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

        // Indigo Gradient for Country A
        const gradA = ctx.createLinearGradient(0, 0, 0, 300);
        gradA.addColorStop(0, '#6366f1');
        gradA.addColorStop(1, '#a5b4fc');

        // Emerald Gradient for Country B
        const gradB = ctx.createLinearGradient(0, 0, 0, 300);
        gradB.addColorStop(0, '#10b981');
        gradB.addColorStop(1, '#6ee7b7');

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['GDP', 'Inflasi', 'Kurs', 'Risiko'],
                datasets: [
                    {
                        label: c1.name,
                        data: data1,
                        backgroundColor: gradA,
                        borderColor: '#6366f1',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: c2.name,
                        data: data2,
                        backgroundColor: gradB,
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { family: 'Plus Jakarta Sans', weight: '600' } }
                    },
                    tooltip: {
                        padding: 12,
                        titleFont: { family: 'Plus Jakarta Sans', weight: '700' },
                        bodyFont: { family: 'Plus Jakarta Sans' },
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const label = context.dataset.label;
                                let value = '';
                                if (index === 0) {
                                    value = label === c1.name ? gdp1.toLocaleString() : gdp2.toLocaleString();
                                    return ` GDP: $${value}`;
                                } else if (index === 1) {
                                    value = label === c1.name ? inf1 : inf2;
                                    return ` Inflasi: ${value.toFixed(1)}%`;
                                } else if (index === 2) {
                                    value = label === c1.name ? rate1 : rate2;
                                    return ` Kurs: ${value.toFixed(4)}`;
                                } else if (index === 3) {
                                    value = label === c1.name ? risk1 : risk2;
                                    return ` Risiko: ${value.toFixed(1)}%`;
                                }
                                return ` ${context.parsed.y.toFixed(0)}%`;
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
                        beginAtZero: true,
                        max: 100,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans' },
                            callback: function(value) { return value + '%'; }
                        }
                    }
                }
            }
        });
    }

    compareBtn.addEventListener('click', function() {
        loadComparison(countryA.value, countryB.value);
    });

    countryA.addEventListener('change', function() {
        if (countryA.value && countryB.value) loadComparison(countryA.value, countryB.value);
    });
    countryB.addEventListener('change', function() {
        if (countryA.value && countryB.value) loadComparison(countryA.value, countryB.value);
    });

    // Auto-load comparison on page load since A/B default target values are selected
    if (countryA.value && countryB.value) {
        loadComparison(countryA.value, countryB.value);
    }
});
</script>
@endpush