@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>📊 Supply Chain Risk Intelligence Dashboard</h1>
        <p>Memantau risiko rantai pasok global secara real-time.</p>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-globe"></i> Total Negara</h5>
                <p class="card-text display-4" id="totalCountries">-</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chart-line"></i> Rata-rata Risiko</h5>
                <p class="card-text display-4" id="avgRisk">-</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-cloud-sun"></i> Cuaca Terkini</h5>
                <p class="card-text" id="weatherSummary">Memuat...</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-newspaper"></i> Berita Terbaru</h5>
                <p class="card-text" id="newsCount">0</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle"></i> 5 Negara dengan Risiko Tertinggi
            </div>
            <div class="card-body">
                <ul class="list-group" id="topRisks">
                    <li class="list-group-item">Memuat data...</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i> Distribusi Risiko
            </div>
            <div class="card-body">
                <canvas id="riskChart" style="height:200px;"></canvas>
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
                document.getElementById('avgRisk').textContent = data.avg_risk ? data.avg_risk + '%' : '-';
                
                // Update top risks list
                const topRisksList = document.getElementById('topRisks');
                topRisksList.innerHTML = '';
                if (data.top_risks && data.top_risks.length > 0) {
                    data.top_risks.forEach(risk => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.innerHTML = `
                            <span><span class="badge bg-secondary me-2">${risk.code}</span> ${risk.country}</span>
                            <span class="badge bg-danger rounded-pill">${risk.total_score}%</span>
                        `;
                        topRisksList.appendChild(li);
                    });
                } else {
                    topRisksList.innerHTML = '<li class="list-group-item">Belum ada data risiko.</li>';
                }

                // Buat chart distribusi risiko (contoh sederhana)
                const ctx = document.getElementById('riskChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Low (<30)', 'Medium (30-60)', 'High (>60)'],
                        datasets: [{
                            data: [30, 40, 30], // nanti bisa diganti dengan data real
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                // Update cuaca dan berita (sementara)
                document.getElementById('weatherSummary').textContent = 'Data cuaca siap';
                document.getElementById('newsCount').textContent = '0';
            })
            .catch(error => {
                console.error('Error fetching dashboard data:', error);
                document.getElementById('totalCountries').textContent = 'Error';
                document.getElementById('avgRisk').textContent = 'Error';
            });
    });
</script>
@endpush