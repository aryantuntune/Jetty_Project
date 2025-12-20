{{--
================================================================================
OLD DESIGN - COMMENTED OUT (Bootstrap Version)
================================================================================
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Ferry Booking</title>

    <!-- jQuery MUST load first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 AFTER jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    /* Attractive Select2 Style */
    .select2-container .select2-selection--single {
        height: 45px !important;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ced4da;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 45px !important;
    }

    .select2-selection__rendered {
        line-height: 30px !important;
        font-size: 14px;
    }
</style>
<body class="bg-light">

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-ship"></i> Ferry Booking
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="/booking">
                        <i class="bi bi-ticket-perforated"></i> Book Ferry
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/booking/history">
                        <i class="bi bi-clock-history"></i> Booking History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="bi bi-house"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn nav-link text-white border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ================= MAIN CONTENT ================= -->
<section id="booking-form" class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5">
                    <h3 class="text-center mb-4 text-primary fw-bold">
                        <i class="bi bi-ship"></i> Online Ferry Booking
                    </h3>

                    @if(session('success'))
                        <div class="alert alert-success text-center fw-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('booking.submit') }}" method="POST" id="ferryBookingForm">
                        @csrf
                        <!-- Form fields here... -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</body>
</html>

NOTE: The full old code with all JavaScript functions is preserved in the
Jetty_Latest repository at: resources/views/customer/dashboard.blade.php
================================================================================
END OF OLD DESIGN
================================================================================
--}}

