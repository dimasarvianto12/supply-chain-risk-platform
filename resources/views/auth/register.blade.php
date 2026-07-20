@extends('layouts.guest')

@section('title', 'Register')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger" style="font-size: 0.875rem;">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register.post') }}">
    @csrf
    
    <!-- Name -->
    <div class="mb-3">
        <label for="name" class="form-label auth-label">Name</label>
        <input type="text" class="form-control auth-input w-100" id="name" name="name" value="{{ old('name') }}" required autofocus>
    </div>

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label auth-label">Email</label>
        <input type="email" class="form-control auth-input w-100" id="email" name="email" value="{{ old('email') }}" required>
    </div>
    
    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label auth-label">Password</label>
        <input type="password" class="form-control auth-input w-100" id="password" name="password" required>
    </div>

    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label auth-label">Confirm Password</label>
        <input type="password" class="form-control auth-input w-100" id="password_confirmation" name="password_confirmation" required>
    </div>
    
    <!-- Actions -->
    <div class="d-flex align-items-center justify-content-between mt-4">
        <a class="auth-link" href="{{ route('login') }}">
            Already registered?
        </a>
        <button type="submit" class="auth-btn">
            Register
        </button>
    </div>
</form>

@endsection