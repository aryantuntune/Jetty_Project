{{--
================================================================================
OLD DESIGN - COMMENTED OUT (Bootstrap Version)
================================================================================
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ferry Booking</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root { --brand:#3087FF; --cta1:#FFD700; --cta2:#FF8C00; }
    html, body { height:100%; margin:0; }
    body { font-family: system-ui, -apple-system, Roboto, Arial, sans-serif; }

    /* ---------------- VIDEO BACKGROUND ---------------- */
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
        /* Removed darkening filter */
        filter: brightness(95%);
    }

    /* Light overlay so text is visible but video remains CLEAR */
    .hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        color: #fff;
    }

    /* More transparent gradient */
    .hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to bottom right,
            rgba(0,0,0,0.20),
            rgba(0,0,0,0.10)
        );
        z-index: 1;
    }

    .hero .container {
        position: relative;
        z-index: 2;
    }

    .hero h1 {
      font-weight: 800;
      font-size: clamp(2.2rem, 5vw, 4rem);
      text-shadow: 0 4px 14px rgba(0,0,0,.45);
      line-height: 1.1;
    }

    .btn-booking{
      background: linear-gradient(45deg, var(--cta1), var(--cta2));
      border:none;
      color:#111;
      font-weight:800;
      padding:14px 32px;
      border-radius:40px;
      font-size:1.15rem;
      box-shadow:0 12px 30px rgba(0,0,0,.35);
      transition:.3s ease;
    }
    .btn-booking:hover{
      transform: translateX(8px);
      filter: saturate(1.2);
    }

    .navbar{
      background: rgba(0,0,0,.15);
      backdrop-filter: blur(4px);
    }
    .navbar .nav-link{ color:#fff; font-weight:600; }
    .navbar .nav-link:hover{ color:var(--cta1); }
</style>

</head>
<body>

<!-- VIDEO BACKGROUND -->
<div class="video-bg">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
    </video>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand text-white" href="{{ url('/ticket-entry') }}">
        <strong>Ferry Booking</strong>
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMini">
        <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navMini">
        <ul class="navbar-nav gap-lg-3">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('customer.login') }}">Book Now</a>
          </li>

          @guest
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">Admin Login</a>
            </li>
          @endguest

          @auth
            <li class="nav-item">
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-light btn-sm">Logout</button>
              </form>
            </li>
          @endauth
        </ul>
      </div>
    </div>
</nav>

<!-- HERO WITH TITLE & BUTTON -->
<section class="hero">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-7">

          <h1>Ready to<br>Begin Your<br>Journey?</h1>

          <p class="lead mt-3">Fast. Safe. Scenic. Book your ferry in seconds.</p>

          <a href="{{ route('customer.login') }}" class="btn btn-booking mt-3">
            Book Now
          </a>

        </div>
      </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
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
    <title>Jetty - Ferry Booking</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'fade-in-left': 'fadeInLeft 0.8s ease-out forwards',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'shimmer': 'shimmer 2s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeInLeft: {
                            '0%': { opacity: '0', transform: 'translateX(-30px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        html { scroll-behavior: smooth; }

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
            filter: brightness(0.7);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .glass-nav {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #93c5fd 50%, #fbbf24 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(251, 191, 36, 0.4);
        }

        .btn-shimmer {
            background: linear-gradient(90deg,
                transparent,
                rgba(255,255,255,0.3),
                transparent
            );
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
        }

        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
        }

        .scroll-indicator {
            animation: float 2s ease-in-out infinite;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        /* Mobile menu */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }
    </style>
</head>

<body class="font-sans antialiased text-white overflow-x-hidden">

<!-- Video Background -->
<div class="video-bg">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
    </video>
</div>