{{-- NEW DESIGN - Modern TailwindCSS Version --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ferry - Jetty</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Razorpay -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

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
                        'fade-in-up': 'fadeInUp 0.5s ease-out forwards',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-up': 'slideUp 0.3s ease-out forwards',
                        'scale-up': 'scaleUp 0.3s ease-out forwards',
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
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleUp: {
                            '0%': { opacity: '0', transform: 'scale(0.95)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        html { scroll-behavior: smooth; }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .input-modern {
            transition: all 0.3s ease;
        }

        .input-modern:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(251, 191, 36, 0.35);
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        /* Select2 Custom Styles */
        .select2-container--default .select2-selection--single {
            height: 48px !important;
            padding: 10px 14px;
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
            background: white !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px !important;
            right: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #374151;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        }

        .select2-dropdown {
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden;
        }

        .select2-results__option--highlighted {
            background: #3b82f6 !important;
        }

        .item-row {
            animation: slideUp 0.3s ease-out forwards;
        }

        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #1f2937;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Route visual */
        .route-line {
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #fbbf24);
            position: relative;
        }

        .route-line::before,
        .route-line::after {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .route-line::before {
            left: -4px;
            background: #3b82f6;
        }

        .route-line::after {
            right: -4px;
            background: #fbbf24;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">

<!-- Navbar -->
<nav class="glass-nav fixed top-0 left-0 right-0 z-50 border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-lg group-hover:shadow-primary-500/30 transition-all duration-300">
                    <i data-lucide="ship" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-tight">Jetty</span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-2">
                <a href="/booking" class="flex items-center space-x-2 px-4 py-2 rounded-xl bg-primary-50 text-primary-600 font-medium transition-colors">
                    <i data-lucide="ticket" class="w-4 h-4"></i>
                    <span>Book Ferry</span>
                </a>
                <a href="/booking/history" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-100 font-medium transition-colors">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>History</span>
                </a>
                <a href="/" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-100 font-medium transition-colors">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span>Home</span>
                </a>

                <div class="w-px h-6 bg-gray-200 mx-2"></div>

                <form action="{{ route('customer.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-red-600 hover:bg-red-50 font-medium transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-lucide="menu" class="w-6 h-6 text-gray-600"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white">
        <div class="px-4 py-4 space-y-2">
            <a href="/booking" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-primary-50 text-primary-600 font-medium">
                <i data-lucide="ticket" class="w-5 h-5"></i>
                <span>Book Ferry</span>
            </a>
            <a href="/booking/history" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 font-medium">
                <i data-lucide="history" class="w-5 h-5"></i>
                <span>Booking History</span>
            </a>
            <a href="/" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 font-medium">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span>Home</span>
            </a>
            <form action="{{ route('customer.logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 font-medium w-full">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="pt-24 pb-12 px-4">
    <div class="max-w-5xl mx-auto">

        <!-- Page Header -->
        <div class="text-center mb-8 animate-fade-in-up">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Book Your Ferry</h1>
            <p class="text-gray-500">Select your route and items to begin your journey</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 flex items-center space-x-3 animate-fade-in-up">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
            </div>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Booking Form Card -->
        <div class="glass-card rounded-3xl shadow-xl border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
            <form action="{{ route('booking.submit') }}" method="POST" id="ferryBookingForm">
                @csrf

                <!-- Route Selection Section -->
                <div class="p-6 md:p-8 border-b border-gray-100">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                            <i data-lucide="map-pin" class="w-5 h-5 text-primary-600"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Select Route</h2>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <!-- From Branch -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Departure</label>
                            <select name="from_branch" id="fromBranch" class="w-full" required>
                                <option value="">Select departure point</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Route Visualization -->
                        <div class="hidden md:flex items-end justify-center pb-4">
                            <div class="flex items-center space-x-2">
                                <div class="route-line"></div>
                            </div>
                        </div>

                        <!-- To Branch -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
                            <select name="to_branch" id="toBranch" class="w-full" required>
                                <option value="">Select destination</option>
                            </select>
                        </div>
                    </div>

                    <!-- Travel Date -->
                    <div class="mt-6 max-w-xs">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Travel Date</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="calendar" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="text" disabled name="date" id="travelDate"
                                class="input-modern w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-600 focus:outline-none" required>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="p-6 md:p-8 bg-gray-50/50">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-accent-gold/20 flex items-center justify-center">
                                <i data-lucide="package" class="w-5 h-5 text-accent-orange"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Items & Passengers</h2>
                        </div>
                        <button type="button" onclick="addItemRow()"
                            class="flex items-center space-x-2 px-4 py-2 rounded-xl bg-green-500 text-white font-medium hover:bg-green-600 transition-colors shadow-lg shadow-green-500/20">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Add Item</span>
                        </button>
                    </div>

                    <!-- Items Container -->
                    <div id="itemsContainer" class="space-y-4">
                        <!-- Dynamic rows will be added here -->
                    </div>

                    <!-- Grand Total -->
                    <div class="mt-6 p-4 rounded-2xl bg-primary-50 border border-primary-100">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-medium">Grand Total</span>
                            <span class="text-2xl font-bold text-primary-600">
                                <span class="text-lg">&#8377;</span> <span id="grandTotal">0.00</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="p-6 md:p-8 border-t border-gray-100">
                    <button type="button" onclick="showPreviewModal()"
                        class="btn-gradient w-full py-4 rounded-xl font-bold text-gray-900 text-lg flex items-center justify-center space-x-2 group">
                        <span>Review & Submit Booking</span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 modal-overlay hidden flex items-center justify-center z-50 p-4">
    <div class="glass-card rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden animate-scale-up">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-primary-500 to-primary-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white">Review Your Booking</h2>
                </div>
                <button onclick="closePreviewModal()" class="p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-white"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <!-- Route Info -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Ferry Route</h3>
                <p id="previewRoute" class="text-lg font-semibold text-gray-800"></p>
            </div>

            <!-- Travel Date -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Travel Date</h3>
                <p id="previewDate" class="text-lg font-semibold text-gray-800"></p>
            </div>

            <!-- Items Table -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-500 mb-3">Items</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Rate</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Levy</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vehicle No.</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody id="previewItems" class="divide-y divide-gray-100">
                            <!-- Dynamic content -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="p-4 rounded-2xl bg-primary-50 border border-primary-100">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 font-medium">Grand Total</span>
                    <span class="text-2xl font-bold text-primary-600">
                        <span class="text-lg">&#8377;</span> <span id="previewGrandTotal"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row gap-3">
            <button onclick="closePreviewModal()" class="flex-1 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                Edit Booking
            </button>
            <button onclick="proceedToPayment()" id="payBtn" class="flex-1 btn-gradient py-3 rounded-xl font-bold text-gray-900 flex items-center justify-center space-x-2">
                <i data-lucide="wallet" class="w-5 h-5"></i>
                <span id="payBtnText">Proceed to Pay</span>
                <div id="paySpinner" class="spinner hidden"></div>
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 modal-overlay hidden flex items-center justify-center z-50 p-4">
    <div class="glass-card rounded-3xl shadow-2xl w-full max-w-md animate-scale-up">
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-red-100 flex items-center justify-center mb-4">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Missing Details</h2>
            <p id="errorModalMessage" class="text-gray-600 mb-6"></p>
            <button onclick="closeErrorModal()" class="w-full py-3 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 transition-colors">
                OK, Got it
            </button>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    /* ============================================================
       GLOBAL INITIALIZATION
    ============================================================ */
    let rowIndex = 0;

    // Set today's date
    document.getElementById('travelDate').value = new Date().toISOString().split('T')[0];

    // Initialize Select2
    $(document).ready(function() {
        $('#fromBranch').select2({
            placeholder: 'Select departure point',
            allowClear: true
        });

        $('#toBranch').select2({
            placeholder: 'Select destination',
            allowClear: true
        });
    });

    // When FROM branch changes
    document.getElementById('fromBranch').addEventListener('change', function() {
        const branchId = this.value;
        loadToBranches(branchId);
        resetItemsAndAddFirstRow();
    });

    /* ============================================================
       LOAD DESTINATION BRANCHES
    ============================================================ */
    function loadToBranches(branchId) {
        const toBranchSelect = $('#toBranch');
        toBranchSelect.empty().append('<option value="">Loading...</option>');

        fetch(`/booking/to-branches/${branchId}`)
            .then(response => response.json())
            .then(data => {
                toBranchSelect.empty().append('<option value="">Select destination</option>');
                data.forEach(branch => {
                    toBranchSelect.append(new Option(branch.branch_name, branch.id));
                });
                toBranchSelect.trigger('change');
            });
    }

    /* ============================================================
       LOAD ITEMS FOR A BRANCH
    ============================================================ */
    function loadItemsIntoDropdown(index) {
        const dropdown = document.querySelector(`[name="items[${index}][item_rate_id]"]`);
        const branchId = document.getElementById('fromBranch').value;

        if (!branchId || !dropdown) return;

        dropdown.innerHTML = '<option value="">Loading...</option>';

        fetch(`/booking/items/${branchId}`)
            .then(res => res.json())
            .then(data => {
                dropdown.innerHTML = '<option value="">Select item</option>';
                data.forEach(item => {
                    dropdown.innerHTML += `<option value="${item.id}">${item.item_name}</option>`;
                });
            });
    }

    /* ============================================================
       ITEM ROW HANDLING
    ============================================================ */
    function resetItemsAndAddFirstRow() {
        document.getElementById("itemsContainer").innerHTML = "";
        rowIndex = 0;
        addItemRow();
    }

    function addItemRow() {
        rowIndex++;

        const html = `
        <div class="item-row bg-white rounded-2xl border border-gray-200 p-4" data-index="${rowIndex}">
            <div class="grid grid-cols-12 gap-4">
                <!-- Description -->
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                    <select name="items[${rowIndex}][item_rate_id]" class="itemDescription w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 text-sm" required>
                        <option value="">Select item</option>
                    </select>
                </div>

                <!-- Quantity -->
                <div class="col-span-4 md:col-span-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Qty</label>
                    <input type="number" name="items[${rowIndex}][quantity]" class="qty w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 text-sm text-center" min="1" value="1" required>
                </div>

                <!-- Vehicle No -->
                <div class="col-span-8 md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Vehicle No</label>
                    <input type="text" name="items[${rowIndex}][vehicle_no]" class="vehicle_no w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 text-sm uppercase" placeholder="XX-00-XX-0000">
                </div>

                <!-- Rate -->
                <div class="col-span-4 md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Rate</label>
                    <input type="number" name="items[${rowIndex}][rate]" class="rate w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm" readonly>
                </div>

                <!-- Levy -->
                <div class="col-span-4 md:col-span-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Levy</label>
                    <input type="number" name="items[${rowIndex}][lavy]" class="lavy w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm" readonly>
                </div>

                <!-- Total -->
                <div class="col-span-4 md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Total</label>
                    <input type="number" class="itemTotal w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-primary-50 text-primary-700 font-semibold text-sm" readonly>
                </div>

                <!-- Delete Button -->
                <div class="col-span-12 md:col-span-1 flex items-end justify-end">
                    <button type="button" onclick="removeItemRow(this)" class="p-2.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>`;

        document.getElementById("itemsContainer").insertAdjacentHTML("beforeend", html);
        loadItemsIntoDropdown(rowIndex);
        lucide.createIcons();
    }

    function removeItemRow(btn) {
        btn.closest(".item-row").remove();
        calculateGrandTotal();
    }

    /* ============================================================
       AUTO FILL RATE / LEVY / TOTAL
    ============================================================ */
    document.addEventListener("change", function(e) {
        if (e.target.classList.contains("itemDescription")) {
            const row = e.target.closest(".item-row");

            fetch(`/booking/item-rate/${e.target.value}`)
                .then(res => res.json())
                .then(data => {
                    row.querySelector(".rate").value = data.item_rate;
                    row.querySelector(".lavy").value = data.item_lavy;
                    calculateItemTotal(row);
                });
        }
    });

    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("qty")) {
            const row = e.target.closest(".item-row");
            calculateItemTotal(row);
        }
        if (e.target.classList.contains("vehicle_no")) {
            e.target.value = e.target.value.toUpperCase();
        }
    });

    function calculateItemTotal(row) {
        const qty = parseFloat(row.querySelector(".qty").value) || 0;
        const rate = parseFloat(row.querySelector(".rate").value) || 0;
        const lavy = parseFloat(row.querySelector(".lavy").value) || 0;
        const total = (qty * rate) + lavy;

        row.querySelector(".itemTotal").value = total.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let sum = 0;
        document.querySelectorAll(".itemTotal").forEach(el => {
            sum += parseFloat(el.value) || 0;
        });
        document.getElementById("grandTotal").innerText = sum.toFixed(2);
    }

    /* ============================================================
       PREVIEW MODAL
    ============================================================ */
    function showPreviewModal() {
        let isValid = true;
        let errorMsg = "";

        document.querySelectorAll(".item-row").forEach(row => {
            let desc = row.querySelector(".itemDescription").value;
            let qty = row.querySelector(".qty").value;

            if (desc === "") {
                isValid = false;
                errorMsg = "Please select a description for all items.";
            } else if (qty === "" || qty <= 0) {
                isValid = false;
                errorMsg = "Please enter a valid quantity.";
            }
        });

        if (!isValid) {
            document.getElementById("errorModalMessage").innerHTML = errorMsg;
            document.getElementById('errorModal').classList.remove('hidden');
            return;
        }

        const from = document.querySelector("#fromBranch option:checked").textContent;
        const to = document.querySelector("#toBranch option:checked").textContent;
        const date = document.getElementById("travelDate").value;

        document.getElementById("previewRoute").innerHTML = `<span class="text-primary-600">${from}</span> <span class="text-gray-400 mx-2">→</span> <span class="text-accent-orange">${to}</span>`;
        document.getElementById("previewDate").innerText = date;

        let rows = "";
        document.querySelectorAll(".item-row").forEach(row => {
            rows += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-800">${row.querySelector(".itemDescription option:checked").textContent}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 text-center">${row.querySelector(".qty").value}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 text-right">&#8377;${row.querySelector(".rate").value}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 text-right">&#8377;${row.querySelector(".lavy").value}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${row.querySelector(".vehicle_no").value || '-'}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-800 text-right">&#8377;${row.querySelector(".itemTotal").value}</td>
                </tr>`;
        });

        document.getElementById("previewItems").innerHTML = rows;
        document.getElementById("previewGrandTotal").innerText = document.getElementById("grandTotal").innerText;
        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreviewModal() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    function closeErrorModal() {
        document.getElementById('errorModal').classList.add('hidden');
    }

    /* ============================================================
       PAYMENT
    ============================================================ */
    function collectItems() {
        let items = [];
        document.querySelectorAll(".item-row").forEach(row => {
            items.push({
                item_rate_id: row.querySelector(".itemDescription").value,
                quantity: row.querySelector(".qty").value,
                rate: row.querySelector(".rate").value,
                lavy: row.querySelector(".lavy").value,
                vehicle_no: row.querySelector(".vehicle_no").value,
                total: row.querySelector(".itemTotal").value
            });
        });
        return items;
    }

    function proceedToPayment() {
        const payBtn = document.getElementById('payBtn');
        const payBtnText = document.getElementById('payBtnText');
        const paySpinner = document.getElementById('paySpinner');

        payBtn.disabled = true;
        payBtnText.classList.add('hidden');
        paySpinner.classList.remove('hidden');

        let grand_total = document.getElementById('grandTotal').innerText;

        fetch("{{ route('payment.createOrder') }}", {
            method: "POST",
            credentials: "include",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                grand_total: grand_total,
                from_branch: document.getElementById("fromBranch").value,
                to_branch: document.getElementById("toBranch").value,
                items: collectItems()
            })
        })
        .then(async res => {
            let text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Server returned HTML instead of JSON:", text);
                alert("Server Error: Payment order failed.");
                throw new Error("Invalid JSON");
            }
        })
        .then(data => {
            var options = {
                "key": data.key,
                "amount": data.amount,
                "currency": "INR",
                "name": "Jetty - Ferry Booking",
                "description": "Online Ferry Ticket",
                "order_id": data.order_id,
                "handler": function(response) {
                    fetch("{{ route('payment.verify') }}", {
                        method: "POST",
                        credentials: "include",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(response)
                    })
                    .then(() => window.location.href = "/booking?success=1");
                },
                "modal": {
                    "ondismiss": function() {
                        payBtn.disabled = false;
                        payBtnText.classList.remove('hidden');
                        paySpinner.classList.add('hidden');
                    }
                },
                "theme": { "color": "#3b82f6" }
            };

            var rzp1 = new Razorpay(options);
            rzp1.open();
        })
        .catch(err => {
            console.error("Payment Error:", err);
            payBtn.disabled = false;
            payBtnText.classList.remove('hidden');
            paySpinner.classList.add('hidden');
        });
    }

    // Close modals on backdrop click
    document.getElementById('previewModal').addEventListener('click', function(e) {
        if (e.target === this) closePreviewModal();
    });

    document.getElementById('errorModal').addEventListener('click', function(e) {
        if (e.target === this) closeErrorModal();
    });
</script>

</body>
</html>
