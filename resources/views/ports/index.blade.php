@extends('layouts.app')

@section('title', 'Port Location Dashboard')

@section('styles')
<style>
    #portMap {
        height: 550px;
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .port-search-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .port-list {
        max-height: 550px;
        overflow-y: auto;
        background: white;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .port-list .list-group-item {
        border-left: 3px solid #007bff;
        margin-bottom: 5px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .port-list .list-group-item:hover {
        background: #e9ecef;
    }
    .port-list .list-group-item .port-name {
        font-weight: 600;
    }
    .port-list .list-group-item .port-country {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .leaflet-popup-content {
        font-size: 0.95rem;
    }
    .leaflet-popup-content strong {
        display: block;
        font-size: 1.05rem;
        margin-bottom: 3px;
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
        padding: 30px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 2.5rem;
        color: #dee2e6;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-ship"></i> Port Location Dashboard</h1>
        <p>Lokasi pelabuhan dunia dengan fitur pencarian dan marker interaktif.</p>
        <hr>
    </div>
</div>

<!-- Search & Filter -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="port-search-box">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="searchPort" class="form-label">Cari Pelabuhan</label>
                    <input type="text" id="searchPort" class="form-control" placeholder="Nama pelabuhan...">
                </div>
                <div class="col-md-4">
                    <label for="searchCountry" class="form-label">Pilih Negara</label>
                    <select id="searchCountry" class="form-select">
                        <option value="">Semua Negara</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button id="searchBtn" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map & List -->
<div class="row">
    <div class="col-lg-8">
        <div id="portMap"></div>
    </div>
    <div class="col-lg-4">
        <div class="port-list" id="portList">
            <div class="text-center py-4">
                <div class="loading-spinner"></div>
                <p class="mt-2">Memuat pelabuhan...</p>
            </div>
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
    const mapContainer = document.getElementById('portMap');
    const portList = document.getElementById('portList');
    const searchPort = document.getElementById('searchPort');
    const searchCountry = document.getElementById('searchCountry');
    const searchBtn = document.getElementById('searchBtn');

    let map = null;
    let markers = [];
    let allPorts = [];

    // ==========================================
    // 2. INISIALISASI PETA
    // ==========================================
    function initMap() {
        map = L.map('portMap').setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        console.log('🗺️ Peta pelabuhan siap.');
    }

    // ==========================================
    // 3. FUNGSI MEMUAT DATA PELABUHAN
    // ==========================================
    function loadPorts(params = {}) {
        const query = new URLSearchParams(params);
        const url = `/api/ports?${query.toString()}`;

        portList.innerHTML = `
            <div class="text-center py-4">
                <div class="loading-spinner"></div>
                <p class="mt-2">Memuat pelabuhan...</p>
            </div>
        `;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data');
                return response.json();
            })
            .then(data => {
                console.log('📦 Data pelabuhan:', data);
                allPorts = data.data || data;
                if (!Array.isArray(allPorts)) {
                    allPorts = [];
                }
                renderPorts(allPorts);
            })
            .catch(error => {
                console.error('❌ Error:', error);
                portList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle" style="color: #dc3545;"></i>
                        <p class="text-danger">Gagal memuat data: ${error.message}</p>
                    </div>
                `;
            });
    }

    // ==========================================
    // 4. RENDER PETA DAN DAFTAR
    // ==========================================
    function renderPorts(ports) {
        // Hapus marker lama
        if (markers.length) {
            markers.forEach(m => map.removeLayer(m));
            markers = [];
        }

        if (!ports || ports.length === 0) {
            portList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-ship"></i>
                    <p>Tidak ada pelabuhan ditemukan.</p>
                    <small class="text-muted">Coba ubah kata kunci pencarian.</small>
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

            const marker = L.marker([lat, lng])
                .addTo(map)
                .bindPopup(`
                    <strong>${port.name}</strong>
                    <span style="display:block; color:#6c757d;">${port.country}</span>
                    <span class="badge bg-secondary mt-1">${port.status || 'active'}</span>
                `);
            markers.push(marker);
            markerGroup.addLayer(marker);

            listHtml += `
                <div class="list-group-item" data-lat="${lat}" data-lng="${lng}">
                    <div class="port-name">${port.name}</div>
                    <div class="port-country"><i class="fas fa-map-marker-alt"></i> ${port.country}</div>
                    <span class="badge bg-secondary">${port.status || 'active'}</span>
                </div>
            `;
        });

        if (markers.length > 0) {
            map.fitBounds(markerGroup.getBounds(), { padding: [50, 50] });
            setTimeout(() => map.invalidateSize(), 200);
        }

        portList.innerHTML = listHtml;

        document.querySelectorAll('.list-group-item').forEach(item => {
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

        console.log(`✅ ${markers.length} pelabuhan ditampilkan.`);
    }

    // ==========================================
    // 5. FUNGSI PENCARIAN
    // ==========================================
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