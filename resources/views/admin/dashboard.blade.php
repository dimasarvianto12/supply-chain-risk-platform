@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('admin-content')
<h2><i class="fas fa-chart-pie"></i> Admin Dashboard</h2>
<hr>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users"></i> Total Users</h5>
                <p class="card-text display-4">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-ship"></i> Total Ports</h5>
                <p class="card-text display-4">{{ $totalPorts }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-newspaper"></i> Total Articles</h5>
                <p class="card-text display-4">{{ $totalArticles }}</p>
            </div>
        </div>
    </div>
</div>
@endsection