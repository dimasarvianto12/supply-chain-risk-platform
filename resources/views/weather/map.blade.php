@extends('layouts.app')

@section('title', 'Weather Monitoring Map')

@section('styles')
<style>
    #weatherMap {
        height: 600px;
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-height: 500px; /* Tambahan untuk memastikan ukuran */
        background-color: #f0f0f0; /* Warna latar sementara agar terlihat */
    }
    .weather-popup .country-name {
        font-weight: bold;
        font-size: 1.1em;
    }
    .weather-popup .weather-detail {
        margin: 3px 0;
    }
    .alert-fixed {
        margin-top: 10px;
    }
    /* Pastikan container tidak memiliki overflow tersembunyi */
    .container-fluid {
        overflow: visible;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-cloud-sun"></i> Global Weather Monitoring</h1>
        <p>Peta cuaca dunia dengan kondisi terkini untuk setiap negara.</p>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Container peta -->
        <div id="weatherMap"></div>
        <!-- Tempat untuk pesan error jika peta gagal -->
        <div id="weatherMapFallback" class="mt-2"></div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Legenda Cuaca
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><span class="badge bg-primary"><i class="fas fa-sun"></i> Cerah</span></div>
                    <div class="col-md-3"><span class="badge bg-secondary"><i class="fas fa-cloud"></i> Berawan</span></div>
                    <div class="col-md-3"><span class="badge bg-info"><i class="fas fa-cloud-rain"></i> Hujan</span></div>
                    <div class="col-md-3"><span class="badge bg-warning"><i class="fas fa-bolt"></i> Badai</span></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3"><span class="badge bg-danger"><i class="fas fa-wind"></i> Angin Kencang</span></div>
                    <div class="col-md-3"><span class="badge bg-light text-dark"><i class="fas fa-smog"></i> Kabut</span></div>
                    <div class="col-md-3"><span class="badge bg-success"><i class="fas fa-snowflake"></i> Salju</span></div>
                    <div class="col-md-3"><span class="badge bg-dark"><i class="fas fa-question"></i> Tidak Diketahui</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Pastikan Leaflet JS dimuat dari CDN yang stabil -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM siap.');

    // Tunggu 200ms agar DOM dan CSS selesai di-render
    setTimeout(function() {
        try {
            // Cek Leaflet
            if (typeof L === 'undefined') {
                throw new Error('Leaflet tidak ditemukan.');
            }
            console.log('✅ Leaflet ditemukan.');

            // Ambil container
            const container = document.getElementById('weatherMap');
            if (!container) {
                throw new Error('Container #weatherMap tidak ditemukan.');
            }

            // Cek ukuran container
            console.log('📐 Ukuran container:', container.offsetWidth, 'x', container.offsetHeight);

            // Jika ukuran 0, beri ukuran minimal
            if (container.offsetHeight === 0) {
                container.style.height = '600px';
                console.log('⚠️ Container diperbaiki ukurannya menjadi 600px.');
            }

            // Inisialisasi peta
            const map = L.map('weatherMap', {
                center: [20, 0],
                zoom: 2,
                zoomControl: true,
            });

            // Tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            console.log('✅ Peta berhasil diinisialisasi.');

            // Paksa refresh ukuran peta
            map.invalidateSize();

            // Ambil data cuaca
            fetch('/api/weather')
                .then(response => {
                    if (!response.ok) throw new Error('HTTP error ' + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('📦 Data cuaca:', data);

                    if (!data || data.length === 0) {
                        document.getElementById('weatherMapFallback').innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Belum ada data cuaca. Jalankan <code>php artisan app:fetch-weather</code>
                            </div>
                        `;
                        return;
                    }

                    const markers = [];
                    let markerCount = 0;

                    data.forEach(country => {
                        if (!country.weather) {
                            console.warn(`⚠️ ${country.name} tidak punya cuaca`);
                            return;
                        }

                        const lat = parseFloat(country.latitude);
                        const lng = parseFloat(country.longitude);

                        if (isNaN(lat) || isNaN(lng)) {
                            console.warn(`⚠️ Koordinat tidak valid untuk ${country.name}`);
                            return;
                        }

                        console.log(`📍 ${country.name}: lat=${lat}, lng=${lng}`);

                        // Buat marker default
                        const marker = L.marker([lat, lng])
                            .addTo(map)
                            .bindPopup(`
                                <div>
                                    <strong>${country.name}</strong> (${country.code})<br>
                                    🌡️ ${country.weather.temperature ?? 'N/A'} °C<br>
                                    💧 ${country.weather.humidity ?? 'N/A'}%<br>
                                    💨 ${country.weather.wind_speed ?? 'N/A'} km/h<br>
                                    ☁️ ${country.weather.description ?? 'N/A'}
                                </div>
                            `);

                        markers.push(marker);
                        markerCount++;
                    });

                    console.log(`✅ ${markerCount} marker dibuat.`);

                    if (markers.length > 0) {
                        // Zoom ke semua marker
                        const group = L.featureGroup(markers);
                        map.fitBounds(group.getBounds(), { padding: [50, 50] });

                        // Paksa refresh ukuran peta SETELAH marker ditambahkan
                        setTimeout(() => {
                            map.invalidateSize();
                            console.log('✅ Peta di-refresh ukurannya.');
                        }, 300);

                        console.log(`✅ ${markers.length} marker ditambahkan ke peta.`);
                    } else {
                        console.warn('⚠️ Tidak ada marker yang valid.');
                        document.getElementById('weatherMapFallback').innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Tidak ada data cuaca yang valid untuk ditampilkan.
                            </div>
                        `;
                    }

                    // Tambahan: log info peta
                    console.log('🗺️ Peta bounds:', map.getBounds());
                })
                .catch(error => {
                    console.error('❌ Fetch error:', error);
                    document.getElementById('weatherMapFallback').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> 
                            Gagal memuat data: ${error.message}
                        </div>
                    `;
                });

        } catch (error) {
            console.error('❌ Uncaught error:', error);
            document.getElementById('weatherMapFallback').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Terjadi kesalahan: ${error.message}
                </div>
            `;
        }
    }, 200); // Delay 200ms agar DOM stabil
});
</script>
@endpush