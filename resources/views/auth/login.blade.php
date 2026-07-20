@extends('layouts.guest')

@section('title', 'Log in')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger" style="font-size: 0.875rem;">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('login.post') }}">
    @csrf
    
    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label auth-label">Email</label>
        <input type="email" class="form-control auth-input w-100" id="email" name="email" value="{{ old('email') }}" required autofocus>
    </div>
    
    <!-- Password -->
    <div class="mb-4">
        <label for="password" class="form-label auth-label">Password</label>
        <input type="password" class="form-control auth-input w-100" id="password" name="password" required>
    </div>
    
    <!-- Actions -->
    <div class="d-flex align-items-center justify-content-between mt-4">
        <a class="auth-link" href="{{ route('register') }}">
            Belum punya akun?
        </a>
        <button type="submit" class="auth-btn">
            Log in
        </button>
    </div>
</form>

@endsection