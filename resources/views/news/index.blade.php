@extends('layouts.app')

@section('title', 'News Intelligence')

@push('styles')
<style>
    .news-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        overflow: hidden;
    }
    .news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }
    .sentiment-badge {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .sentiment-positive { 
        background: #dcfce7; 
        color: #15803d; 
        border: 1px solid #bbf7d0; 
    }
    .sentiment-negative { 
        background: #fee2e2; 
        color: #b91c1c; 
        border: 1px solid #fecaca; 
    }
    .sentiment-neutral { 
        background: #f1f5f9; 
        color: #475569; 
        border: 1px solid #e2e8f0; 
    }
    
    .news-card.border-positive { border-left: 5px solid #10B981 !important; }
    .news-card.border-negative { border-left: 5px solid #EF4444 !important; }
    .news-card.border-neutral { border-left: 5px solid #64748B !important; }

    .news-title {
        font-size: 1.15rem;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 8px;
    }
    .news-title a {
        color: #1e293b;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .news-title a:hover {
        color: #6366f1;
    }
    .news-meta {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
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
        padding: 60px 20px;
        text-align: center;
        color: #64748b;
    }
    .empty-state i {
        font-size: 3.5rem;
        margin-bottom: 16px;
        color: #cbd5e1;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-extrabold text-dark tracking-tight mb-1" style="font-size: 2.25rem;">
            <i class="fas fa-newspaper text-primary"></i> News Intelligence
        </h1>
        <p class="text-muted fs-5 mb-0">Berita terkini terkait logistik, perdagangan, pengiriman, dan ekonomi global.</p>
        <hr>
    </div>
</div>

<!-- Filter Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card premium-card">
            <div class="premium-card-header">
                <i class="fas fa-filter text-primary"></i>
                <span>Filter Feed Berita</span>
            </div>
            <div class="premium-card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="countryFilter" class="form-label fw-bold text-secondary mb-2" style="font-size: 0.85rem;">NEGARA</label>
                        <select id="countryFilter" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                            <option value="">Semua Negara</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->code }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="sentimentFilter" class="form-label fw-bold text-secondary mb-2" style="font-size: 0.85rem;">SENTIMEN</label>
                        <select id="sentimentFilter" class="form-select py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;">
                            <option value="">Semua Sentimen</option>
                            <option value="positive">Positif</option>
                            <option value="neutral">Netral</option>
                            <option value="negative">Negatif</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="keywordFilter" class="form-label fw-bold text-secondary mb-2" style="font-size: 0.85rem;">KATA KUNCI</label>
                        <input type="text" id="keywordFilter" class="form-control py-2 rounded-3" style="border-color: #cbd5e1; box-shadow: none;" placeholder="Cari topik berita...">
                    </div>
                    <div class="col-md-2">
                        <button id="applyFilterBtn" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- ARTIKEL INTERNAL (dari database admin)      -->
<!-- ========================================== -->
@if(isset($internalArticles) && $internalArticles->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card premium-card" style="border-left: 5px solid #0ea5e9;">
            <div class="premium-card-header bg-light d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-pen-fancy text-info"></i> Artikel Internal
                </span>
                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1.5 rounded-pill fw-bold">
                    {{ $internalArticles->count() }} artikel
                </span>
            </div>
            <div class="premium-card-body">
                @foreach($internalArticles as $article)
                    <div class="border-bottom pb-3 mb-3 last:border-0 last:mb-0">
                        <h5 class="fw-bold text-dark mb-2" style="font-size: 1.2rem;">{{ $article->title }}</h5>
                        
                        {{-- Deteksi URL: Jika content berupa URL, render sebagai tombol, bukan text mentah --}}
                        @if(filter_var($article->content, FILTER_VALIDATE_URL))
                            <div class="my-2">
                                <a href="{{ $article->content }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3 py-1.5 fw-semibold">
                                    <i class="fas fa-external-link-alt me-1"></i> Buka Link Artikel Asli
                                </a>
                            </div>
                        @else
                            <p class="text-muted" style="line-height: 1.6;">{{ \Illuminate\Support\Str::limit($article->content, 250) }}</p>
                        @endif

                        <div class="news-meta mt-2">
                            <span><i class="fas fa-user-circle me-1"></i> Penulis: <strong>{{ $article->author ?? 'Admin' }}</strong></span>
                            <span><i class="far fa-clock me-1"></i> ${ $article->created_at->format('d M Y, H:i') }</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- News List -->
<div class="row">
    <div class="col-12">
        <div id="newsContainer">
            <div class="text-center py-5 text-muted">
                <div class="loading-spinner"></div>
                <p class="mt-2">Mengunduh feed berita terbaru...</p>
            </div>
        </div>
    </div>
</div>

<!-- Load More Button -->
<div class="row mt-3">
    <div class="col-12 text-center">
        <button id="loadMoreBtn" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">
            <i class="fas fa-arrow-down me-1"></i> Muat Lebih Banyak
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    function loadNews(page = 1, append = false) {
        if (isLoading) return;
        isLoading = true;

        if (!append) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <div class="loading-spinner"></div>
                    <p class="mt-2">Memuat feed berita...</p>
                </div>
            `;
        }

        const params = new URLSearchParams();
        params.append('page', page);
        params.append('per_page', 10);

        if (countryFilter.value) params.append('country', countryFilter.value);
        if (sentimentFilter.value) params.append('sentiment', sentimentFilter.value);
        if (keywordFilter.value.trim()) params.append('keyword', keywordFilter.value.trim());

        currentParams = params;

        fetch(`/api/news?${params.toString()}`)
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data');
                return response.json();
            })
            .then(data => {
                const news = data.data || [];
                const meta = data.meta || {};

                if (news.length === 0) {
                    if (!append) {
                        container.innerHTML = `
                            <div class="card premium-card py-5">
                                <div class="empty-state">
                                    <i class="fas fa-newspaper"></i>
                                    <h5 class="fw-bold">Tidak ada berita ditemukan</h5>
                                    <p class="text-muted mb-0">Coba gunakan filter lain atau cari topik yang berbeda.</p>
                                </div>
                            </div>
                        `;
                    }
                    hasMore = false;
                    loadMoreBtn.style.display = 'none';
                    isLoading = false;
                    return;
                }

                let html = '';
                news.forEach(item => {
                    const sentiment = item.sentiment || 'neutral';
                    const sentimentClass = `sentiment-${sentiment}`;
                    const borderClass = `border-${sentiment}`;
                    const sentimentLabel = sentiment.charAt(0).toUpperCase() + sentiment.slice(1);
                    const published = item.published_at ? new Date(item.published_at).toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }) : 'N/A';
                    const description = item.description || 'Tidak ada deskripsi.';

                    html += `
                        <div class="card news-card ${borderClass} mb-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div class="flex-grow-1">
                                        <h5 class="news-title">
                                            <a href="${item.url || '#'}" target="_blank">${item.title}</a>
                                        </h5>
                                        <p class="text-muted mt-2 mb-3" style="line-height: 1.5;">${description}</p>
                                        <div class="news-meta">
                                            <span class="badge bg-light text-secondary border px-2 py-1.5 rounded">${item.country || 'Global'}</span>
                                            <span><i class="far fa-calendar-alt me-1"></i> ${published}</span>
                                            ${item.url ? `<a href="${item.url}" target="_blank" class="text-primary fw-semibold text-decoration-none"><i class="fas fa-external-link-alt me-1"></i> Sumber</a>` : ''}
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
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
                        <div class="card premium-card py-5">
                            <div class="empty-state text-danger">
                                <i class="fas fa-exclamation-circle text-danger"></i>
                                <h5 class="fw-bold">Gagal memuat berita</h5>
                                <p class="text-muted mb-0">${error.message}</p>
                            </div>
                        </div>
                    `;
                }
                isLoading = false;
            });
    }

    function refreshNews() {
        currentPage = 1;
        hasMore = true;
        loadMoreBtn.style.display = 'none';
        loadNews(1, false);
    }

    applyFilterBtn.addEventListener('click', refreshNews);

    keywordFilter.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            refreshNews();
        }
    });

    loadMoreBtn.addEventListener('click', function() {
        if (hasMore && !isLoading) {
            loadNews(currentPage, true);
        }
    });

    refreshNews();
});
</script>
@endpush