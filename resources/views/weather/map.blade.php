@extends('layouts.app')

@section('title', 'Weather Monitoring Map')

@push('styles')
<style>
    #weatherMap {
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
    }
    .weather-popup {
        font-family: 'Plus Jakarta Sans', sans-serif;
        padding: 5px;
    }
    .weather-popup h6 {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }
    .weather-popup-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        border-bottom: 1px solid #f1f3f9;
        padding: 4px 0;
    }
    .weather-popup-row:last-child {
        border-bottom: none;
    }
    .weather-icon-bubble {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
    }
    .weather-icon-bubble:hover {
        transform: scale(1.15);
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #f1f3f9;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .legend-item:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }
    .legend-item.sunny { border-left: 4px solid #F59E0B; }
    .legend-item.cloudy { border-left: 4px solid #64748B; }
    .legend-item.rainy { border-left: 4px solid #3B82F6; }
    .legend-item.stormy { border-left: 4px solid #EF4444; }
    .legend-item.windy { border-left: 4px solid #06B6D4; }
    .legend-item.foggy { border-left: 4px solid #1E293B; }
    
    .legend-text {
        display: flex;
        flex-direction: column;
    }
    .legend-title {
        font-weight: 700;
        color: #1e293b;
    }
    .legend-desc {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 1px;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-cloud-sun text-warning"></i> Global Weather Monitoring
        </h1>
        <p class="text-muted fs-5 mb-0">Peta cuaca dunia dengan kondisi terkini untuk setiap negara secara real-time.</p>
        <hr>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="row mb-4 g-4">
            <!-- Peta (col-md-7) -->
            <div class="col-md-7">
                <div id="weatherMap"></div>
                <div id="weatherMapFallback" class="mt-2"></div>
            </div>
            
            <!-- Legenda (col-md-5) -->
            <div class="col-md-5">
                <div class="card premium-card h-100">
                    <div class="premium-card-header">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span>Legenda Cuaca</span>
                    </div>
                    <div class="premium-card-body d-flex flex-column gap-3 justify-content-center">
                        <div class="legend-item sunny">
                            <span class="badge bg-warning p-2 rounded-circle"><i class="fas fa-sun text-white"></i></span> 
                            <div class="legend-text">
                                <span class="legend-title">Cerah / Terang</span>
                                <span class="legend-desc">Kondisi langit cerah, pelayaran sangat aman.</span>
                            </div>
                        </div>
                        <div class="legend-item cloudy">
                            <span class="badge bg-secondary p-2 rounded-circle"><i class="fas fa-cloud text-white"></i></span> 
                            <div class="legend-text">
                                <span class="legend-title">Berawan / Mendung</span>
                                <span class="legend-desc">Langit tertutup awan, jarak pandang normal.</span>
                            </div>
                        </div>
                        <div class="legend-item rainy">
                            <span class="badge bg-primary p-2 rounded-circle"><i class="fas fa-cloud-showers-heavy text-white"></i></span> 
                            <div class="legend-text">
                                <span class="legend-title">Hujan Ringan/Lebat</span>
                                <span class="legend-desc">Mengurangi jarak pandang, waspada genangan.</span>
                            </div>
                        </div>
                        <div class="legend-item stormy">
                            <span class="badge bg-danger p-2 rounded-circle"><i class="fas fa-bolt text-white"></i></span> 
                            <div class="legend-text">
                                <span class="legend-title">Badai / Petir</span>
                                <span class="legend-desc">Risiko tinggi bagi kapal kargo, hindari wilayah ini.</span>
                            </div>
                        </div>
                        <div class="legend-item windy">
                            <span class="badge bg-info p-2 rounded-circle"><i class="fas fa-wind text-white"></i></span> 
                            <div class="legend-text">
                                <span class="legend-title">Angin Kencang</span>
                                <span class="legend-desc">Dapat menimbulkan gelombang tinggi di lautan.</span>
                            </div>
                        </div>
                        <div class="legend-item foggy">
                            <span class="badge bg-dark p-2 rounded-circle"><i class="fas fa-smog text-white"></i></span> 
                            <div class="legend-text">
                                <span class="legend-title">Kabut / Smog</span>
                                <span class="legend-desc">Membatasi navigasi visual secara signifikan.</span>
                            </div>
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
    console.log('✅ DOM siap.');

    // Tunggu 200ms agar DOM dan CSS selesai di-render
    setTimeout(function() {
        try {
            if (typeof L === 'undefined') {
                throw new Error('Leaflet tidak ditemukan.');
            }

            const container = document.getElementById('weatherMap');
            if (!container) {
                throw new Error('Container #weatherMap tidak ditemukan.');
            }

            // Inisialisasi peta
            const map = L.map('weatherMap', {
                center: [20, 0],
                zoom: 2.3,
                zoomControl: true,
            });

            // Menggunakan peta CartoDB Voyager yang bernuansa premium, minimalis, dan modern
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // Ambil data cuaca
            fetch('/api/weather')
                .then(response => {
                    if (!response.ok) throw new Error('HTTP error ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (!data || data.length === 0) {
                        document.getElementById('weatherMapFallback').innerHTML = `
                            <div class="alert alert-warning rounded-3 border-0 shadow-sm mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i> 
                                Belum ada data cuaca. Jalankan perintah <code>php artisan app:fetch-weather</code> di terminal Anda.
                            </div>
                        `;
                        return;
                    }

                    const markers = [];
                    data.forEach(country => {
                        if (!country.weather) return;

                        const lat = parseFloat(country.latitude);
                        const lng = parseFloat(country.longitude);

                        if (isNaN(lat) || isNaN(lng)) return;

                        // Tentukan ikon & kelas warna berdasarkan deskripsi cuaca
                        let iconHtml = '';
                        let colorClass = 'bg-secondary';
                        const desc = (country.weather.description || '').toLowerCase();
                        
                        if (desc.includes('storm') || desc.includes('badai') || desc.includes('thunderstorm') || desc.includes('lightning')) {
                            iconHtml = '<i class="fas fa-bolt text-white"></i>';
                            colorClass = 'bg-danger';
                        } else if (desc.includes('rain') || desc.includes('hujan') || desc.includes('drizzle') || desc.includes('shower')) {
                            iconHtml = '<i class="fas fa-cloud-showers-heavy text-white"></i>';
                            colorClass = 'bg-primary';
                        } else if (desc.includes('wind') || desc.includes('angin') || desc.includes('gale')) {
                            iconHtml = '<i class="fas fa-wind text-white"></i>';
                            colorClass = 'bg-info';
                        } else if (desc.includes('cloud') || desc.includes('berawan') || desc.includes('overcast') || desc.includes('mendung')) {
                            iconHtml = '<i class="fas fa-cloud text-white"></i>';
                            colorClass = 'bg-secondary';
                        } else if (desc.includes('clear') || desc.includes('cerah') || desc.includes('sunny') || desc.includes('sun')) {
                            iconHtml = '<i class="fas fa-sun text-white"></i>';
                            colorClass = 'bg-warning';
                        } else if (desc.includes('mist') || desc.includes('fog') || desc.includes('kabut') || desc.includes('haze') || desc.includes('smog')) {
                            iconHtml = '<i class="fas fa-smog text-white"></i>';
                            colorClass = 'bg-dark';
                        } else {
                            iconHtml = '<i class="fas fa-cloud-sun text-white"></i>';
                            colorClass = 'bg-dark';
                        }

                        // DivIcon Kustom yang Elegan
                        const customIcon = L.divIcon({
                            html: `<div class="weather-icon-bubble ${colorClass}">${iconHtml}</div>`,
                            className: 'leaflet-weather-marker',
                            iconSize: [36, 36],
                            iconAnchor: [18, 18]
                        });

                        const marker = L.marker([lat, lng], { icon: customIcon })
                            .addTo(map)
                            .bindPopup(`
                                <div class="weather-popup">
                                    <h6>${country.name} (${country.code})</h6>
                                    <div class="weather-popup-row">
                                        <span class="text-muted">Suhu</span>
                                        <strong>🌡️ ${country.weather.temperature ?? 'N/A'} °C</strong>
                                    </div>
                                    <div class="weather-popup-row">
                                        <span class="text-muted">Kelembaban</span>
                                        <strong>💧 ${country.weather.humidity ?? 'N/A'}%</strong>
                                    </div>
                                    <div class="weather-popup-row">
                                        <span class="text-muted">Kec. Angin</span>
                                        <strong>💨 ${country.weather.wind_speed ?? 'N/A'} km/h</strong>
                                    </div>
                                    <div class="weather-popup-row">
                                        <span class="text-muted">Kondisi</span>
                                        <strong class="text-capitalize">${country.weather.description ?? 'N/A'}</strong>
                                    </div>
                                </div>
                            `);

                        markers.push(marker);
                    });

                    if (markers.length > 0) {
                        const group = L.featureGroup(markers);
                        map.fitBounds(group.getBounds(), { padding: [50, 50] });

                        setTimeout(() => {
                            map.invalidateSize();
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('❌ Fetch error:', error);
                    document.getElementById('weatherMapFallback').innerHTML = `
                        <div class="alert alert-danger rounded-3 border-0 shadow-sm mt-3">
                            <i class="fas fa-exclamation-circle me-2"></i> Gagal memuat data cuaca: ${error.message}
                        </div>
                    `;
                });

        } catch (error) {
            console.error('❌ Uncaught error:', error);
            document.getElementById('weatherMapFallback').innerHTML = `
                <div class="alert alert-danger rounded-3 border-0 shadow-sm mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i> Terjadi kesalahan: ${error.message}
                </div>
            `;
        }
    }, 200);
});
</script>
@endpush