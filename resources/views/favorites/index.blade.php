@extends('layouts.app')

@section('title', 'Favorite Monitoring List')

@section('styles')
<style>
    .favorite-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 20px;
        margin-bottom: 20px;
        transition: transform 0.2s;
    }
    .favorite-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }
    .favorite-card .flag-img {
        height: 30px;
        width: auto;
        margin-right: 10px;
    }
    .favorite-card .country-name {
        font-weight: 600;
        font-size: 1.1rem;
    }
    .favorite-card .country-code {
        color: #6c757d;
        font-size: 0.85rem;
    }
    .favorite-card .stat-item {
        text-align: center;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 6px;
        margin: 0 5px;
    }
    .favorite-card .stat-item .stat-value {
        font-weight: 600;
        font-size: 1.05rem;
    }
    .favorite-card .stat-item .stat-label {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .remove-btn {
        color: #dc3545;
        background: none;
        border: none;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .remove-btn:hover {
        transform: scale(1.2);
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 20px;
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
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-star"></i> Favorite Monitoring List</h1>
        <p>Daftar negara yang Anda pantau secara rutin.</p>
        <hr>
    </div>
</div>

<div id="favoritesContainer">
    <div class="text-center py-5">
        <div class="loading-spinner"></div>
        <p class="mt-2">Memuat daftar favorit...</p>
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
            // Cek status HTTP
            if (response.status === 401) {
                throw new Error('Silakan login terlebih dahulu.');
            }
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }

            // Periksa apakah response berupa JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Jika bukan JSON, kemungkinan redirect ke HTML (login)
                throw new Error('Sesi mungkin telah berakhir. Silakan <a href="/login">login</a> kembali.');
            }

            return response.json();
        })
        .then(data => {
            renderFavorites(data);
        })
        .catch(error => {
            console.error('❌ Error:', error);
            // Tampilkan pesan error dengan link login jika perlu
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    ${error.message}
                    ${error.message.includes('login') ? '' : '<br><small>Silakan <a href="/login">login</a> untuk melihat favorit.</small>'}
                </div>
            `;
        });
    }

    function renderFavorites(favorites) {
        if (!favorites || favorites.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-star"></i>
                    <h5>Belum ada negara favorit</h5>
                    <p class="text-muted">Kunjungi halaman <a href="{{ route('countries.index') }}">Country Dashboard</a>
                    dan tambahkan negara ke favorit.</p>
                </div>
            `;
            return;
        }

        let html = '<div class="row">';
        favorites.forEach(country => {
            const temp = country.weather?.temperature ?? 'N/A';
            const risk = country.risk?.total ?? 'N/A';
            const added = country.added_at ? new Date(country.added_at).toLocaleDateString('id-ID') : 'N/A';

            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="favorite-card" data-code="${country.code}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                ${country.flag ? `<img src="${country.flag}" class="flag-img">` : ''}
                                <span class="country-name">${country.name}</span>
                                <span class="country-code">(${country.code})</span>
                            </div>
                            <button class="remove-btn" onclick="removeFavorite('${country.code}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row mt-3">
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-value">${temp}°C</div>
                                    <div class="stat-label">Suhu</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-value">${risk}%</div>
                                    <div class="stat-label">Risiko</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-value">${country.currency || 'N/A'}</div>
                                    <div class="stat-label">Mata Uang</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 text-muted small">
                            <i class="fas fa-clock"></i> Ditambahkan: ${added}
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    // Fungsi global untuk remove favorite (dipanggil dari onclick)
    window.removeFavorite = function(countryCode) {
        if (!confirm(`Hapus negara ini dari favorit?`)) return;

        fetch(`/api/favorites/${countryCode}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.favorited === false) {
                // Hapus card dari DOM
                const card = document.querySelector(`.favorite-card[data-code="${countryCode}"]`);
                if (card) {
                    const parentCol = card.closest('.col-md-6.col-lg-4');
                    if (parentCol) parentCol.remove();
                }
                // Jika tidak ada card tersisa, reload data
                if (document.querySelectorAll('.favorite-card').length === 0) {
                    loadFavorites();
                }
            }
        })
        .catch(error => {
            console.error('❌ Error removing favorite:', error);
            alert('Gagal menghapus favorit. Silakan coba lagi.');
        });
    };

    // Muat data saat halaman siap
    loadFavorites();
});
</script>
@endpush