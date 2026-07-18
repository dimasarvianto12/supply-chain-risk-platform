@extends('layouts.app')

@section('title', 'News Intelligence')

@section('styles')
<style>
    .news-card {
        border-left: 4px solid #007bff;
        transition: all 0.2s;
    }
    .news-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .sentiment-badge {
        font-size: 0.8rem;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .sentiment-positive { background: #28a745; color: white; }
    .sentiment-negative { background: #dc3545; color: white; }
    .sentiment-neutral { background: #6c757d; color: white; }
    .news-title {
        font-weight: 600;
        color: #2c3e50;
    }
    .news-title a {
        color: #2c3e50;
        text-decoration: none;
    }
    .news-title a:hover {
        color: #007bff;
    }
    .news-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }
    #loadMoreBtn {
        display: none;
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
        padding: 40px 20px;
        text-align: center;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1><i class="fas fa-newspaper"></i> News Intelligence</h1>
        <p>Berita terkini terkait logistik, perdagangan, pengiriman, dan ekonomi global.</p>
        <hr>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="filter-section">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="countryFilter" class="form-label">Negara</label>
                    <select id="countryFilter" class="form-select">
                        <option value="">Semua Negara</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->code }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sentimentFilter" class="form-label">Sentimen</label>
                    <select id="sentimentFilter" class="form-select">
                        <option value="">Semua Sentimen</option>
                        <option value="positive">Positif</option>
                        <option value="neutral">Netral</option>
                        <option value="negative">Negatif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="keywordFilter" class="form-label">Kata Kunci</label>
                    <input type="text" id="keywordFilter" class="form-control" placeholder="Cari berita...">
                </div>
                <div class="col-md-2">
                    <button id="applyFilterBtn" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- News List -->
<div class="row">
    <div class="col-12">
        <div id="newsContainer">
            <div class="text-center py-5">
                <div class="loading-spinner"></div>
                <p class="mt-2">Memuat berita...</p>
            </div>
        </div>
    </div>
</div>

<!-- Load More Button -->
<div class="row">
    <div class="col-12 text-center">
        <button id="loadMoreBtn" class="btn btn-outline-primary">
            <i class="fas fa-arrow-down"></i> Muat Lebih Banyak
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // 1. ELEMEN
    // ==========================================
    const container = document.getElementById('newsContainer');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const countryFilter = document.getElementById('countryFilter');
    const sentimentFilter = document.getElementById('sentimentFilter');
    const keywordFilter = document.getElementById('keywordFilter');
    const applyFilterBtn = document.getElementById('applyFilterBtn');

    let currentPage = 1;
    let isLoading = false;
    let hasMore = true;
    let currentParams = {};

    // ==========================================
    // 2. FUNGSI LOAD NEWS
    // ==========================================
    function loadNews(page = 1, append = false) {
        if (isLoading) return;
        isLoading = true;

        // Tampilkan loading
        if (!append) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="loading-spinner"></div>
                    <p class="mt-2">Memuat berita...</p>
                </div>
            `;
        }

        // Bangun parameter
        const params = new URLSearchParams();
        params.append('page', page);
        params.append('per_page', 10);

        if (countryFilter.value) {
            params.append('country', countryFilter.value);
        }
        if (sentimentFilter.value) {
            params.append('sentiment', sentimentFilter.value);
        }
        if (keywordFilter.value.trim()) {
            params.append('keyword', keywordFilter.value.trim());
        }

        currentParams = params;

        fetch(`/api/news?${params.toString()}`)
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data');
                return response.json();
            })
            .then(data => {
                console.log('📰 News data:', data);

                // Cek data pagination
                const news = data.data || [];
                const meta = data.meta || {};

                if (news.length === 0) {
                    if (!append) {
                        container.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-newspaper"></i>
                                <h5>Tidak ada berita ditemukan</h5>
                                <p class="text-muted">Coba ubah filter atau kata kunci pencarian.</p>
                            </div>
                        `;
                    }
                    hasMore = false;
                    loadMoreBtn.style.display = 'none';
                    isLoading = false;
                    return;
                }

                // Render berita
                let html = '';
                news.forEach(item => {
                    const sentiment = item.sentiment || 'neutral';
                    const sentimentClass = `sentiment-${sentiment}`;
                    const sentimentLabel = sentiment.charAt(0).toUpperCase() + sentiment.slice(1);
                    const published = item.published_at ? new Date(item.published_at).toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }) : 'N/A';
                    const description = item.description || 'Tidak ada deskripsi.';

                    html += `
                        <div class="card news-card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="news-title">
                                            <a href="${item.url || '#'}" target="_blank">${item.title}</a>
                                        </div>
                                        <p class="text-muted mt-2 mb-1">${description}</p>
                                        <div class="news-meta">
                                            <span class="badge bg-secondary me-2">${item.country || 'Global'}</span>
                                            <span class="me-2">📅 ${published}</span>
                                            ${item.url ? `<a href="${item.url}" target="_blank" class="text-muted"><i class="fas fa-external-link-alt"></i> Sumber</a>` : ''}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="sentiment-badge ${sentimentClass}">${sentimentLabel}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                if (append) {
                    container.insertAdjacentHTML('beforeend', html);
                } else {
                    container.innerHTML = html;
                }

                // Cek apakah masih ada halaman berikutnya
                hasMore = meta.current_page < meta.last_page;
                if (hasMore) {
                    loadMoreBtn.style.display = 'inline-block';
                    currentPage = meta.current_page + 1;
                } else {
                    loadMoreBtn.style.display = 'none';
                }

                isLoading = false;
            })
            .catch(error => {
                console.error('❌ Error:', error);
                if (!append) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-exclamation-circle" style="color: #dc3545;"></i>
                            <h5 class="text-danger">Gagal memuat berita</h5>
                            <p class="text-muted">${error.message}</p>
                        </div>
                    `;
                }
                isLoading = false;
            });
    }

    // ==========================================
    // 3. FUNGSI REFRESH
    // ==========================================
    function refreshNews() {
        currentPage = 1;
        hasMore = true;
        loadMoreBtn.style.display = 'none';
        loadNews(1, false);
    }

    // ==========================================
    // 4. EVENT LISTENERS
    // ==========================================
    applyFilterBtn.addEventListener('click', refreshNews);

    // Enter key on keyword filter
    keywordFilter.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            refreshNews();
        }
    });

    // Load more
    loadMoreBtn.addEventListener('click', function() {
        if (hasMore && !isLoading) {
            loadNews(currentPage, true);
        }
    });

    // ==========================================
    // 5. INISIALISASI
    // ==========================================
    refreshNews();
});
</script>
@endpush