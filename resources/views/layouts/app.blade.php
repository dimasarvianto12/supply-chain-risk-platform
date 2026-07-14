<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Supply Chain Risk Platform')</title>
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">🚚 Risk Platform</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('countries.index') }}">Countries</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('weather.map') }}">Weather Map</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('currency.dashboard') }}">Currency</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('news.index') }}">News</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('ports.index') }}">Ports</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('visualization.index') }}">Visualization</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('compare.index') }}">Compare</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('favorites.index') }}">Favorites</a></li>
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