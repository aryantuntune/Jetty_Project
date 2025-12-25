{{-- Customer Booking History Page --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History - Jetty</title>

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
                }
            }
        }
    </script>

    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
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
                <a href="/booking" class="flex items-center space-x-2 px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-100 font-medium transition-colors">
                    <i data-lucide="ticket" class="w-4 h-4"></i>
                    <span>Book Ferry</span>
                </a>
                <a href="/booking/history" class="flex items-center space-x-2 px-4 py-2 rounded-xl bg-primary-50 text-primary-600 font-medium transition-colors">
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
            <a href="/booking" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 font-medium">
                <i data-lucide="ticket" class="w-5 h-5"></i>
                <span>Book Ferry</span>
            </a>
            <a href="/booking/history" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-primary-50 text-primary-600 font-medium">
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
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Booking History</h1>
            <p class="text-gray-500">View all your past ferry bookings</p>
        </div>

        <!-- Bookings List -->
        @if($bookings->count() > 0)
            <div class="space-y-4">
                @foreach($bookings as $booking)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Booking Header -->
                    <div class="p-4 md:p-6 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-blue-50">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                                    <i data-lucide="ticket" class="w-6 h-6 text-primary-600"></i>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-gray-800">{{ $booking->from_branch_name }}</span>
                                        <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400"></i>
                                        <span class="font-semibold text-gray-800">{{ $booking->to_branch_name }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                        {{ $booking->created_at->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($booking->status == 'success') bg-green-100 text-green-700
                                    @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    @if($booking->status == 'success')
                                        <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                    @elseif($booking->status == 'pending')
                                        <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                    @else
                                        <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                    @endif
                                    {{ ucfirst($booking->status) }}
                                </span>
                                <span class="text-xl font-bold text-primary-600">₹{{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="p-4 md:p-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-3">Items</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Description</th>
                                        <th class="px-4 py-2 text-center font-medium text-gray-600">Qty</th>
                                        <th class="px-4 py-2 text-right font-medium text-gray-600">Rate</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Vehicle No</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($booking->items_decoded as $item)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-800">{{ $item['item_name'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-center text-gray-600">{{ $item['quantity'] ?? '-' }}</td>
                                        <td class="px-4 py-2 text-right text-gray-600">₹{{ $item['rate'] ?? '0' }}</td>
                                        <td class="px-4 py-2 text-gray-600">{{ $item['vehicle_no'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($booking->payment_id)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-400">
                                <span class="font-medium">Payment ID:</span> {{ $booking->payment_id }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="calendar-x" class="w-10 h-10 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Bookings Yet</h3>
                <p class="text-gray-500 mb-6">You haven't made any ferry bookings yet.</p>
                <a href="/booking" class="inline-flex items-center space-x-2 px-6 py-3 rounded-xl bg-primary-600 text-white font-medium hover:bg-primary-700 transition-colors">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                    <span>Book Your First Ferry</span>
                </a>
            </div>
        @endif
    </div>
</main>

<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>

</body>
</html>
