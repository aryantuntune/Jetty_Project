@extends('layouts.public')

@section('title', 'Suvarnadurga Shipping & Marine Services - Ferry Services in Maharashtra')
@section('description', 'Book ferry tickets online for Dabhol-Dhopave, Jaigad-Tawsal, Dighi-Agardande, Veshvi-Bagmandale, Vasai-Bhayander routes. Operating since 2003.')

@push('styles')
<style>
    .hero-video-section {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .hero-video-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    .hero-video-bg video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(58, 134, 255, 0.7) 0%, rgba(26, 26, 46, 0.8) 100%);
        z-index: 0;
    }

    .hero-video-content {
        position: relative;
        z-index: 1;
        text-align: center;
        color: #fff;
        padding: 20px;
        max-width: 800px;
    }

    .hero-video-content h1 {
        font-size: 56px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 20px;
        text-shadow: 2px 4px 10px rgba(0,0,0,0.3);
    }

    .hero-video-content p {
        font-size: 22px;
        margin-bottom: 30px;
        opacity: 0.95;
    }

    .hero-btn-group {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-hero-primary {
        display: inline-block;
        background: var(--secondary-orange);
        color: #fff;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 18px;
        padding: 18px 45px;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 30px rgba(251, 86, 7, 0.4);
    }

    .btn-hero-primary:hover {
        background: #e04d00;
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(251, 86, 7, 0.5);
    }

    .btn-hero-secondary {
        display: inline-block;
        background: transparent;
        color: #fff;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 16px;
        padding: 16px 40px;
        border-radius: 50px;
        border: 2px solid rgba(255,255,255,0.5);
        transition: all 0.3s ease;
    }

    .btn-hero-secondary:hover {
        background: rgba(255,255,255,0.1);
        border-color: #fff;
    }

    .scroll-indicator {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1;
        animation: bounce 2s infinite;
    }

    .scroll-indicator a {
        color: rgba(255,255,255,0.7);
        font-size: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .scroll-indicator a:hover {
        color: #fff;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
        40% { transform: translateX(-50%) translateY(-10px); }
        60% { transform: translateX(-50%) translateY(-5px); }
    }

    @media (max-width: 768px) {
        .hero-video-content h1 {
            font-size: 36px;
        }

        .hero-video-content p {
            font-size: 18px;
        }

        .btn-hero-primary {
            padding: 14px 35px;
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section with Video Background -->
<section class="hero-video-section">
    <div class="hero-video-bg">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
        </video>
    </div>
    <div class="hero-video-overlay"></div>

    <div class="hero-video-content">
        <h1>Ready to Begin Your Journey?</h1>
        <p>Experience seamless ferry travel across Maharashtra's beautiful Konkan coast</p>
        <div class="hero-btn-group">
            <a href="{{ route('customer.login') }}" class="btn-hero-primary">Book Your Ferry</a>
            <a href="#routes" class="btn-hero-secondary">View Routes</a>
        </div>
    </div>

    <div class="scroll-indicator">
        <a href="#routes">
            <span>Scroll to explore</span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </a>
    </div>
</section>

<!-- Ferry Routes Section -->
<section class="routes-section">
    <div class="container">
        <h2 class="section-title">Our Ferry Services</h2>
        <p class="section-subtitle">Connecting Maharashtra's beautiful Konkan coast with reliable ferry services since 2003</p>

        <div class="routes-grid">
            <!-- Dabhol - Dhopave -->
            <div class="route-card">
                <div class="route-card-image">
                    <img src="{{ asset('images/carferry/routes/dabhol-dhopave.jpg') }}" alt="Dabhol Dhopave Ferry">
                </div>
                <div class="route-card-content">
                    <h3 class="route-card-title">Dabhol – Dhopave</h3>
                    <p class="route-card-desc">
                        The very first site which was started on 21.10.2003 & constantly working at all times and in all seasons since its first day.
                    </p>
                    <a href="{{ url('/route/dabhol-dhopave') }}" class="route-card-link">
                        Know More
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Jaigad - Tawsal -->
            <div class="route-card">
                <div class="route-card-image">
                    <img src="{{ asset('images/carferry/routes/jaigad-tawsal.jpg') }}" alt="Jaigad Tawsal Ferry">
                </div>
                <div class="route-card-content">
                    <h3 class="route-card-title">Jaigad – Tawsal</h3>
                    <p class="route-card-desc">
                        This Ferry service was started for the easy & better transportation from Guhaghar to Ratnagiri region.
                    </p>
                    <a href="{{ url('/route/jaigad-tawsal') }}" class="route-card-link">
                        Know More
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Dighi - Agardande -->
            <div class="route-card">
                <div class="route-card-image">
                    <img src="{{ asset('images/carferry/routes/dighi-agardande.jpg') }}" alt="Dighi Agardande Ferry">
                </div>
                <div class="route-card-content">
                    <h3 class="route-card-title">Dighi – Agardande</h3>
                    <p class="route-card-desc">
                        Connecting to National Highway 17, this route provides easy access to destinations like Murud-Janjeera, Kashid beach, and Alibaug.
                    </p>
                    <a href="{{ url('/route/dighi-agardande') }}" class="route-card-link">
                        Know More
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Veshvi - Bagmandale -->
            <div class="route-card">
                <div class="route-card-image">
                    <img src="{{ asset('images/carferry/routes/veshvi-bagmandale.jpg') }}" alt="Veshvi Bagmandale Ferry">
                </div>
                <div class="route-card-content">
                    <h3 class="route-card-title">Veshvi – Bagmandale</h3>
                    <p class="route-card-desc">
                        Operating since 2007, this ferry made the journey from Raigad to Ratnagiri very easy and quick.
                    </p>
                    <a href="{{ url('/route/veshvi-bagmandale') }}" class="route-card-link">
                        Know More
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Vasai - Bhayander -->
            <div class="route-card">
                <div class="route-card-image">
                    <img src="{{ asset('images/carferry/routes/vasai-bhayander.jpg') }}" alt="Vasai Bhayander Ferry">
                </div>
                <div class="route-card-content">
                    <h3 class="route-card-title">Vasai – Bhayander</h3>
                    <p class="route-card-desc">
                        Our newest RORO service operating under the Sagarmala Project, connecting Vasai and Bhayander.
                    </p>
                    <a href="{{ url('/route/vasai-bhayander') }}" class="route-card-link">
                        Know More
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Ambet - Mahpral -->
            <div class="route-card">
                <div class="route-card-image">
                    <img src="{{ asset('images/carferry/routes/ambet-mahpral.jpg') }}" alt="Ambet Mahpral Ferry">
                </div>
                <div class="route-card-content">
                    <h3 class="route-card-title">Ambet – Mahpral</h3>
                    <p class="route-card-desc">
                        Connecting coastal communities with reliable ferry services for passengers and vehicles.
                    </p>
                    <a href="{{ url('/route/ambet-mahpral') }}" class="route-card-link">
                        Know More
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2 class="section-title">What We Offer</h2>
        <p class="section-subtitle">Comprehensive ferry services for passengers, vehicles, and cargo</p>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-card-image">
                    <img src="{{ asset('images/carferry/backgrounds/cruise-services.jpg') }}" alt="Passenger Services">
                </div>
                <div class="service-card-content">
                    <h3>Passenger Ferry Services</h3>
                    <p>Safe and comfortable ferry rides for passengers across all our routes. Travel with ease and enjoy the scenic Konkan coastline.</p>
                </div>
            </div>

            <div class="service-card">
                <div class="service-card-image">
                    <img src="{{ asset('images/carferry/backgrounds/inland-services.jpg') }}" alt="Vehicle Transport">
                </div>
                <div class="service-card-content">
                    <h3>Vehicle Transportation</h3>
                    <p>Transport your cars, bikes, and commercial vehicles safely. Our RORO ferries can accommodate all types of vehicles.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Preview Section -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div class="about-image">
                <img src="{{ asset('images/carferry/misc/team-photo.jpg') }}" alt="Our Leadership">
            </div>
            <div class="about-content">
                <h2>About Suvarnadurga Shipping</h2>
                <p>
                    Suvarnadurga Shipping & Marine Services Pvt. Ltd. was established in October 2003 by Dr. Mokal C.J. (former MLA of Dapoli-Mandangad), with Dr. Mokal Y.C. serving as Managing Director.
                </p>
                <p>
                    Our first venture was the Dabhol-Dhopave ferry service - the first Ferry Boat Service in Maharashtra. Since then, we have expanded to operate 7 routes across the Konkan coast, serving thousands of passengers daily.
                </p>
                <p>
                    With approximately 65 employees and a commitment to safety and reliability, we continue to connect coastal communities and boost tourism in the region.
                </p>
                <a href="{{ url('/about') }}" class="btn-secondary" style="margin-top: 20px;">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Bar -->
<section class="contact-bar">
    <div class="container">
        <div class="contact-bar-grid">
            <div class="contact-bar-item">
                <h4>Dabhol Office</h4>
                <p><a href="tel:02348248900">02348-248900</a></p>
                <p><a href="tel:+919767248900">+91 9767248900</a></p>
            </div>
            <div class="contact-bar-item">
                <h4>Veshvi Office</h4>
                <p><a href="tel:02350223300">02350-223300</a></p>
                <p><a href="tel:+918767980300">+91 8767980300</a></p>
            </div>
            <div class="contact-bar-item">
                <h4>Operating Hours</h4>
                <p>9:00 AM - 5:00 PM</p>
                <p>Open 7 Days a Week</p>
            </div>
        </div>
    </div>
</section>
@endsection
