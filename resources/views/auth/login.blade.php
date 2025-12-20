{{--
================================================================================
OLD DESIGN - COMMENTED OUT (Bootstrap Version)
================================================================================
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
================================================================================
END OF OLD DESIGN
================================================================================
--}}

{{-- NEW DESIGN - Modern TailwindCSS Admin Login --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - {{ config('app.name', 'Jetty') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

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
                            950: '#172554',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-up': 'slideUp 0.5s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .bg-gradient-mesh {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .input-modern {
            transition: all 0.3s ease;
        }

        .input-modern:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
        }

        .btn-primary-modern:active {
            transform: translateY(0);
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
    </style>
</head>

<body class="font-sans antialiased min-h-screen bg-gradient-mesh flex items-center justify-center p-4">

    <!-- Background Shapes -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-white/10 animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-white/5 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/4 w-64 h-64 rounded-full bg-white/5 animate-float" style="animation-delay: 4s;"></div>
    </div>

    <!-- Login Card -->
    <div class="relative w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8 opacity-0 animate-slide-up">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-3 group">
                <div class="w-14 h-14 rounded-2xl bg-white shadow-xl flex items-center justify-center group-hover:shadow-2xl transition-shadow">
                    <i data-lucide="ship" class="w-8 h-8 text-primary-600"></i>
                </div>
                <span class="text-3xl font-bold text-white">Jetty</span>
            </a>
            <p class="mt-2 text-white/70 text-sm">Admin Portal</p>
        </div>

        <!-- Card -->
        <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-10 opacity-0 animate-slide-up delay-100">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-slate-800 mb-2">Welcome Back</h1>
                <p class="text-slate-500">Sign in to access the admin dashboard</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                <div class="flex items-center space-x-2 text-red-700">
                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="text-sm font-medium">{{ $errors->first() }}</span>
                </div>
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div class="opacity-0 animate-slide-up delay-200">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="input-modern w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 focus:outline-none text-slate-800 placeholder-slate-400 @error('email') border-red-500 @enderror"
                            placeholder="admin@example.com"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password Field -->
                <div class="opacity-0 animate-slide-up delay-200">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="input-modern w-full pl-12 pr-12 py-3.5 rounded-xl border border-slate-200 focus:outline-none text-slate-800 placeholder-slate-400 @error('password') border-red-500 @enderror"
                            placeholder="Enter your password"
                            required
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                        >
                            <i data-lucide="eye" id="eye-icon" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between opacity-0 animate-slide-up delay-300">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-slate-600">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="opacity-0 animate-slide-up delay-300">
                    <button
                        type="submit"
                        class="btn-primary-modern w-full py-4 rounded-xl font-semibold text-white text-lg flex items-center justify-center space-x-2"
                    >
                        <span>Sign In</span>
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-8 opacity-0 animate-slide-up delay-300">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 text-white/80 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/50 text-sm mt-6 opacity-0 animate-fade-in delay-300">
            &copy; {{ date('Y') }} Jetty. All rights reserved.
        </p>
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

            lucide.createIcons();
        }
    </script>
</body>
</html>
