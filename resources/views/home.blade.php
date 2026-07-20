@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Header Dashboard -->
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
                📊 Supply Chain Risk Intelligence Dashboard
            </h1>
            <p class="text-muted fs-5 mb-0">
                Memantau risiko rantai pasok global secara real-time.
            </p>
        </div>

    </div>
</div>

<!-- Metric Cards (Gradients & Micro-Animations) -->
<div class="row mb-4">
    <!-- Total Negara -->
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="card premium-card metric-card gradient-blue h-100">
            <div class="premium-card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-title">TOTAL NEGARA</p>
                        <h3 class="metric-value" id="totalCountries">-</h3>
                    </div>
                    <div class="metric-icon-wrapper">
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
                <p class="metric-desc">Telah terdaftar dalam sistem monitoring</p>
            </div>
        </div>
    </div>
    
    <!-- Rata-rata Risiko -->
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="card premium-card metric-card gradient-green h-100" id="avgRiskCard">
            <div class="premium-card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-title">RATA-RATA RISIKO GLOBAL</p>
                        <h3 class="metric-value" id="avgRisk">-</h3>
                    </div>
                    <div class="metric-icon-wrapper">
                        <i class="fas fa-shield-virus"></i>
                    </div>
                </div>
                <p class="metric-desc">Tingkat kerawanan rata-rata saat ini</p>
            </div>
        </div>
    </div>
    
    <!-- Cuaca Terkini -->
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="card premium-card metric-card gradient-orange h-100">
            <div class="premium-card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-title">CUACA EKSTREM</p>
                        <h3 class="metric-value" id="weatherSummary">-</h3>
                    </div>
                    <div class="metric-icon-wrapper">
                        <i class="fas fa-cloud-bolt"></i>
                    </div>
                </div>
                <p class="metric-desc">Negara dengan kerentanan cuaca >60%</p>
            </div>
        </div>
    </div>
    
    <!-- Berita Terbaru -->
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="card premium-card metric-card gradient-purple h-100">
            <div class="premium-card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-title">BERITA & ARTIKEL LOGISTIK</p>
                        <h3 class="metric-value" id="newsCount">0</h3>
                    </div>
                    <div class="metric-icon-wrapper">
                        <i class="fas fa-rss"></i>
                    </div>
                </div>
                <p class="metric-desc">Total feed berita & analisis tersimpan</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top 5 Risk Countries -->
    <div class="col-lg-5 mb-4 mb-lg-0">
        <div class="card premium-card h-100">
            <div class="premium-card-header">
                <i class="fas fa-fire text-danger fs-5"></i> 
                <span>5 Negara dengan Risiko Tertinggi</span>
            </div>
            <div class="premium-card-body d-flex flex-column justify-content-between" style="min-height: 380px;">
                <div id="topRisksContainer">
                    <p class="text-muted text-center py-5" id="topRisksLoader">
                        <i class="fas fa-circle-notch fa-spin me-2"></i> Memuat data risiko...
                    </p>
                    <div id="topRisksList" style="display: none;">
                        <!-- Digenerate dinamis -->
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 rounded-pill">
                        Lihat Semua Negara <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Risk Distribution Chart -->
    <div class="col-lg-7">
        <div class="card premium-card h-100">
            <div class="premium-card-header">
                <i class="fas fa-chart-pie text-primary fs-5"></i> 
                <span>Distribusi Kerawanan Risiko</span>
            </div>
            <div class="premium-card-body d-flex align-items-center justify-content-center" style="min-height: 380px;">
                <div class="row w-100 align-items-center">
                    <div class="col-md-7 position-relative">
                        <canvas id="riskChart" style="max-height: 280px;"></canvas>
                    </div>
                    <div class="col-md-5 mt-4 mt-md-0">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Status Risiko Rantai Pasok</h5>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success me-2" style="width: 12px; height: 12px; border-radius: 50%; padding: 0;">&nbsp;</span>
                            <span class="flex-grow-1 text-muted fs-6">Rendah (&lt;30)</span>
                            <strong id="distLow" class="fs-6">-</strong>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-warning me-2" style="width: 12px; height: 12px; border-radius: 50%; padding: 0;">&nbsp;</span>
                            <span class="flex-grow-1 text-muted fs-6">Sedang (30-60)</span>
                            <strong id="distMedium" class="fs-6">-</strong>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-2" style="width: 12px; height: 12px; border-radius: 50%; padding: 0;">&nbsp;</span>
                            <span class="flex-grow-1 text-muted fs-6">Tinggi (&gt;60)</span>
                            <strong id="distHigh" class="fs-6">-</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data dari API
        fetch('/api/dashboard/summary')
            .then(response => response.json())
            .then(data => {
                // Update cards
                document.getElementById('totalCountries').textContent = data.total_countries || 0;
                
                const avgRiskVal = data.avg_risk ? data.avg_risk + '%' : '-';
                document.getElementById('avgRisk').textContent = avgRiskVal;
                
                // Warn Average Risk Card dynamically if high
                const avgRiskCard = document.getElementById('avgRiskCard');
                if (data.avg_risk > 60) {
                    avgRiskCard.className = 'card premium-card metric-card gradient-purple h-100'; // danger purple
                } else if (data.avg_risk > 30) {
                    avgRiskCard.className = 'card premium-card metric-card gradient-orange h-100'; // warning amber
                }
                
                document.getElementById('weatherSummary').textContent = data.extreme_weather_count + ' Negara';
                document.getElementById('newsCount').textContent = data.news_count || 0;

                // Update Legend Text
                document.getElementById('distLow').textContent = (data.distribution.low || 0) + ' Neg';
                document.getElementById('distMedium').textContent = (data.distribution.medium || 0) + ' Neg';
                document.getElementById('distHigh').textContent = (data.distribution.high || 0) + ' Neg';
                
                // Update top risks list
                const loader = document.getElementById('topRisksLoader');
                const listContainer = document.getElementById('topRisksList');
                loader.style.display = 'none';
                listContainer.style.display = 'block';
                listContainer.innerHTML = '';
                
                if (data.top_risks && data.top_risks.length > 0) {
                    data.top_risks.forEach(risk => {
                        const div = document.createElement('div');
                        div.className = 'premium-list-item d-flex align-items-center justify-content-between';
                        
                        // Risk bar color class
                        let barClass = 'bg-success';
                        let badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                        if (risk.total_score > 60) {
                            barClass = 'bg-danger';
                            badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                        } else if (risk.total_score > 30) {
                            barClass = 'bg-warning';
                            badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                        }
                        
                        div.innerHTML = `
                            <div class="flex-grow-1 me-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark"><span class="badge bg-light text-secondary border me-2">${risk.code}</span> ${risk.country}</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 3px; background-color: #f1f3f9;">
                                    <div class="progress-bar ${barClass}" role="progressbar" style="width: ${risk.total_score}%" aria-valuenow="${risk.total_score}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <span class="badge ${badgeClass} fs-6 px-3 py-2 rounded-pill fw-bold" style="min-width: 70px;">
                                ${risk.total_score}%
                            </span>
                        `;
                        listContainer.appendChild(div);
                    });
                } else {
                    listContainer.innerHTML = '<div class="alert alert-light text-center">Belum ada data risiko.</div>';
                }

                // Buat chart distribusi risiko menggunakan data dinamis yang valid
                const ctx = document.getElementById('riskChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Rendah (<30)', 'Sedang (30-60)', 'Tinggi (>60)'],
                        datasets: [{
                            data: [
                                data.distribution.low || 0,
                                data.distribution.medium || 0,
                                data.distribution.high || 0
                            ],
                            backgroundColor: [
                                '#10B981', // Emerald green
                                '#F59E0B', // Amber
                                '#EF4444'  // Rose red
                            ],
                            borderWidth: 6,
                            borderColor: '#ffffff',
                            hoverBorderWidth: 6,
                            hoverBorderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { 
                                display: false // disembunyikan karena kita buat legend HTML kustom
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + ' Negara';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching dashboard data:', error);
                document.getElementById('totalCountries').textContent = 'Err';
                document.getElementById('avgRisk').textContent = 'Err';
                document.getElementById('weatherSummary').textContent = 'Err';
            });
    });
</script>
@endpush