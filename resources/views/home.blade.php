@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Supply Chain Risk Intelligence Platform</h1>
        <p>Selamat datang di dashboard pemantauan risiko rantai pasok global.</p>
        <hr>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Negara Dipantau</h5>
                <p class="card-text display-4" id="countryCount">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Skor Risiko Rata-rata</h5>
                <p class="card-text display-4" id="avgRisk">-</p>
            </div>
        </div>
    </div>
    <!-- Tambahkan card lain sesuai kebutuhan -->
</div>
@endsection

@push('scripts')
<script>
    // Nanti kita isi dengan AJAX untuk mengambil data ringkasan
    console.log('Dashboard siap');
</script>
@endpush