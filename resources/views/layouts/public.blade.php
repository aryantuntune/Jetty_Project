<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Suvarnadurga Shipping & Marine Services')</title>
    <meta name="description"
        content="@yield('description', 'Ferry services across Maharashtra - Dabhol-Dhopave, Jaigad-Tawsal, Dighi-Agardande, Veshvi-Bagmandale, Vasai-Bhayander, Virar-Saphale, Ambet-Mahpral')">

    <!-- Google Fonts - Matching carferry.in -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/carferry/logos/logo.png') }}">

    <style>
        /* ============================================
           EXACT CARFERRY.IN COLOR SCHEME & TYPOGRAPHY
           ============================================ */
        :root {
            --primary-blue: #3A86FF;
            --secondary-orange: #FB5607;
            --light-blue-bg: #EBF2FF;
            --text-dark: #495057;
            --text-heading: #000000;
            --white: #FFFFFF;
            --footer-bg: #1a1a2e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            color: var(--text-dark);
            line-height: 1.6;
            background: var(--white);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: var(--text-heading);
            line-height: 1.2;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ============================================
           HEADER / NAVIGATION - Matching carferry.in
           ============================================ */
        .main-header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .top-bar {
            background: var(--primary-blue);
            color: var(--white);
            padding: 8px 0;
            font-size: 14px;
        }

        .top-bar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .top-bar a {
            color: var(--white);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .top-bar a:hover {
            opacity: 0.9;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        .logo img {
            height: 60px;
            width: auto;
        }

        .main-nav {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .main-nav a {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            color: var(--text-heading);
            padding: 12px 18px;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .main-nav a:hover,
        .main-nav a.active {
            color: var(--secondary-orange);
        }

        /* Dropdown Menu */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown>a {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-dropdown>a::after {
            content: '▼';
            font-size: 10px;
            transition: transform 0.3s ease;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--white);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .nav-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu a {
            display: block;
            padding: 12px 20px;
            font-size: 13px;
            border-bottom: 1px solid #f0f0f0;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }

        .dropdown-menu a:hover {
            background: var(--light-blue-bg);
            color: var(--primary-blue);
        }

        /* Mobile Menu Toggle */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: var(--text-heading);
        }

        /* ============================================
           HERO SECTION - Matching carferry.in
           ============================================ */
        .hero-section {
            background: linear-gradient(135deg, var(--light-blue-bg) 0%, var(--white) 100%);
            padding: 140px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 48px;
            font-weight: 800;
            color: var(--primary-blue);
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: 20px;
            color: var(--text-dark);
            margin-bottom: 30px;
        }

        .btn-primary {
            display: inline-block;
            background: var(--secondary-orange);
            color: var(--white);
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 16px;
            padding: 15px 40px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(251, 86, 7, 0.3);
        }

        .btn-primary:hover {
            background: #e04d00;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(251, 86, 7, 0.4);
        }

        .btn-secondary {
            display: inline-block;
            background: var(--primary-blue);
            color: var(--white);
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #2a76ef;
            transform: translateY(-2px);
        }

        /* Wave Divider */
        .wave-divider {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            overflow: hidden;
        }

        .wave-divider svg {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60px;
        }

        /* ============================================
           FERRY ROUTES SECTION
           ============================================ */
        .routes-section {
            padding: 80px 0;
            background: var(--white);
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            color: var(--primary-blue);
            margin-bottom: 15px;
        }

        .section-subtitle {
            text-align: center;
            font-size: 18px;
            color: var(--text-dark);
            margin-bottom: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .routes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .route-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
        }

        .route-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .route-card-image {
            height: 200px;
            overflow: hidden;
        }

        .route-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .route-card:hover .route-card-image img {
            transform: scale(1.1);
        }

        .route-card-content {
            padding: 25px;
        }

        .route-card-title {
            font-size: 20px;
            color: var(--text-heading);
            margin-bottom: 10px;
        }

        .route-card-desc {
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .route-card-link {
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: gap 0.3s ease;
        }

        .route-card-link:hover {
            gap: 12px;
            color: var(--secondary-orange);
        }

        /* ============================================
           ABOUT SECTION
           ============================================ */
        .about-section {
            padding: 80px 0;
            background: var(--light-blue-bg);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .about-image img {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .about-content h2 {
            font-size: 36px;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }

        .about-content p {
            font-size: 16px;
            margin-bottom: 15px;
            line-height: 1.8;
        }

        /* ============================================
           SERVICES SECTION
           ============================================ */
        .services-section {
            padding: 80px 0;
            background: var(--white);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .service-card {
            background: var(--light-blue-bg);
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            align-items: stretch;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .service-card-image {
            width: 40%;
            min-height: 200px;
        }

        .service-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .service-card-content {
            padding: 30px;
            flex: 1;
        }

        .service-card-content h3 {
            font-size: 22px;
            color: var(--primary-blue);
            margin-bottom: 15px;
        }

        .service-card-content p {
            font-size: 14px;
            line-height: 1.7;
        }

        /* ============================================
           CONTACT INFO BAR
           ============================================ */
        .contact-bar {
            background: var(--primary-blue);
            color: var(--white);
            padding: 40px 0;
        }

        .contact-bar-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            text-align: center;
        }

        .contact-bar-item h4 {
            font-size: 18px;
            color: var(--white);
            margin-bottom: 10px;
        }

        .contact-bar-item p {
            font-size: 14px;
            opacity: 0.9;
        }

        .contact-bar-item a {
            color: var(--white);
        }

        .contact-bar-item a:hover {
            text-decoration: underline;
        }

        /* ============================================
           FOOTER - Matching carferry.in
           ============================================ */
        .main-footer {
            background: var(--footer-bg) url('{{ asset("images/carferry/backgrounds/water-ripples.jpg") }}') center/cover;
            position: relative;
            color: var(--white);
            padding: 60px 0 0;
        }

        .main-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(26, 26, 46, 0.95);
        }

        .footer-content {
            position: relative;
            z-index: 1;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            padding-bottom: 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-logo img {
            height: 80px;
            margin-bottom: 20px;
        }

        .footer-about p {
            font-size: 14px;
            line-height: 1.8;
            opacity: 0.8;
        }

        .footer-title {
            font-size: 18px;
            color: var(--white);
            margin-bottom: 25px;
            position: relative;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--secondary-orange);
        }

        .footer-links a {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            padding: 8px 0;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--secondary-orange);
            padding-left: 10px;
        }

        .footer-contact p {
            font-size: 14px;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            opacity: 0.8;
        }

        .footer-bottom {
            position: relative;
            z-index: 1;
            padding: 20px 0;
            text-align: center;
            font-size: 14px;
            opacity: 0.7;
        }

        /* ============================================
           RESPONSIVE DESIGN
           ============================================ */
        @media (max-width: 992px) {
            .main-nav {
                display: none;
            }

            .mobile-toggle {
                display: block;
            }

            .hero-title {
                font-size: 36px;
            }

            .about-grid {
                grid-template-columns: 1fr;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .service-card {
                flex-direction: column;
            }

            .service-card-image {
                width: 100%;
                height: 200px;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .top-bar-content {
                justify-content: center;
                text-align: center;
            }

            .hero-section {
                padding: 120px 0 60px;
            }

            .hero-title {
                font-size: 28px;
            }

            .section-title {
                font-size: 28px;
            }

            .routes-grid {
                grid-template-columns: 1fr;
            }

            .contact-bar-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-title::after {
                left: 50%;
                transform: translateX(-50%);
            }
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--white);
            z-index: 2000;
            padding: 80px 30px 30px;
            overflow-y: auto;
        }

        .mobile-menu.active {
            display: block;
        }

        .mobile-menu-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 30px;
            cursor: pointer;
        }

        .mobile-menu a {
            display: block;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 18px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            color: var(--text-heading);
        }

        .mobile-menu a:hover {
            color: var(--primary-blue);
        }

        .mobile-submenu {
            padding-left: 20px;
        }

        .mobile-submenu a {
            font-size: 15px;
            font-weight: 500;
        }

        /* Page specific padding for fixed header */
        .page-content {
            padding-top: 100px;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Top Bar -->
    <header class="main-header">
        <div class="top-bar">
            <div class="container">
                <div class="top-bar-content">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <a href="tel:+919422431371">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            +91 9422431371
                        </a>
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            Open 9:00 AM - 5:00 PM (7 Days)
                        </span>
                    </div>
                    <a href="{{ route('login') }}"
                        style="display: flex; align-items: center; gap: 6px; opacity: 0.85; font-size: 13px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Staff Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="container">
            <div class="nav-container">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('images/carferry/logos/logo.png') }}"
                        alt="Suvarnadurga Shipping & Marine Services">
                </a>

                <nav class="main-nav">
                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>

                    <div class="nav-dropdown">
                        <a href="#">Ferry Services</a>
                        <div class="dropdown-menu">
                            <a href="{{ url('/route/dabhol-dhopave') }}">Dabhol – Dhopave</a>
                            <a href="{{ url('/route/jaigad-tawsal') }}">Jaigad – Tawsal</a>
                            <a href="{{ url('/route/dighi-agardande') }}">Dighi – Agardande</a>
                            <a href="{{ url('/route/veshvi-bagmandale') }}">Veshvi – Bagmandale</a>
                            <a href="{{ url('/route/vasai-bhayander') }}">Vasai – Bhayander</a>
                            <a href="{{ url('/route/virar-saphale') }}">Virar – Saphale</a>
                            <a href="{{ url('/route/ambet-mahpral') }}">Ambet – Mahpral</a>
                        </div>
                    </div>

                    <a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">About Us</a>
                    <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Contact
                        Us</a>
                    <a href="{{ route('houseboat.index') }}" class="btn-secondary"
                        style="background: white; color: var(--primary-blue); border: 2px solid var(--primary-blue);">Houseboat
                        Booking</a>
                    <a href="{{ route('customer.login') }}" class="btn-secondary">Book Now</a>
                </nav>

                <button class="mobile-toggle" onclick="openMobileMenu()">☰</button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <button class="mobile-menu-close" onclick="closeMobileMenu()">×</button>
        <a href="{{ url('/') }}">Home</a>
        <a href="#">Ferry Services ▼</a>
        <div class="mobile-submenu">
            <a href="{{ url('/route/dabhol-dhopave') }}">Dabhol – Dhopave</a>
            <a href="{{ url('/route/jaigad-tawsal') }}">Jaigad – Tawsal</a>
            <a href="{{ url('/route/dighi-agardande') }}">Dighi – Agardande</a>
            <a href="{{ url('/route/veshvi-bagmandale') }}">Veshvi – Bagmandale</a>
            <a href="{{ url('/route/vasai-bhayander') }}">Vasai – Bhayander</a>
            <a href="{{ url('/route/virar-saphale') }}">Virar – Saphale</a>
            <a href="{{ url('/route/ambet-mahpral') }}">Ambet – Mahpral</a>
        </div>
        <a href="{{ url('/about') }}">About Us</a>
        <a href="{{ url('/contact') }}">Contact Us</a>
        <a href="{{ route('customer.login') }}" style="color: var(--secondary-orange);">Book Now</a>
        <a href="{{ route('login') }}"
            style="color: var(--primary-blue); margin-top: 20px; border-top: 2px solid #eee; padding-top: 20px;">Staff
            Login</a>
    </div>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container footer-content">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="footer-logo">
                        <img src="{{ asset('images/carferry/logos/logo-white.png') }}" alt="Suvarnadurga Logo">
                    </div>
                    <p>
                        Suvarnadurga Shipping & Marine Services Pvt. Ltd. has been providing reliable ferry services
                        across Maharashtra's Konkan coast since 2003. We operate 7 routes connecting coastal
                        communities.
                    </p>
                </div>

                <div>
                    <h4 class="footer-title">Quick Links</h4>
                    <div class="footer-links">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ url('/about') }}">About Us</a>
                        <a href="{{ url('/contact') }}">Contact Us</a>
                        <a href="{{ route('customer.login') }}">Book Ferry</a>
                        <a href="{{ route('login') }}">Staff Login</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-title">Ferry Routes</h4>
                    <div class="footer-links">
                        <a href="{{ url('/route/dabhol-dhopave') }}">Dabhol – Dhopave</a>
                        <a href="{{ url('/route/jaigad-tawsal') }}">Jaigad – Tawsal</a>
                        <a href="{{ url('/route/dighi-agardande') }}">Dighi – Agardande</a>
                        <a href="{{ url('/route/veshvi-bagmandale') }}">Veshvi – Bagmandale</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-title">Contact Info</h4>
                    <div class="footer-contact">
                        <p>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            Dabhol FerryBoat Jetty, Dapoli, Dist. Ratnagiri, Maharashtra - 415712
                        </p>
                        <p>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <a href="tel:+919422431371">+91 9422431371</a>
                        </p>
                        <p>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            <a href="mailto:ssmsdapoli@rediffmail.com">ssmsdapoli@rediffmail.com</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Suvarnadurga Shipping & Marine Services Pvt. Ltd. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function openMobileMenu() {
            document.getElementById('mobileMenu').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            document.getElementById('mobileMenu').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    </script>

    @stack('scripts')
</body>

</html>