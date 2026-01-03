@extends('layouts.public')

@section('title', $route['name'] . ' Ferry Service - Suvarnadurga Shipping')
@section('description', $route['description'])

@push('styles')
<style>
    .page-hero {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #1a5cbd 100%);
        padding: 140px 0 80px;
        color: var(--white);
        text-align: center;
    }

    .page-hero h1 {
        font-size: 42px;
        color: var(--white);
        margin-bottom: 15px;
    }

    .page-hero p {
        font-size: 18px;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .breadcrumb {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
        font-size: 14px;
    }

    .breadcrumb a {
        color: rgba(255,255,255,0.8);
    }

    .breadcrumb a:hover {
        color: var(--white);
    }

    .breadcrumb span {
        color: var(--secondary-orange);
    }

    .route-content {
        padding: 80px 0;
    }

    .route-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 60px;
    }

    .route-main h2 {
        font-size: 28px;
        color: var(--primary-blue);
        margin-bottom: 20px;
    }

    .route-main p {
        font-size: 15px;
        line-height: 1.9;
        margin-bottom: 20px;
    }

    .route-image {
        margin: 30px 0;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }

    .route-image img {
        width: 100%;
        display: block;
    }

    /* Sidebar */
    .route-sidebar {
        position: sticky;
        top: 120px;
    }

    .sidebar-card {
        background: var(--light-blue-bg);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
    }

    .sidebar-card h3 {
        font-size: 18px;
        color: var(--primary-blue);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--primary-blue);
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .contact-item svg {
        width: 20px;
        height: 20px;
        color: var(--primary-blue);
        flex-shrink: 0;
    }

    .contact-item a {
        color: var(--text-dark);
        font-weight: 500;
    }

    .contact-item a:hover {
        color: var(--secondary-orange);
    }

    .book-now-card {
        background: var(--secondary-orange);
        color: var(--white);
        text-align: center;
    }

    .book-now-card h3 {
        color: var(--white);
        border-bottom-color: rgba(255,255,255,0.3);
    }

    .book-now-card p {
        font-size: 14px;
        margin-bottom: 20px;
        opacity: 0.9;
    }

    .book-now-btn {
        display: block;
        background: var(--white);
        color: var(--secondary-orange);
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        padding: 14px 30px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .book-now-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* Timetable & Rate Card */
    .schedule-section {
        padding: 60px 0;
        background: var(--light-blue-bg);
    }

    .schedule-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .schedule-card {
        background: var(--white);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 30px rgba(0,0,0,0.1);
    }

    .schedule-card-header {
        background: var(--primary-blue);
        color: var(--white);
        padding: 20px;
        text-align: center;
    }

    .schedule-card-header h3 {
        font-size: 20px;
        color: var(--white);
        margin: 0;
    }

    .schedule-card-body {
        padding: 20px;
    }

    .schedule-card-body img {
        width: 100%;
        border-radius: 10px;
    }

    .schedule-note {
        text-align: center;
        margin-top: 30px;
        font-size: 14px;
        color: var(--text-dark);
    }

    /* Tourist Info */
    .tourist-section {
        padding: 60px 0;
    }

    .tourist-section h2 {
        text-align: center;
        font-size: 32px;
        color: var(--primary-blue);
        margin-bottom: 40px;
    }

    .attractions-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .attraction-item {
        background: var(--white);
        border: 2px solid #eee;
        border-radius: 10px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .attraction-item:hover {
        border-color: var(--primary-blue);
        box-shadow: 0 5px 20px rgba(58, 134, 255, 0.1);
    }

    .attraction-icon {
        width: 50px;
        height: 50px;
        background: var(--light-blue-bg);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .attraction-icon svg {
        color: var(--primary-blue);
    }

    /* Other Routes */
    .other-routes {
        padding: 60px 0;
        background: var(--light-blue-bg);
    }

    .other-routes h2 {
        text-align: center;
        font-size: 32px;
        color: var(--primary-blue);
        margin-bottom: 40px;
    }

    .other-routes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .other-route-card {
        background: var(--white);
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        display: block;
    }

    .other-route-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transform: translateY(-5px);
    }

    .other-route-card h4 {
        font-size: 18px;
        color: var(--primary-blue);
        margin-bottom: 10px;
    }

    .other-route-card p {
        font-size: 13px;
        color: var(--text-dark);
    }

    @media (max-width: 992px) {
        .route-grid {
            grid-template-columns: 1fr;
        }

        .route-sidebar {
            position: static;
        }

        .schedule-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .page-hero h1 {
            font-size: 32px;
        }
    }
</style>
@endpush

@section('content')
<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1>{{ $route['name'] }}</h1>
        <p>{{ $route['tagline'] }}</p>
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Home</a>
            <span>/</span>
            <a href="#">Ferry Services</a>
            <span>/</span>
            <span>{{ $route['name'] }}</span>
        </div>
    </div>
</section>

<!-- Route Content -->
<section class="route-content">
    <div class="container">
        <div class="route-grid">
            <!-- Main Content -->
            <div class="route-main">
                <h2>About This Route</h2>

                @foreach($route['paragraphs'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach

                @if($route['image'])
                <div class="route-image">
                    <img src="{{ asset('images/carferry/routes/' . $route['image']) }}" alt="{{ $route['name'] }} Ferry">
                </div>
                @endif

                @if(!empty($route['additional_info']))
                    <h2 style="margin-top: 40px;">Tourist Destinations</h2>
                    <p>{{ $route['additional_info'] }}</p>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="route-sidebar">
                <!-- Contact Info -->
                <div class="sidebar-card">
                    <h3>Contact Information</h3>

                    @foreach($route['contacts'] as $location => $numbers)
                        <div class="contact-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <div>
                                <strong>{{ $location }}:</strong><br>
                                @foreach($numbers as $number)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $number) }}">{{ $number }}</a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Operating Hours -->
                <div class="sidebar-card">
                    <h3>Operating Hours</h3>
                    <div class="contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <div>
                            <strong>Daily Service</strong><br>
                            Check timetable for schedule
                        </div>
                    </div>
                </div>

                <!-- Book Now -->
                <div class="sidebar-card book-now-card">
                    <h3>Book Your Ticket</h3>
                    <p>Skip the queue! Book your ferry ticket online and travel hassle-free.</p>
                    <a href="{{ route('customer.login') }}" class="book-now-btn">Book Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Timetable & Rate Card -->
@if($route['timetable'] || $route['ratecard'])
<section class="schedule-section">
    <div class="container">
        <h2 class="section-title">Schedule & Rates</h2>
        <p class="section-subtitle">Check our timetable and fare information</p>

        <div class="schedule-grid">
            @if($route['timetable'])
            <div class="schedule-card">
                <div class="schedule-card-header">
                    <h3>Ferry Time Table</h3>
                </div>
                <div class="schedule-card-body">
                    <img src="{{ asset('images/carferry/timetables/' . $route['timetable']) }}" alt="{{ $route['name'] }} Timetable">
                </div>
            </div>
            @endif

            @if($route['ratecard'])
            <div class="schedule-card">
                <div class="schedule-card-header">
                    <h3>Ferry Rate Card</h3>
                </div>
                <div class="schedule-card-body">
                    <img src="{{ asset('images/carferry/ratecards/' . $route['ratecard']) }}" alt="{{ $route['name'] }} Rate Card">
                </div>
            </div>
            @endif
        </div>

        <p class="schedule-note">* Schedules may vary based on weather and tide conditions. Please call to confirm.</p>
    </div>
</section>
@endif

<!-- Other Routes -->
<section class="other-routes">
    <div class="container">
        <h2>Explore Other Routes</h2>
        <div class="other-routes-grid">
            @foreach($otherRoutes as $otherRoute)
                @if($otherRoute['slug'] !== $route['slug'])
                <a href="{{ url('/route/' . $otherRoute['slug']) }}" class="other-route-card">
                    <h4>{{ $otherRoute['name'] }}</h4>
                    <p>{{ Str::limit($otherRoute['tagline'], 60) }}</p>
                </a>
                @endif
            @endforeach
        </div>
    </div>
</section>

<!-- Contact Bar -->
<section class="contact-bar">
    <div class="container">
        <div class="contact-bar-grid">
            <div class="contact-bar-item">
                <h4>Need Help?</h4>
                <p><a href="tel:+919422431371">+91 9422431371</a></p>
            </div>
            <div class="contact-bar-item">
                <h4>Email Us</h4>
                <p><a href="mailto:ssmsdapoli@rediffmail.com">ssmsdapoli@rediffmail.com</a></p>
            </div>
            <div class="contact-bar-item">
                <h4>Operating Hours</h4>
                <p>9:00 AM - 5:00 PM (7 Days)</p>
            </div>
        </div>
    </div>
</section>
@endsection
