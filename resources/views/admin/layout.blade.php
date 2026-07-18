@extends('layouts.app')

@section('styles')
<style>
    .admin-sidebar {
        background: #2c3e50;
        min-height: 100vh;
        padding: 20px 0;
        border-radius: 8px;
    }
    .admin-sidebar .nav-link {
        color: #ecf0f1;
        padding: 10px 20px;
        border-radius: 5px;
        margin: 2px 10px;
    }
    .admin-sidebar .nav-link:hover,
    .admin-sidebar .nav-link.active {
        background: #34495e;
        color: #fff;
    }
    .admin-sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
</style>
@parent
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="admin-sidebar">
            <h5 class="text-white text-center mb-3">📊 Admin Panel</h5>
            <hr class="text-white-50">
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home"></i> Dashboard
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
            <hr class="text-white-50">
            <a class="nav-link" href="{{ url('/') }}" style="color:#ecf0f1;">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
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