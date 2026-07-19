<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Supply Chain Risk Platform')</title>
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-extrabold text-white" href="{{ url('/') }}">
                <div class="d-flex align-items-center justify-content-center bg-primary rounded-3 text-white" style="width: 32px; height: 32px; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.35);">
                    <i class="fas fa-shield-halved text-white fs-6"></i>
                </div>
                <span class="tracking-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">RiskPlatform</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu kiri -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('countries.index') }}">Countries</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('weather.map') }}">Weather Map</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('currency.dashboard') }}">Currency</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('news.index') }}">News</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('ports.index') }}">Ports</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('visualization.index') }}">Visualization</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('compare.index') }}">Compare</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('decision.index') }}">Import Analyzer</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('favorites.index') }}">Favorites</a></li>
                </ul>

                <!-- Menu kanan (Login/Register/Logout + Admin) -->
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item">
                            <span class="navbar-text me-2">
                                <i class="fas fa-user"></i> {{ Auth::user()->name }}
                            </span>
                        </li>
                        @if(Auth::user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Admin
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link" style="display:inline; border:none; background:none; color:rgba(255,255,255,.55);">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> Daftar
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-3">
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>