@extends('layouts.app')

@section('styles')
<style>
    .admin-sidebar {
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        min-height: calc(100vh - 50px);
        padding: 24px 16px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .admin-sidebar .nav-link {
        color: #94a3b8;
        padding: 12px 20px;
        border-radius: 12px;
        margin: 4px 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .admin-sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #f8fafc;
        transform: translateX(4px);
    }
    .admin-sidebar .nav-link.active {
        background: #6366f1;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    .admin-sidebar .nav-link i {
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }
</style>
@parent
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="admin-sidebar d-flex flex-column justify-content-between">
            <div>
                <h5 class="text-white text-center fw-bold mb-4 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-lock text-primary"></i> Control Center
                </h5>
                <hr class="text-white-50 opacity-25 mb-4">
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.ports.*') ? 'active' : '' }}" 
                       href="{{ route('admin.ports.index') }}">
                        <i class="fas fa-ship"></i> Ports
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" 
                       href="{{ route('admin.articles.index') }}">
                        <i class="fas fa-newspaper"></i> Articles
                    </a>
                </nav>
            </div>
            <div>
                <hr class="text-white-50 opacity-25 my-4">
                <a class="nav-link text-white-50" href="{{ url('/') }}">
                    <i class="fas fa-arrow-left-long"></i> Back to Main
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('admin-content')
    </div>
</div>
@endsection