<!-- Navbar -->
<nav class="glass-nav fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-lg group-hover:shadow-primary-500/50 transition-all duration-300">
                    <i data-lucide="ship" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                </div>
                <span class="text-xl md:text-2xl font-bold tracking-tight">Jetty</span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-white/80 hover:text-white font-medium transition-colors duration-200">Features</a>
                <a href="#how-it-works" class="text-white/80 hover:text-white font-medium transition-colors duration-200">How It Works</a>

                @guest
                <a href="{{ route('login') }}" class="text-white/80 hover:text-white font-medium transition-colors duration-200">Admin</a>
                @endguest

                @auth
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-white/80 hover:text-white font-medium transition-colors duration-200">Logout</button>
                </form>
                @endauth

                <a href="{{ route('customer.login') }}" class="btn-gradient px-6 py-2.5 rounded-full font-semibold text-gray-900 shadow-lg hover:shadow-xl">
                    Book Now
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed top-0 right-0 h-full w-72 glass-card z-50 md:hidden">
        <div class="p-6">
            <button id="close-menu-btn" class="absolute top-4 right-4 p-2 rounded-lg hover:bg-white/10 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>

            <div class="mt-12 space-y-6">
                <a href="#features" class="block text-lg font-medium text-white/90 hover:text-white">Features</a>
                <a href="#how-it-works" class="block text-lg font-medium text-white/90 hover:text-white">How It Works</a>

                @guest
                <a href="{{ route('login') }}" class="block text-lg font-medium text-white/90 hover:text-white">Admin Login</a>
                @endguest

                <a href="{{ route('customer.login') }}" class="block btn-gradient px-6 py-3 rounded-full font-semibold text-gray-900 text-center mt-8">
                    Book Now
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="min-h-screen flex items-center justify-center relative px-4">
    <div class="max-w-7xl mx-auto w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 glass-card px-4 py-2 rounded-full mb-6 opacity-0 animate-fade-in-up">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium text-white/80">Online Booking Available</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold leading-tight mb-6 opacity-0 animate-fade-in-up delay-100">
                    <span class="block">Your Journey</span>
                    <span class="block gradient-text">Starts Here</span>
                </h1>

                <p class="text-lg sm:text-xl text-white/70 max-w-xl mx-auto lg:mx-0 mb-8 opacity-0 animate-fade-in-up delay-200">
                    Experience seamless ferry booking with real-time availability, instant confirmations, and the most scenic routes across the waters.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 opacity-0 animate-fade-in-up delay-300">
                    <a href="{{ route('customer.login') }}" class="btn-gradient relative overflow-hidden px-8 py-4 rounded-full font-bold text-lg text-gray-900 shadow-2xl w-full sm:w-auto text-center group">
                        <span class="relative z-10 flex items-center justify-center space-x-2">
                            <span>Book Your Ferry</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </span>
                        <div class="absolute inset-0 btn-shimmer"></div>
                    </a>

                    <a href="#how-it-works" class="flex items-center space-x-2 text-white/80 hover:text-white font-medium transition-colors group">
                        <span class="w-12 h-12 rounded-full border-2 border-white/30 flex items-center justify-center group-hover:border-white/60 transition-colors">
                            <i data-lucide="play" class="w-5 h-5 ml-0.5"></i>
                        </span>
                        <span>See How It Works</span>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 mt-12 pt-8 border-t border-white/10 opacity-0 animate-fade-in-up delay-400">
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold text-accent-gold">500+</div>
                        <div class="text-sm text-white/60">Daily Trips</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold text-accent-gold">50K+</div>
                        <div class="text-sm text-white/60">Happy Travelers</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-bold text-accent-gold">20+</div>
                        <div class="text-sm text-white/60">Routes</div>
                    </div>
                </div>
            </div>

            <!-- Right Content - Feature Cards -->
            <div class="hidden lg:block relative">
                <div class="absolute inset-0 bg-gradient-to-r from-primary-500/20 to-accent-gold/20 rounded-3xl blur-3xl"></div>

                <div class="relative glass-card rounded-3xl p-8 animate-float">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="feature-card glass-card rounded-2xl p-6 text-center">
                            <div class="w-14 h-14 mx-auto rounded-xl bg-primary-500/20 flex items-center justify-center mb-4">
                                <i data-lucide="clock" class="w-7 h-7 text-primary-300"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Real-Time</h3>
                            <p class="text-sm text-white/60">Live schedules</p>
                        </div>

                        <div class="feature-card glass-card rounded-2xl p-6 text-center">
                            <div class="w-14 h-14 mx-auto rounded-xl bg-green-500/20 flex items-center justify-center mb-4">
                                <i data-lucide="shield-check" class="w-7 h-7 text-green-300"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Secure</h3>
                            <p class="text-sm text-white/60">Safe payments</p>
                        </div>

                        <div class="feature-card glass-card rounded-2xl p-6 text-center">
                            <div class="w-14 h-14 mx-auto rounded-xl bg-accent-gold/20 flex items-center justify-center mb-4">
                                <i data-lucide="zap" class="w-7 h-7 text-accent-gold"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Instant</h3>
                            <p class="text-sm text-white/60">Quick booking</p>
                        </div>

                        <div class="feature-card glass-card rounded-2xl p-6 text-center">
                            <div class="w-14 h-14 mx-auto rounded-xl bg-purple-500/20 flex items-center justify-center mb-4">
                                <i data-lucide="smartphone" class="w-7 h-7 text-purple-300"></i>
                            </div>
                            <h3 class="font-semibold mb-1">Mobile</h3>
                            <p class="text-sm text-white/60">Book anywhere</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 scroll-indicator">
        <a href="#features" class="flex flex-col items-center text-white/60 hover:text-white transition-colors">
            <span class="text-sm mb-2">Scroll to explore</span>
            <i data-lucide="chevron-down" class="w-6 h-6"></i>
        </a>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-24 px-4 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-black/50 to-black/70"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4">Why Choose Jetty?</h2>
            <p class="text-lg text-white/60 max-w-2xl mx-auto">Experience the future of ferry travel with our modern booking platform</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="feature-card glass-card rounded-2xl p-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center mb-6 shadow-lg shadow-primary-500/30">
                    <i data-lucide="calendar-check" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Easy Scheduling</h3>
                <p class="text-white/60">Browse available schedules and pick the perfect time for your journey with just a few taps.</p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card glass-card rounded-2xl p-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center mb-6 shadow-lg shadow-green-500/30">
                    <i data-lucide="ticket" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Digital Tickets</h3>
                <p class="text-white/60">Get instant e-tickets delivered to your device. No printing required, just show and go.</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card glass-card rounded-2xl p-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-accent-gold to-accent-orange flex items-center justify-center mb-6 shadow-lg shadow-accent-gold/30">
                    <i data-lucide="map-pin" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Multiple Routes</h3>
                <p class="text-white/60">Connect to various destinations with our extensive network of ferry routes.</p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card glass-card rounded-2xl p-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mb-6 shadow-lg shadow-purple-500/30">
                    <i data-lucide="users" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Group Bookings</h3>
                <p class="text-white/60">Traveling with family or friends? Book multiple passengers in a single transaction.</p>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card glass-card rounded-2xl p-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center mb-6 shadow-lg shadow-rose-500/30">
                    <i data-lucide="car" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Vehicle Transport</h3>
                <p class="text-white/60">Bring your vehicle along. We support cars, bikes, and commercial vehicles.</p>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card glass-card rounded-2xl p-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center mb-6 shadow-lg shadow-cyan-500/30">
                    <i data-lucide="headphones" class="w-8 h-8 text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">24/7 Support</h3>
                <p class="text-white/60">Our customer service team is always ready to assist you with any queries.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-24 px-4 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-black/70 to-black/50"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4">How It Works</h2>
            <p class="text-lg text-white/60 max-w-2xl mx-auto">Book your ferry in three simple steps</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 relative">
            <!-- Connecting Line (Desktop) -->
            <div class="hidden md:block absolute top-24 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-primary-500 via-accent-gold to-primary-500"></div>

            <!-- Step 1 -->
            <div class="relative text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center mb-6 shadow-xl shadow-primary-500/30 relative z-10">
                    <span class="text-2xl font-bold">1</span>
                </div>
                <h3 class="text-xl font-bold mb-3">Select Route</h3>
                <p class="text-white/60">Choose your departure and destination from our available routes</p>
            </div>

            <!-- Step 2 -->
            <div class="relative text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-accent-gold to-accent-orange flex items-center justify-center mb-6 shadow-xl shadow-accent-gold/30 relative z-10">
                    <span class="text-2xl font-bold text-gray-900">2</span>
                </div>
                <h3 class="text-xl font-bold mb-3">Pick Schedule</h3>
                <p class="text-white/60">Select your preferred date and time from available ferry schedules</p>
            </div>

            <!-- Step 3 -->
            <div class="relative text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center mb-6 shadow-xl shadow-green-500/30 relative z-10">
                    <span class="text-2xl font-bold">3</span>
                </div>
                <h3 class="text-xl font-bold mb-3">Confirm & Go</h3>
                <p class="text-white/60">Complete your booking and receive instant confirmation</p>
            </div>
        </div>

        <!-- CTA -->
        <div class="text-center mt-16">
            <a href="{{ route('customer.login') }}" class="btn-gradient inline-flex items-center space-x-2 px-8 py-4 rounded-full font-bold text-lg text-gray-900 shadow-2xl">
                <span>Start Booking Now</span>
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="py-12 px-4 relative">
    <div class="absolute inset-0 bg-black/80"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                    <i data-lucide="ship" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xl font-bold">Jetty</span>
            </div>

            <div class="flex items-center space-x-6 text-white/60">
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
                <a href="#" class="hover:text-white transition-colors">Contact</a>
            </div>

            <p class="text-white/40 text-sm">
                &copy; {{ date('Y') }} Jetty. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const closeMenuBtn = document.getElementById('close-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.add('open');
    });

    closeMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
    });

    // Close menu when clicking a link
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
        });
    });

    // Navbar background on scroll
    const navbar = document.querySelector('nav');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-black/40');
        } else {
            navbar.classList.remove('bg-black/40');
        }
    });

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.feature-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    });
</script>

</body>
</html>
