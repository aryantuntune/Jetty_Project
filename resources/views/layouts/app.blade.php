<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @laravelPWA

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Show dropdown on hover */
        .navbar .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>

<body>
    <div id="app">
        @auth
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container-fluid">

                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <!-- Counter Options -->
                        <li class="nav-item">
  <a class="nav-link" href="{{ route('ticket-entry.create') }}">Counter Options</a>
</li>


                        <!-- Reports -->
                       <!-- Reports Dropdown -->
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    Reports
  </a>
  <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
    <li>
      {{-- absolute via route name --}}
      <a class="dropdown-item" href="{{ route('reports.tickets') }}">Ticket Details</a>
    </li>
    <li>
      <a class="dropdown-item" href="{{ route('reports.vehicle_tickets') }}">Vehicle-wise Ticket Details</a>
    </li>
  </ul>
</li>



                        <!-- Masters -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Masters</a>
                            <ul class="dropdown-menu">
<li>
  <a class="dropdown-item" href="{{ route('items.from_rates.index') }}">Items</a>
</li>
                                <li><a class="dropdown-item" href="{{ route('item_categories.index') }}">Item Categories</a></li>
                                <li>
                                <a class="dropdown-item" href="{{ route('item-rates.index') }}">Item Rate Slabs</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('ferryboats.index') }}">Ferry Boats</a></li>
                                <li><a class="dropdown-item" href="{{ route('ferry_schedules.index') }}">Ferry Schedules</a></li>
                                <li><a class="dropdown-item" href="{{ route('guests.index') }}">Guests</a></li>
                                <li><a class="dropdown-item" href="{{ route('guest_categories.index') }}">Guest Categories</a></li>
                                <li><a class="dropdown-item" href="#">Customers</a></li>
                                <li><a class="dropdown-item" href="#">Verify Vehicle Names</a></li>
                                <li><a class="dropdown-item" href="{{ route('branches.index') }}">Branches</a></li>
                                  @if(in_array(auth()->user()->role_id, [1,2]))
                                <li><a class="dropdown-item" href="{{ route('special-charges.index') }}">Special Charges</a></li>
                                @endif
                            </ul>
                        </li>

                        <!-- Import / Export -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">Import / Export</a>
                        </li>

                        <!-- Transfer -->
                          @if(in_array(auth()->user()->role_id, [1,2]))
                        <li class="nav-item">
                              <a class="nav-link" href="{{ route('employees.transfer.index') }}">Transfer</a>
                        </li>
                        @endif

                        <!-- System Maintenance -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">System Maintenance</a>
                        </li>

                        <!-- Admin -->
                          @if(in_array(auth()->user()->role_id, [1,2]))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Admin</a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header">Users</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.index') }}">Administrator</a></li>
                                <li><a class="dropdown-item" href="{{ route('manager.index') }}">Manager</a></li>
                                <li><a class="dropdown-item" href="{{ route('operator.index') }}">Operator</a></li>
                                 <li><a class="dropdown-item" href="{{ route('checker.index') }}">Checker</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">User Access</a></li>
                            </ul>
                        </li>
                        @endif
                        <!-- Utility -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">Utility</a>
                        </li>
                    </ul>

                    <!-- Right Side (Auth User Dropdown) -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                </li>
                            </ul>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @endauth

        <main class="p-3">
            @yield('content')
        </main>
    </div>
    
{{-- Install App button (shown only when page becomes installable) --}}
<button id="installPWA" class="btn btn-outline-primary" title="Install App" style="display:none;">
    Install App
</button>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

<script>
(function () {
  const btn = document.getElementById('installPWA');

  // Guard: button exists
  if (!btn) return;

  // Hide on iOS (no beforeinstallprompt there)
  const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
  if (isIOS) {
    btn.style.display = 'none';
    return;
  }

  // Hide if already installed (standalone)
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches
                       || window.navigator.standalone === true;
  if (isStandalone) {
    btn.style.display = 'none';
    return;
  }

  let deferredPrompt = null;

  window.addEventListener('beforeinstallprompt', (e) => {
    // Fires only when manifest + SW + secure context are OK
    e.preventDefault();
    deferredPrompt = e;
    btn.style.display = 'inline-block';
  });

  btn.addEventListener('click', async () => {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    try {
      await deferredPrompt.userChoice; // { outcome, platform }
    } finally {
      deferredPrompt = null;
      btn.style.display = 'none';
    }
  });

  window.addEventListener('appinstalled', () => {
    btn.style.display = 'none';
    console.log('PWA installed');
  });
})();
</script>
</body>


</html>