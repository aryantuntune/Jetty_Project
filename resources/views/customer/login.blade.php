{{--
================================================================================
OLD DESIGN - COMMENTED OUT (Bootstrap Version with layouts.app)
================================================================================
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
================================================================================
END OF OLD DESIGN
================================================================================
--}}

{{-- NEW DESIGN - TailwindCSS Version --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Jetty Ferry Booking</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        accent: {
                            gold: '#fbbf24',
                            orange: '#f97316',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-ring': 'pulseRing 2s ease-out infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        pulseRing: {
                            '0%': { transform: 'scale(0.95)', opacity: '1' },
                            '50%': { transform: 'scale(1)', opacity: '0.5' },
                            '100%': { transform: 'scale(0.95)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .video-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .video-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.5);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .input-glass:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .input-glass::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(251, 191, 36, 0.35);
        }

        .btn-gradient:active {
            transform: translateY(0);
        }

        .link-underline {
            position: relative;
        }

        .link-underline::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #fbbf24, #f97316);
            transition: width 0.3s ease;
        }

        .link-underline:hover::after {
            width: 100%;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
    </style>
</head>

<body class="font-sans antialiased text-white min-h-screen">

<!-- Video Background -->
<div class="video-bg">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
    </video>
</div>

<!-- Main Container -->
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <!-- Logo & Brand -->
        <div class="text-center mb-8 opacity-0 animate-fade-in-up">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-3 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-xl group-hover:shadow-primary-500/50 transition-all duration-300 animate-float">
                    <i data-lucide="ship" class="w-7 h-7 text-white"></i>
                </div>
                <span class="text-3xl font-bold tracking-tight">Jetty</span>
            </a>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-3xl p-8 md:p-10 shadow-2xl opacity-0 animate-fade-in-up delay-100">

            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold mb-2">Welcome Back</h1>
                <p class="text-white/60">Sign in to book your next ferry journey</p>
            </div>

            <!-- Error Alert -->
            @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500/30">
                <div class="flex items-center space-x-2 text-red-300">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span class="text-sm font-medium">{{ $errors->first() }}</span>
                </div>
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('customer.login.submit') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div class="opacity-0 animate-fade-in-up delay-200">
                    <label class="block text-sm font-medium text-white/80 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-white/40"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="input-glass w-full pl-12 pr-4 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none @error('email') border-red-500/50 @enderror"
                            placeholder="you@example.com"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password Field -->
                <div class="opacity-0 animate-fade-in-up delay-300">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-white/80">
                            Password
                        </label>
                        <a href="{{ route('customer.password.request') }}" class="text-sm text-accent-gold hover:text-accent-orange transition-colors link-underline">
                            Forgot password?
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-white/40"></i>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="input-glass w-full pl-12 pr-12 py-3.5 rounded-xl text-white placeholder-white/50 focus:outline-none @error('password') border-red-500/50 @enderror"
                            placeholder="Enter your password"
                            required
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/40 hover:text-white/70 transition-colors"
                        >
                            <i data-lucide="eye" id="eye-icon" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center opacity-0 animate-fade-in-up delay-300">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="w-4 h-4 rounded border-white/30 bg-white/10 text-primary-500 focus:ring-primary-500 focus:ring-offset-0"
                    >
                    <label for="remember" class="ml-2 text-sm text-white/70">
                        Remember me for 30 days
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="opacity-0 animate-fade-in-up delay-400">
                    <button
                        type="submit"
                        class="btn-gradient w-full py-4 rounded-xl font-bold text-gray-900 text-lg flex items-center justify-center space-x-2 group"
                    >
                        <span>Sign In</span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-8 opacity-0 animate-fade-in-up delay-400">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-transparent text-white/40">New to Jetty?</span>
                </div>
            </div>

            <!-- Register Link -->
            <div class="text-center opacity-0 animate-fade-in-up delay-400">
                <a
                    href="{{ route('customer.register') }}"
                    class="inline-flex items-center justify-center space-x-2 w-full py-3.5 rounded-xl border border-white/20 text-white font-medium hover:bg-white/10 transition-all duration-300"
                >
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                    <span>Create an Account</span>
                </a>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6 opacity-0 animate-fade-in-up delay-400">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-white/60 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/30 text-sm mt-8 opacity-0 animate-fade-in delay-400">
            &copy; {{ date('Y') }} Jetty. All rights reserved.
        </p>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }

        // Reinitialize the icon
        lucide.createIcons();
    }
</script>

</body>
</html>
