@extends('layouts.app')

@section('content')

<style>
    /* Fullscreen video background */
    .bg-video {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
        filter: brightness(65%);
    }

    .login-card {
        width: 420px;
        border-radius: 20px;
        padding: 40px 35px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.2);
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        animation: fadeIn 0.7s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Input style */
    .form-control {
        height: 48px;
        border-radius: 12px;
    }

    .form-control:focus {
        border-color: #00b3ff;
        box-shadow: 0 0 0 4px rgba(0,179,255,0.25);
    }

    /* LEFT TO RIGHT button animation */
    .login-btn {
        border-radius: 30px;
        padding: 12px;
        font-size: 17px;
        font-weight: 600;
        border: none;
        color: white;
        background: linear-gradient(90deg, #0077ff, #00d4ff, #0077ff);
        background-size: 300% 100%;
        transition: all 0.5s ease;
    }

    .login-btn:hover {
        background-position: 100% 0;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,212,255,0.5);
    }

    .title {
        font-weight: 700;
        color: #ffffff;
        text-shadow: 0px 3px 10px rgba(0,0,0,0.4);
    }
</style>

{{-- Sea Video Background --}}
<video autoplay loop muted playsinline class="bg-video">
    <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
</video>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="login-card">

        <h3 class="title text-center mb-4">
            <i class="bi bi-ship"></i> Jetty Booking Login
        </h3>

        <form method="POST" action="{{ route('customer.login.submit') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label text-white fw-semibold">Email Address</label>
                <input 
                    type="email" 
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="Enter your email"
                    required
                >
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label class="form-label text-white fw-semibold">Password</label>
                <input 
                    type="password" 
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter your password"
                    required
                >
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            {{-- Login Button --}}
            <button type="submit" class="btn login-btn w-100 mt-2">
                Log In
            </button>

            <div class="text-center mt-3 text-white">
                <small>Don't have an account?
                    <a href="{{ route('customer.register') }}" class="fw-bold text-white text-decoration-underline">Sign Up</a>
                </small>
            </div>

        </form>
    </div>
</div>

@endsection
