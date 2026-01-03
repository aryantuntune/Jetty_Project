@extends('layouts.public')

@section('title', 'About Us - Suvarnadurga Shipping & Marine Services')
@section('description', 'Learn about Suvarnadurga Shipping & Marine Services - Maharashtra\'s first ferry boat service provider since 2003.')

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

    .about-main {
        padding: 80px 0;
    }

    .about-intro {
        max-width: 900px;
        margin: 0 auto 60px;
        text-align: center;
    }

    .about-intro h2 {
        font-size: 32px;
        color: var(--primary-blue);
        margin-bottom: 20px;
    }

    .about-intro p {
        font-size: 16px;
        line-height: 1.9;
    }

    .founder-section {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 60px;
        align-items: center;
        margin-bottom: 60px;
    }

    .founder-image img {
        border-radius: 15px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
    }

    .founder-content h3 {
        font-size: 28px;
        color: var(--primary-blue);
        margin-bottom: 20px;
    }

    .founder-content p {
        font-size: 15px;
        line-height: 1.9;
        margin-bottom: 15px;
    }

    .stats-section {
        background: var(--light-blue-bg);
        padding: 60px 0;
        margin: 60px 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        text-align: center;
    }

    .stat-item {
        padding: 30px;
    }

    .stat-number {
        font-family: 'Montserrat', sans-serif;
        font-size: 48px;
        font-weight: 800;
        color: var(--primary-blue);
        margin-bottom: 10px;
    }

    .stat-label {
        font-size: 14px;
        color: var(--text-dark);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .routes-operated {
        padding: 60px 0;
    }

    .routes-operated h2 {
        text-align: center;
        font-size: 32px;
        color: var(--primary-blue);
        margin-bottom: 40px;
    }

    .routes-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .route-item {
        background: var(--white);
        border: 2px solid #eee;
        border-radius: 10px;
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .route-item:hover {
        border-color: var(--primary-blue);
        box-shadow: 0 5px 20px rgba(58, 134, 255, 0.1);
    }

    .route-icon {
        width: 50px;
        height: 50px;
        background: var(--light-blue-bg);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .route-icon svg {
        color: var(--primary-blue);
    }

    .route-item h4 {
        font-size: 16px;
        margin: 0;
    }

    .compliance-section {
        background: var(--white);
        padding: 60px 0;
    }

    .compliance-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }

    .compliance-card {
        text-align: center;
        padding: 40px 30px;
        background: var(--light-blue-bg);
        border-radius: 15px;
    }

    .compliance-icon {
        width: 70px;
        height: 70px;
        background: var(--primary-blue);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .compliance-icon svg {
        color: var(--white);
        width: 32px;
        height: 32px;
    }

    .compliance-card h4 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .compliance-card p {
        font-size: 14px;
        line-height: 1.7;
    }

    @media (max-width: 992px) {
        .founder-section {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .compliance-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .page-hero h1 {
            font-size: 32px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-number {
            font-size: 36px;
        }
    }
</style>
@endpush

@section('content')
<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1>About Us</h1>
        <p>Maharashtra's First Ferry Boat Service Since 2003</p>
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Home</a>
            <span>/</span>
            <span>About Us</span>
        </div>
    </div>
</section>

<!-- Main About Content -->
<section class="about-main">
    <div class="container">
        <div class="about-intro">
            <h2>Suvarnadurga Shipping & Marine Services Pvt. Ltd.</h2>
            <p>
                We are a transportation company focused on ferry services across Maharashtra's coastal regions. Our organization emphasizes fuel efficiency and serves both the public and tourism sectors. Since our inception in 2003, we have been committed to providing safe, reliable, and affordable ferry services to connect the beautiful Konkan coast.
            </p>
        </div>

        <div class="founder-section">
            <div class="founder-image">
                <img src="{{ asset('images/carferry/misc/team-photo.jpg') }}" alt="Our Leadership Team">
            </div>
            <div class="founder-content">
                <h3>Our Story</h3>
                <p>
                    The company was established in October 2003 by <strong>Dr. Mokal C.J.</strong> (former MLA of Dapoli-Mandangad), with <strong>Dr. Mokal Y.C.</strong> serving as Managing Director.
                </p>
                <p>
                    Our first venture was the Dabhol-Dhopave ferry service, described as "a first Ferry Boat Service in Maharashtra," eliminating the need for expensive highway travel. This pioneering service opened up new possibilities for coastal transportation and tourism.
                </p>
                <p>
                    Since then, we have expanded to operate seven ferry routes across the Konkan coast, connecting communities, supporting local businesses, and promoting tourism in the region. Our ferries serve thousands of passengers daily, providing a vital link between coastal towns.
                </p>
                <p>
                    With approximately <strong>65 employees</strong> across different locations, we continue to grow while maintaining our commitment to safety, reliability, and customer service.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">20+</div>
                <div class="stat-label">Years of Service</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">7</div>
                <div class="stat-label">Ferry Routes</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">65+</div>
                <div class="stat-label">Employees</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">1M+</div>
                <div class="stat-label">Passengers Served</div>
            </div>
        </div>
    </div>
</section>

<!-- Routes Operated -->
<section class="routes-operated">
    <div class="container">
        <h2>Ferry Routes We Operate</h2>
        <div class="routes-list">
            <a href="{{ url('/route/dabhol-dhopave') }}" class="route-item">
                <div class="route-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 16l-4-4 4-4"/>
                        <path d="M6 12h12"/>
                    </svg>
                </div>
                <h4>Dabhol – Dhopave</h4>
            </a>

            <a href="{{ url('/route/jaigad-tawsal') }}" class="route-item">
                <div class="route-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 16l-4-4 4-4"/>
                        <path d="M6 12h12"/>
                    </svg>
                </div>
                <h4>Jaigad – Tawsal</h4>
            </a>

            <a href="{{ url('/route/dighi-agardande') }}" class="route-item">
                <div class="route-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 16l-4-4 4-4"/>
                        <path d="M6 12h12"/>
                    </svg>
                </div>
                <h4>Dighi – Agardande</h4>
            </a>

            <a href="{{ url('/route/veshvi-bagmandale') }}" class="route-item">
                <div class="route-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 16l-4-4 4-4"/>
                        <path d="M6 12h12"/>
                    </svg>
                </div>
                <h4>Veshvi – Bagmandale</h4>
            </a>

            <a href="{{ url('/route/vasai-bhayander') }}" class="route-item">
                <div class="route-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 16l-4-4 4-4"/>
                        <path d="M6 12h12"/>
                    </svg>
                </div>
                <h4>Vasai – Bhayander</h4>
            </a>

            <a href="{{ url('/route/virar-saphale') }}" class="route-item">
                <div class="route-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 16l-4-4 4-4"/>
                        <path d="M6 12h12"/>
                    </svg>
                </div>
                <h4>Virar – Saphale (Jalsar)</h4>
            </a>

            <a href="{{ url('/route/ambet-mahpral') }}" class="route-item">
                <div class="route-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 16l-4-4 4-4"/>
                        <path d="M6 12h12"/>
                    </svg>
                </div>
                <h4>Ambet – Mahpral</h4>
            </a>
        </div>
    </div>
</section>

<!-- Compliance Section -->
<section class="compliance-section">
    <div class="container">
        <h2 class="section-title">Our Commitment</h2>
        <p class="section-subtitle">Safety, compliance, and customer satisfaction are our top priorities</p>

        <div class="compliance-grid">
            <div class="compliance-card">
                <div class="compliance-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h4>Safety First</h4>
                <p>All our vessels are equipped with life-saving equipment and undergo annual inspections. Your safety is our priority.</p>
            </div>

            <div class="compliance-card">
                <div class="compliance-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <h4>Government Approved</h4>
                <p>All ticket rates and permits are approved by the Maharashtra Maritime Board. We pay approximately ₹4,00,000 annually in levies per ferry boat.</p>
            </div>

            <div class="compliance-card">
                <div class="compliance-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <h4>Reliable Service</h4>
                <p>Operating 7 days a week, in all seasons. Our ferries have been running continuously since 2003 with minimal disruptions.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Bar -->
<section class="contact-bar">
    <div class="container">
        <div class="contact-bar-grid">
            <div class="contact-bar-item">
                <h4>Head Office</h4>
                <p>Dabhol FerryBoat Jetty, Dapoli</p>
                <p>Dist. Ratnagiri, Maharashtra - 415712</p>
            </div>
            <div class="contact-bar-item">
                <h4>Contact Numbers</h4>
                <p><a href="tel:02348248900">02348-248900</a></p>
                <p><a href="tel:+919767248900">+91 9767248900</a></p>
            </div>
            <div class="contact-bar-item">
                <h4>Email Us</h4>
                <p><a href="mailto:ssmsdapoli@rediffmail.com">ssmsdapoli@rediffmail.com</a></p>
                <p><a href="mailto:y.mokal@rediffmail.com">y.mokal@rediffmail.com</a></p>
            </div>
        </div>
    </div>
</section>
@endsection
