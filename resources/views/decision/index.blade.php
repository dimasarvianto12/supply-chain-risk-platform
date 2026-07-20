@extends('layouts.app')

@section('title', 'Import Analyzer')

@push('styles')
<style>
    .progress {
        height: 8px;
        background-color: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
    }
    .risk-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        letter-spacing: 0.3px;
    }
    .risk-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
        float: right;
    }
    .recommendation-banner {
        padding: 20px;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .recommendation-banner.success {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .recommendation-banner.warning {
        background-color: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .recommendation-banner.danger {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .recommendation-title {
        font-size: 1.25rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }
    .recommendation-desc {
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-balance-scale text-primary"></i> Import Analyzer (Decision Support)
        </h1>
        <p class="text-muted fs-5 mb-0">Simulasi pengambilan keputusan impor berdasarkan 5 pilar risiko utama logistik.</p>
        <hr>
    </div>
</div>

<div class="row g-4">
    <!-- Form Card -->
    <div class="col-lg-4">
        <div class="card premium-card">
            <div class="premium-card-header">
                <i class="fas fa-route text-primary"></i>
                <span>Jalur Pengiriman</span>
            </div>
            <div class="premium-card-body">
                <form id="analyzeForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">NEGARA ASAL (EKSPORTIR)</label>
                        <select class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" id="origin_country_id" name="origin_country_id" required>
                            <option value="">Pilih Negara Asal</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" data-name="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">PELABUHAN MUAT (ORIGIN PORT)</label>
                        <select class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" id="origin_port_id" name="origin_port_id" required disabled>
                            <option value="">Pilih Negara Asal Terlebih Dahulu</option>
                            @foreach($ports as $p)
                                <option value="{{ $p->id }}" data-country="{{ $p->country }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">NEGARA TUJUAN (IMPORTIR)</label>
                        <select class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" id="dest_country_id" name="destination_country_id" required>
                            <option value="">Pilih Negara Tujuan</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" data-name="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">PELABUHAN BONGKAR (DESTINATION PORT)</label>
                        <select class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" id="dest_port_id" name="destination_port_id" required disabled>
                            <option value="">Pilih Negara Tujuan Terlebih Dahulu</option>
                            @foreach($ports as $p)
                                <option value="{{ $p->id }}" data-country="{{ $p->country }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold" id="btnAnalyze">
                        <i class="fas fa-search-dollar me-1"></i> Analisis Risiko Impor
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Panel -->
    <div class="col-lg-8" id="resultContainer" style="display: none;">
        <div class="row g-4">
            <!-- Risks card -->
            <div class="col-md-6">
                <div class="card premium-card h-100">
                    <div class="premium-card-header">
                        <i class="fas fa-chart-pie text-primary"></i>
                        <span>Komposisi Risiko Gabungan</span>
                    </div>
                    <div class="premium-card-body">
                        <div class="mb-3">
                            <span class="risk-label">TOTAL RISIKO IMPOR</span>
                            <span class="risk-value" id="valTotal">0%</span>
                            <div class="progress mt-2" style="height: 12px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="barTotal" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <hr class="my-4" style="border-color: #e2e8f0;">
                        <div class="mb-3">
                            <span class="risk-label">1. Gangguan Pelabuhan (Congestion)</span>
                            <span class="risk-value" id="valPort">0%</span>
                            <div class="progress mt-1.5">
                                <div class="progress-bar bg-info" id="barPort" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="risk-label">2. Risiko Cuaca (Pelayaran)</span>
                            <span class="risk-value" id="valWeather">0%</span>
                            <div class="progress mt-1.5">
                                <div class="progress-bar bg-primary" id="barWeather" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="risk-label">3. Risiko Geopolitik (Berita)</span>
                            <span class="risk-value" id="valPolitical">0%</span>
                            <div class="progress mt-1.5">
                                <div class="progress-bar bg-warning" id="barPolitical" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="risk-label">4. Fluktuasi Nilai Tukar</span>
                            <span class="risk-value" id="valCurrency">0%</span>
                            <div class="progress mt-1.5">
                                <div class="progress-bar bg-success" id="barCurrency" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="risk-label">5. Risiko Inflasi (Biaya Produksi)</span>
                            <span class="risk-value" id="valInflation">0%</span>
                            <div class="progress mt-1.5">
                                <div class="progress-bar bg-danger" id="barInflation" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Shipping card & Recommendation -->
            <div class="col-md-6 d-flex flex-column justify-content-between">
                <div class="card premium-card mb-4" style="flex-grow: 1;">
                    <div class="premium-card-header">
                        <i class="fas fa-anchor text-primary"></i>
                        <span>Status & Estimasi Pengiriman</span>
                    </div>
                    <div class="premium-card-body d-flex flex-column justify-content-between">
                        <div>
                            <p class="mb-2"><i class="fas fa-plane-departure text-muted me-1.5" style="width: 16px;"></i><strong>Asal:</strong> <span id="txtOriginPort" class="text-dark"></span> (<span id="txtOriginCongestion" class="fw-bold"></span>)</p>
                            <p class="mb-3"><i class="fas fa-plane-arrival text-muted me-1.5" style="width: 16px;"></i><strong>Tujuan:</strong> <span id="txtDestPort" class="text-dark"></span> (<span id="txtDestCongestion" class="fw-bold"></span>)</p>
                            <hr class="my-3" style="border-color: #e2e8f0;">
                            <p class="mb-2 text-secondary" style="font-size: 0.9rem;"><i class="fas fa-route me-1.5" style="width: 16px;"></i> Jarak Pelayaran: <strong id="txtDistance" class="text-dark">0 km</strong></p>
                            <p class="mb-2 text-secondary" style="font-size: 0.9rem;"><i class="fas fa-hourglass-half me-1.5" style="width: 16px;"></i> Waktu Berlayar: <strong id="txtBaseDays" class="text-dark">0 Hari</strong> <span class="small text-muted">(Kecepatan 20 knots)</span></p>
                            <p class="mb-4 text-secondary" style="font-size: 0.9rem;"><i class="fas fa-clock me-1.5" style="width: 16px;"></i> Delay Kemacetan: <strong id="txtTotalDelay" class="text-danger">0 Hari</strong></p>
                        </div>
                        
                        <div class="alert alert-primary mb-0 py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-between">
                            <span class="fw-bold"><i class="fas fa-calendar-check me-1.5"></i> Total Estimasi Tiba:</span>
                            <strong id="txtTotalDays" class="fs-5 text-primary">0 Hari</strong>
                        </div>
                    </div>
                </div>

                <div id="recommendationBox" class="recommendation-banner success">
                    <div class="recommendation-title" id="recTitle"><i class="fas fa-check-circle"></i> AMAN</div>
                    <p class="recommendation-desc" id="recDesc">Kondisi sangat kondusif untuk melakukan impor.</p>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card premium-card">
                    <div class="premium-card-header">
                        <i class="fas fa-map-marked-alt text-primary"></i>
                        <span>Peta Rute Pelayaran</span>
                    </div>
                    <div class="premium-card-body p-0">
                        <div id="routeMap" style="height: 350px; width: 100%; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; z-index: 1;"></div>
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
    const form = document.getElementById('analyzeForm');
    const btn = document.getElementById('btnAnalyze');
    const resultContainer = document.getElementById('resultContainer');

    let routeMap = null;
    let routeLine = null;
    let originMarker = null;
    let destMarker = null;

    const originCountrySelect = document.getElementById('origin_country_id');
    const originPortSelect = document.getElementById('origin_port_id');
    
    const destCountrySelect = document.getElementById('dest_country_id');
    const destPortSelect = document.getElementById('dest_port_id');

    const originPortOptions = Array.from(originPortSelect.options).filter(opt => opt.value !== "");
    const destPortOptions = Array.from(destPortSelect.options).filter(opt => opt.value !== "");

    function filterPorts(countrySelect, portSelect, allPortOptions) {
        portSelect.innerHTML = '<option value="">Pilih Pelabuhan</option>';
        
        const selectedOption = countrySelect.options[countrySelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            portSelect.disabled = true;
            portSelect.innerHTML = '<option value="">Pilih Negara Terlebih Dahulu</option>';
            return;
        }

        const countryName = selectedOption.getAttribute('data-name');
        const matchingPorts = allPortOptions.filter(opt => opt.getAttribute('data-country') === countryName);
        
        matchingPorts.forEach(opt => {
            portSelect.appendChild(opt.cloneNode(true));
        });

        portSelect.disabled = false;
        
        if (matchingPorts.length > 0) {
            portSelect.selectedIndex = 1;
        } else {
            portSelect.innerHTML = '<option value="">Tidak ada pelabuhan di negara ini</option>';
            portSelect.disabled = true;
        }
    }

    originCountrySelect.addEventListener('change', () => filterPorts(originCountrySelect, originPortSelect, originPortOptions));
    destCountrySelect.addEventListener('change', () => filterPorts(destCountrySelect, destPortSelect, destPortOptions));

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menganalisis...';
        btn.disabled = true;

        const formData = new FormData(form);

        fetch('/decision/analyze', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            resultContainer.style.display = 'block';

            updateProgress('Total', data.totalRisk);
            updateProgress('Port', data.portRisk);
            updateProgress('Weather', data.weatherRisk);
            updateProgress('Political', data.politicalRisk);
            updateProgress('Currency', data.currencyRisk);
            updateProgress('Inflation', data.inflationRisk);

            document.getElementById('txtOriginPort').textContent = data.origin.port;
            document.getElementById('txtOriginCongestion').textContent = data.origin.congestion.toUpperCase() + ' (Delay ' + data.origin.delay + ' hr)';
            
            document.getElementById('txtDestPort').textContent = data.destination.port;
            document.getElementById('txtDestCongestion').textContent = data.destination.congestion.toUpperCase() + ' (Delay ' + data.destination.delay + ' hr)';
            
            document.getElementById('txtDistance').textContent = data.shipping.distance_km + ' km';
            document.getElementById('txtBaseDays').textContent = data.shipping.base_days + ' Hari';
            document.getElementById('txtTotalDelay').textContent = (data.origin.delay + data.destination.delay) + ' Hari';
            document.getElementById('txtTotalDays').textContent = data.shipping.total_days + ' Hari';

            const recBox = document.getElementById('recommendationBox');
            const color = data.recommendation.color;
            recBox.className = 'recommendation-banner ' + color;
            
            let icon = 'fa-check-circle';
            if (color === 'warning') icon = 'fa-exclamation-triangle';
            if (color === 'danger') icon = 'fa-times-circle';

            document.getElementById('recTitle').innerHTML = `<i class="fas ${icon}"></i> ${data.recommendation.status.toUpperCase()}`;
            document.getElementById('recDesc').textContent = data.recommendation.message;

            // Render the shipping map
            renderMap(data.origin.lat, data.origin.lng, data.destination.lat, data.destination.lng, data.origin.port, data.destination.port);
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat menghubungi server.');
        })
        .finally(() => {
            btn.innerHTML = '<i class="fas fa-search-dollar me-1"></i> Analisis Risiko Impor';
            btn.disabled = false;
        });
    });

    function updateProgress(id, value) {
        document.getElementById(`val${id}`).textContent = value + '%';
        const bar = document.getElementById(`bar${id}`);
        bar.style.width = value + '%';
        
        if (id === 'Total') {
            bar.className = 'progress-bar progress-bar-striped progress-bar-animated ' + (value > 70 ? 'bg-danger' : (value > 40 ? 'bg-warning text-dark' : 'bg-success'));
        }
    }

    function renderMap(originLat, originLng, destLat, destLng, originName, destName) {
        if (!routeMap) {
            routeMap = L.map('routeMap').setView([20, 0], 2);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(routeMap);
        }
        
        if (originMarker) routeMap.removeLayer(originMarker);
        if (destMarker) routeMap.removeLayer(destMarker);
        if (routeLine) routeMap.removeLayer(routeLine);
        
        // Custom icons
        const iconStart = L.divIcon({
            className: 'custom-div-icon',
            html: '<div style="background-color:#ef4444; width:12px; height:12px; border-radius:50%; border:2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });
        const iconEnd = L.divIcon({
            className: 'custom-div-icon',
            html: '<div style="background-color:#10b981; width:12px; height:12px; border-radius:50%; border:2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        originMarker = L.marker([originLat, originLng], {icon: iconStart}).bindPopup('<b>Asal (Muat):</b><br>' + originName).addTo(routeMap);
        destMarker = L.marker([destLat, destLng], {icon: iconEnd}).bindPopup('<b>Tujuan (Bongkar):</b><br>' + destName).addTo(routeMap);
        
        // Draw polyline (curved or straight depending on library, we use straight dash)
        const latlngs = [
            [originLat, originLng],
            [destLat, destLng]
        ];
        
        routeLine = L.polyline(latlngs, {color: '#6366f1', weight: 3, dashArray: '8, 8', opacity: 0.8}).addTo(routeMap);
        
        // Force Leaflet to recalculate size when unhidden
        setTimeout(() => {
            routeMap.invalidateSize();
            routeMap.fitBounds(routeLine.getBounds(), {padding: [50, 50]});
        }, 100);
    }
});
</script>
@endpush
