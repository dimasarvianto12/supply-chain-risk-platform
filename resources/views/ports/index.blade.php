@extends('layouts.app')

@section('title', 'Port Location Dashboard')

@push('styles')
<style>
    #portMap {
        height: 580px;
        width: 100%;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .port-list-container {
        height: 580px;
        display: flex;
        flex-direction: column;
    }
    .port-list {
        flex-grow: 1;
        overflow-y: auto;
        padding-right: 4px;
    }
    .port-list-item {
        border: 1px solid #f1f3f9;
        border-radius: 12px;
        margin-bottom: 10px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .port-list-item:hover {
        background: #f8fafc;
        transform: translateX(4px);
        border-color: #cbd5e1;
    }
    
    /* Left border states matching congestion */
    .port-list-item.border-lancar { border-left: 4px solid #10B981 !important; }
    .port-list-item.border-sedang { border-left: 4px solid #F59E0B !important; }
    .port-list-item.border-macet { border-left: 4px solid #EF4444 !important; }

    .port-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }
    .port-country {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 2px;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .port-popup h6 {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
    }
    
    /* Custom Map Icon Bubble */
    .port-icon-bubble {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
    }
    .port-icon-bubble:hover {
        transform: scale(1.15);
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
        padding: 40px 20px;
        color: #64748b;
    }
    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 12px;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-ship text-primary"></i> Port Location Dashboard
            <span id="totalPorts" class="badge bg-primary-subtle text-primary border border-primary-subtle ms-2 fs-5 align-middle px-3 py-1.5 rounded-pill">0 Pelabuhan</span>
        </h1>
        <p class="text-muted fs-5 mb-0">Lokasi pelabuhan dunia dengan fitur pencarian dan penanda kemacetan rute.</p>
        <hr>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card premium-card">
            <div class="premium-card-header">
                <i class="fas fa-filter text-primary"></i>
                <span>Cari & Filter Pelabuhan</span>
            </div>
            <div class="premium-card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="searchPort" class="form-label fw-bold text-secondary mb-2" style="font-size: 0.85rem;">CARI PELABUHAN</label>
                        <input type="text" id="searchPort" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" placeholder="Nama pelabuhan...">
                    </div>
                    <div class="col-md-4">
                        <label for="searchCountry" class="form-label fw-bold text-secondary mb-2" style="font-size: 0.85rem;">PILIH NEGARA</label>
                        <select id="searchCountry" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                            <option value="">Semua Negara</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->name }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button id="searchBtn" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map & List Grid -->
<div class="row g-4 mb-4">
    <!-- Map Column -->
    <div class="col-lg-8">
        <div id="portMap"></div>
    </div>
    
    <!-- Sidebar List Column -->
    <div class="col-lg-4">
        <div class="card premium-card port-list-container">
            <div class="premium-card-header bg-light">
                <i class="fas fa-anchor text-primary"></i>
                <span>Daftar Pelabuhan</span>
            </div>
            <div class="premium-card-body d-flex flex-column p-3" style="overflow: hidden; height: 100%;">
                <div class="port-list" id="portList">
                    <div class="text-center py-5 text-muted">
                        <div class="loading-spinner"></div>
                        <p class="mt-2">Mengunduh data pelabuhan...</p>
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
    const mapContainer = document.getElementById('portMap');
    const portList = document.getElementById('portList');
    const searchPort = document.getElementById('searchPort');
    const searchCountry = document.getElementById('searchCountry');
    const searchBtn = document.getElementById('searchBtn');

    let map = null;
    let markers = [];
    let allPorts = [];

    // Initialize Map with modern CartoDB Voyager layer
    function initMap() {
        map = L.map('portMap').setView([20, 0], 2.2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);
        console.log('🗺️ Peta pelabuhan siap.');
    }

    // Load port data from API
    function loadPorts(params = {}) {
        const query = new URLSearchParams(params);
        const url = `/api/ports?${query.toString()}`;

        portList.innerHTML = `
            <div class="text-center py-5 text-muted">
                <div class="loading-spinner"></div>
                <p class="mt-2">Menghubungi database pelabuhan...</p>
            </div>
        `;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data');
                return response.json();
            })
            .then(data => {
                allPorts = data.data || data;
                if (!Array.isArray(allPorts)) {
                    allPorts = [];
                }
                renderPorts(allPorts);
            })
            .catch(error => {
                console.error('❌ Error:', error);
                portList.innerHTML = `
                    <div class="empty-state text-danger">
                        <i class="fas fa-exclamation-circle text-danger mb-2"></i>
                        <p class="mb-0">Gagal memuat data: ${error.message}</p>
                    </div>
                `;
            });
    }

    // Render Leaflet markers and sidebar HTML
    function renderPorts(ports) {
        if (markers.length) {
            markers.forEach(m => map.removeLayer(m));
            markers = [];
        }

        if (!ports || ports.length === 0) {
            portList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-ship"></i>
                    <p class="fw-bold mb-1">Tidak ada pelabuhan</p>
                    <small class="text-muted">Coba ubah filter atau kata kunci pencarian Anda.</small>
                </div>
            `;
            return;
        }

        let listHtml = '';
        const markerGroup = L.featureGroup();

        ports.forEach(port => {
            const lat = parseFloat(port.latitude);
            const lng = parseFloat(port.longitude);

            if (isNaN(lat) || isNaN(lng)) return;

            // Determine dynamic classes based on congestion level
            let iconColorClass = 'bg-success';
            let borderClass = 'border-lancar';
            let badgeClass = 'bg-success-subtle text-success border border-success-subtle';
            let label = 'Lancar';
            
            if (port.congestion_level === 'high') {
                iconColorClass = 'bg-danger';
                borderClass = 'border-macet';
                badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                label = `Macet (Delay ${port.delay_days} Hr)`;
            } else if (port.congestion_level === 'medium') {
                iconColorClass = 'bg-warning text-dark';
                borderClass = 'border-sedang';
                badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                label = `Sedang (Delay ${port.delay_days} Hr)`;
            }

            // Custom Styled DivIcon
            const customIcon = L.divIcon({
                html: `<div class="port-icon-bubble ${iconColorClass}"><i class="fas fa-anchor text-white" style="font-size:0.85rem;"></i></div>`,
                className: 'leaflet-port-marker',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            const marker = L.marker([lat, lng], {icon: customIcon})
                .addTo(map)
                .bindPopup(`
                    <div class="port-popup">
                        <h6>${port.name}</h6>
                        <p class="text-muted mb-2"><i class="fas fa-map-marker-alt"></i> ${port.country}</p>
                        <span class="badge ${badgeClass} px-2.5 py-1.5 rounded">${label}</span>
                    </div>
                `);
            markers.push(marker);
            markerGroup.addLayer(marker);

            listHtml += `
                <div class="port-list-item ${borderClass}" data-lat="${lat}" data-lng="${lng}">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <div class="port-name">${port.name}</div>
                            <div class="port-country"><i class="fas fa-map-marker-alt text-primary me-1"></i>${port.country}</div>
                        </div>
                        <span class="badge ${badgeClass} rounded-pill px-2.5 py-1.5 fw-bold" style="font-size:0.75rem; min-width: 60px; text-align: center;">
                            ${(port.congestion_level || 'lancar').toUpperCase()}
                        </span>
                    </div>
                </div>
            `;
        });

        if (markers.length > 0) {
            map.fitBounds(markerGroup.getBounds(), { padding: [40, 40] });
            setTimeout(() => map.invalidateSize(), 300);
        }

        portList.innerHTML = listHtml;

        // Click interaction: Fly to port and open popup
        document.querySelectorAll('.port-list-item').forEach(item => {
            item.addEventListener('click', function() {
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);
                if (!isNaN(lat) && !isNaN(lng)) {
                    map.setView([lat, lng], 8);
                    markers.forEach(m => {
                        const pos = m.getLatLng();
                        if (pos.lat === lat && pos.lng === lng) {
                            m.openPopup();
                        }
                    });
                }
            });
        });

        const totalPortsEl = document.getElementById('totalPorts');
        if (totalPortsEl) {
            totalPortsEl.textContent = `${ports.length} Pelabuhan`;
        }
    }

    function searchPorts() {
        const name = searchPort.value.trim();
        const country = searchCountry.value.trim();
        const params = {};
        if (name) params.name = name;
        if (country) params.country = country;
        loadPorts(params);
    }

    // ==========================================
    // 6. EVENT LISTENERS
    // ==========================================
    searchBtn.addEventListener('click', searchPorts);
    searchPort.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') searchPorts();
    });
    // Otomatis cari saat dropdown berubah
    searchCountry.addEventListener('change', searchPorts);

    // ==========================================
    // 7. INISIALISASI
    // ==========================================
    initMap();
    setTimeout(() => {
        loadPorts();
    }, 300);
});
</script>
@endpush