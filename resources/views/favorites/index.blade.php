@extends('layouts.app')

@section('title', 'Favorite Monitoring List')

@push('styles')
<style>
    .favorite-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        cursor: pointer;
    }
    .favorite-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .favorite-card .flag-img {
        height: 28px !important;
        width: auto !important;
        border-radius: 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        vertical-align: middle;
        margin-right: 10px;
        display: inline-block;
    }
    .favorite-card .country-name {
        font-weight: 800;
        font-size: 1.15rem;
        color: #0f172a;
        vertical-align: middle;
    }
    .favorite-card .country-code {
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 4px;
    }
    .favorite-card .stat-item {
        text-align: center;
        padding: 10px 8px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        height: 100%;
        transition: all 0.2s ease;
    }
    .favorite-card .stat-item:hover {
        background: #f1f5f9;
    }
    .favorite-card .stat-item .stat-value {
        font-weight: 700;
        font-size: 1.05rem;
        color: #1e293b;
        margin-bottom: 2px;
    }
    .favorite-card .stat-item .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
    }
    .remove-btn {
        color: #ef4444;
        background: #fee2e2;
        border: 1px solid #fecaca;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .remove-btn:hover {
        background: #ef4444;
        color: #ffffff;
        transform: scale(1.05);
    }
    
    /* Risk colored borders */
    .favorite-card.border-low { border-left: 4px solid #10b981 !important; }
    .favorite-card.border-medium { border-left: 4px solid #f59e0b !important; }
    .favorite-card.border-high { border-left: 4px solid #ef4444 !important; }

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
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-star text-warning"></i> Favorite Monitoring List
        </h1>
        <p class="text-muted fs-5 mb-0">Daftar negara yang Anda pantau secara rutin dalam pemantauan logistik global.</p>
        <hr>
    </div>
</div>

<div id="favoritesContainer">
    <div class="card premium-card py-5 text-center text-muted">
        <div class="loading-spinner"></div>
        <p class="mt-2 mb-0">Memuat daftar negara favorit Anda...</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('favoritesContainer');

    function loadFavorites() {
        fetch('/api/favorites', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (response.status === 401) {
                throw new Error('Silakan login terlebih dahulu.');
            }
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Sesi mungkin telah berakhir. Silakan <a href="/login">login</a> kembali.');
            }

            return response.json();
        })
        .then(data => {
            renderFavorites(data);
        })
        .catch(error => {
            console.error('❌ Error:', error);
            container.innerHTML = `
                <div class="card premium-card py-5 text-center text-danger">
                    <i class="fas fa-exclamation-circle text-danger mb-2" style="font-size: 2.5rem;"></i>
                    <h5 class="fw-bold">Gagal memuat favorit</h5>
                    <p class="text-muted mb-0">${error.message}</p>
                </div>
            `;
        });
    }

    function renderFavorites(favorites) {
        if (!favorites || favorites.length === 0) {
            container.innerHTML = `
                <div class="card premium-card py-5">
                    <div class="empty-state">
                        <i class="fas fa-star"></i>
                        <h5 class="fw-bold">Belum ada negara favorit</h5>
                        <p class="text-muted mb-0">Kunjungi halaman <a href="{{ route('countries.index') }}" class="text-primary fw-bold text-decoration-none">Country Dashboard</a> dan tambahkan negara yang dipantau.</p>
                    </div>
                </div>
            `;
            return;
        }

        let html = '<div class="row g-4">';
        favorites.forEach(country => {
            const temp = country.weather?.temperature !== undefined && country.weather?.temperature !== null ? parseFloat(country.weather.temperature).toFixed(1) : 'N/A';
            const risk = country.risk?.total !== undefined && country.risk?.total !== null ? parseFloat(country.risk.total).toFixed(1) : 0;
            const added = country.added_at ? new Date(country.added_at).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            }) : 'N/A';

            // Calculate dynamic risk border classes
            let borderClass = 'border-low';
            if (risk > 70) {
                borderClass = 'border-high';
            } else if (risk > 30) {
                borderClass = 'border-medium';
            }

            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="card favorite-card ${borderClass} p-4" data-code="${country.code}" onclick="window.location.href='/countries?code=${country.code}'">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                ${country.flag ? `<img src="${country.flag}" class="flag-img" alt="${country.name}">` : ''}
                                <div>
                                    <span class="country-name">${country.name}</span>
                                    <span class="country-code">(${country.code})</span>
                                </div>
                            </div>
                            <button class="remove-btn" onclick="event.stopPropagation(); removeFavorite('${country.code}')" title="Hapus dari Pemantauan">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-value text-info"><i class="fas fa-temperature-half me-1"></i>${temp}°</div>
                                    <div class="stat-label">SUHU</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-value text-warning"><i class="fas fa-triangle-exclamation me-1"></i>${risk}%</div>
                                    <div class="stat-label">RISIKO</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-value text-success"><i class="fas fa-coins me-1"></i>${country.currency || 'N/A'}</div>
                                    <div class="stat-label">KURS</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-muted small mt-auto" style="font-size:0.8rem;">
                            <i class="far fa-calendar-alt me-1"></i> Ditambahkan: <strong>${added}</strong>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    window.removeFavorite = function(countryCode) {
        if (!confirm(`Hapus negara ini dari daftar pemantauan favorit?`)) return;

        fetch(`/api/favorites/${countryCode}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.favorited === false) {
                const card = document.querySelector(`.favorite-card[data-code="${countryCode}"]`);
                if (card) {
                    const parentCol = card.closest('.col-md-6.col-lg-4');
                    if (parentCol) {
                        parentCol.style.opacity = 0;
                        parentCol.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            parentCol.remove();
                            if (document.querySelectorAll('.favorite-card').length === 0) {
                                loadFavorites();
                            }
                        }, 300);
                    }
                }
            }
        })
        .catch(error => {
            console.error('❌ Error removing favorite:', error);
            alert('Gagal menghapus favorit. Silakan coba lagi.');
        });
    };

    loadFavorites();
});
</script>
@endpush