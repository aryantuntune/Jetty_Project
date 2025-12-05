@extends('layouts.app')

@section('content')

<style>
    /* Fullscreen video background */
    .bg-video {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        z-index: -1;
        filter: brightness(65%);
    }

    /* Frosted glass reset card */
    .reset-card {
        min-width: 350px;
        max-width: 400px;
        width: 100%;
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

    /* Button style */
    .reset-btn {
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

    .reset-btn:hover {
        background-position: 100% 0;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,212,255,0.5);
    }

    /* Label text */
    label {
        color: #fff;
        font-weight: 600;
    }
</style>

{{-- Video Background --}}
<video autoplay loop muted playsinline class="bg-video">
    <source src="{{ asset('videos/1.mp4') }}" type="video/mp4" />
    Your browser does not support the video tag.
</video>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh; padding: 15px;">
    <div class="reset-card">

        <h4 class="text-center mb-4" style="color: #fff; font-weight: 700;">Reset Password</h4>

        <form action="{{ route('customer.password.update') }}" method="POST" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $email ?? '') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="Enter your email"
                    required
                    autofocus
                >
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="New password"
                    required
                >
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="Confirm password"
                    required
                >
            </div>

            <button type="submit" class="btn reset-btn w-100">Reset Password</button>
        </form>

    </div>
</div>

@endsection
