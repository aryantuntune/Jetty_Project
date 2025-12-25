<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @laravelPWA

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - {{ config('app.name', 'Jetty') }}</title>

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
                        },
                        sidebar: {
                            DEFAULT: '#0f172a',
                            hover: '#1e293b',
                            active: '#334155',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Sidebar scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }

        /* Collapsible submenu - hidden by default */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.25s ease-out;
        }
        .submenu.open {
            max-height: 500px;
            transition: max-height 0.3s ease-in;
        }

        /* Mobile sidebar */
        .sidebar-overlay {
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .mobile-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .mobile-sidebar.active {
            transform: translateX(0);
        }
        @media (min-width: 1024px) {
            .mobile-sidebar {
                transform: translateX(0);
            }
        }

        /* Table hover effect */
        .table-row-hover:hover {
            background: linear-gradient(90deg, #eff6ff 0%, #f8fafc 100%);
        }

        /* Card hover effect */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Navigation styles - clean, tight, cohesive */
        .nav-item {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            margin: 2px 0;
            border-radius: 8px;
            color: rgba(148, 163, 184, 1);
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .nav-item:hover {
            background: rgba(51, 65, 85, 0.5);
            color: #e2e8f0;
        }
        .nav-item.active {
            background: rgba(99, 102, 241, 0.9);
            color: #ffffff;
        }
        .nav-item.active i { color: #ffffff; }
        .nav-item i {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .nav-item .chevron {
            margin-left: auto;
            width: 16px;
            height: 16px;
            margin-right: 0;
            transition: transform 0.2s ease;
        }
        .nav-item.open .chevron {
            transform: rotate(180deg);
        }

        /* Submenu items */
        .submenu-item {
            display: block;
            padding: 8px 12px 8px 44px;
            margin: 1px 0;
            border-radius: 6px;
            color: rgba(148, 163, 184, 0.9);
            font-size: 13px;
            transition: all 0.15s ease;
        }
        .submenu-item:hover {
            background: rgba(51, 65, 85, 0.4);
            color: #e2e8f0;
        }
        .submenu-item.active {
            background: rgba(99, 102, 241, 0.25);
            color: #a5b4fc;
            border-left: 2px solid #818cf8;
            padding-left: 42px;
        }

        /* Section header */
        .nav-section-header {
            padding: 16px 12px 8px 12px;
            font-size: 11px;
            font-weight: 600;
            color: rgba(100, 116, 139, 0.8);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="flex min-h-screen">

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 z-40 lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="mobile-sidebar fixed lg:static inset-y-0 left-0 z-50 w-64 bg-sidebar flex flex-col">
            <!-- Logo -->
            <div class="flex items-center h-16 px-6 border-b border-white/10">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                        <i data-lucide="ship" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-white">Jetty</span>
                </a>
                <button onclick="toggleSidebar()" class="lg:hidden ml-auto text-white/60 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation -->
            @php
                $currentRoute = Route::currentRouteName() ?? '';
                $isReportsActive = Str::startsWith($currentRoute, 'reports.');
                $masterRoutes = ['items.from_rates.index', 'item_categories.index', 'item_categories.create', 'item_categories.edit', 'item-rates.index', 'item-rates.create', 'item-rates.edit', 'item-rates.show', 'ferryboats.index', 'ferryboats.create', 'ferryboats.edit', 'ferry_schedules.index', 'ferry_schedules.create', 'ferry_schedules.edit', 'guests.index', 'guests.create', 'guests.edit', 'guest_categories.index', 'guest_categories.create', 'branches.index', 'branches.create', 'branches.edit', 'special-charges.index', 'special-charges.create', 'special-charges.edit'];
                $isMastersActive = in_array($currentRoute, $masterRoutes);
                $isTransferActive = Str::contains($currentRoute, 'transfer');
                $isVerifyActive = Str::contains($currentRoute, 'verify');
                $adminRoutes = ['admin.index', 'admin.create', 'admin.edit', 'admin.show', 'manager.index', 'manager.create', 'manager.edit', 'manager.show', 'operator.index', 'operator.create', 'operator.edit', 'operator.show', 'checker.index', 'checker.create', 'checker.edit', 'checker.show'];
                $isAdminActive = in_array($currentRoute, $adminRoutes);
            @endphp
            <nav class="flex-1 px-3 py-4 sidebar-scroll overflow-y-auto">
                <!-- Counter Options -->
                <a href="{{ route('ticket-entry.create') }}" class="nav-item {{ $currentRoute == 'ticket-entry.create' ? 'active' : '' }}">
                    <i data-lucide="ticket"></i>
                    <span>Counter Options</span>
                </a>

                <!-- Reports -->
                <div class="nav-group">
                    <div class="nav-item {{ $isReportsActive ? 'active open' : '' }}" onclick="toggleSubmenu(this)">
                        <i data-lucide="bar-chart-3"></i>
                        <span>Reports</span>
                        <i data-lucide="chevron-down" class="chevron"></i>
                    </div>
                    <div class="submenu {{ $isReportsActive ? 'open' : '' }}">
                        <a href="{{ route('reports.tickets') }}" class="submenu-item {{ $currentRoute == 'reports.tickets' ? 'active' : '' }}">Ticket Details</a>
                        <a href="{{ route('reports.vehicle_tickets') }}" class="submenu-item {{ $currentRoute == 'reports.vehicle_tickets' ? 'active' : '' }}">Vehicle-wise Details</a>
                    </div>
                </div>

                <!-- Masters -->
                <div class="nav-group">
                    <div class="nav-item {{ $isMastersActive ? 'active open' : '' }}" onclick="toggleSubmenu(this)">
                        <i data-lucide="database"></i>
                        <span>Masters</span>
                        <i data-lucide="chevron-down" class="chevron"></i>
                    </div>
                    <div class="submenu {{ $isMastersActive ? 'open' : '' }}">
                        <a href="{{ route('items.from_rates.index') }}" class="submenu-item {{ $currentRoute == 'items.from_rates.index' ? 'active' : '' }}">Items</a>
                        <a href="{{ route('item_categories.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'item_categories.') ? 'active' : '' }}">Item Categories</a>
                        <a href="{{ route('item-rates.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'item-rates.') ? 'active' : '' }}">Item Rate Slabs</a>
                        <a href="{{ route('ferryboats.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'ferryboats.') ? 'active' : '' }}">Ferry Boats</a>
                        <a href="{{ route('ferry_schedules.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'ferry_schedules.') ? 'active' : '' }}">Ferry Schedules</a>
                        <a href="{{ route('guests.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'guests.') ? 'active' : '' }}">Guests</a>
                        <a href="{{ route('guest_categories.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'guest_categories.') ? 'active' : '' }}">Guest Categories</a>
                        <a href="{{ route('branches.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'branches.') ? 'active' : '' }}">Branches</a>
                        @if(in_array(auth()->user()->role_id, [1,2]))
                        <a href="{{ route('special-charges.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'special-charges.') ? 'active' : '' }}">Special Charges</a>
                        @endif
                    </div>
                </div>

                <!-- Transfer -->
                @if(in_array(auth()->user()->role_id, [1,2]))
                <a href="{{ route('employees.transfer.index') }}" class="nav-item {{ $isTransferActive ? 'active' : '' }}">
                    <i data-lucide="arrow-right-left"></i>
                    <span>Transfer</span>
                </a>
                @endif

                <!-- Verify Ticket -->
                @if(in_array(auth()->user()->role_id, [1,2,5]))
                <a href="{{ route('verify.index') }}" class="nav-item {{ $isVerifyActive ? 'active' : '' }}">
                    <i data-lucide="check-circle"></i>
                    <span>Verify Ticket</span>
                </a>
                @endif

                <!-- Admin Section -->
                @if(in_array(auth()->user()->role_id, [1,2]))
                <div class="nav-section-header">Administration</div>
                <div class="nav-group">
                    <div class="nav-item {{ $isAdminActive ? 'active open' : '' }}" onclick="toggleSubmenu(this)">
                        <i data-lucide="users"></i>
                        <span>User Management</span>
                        <i data-lucide="chevron-down" class="chevron"></i>
                    </div>
                    <div class="submenu {{ $isAdminActive ? 'open' : '' }}">
                        <a href="{{ route('admin.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'admin.') ? 'active' : '' }}">Administrators</a>
                        <a href="{{ route('manager.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'manager.') ? 'active' : '' }}">Managers</a>
                        <a href="{{ route('operator.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'operator.') ? 'active' : '' }}">Operators</a>
                        <a href="{{ route('checker.index') }}" class="submenu-item {{ Str::startsWith($currentRoute, 'checker.') ? 'active' : '' }}">Checkers</a>
                    </div>
                </div>
                @endif
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-white/50 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg text-white/60 hover:bg-sidebar-hover hover:text-red-400 transition-colors" title="Logout">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 lg:px-8">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <!-- Page Title -->
                    <h1 class="text-lg font-semibold text-slate-800 hidden lg:block">@yield('page-title', 'Dashboard')</h1>

                    <!-- Right Section -->
                    <div class="flex items-center space-x-4">
                        <!-- Search (Optional) -->
                        <div class="hidden md:flex items-center">
                            <div class="relative">
                                <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            </div>
                        </div>

                        <!-- Notifications (Optional) -->
                        <button class="relative p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- User Avatar (Mobile) -->
                        <div class="lg:hidden">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-8">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                    </div>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                        </div>
                        <p class="text-red-800 font-medium">Please fix the following errors:</p>
                    </div>
                    <ul class="ml-14 list-disc text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="py-4 px-8 border-t border-slate-200 bg-white">
                <p class="text-center text-sm text-slate-500">&copy; {{ date('Y') }} Jetty. All rights reserved.</p>
            </footer>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();

        // Toggle Sidebar (mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Toggle Submenu (click-based collapsible)
        function toggleSubmenu(element) {
            const submenu = element.nextElementSibling;
            const isOpen = submenu.classList.contains('open');

            // Toggle current submenu
            element.classList.toggle('open');
            submenu.classList.toggle('open');
        }

        // Close sidebar on window resize (if moving to desktop)
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebar').classList.remove('active');
                document.getElementById('sidebar-overlay').classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
