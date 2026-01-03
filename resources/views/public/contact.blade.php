@extends('layouts.public')

@section('title', 'Contact Us - Suvarnadurga Shipping & Marine Services')
@section('description', 'Contact Suvarnadurga Shipping & Marine Services for ferry bookings and inquiries. Call us at +91 9422431371.')

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

    .contact-section {
        padding: 80px 0;
    }

    .contact-intro {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 60px;
    }

    .contact-intro h2 {
        font-size: 32px;
        color: var(--primary-blue);
        margin-bottom: 15px;
    }

    .contact-intro p {
        font-size: 16px;
        line-height: 1.8;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
    }

    /* Contact Form */
    .contact-form-wrapper {
        background: var(--white);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .contact-form-wrapper h3 {
        font-size: 24px;
        color: var(--text-heading);
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: var(--text-heading);
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #eee;
        border-radius: 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 15px;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-blue);
    }

    .form-group textarea {
        min-height: 150px;
        resize: vertical;
    }

    .captcha-group {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }

    .captcha-question {
        background: var(--light-blue-bg);
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        color: var(--primary-blue);
    }

    .captcha-input {
        width: 80px !important;
        text-align: center;
    }

    .submit-btn {
        width: 100%;
        padding: 16px;
        background: var(--secondary-orange);
        color: var(--white);
        border: none;
        border-radius: 10px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        background: #e04d00;
        transform: translateY(-2px);
    }

    /* Contact Info */
    .contact-info-wrapper {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .contact-card {
        background: var(--light-blue-bg);
        padding: 30px;
        border-radius: 15px;
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }

    .contact-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-blue);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .contact-icon svg {
        width: 28px;
        height: 28px;
        color: var(--white);
    }

    .contact-details h4 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .contact-details p {
        font-size: 14px;
        line-height: 1.8;
        margin-bottom: 5px;
    }

    .contact-details a {
        color: var(--primary-blue);
        font-weight: 500;
    }

    .contact-details a:hover {
        color: var(--secondary-orange);
    }

    /* Office Contacts */
    .office-contacts {
        padding: 60px 0;
        background: var(--white);
    }

    .office-contacts h2 {
        text-align: center;
        font-size: 32px;
        color: var(--primary-blue);
        margin-bottom: 40px;
    }

    .offices-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .office-card {
        background: var(--light-blue-bg);
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .office-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transform: translateY(-5px);
    }

    .office-card h4 {
        font-size: 18px;
        color: var(--primary-blue);
        margin-bottom: 15px;
    }

    .office-card p {
        font-size: 14px;
        margin-bottom: 8px;
    }

    .office-card a {
        color: var(--text-dark);
        font-weight: 500;
    }

    .office-card a:hover {
        color: var(--secondary-orange);
    }

    /* Map Section */
    .map-section {
        padding: 60px 0;
        background: var(--light-blue-bg);
    }

    .map-section h2 {
        text-align: center;
        font-size: 32px;
        color: var(--primary-blue);
        margin-bottom: 40px;
    }

    .map-container {
        max-width: 900px;
        margin: 0 auto;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }

    .map-container img {
        width: 100%;
        display: block;
    }

    /* Success/Error Messages */
    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 14px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 992px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .page-hero h1 {
            font-size: 32px;
        }

        .contact-form-wrapper {
            padding: 30px 20px;
        }

        .contact-card {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We value your opinion. Please give your feedback.</p>
        <div class="breadcrumb">
            <a href="{{ url('/') }}">Home</a>
            <span>/</span>
            <span>Contact Us</span>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-intro">
            <h2>Get In Touch</h2>
            <p>Have questions about our ferry services? Need help with a booking? We're here to help. Fill out the form below or contact us directly.</p>
        </div>

        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <h3>Send Us a Message</h3>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        Please correct the errors below and try again.
                    </div>
                @endif

                <form action="{{ url('/contact') }}" method="POST" id="contactForm">
                    @csrf
                    <div class="form-group">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter your full name">
                        @error('name')
                            <span style="color: red; font-size: 12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email address">
                        @error('email')
                            <span style="color: red; font-size: 12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number">
                        @error('phone')
                            <span style="color: red; font-size: 12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="message">Your Message *</label>
                        <textarea id="message" name="message" required placeholder="How can we help you?">{{ old('message') }}</textarea>
                        @error('message')
                            <span style="color: red; font-size: 12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="captcha-group">
                        <span class="captcha-question" id="captchaQuestion">5 + 3 = ?</span>
                        <input type="text" class="captcha-input" id="captcha" name="captcha" required placeholder="?">
                        <input type="hidden" id="captchaAnswer" name="captcha_answer" value="8">
                    </div>

                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="contact-info-wrapper">
                <div class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <h4>Head Office Address</h4>
                        <p>Dabhol FerryBoat Jetty,</p>
                        <p>Dapoli, Dist. Ratnagiri,</p>
                        <p>Maharashtra - 415712</p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <h4>Phone Numbers</h4>
                        <p>Dabhol: <a href="tel:02348248900">02348-248900</a>, <a href="tel:+919767248900">9767248900</a></p>
                        <p>Veshvi: <a href="tel:02350223300">02350-223300</a>, <a href="tel:+918767980300">8767980300</a></p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <h4>Email Addresses</h4>
                        <p><a href="mailto:ssmsdapoli@rediffmail.com">ssmsdapoli@rediffmail.com</a></p>
                        <p><a href="mailto:y.mokal@rediffmail.com">y.mokal@rediffmail.com</a></p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="contact-details">
                        <h4>Operating Hours</h4>
                        <p>Monday - Sunday: 9:00 AM - 5:00 PM</p>
                        <p>Open all 7 days of the week</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Office Contacts -->
<section class="office-contacts">
    <div class="container">
        <h2>Route-wise Contact Numbers</h2>
        <div class="offices-grid">
            <div class="office-card">
                <h4>Dabhol – Dhopave</h4>
                <p>Dabhol: <a href="tel:02348248900">02348-248900</a></p>
                <p>Mobile: <a href="tel:+919767248900">9767248900</a></p>
            </div>

            <div class="office-card">
                <h4>Jaigad – Tawsal</h4>
                <p>Jaigad: <a href="tel:02354242500">02354-242500</a></p>
                <p>Mobile: <a href="tel:+918550999884">8550999884</a></p>
            </div>

            <div class="office-card">
                <h4>Dighi – Agardande</h4>
                <p>Dighi: <a href="tel:+919156546700">9156546700</a></p>
                <p>Agardande: <a href="tel:+918550999887">8550999887</a></p>
            </div>

            <div class="office-card">
                <h4>Veshvi – Bagmandale</h4>
                <p>Veshvi: <a href="tel:02350223300">02350-223300</a></p>
                <p>Bagmandale: <a href="tel:+919322819161">9322819161</a></p>
            </div>

            <div class="office-card">
                <h4>Vasai – Bhayander</h4>
                <p>Contact 1: <a href="tel:+918624063900">8624063900</a></p>
                <p>Contact 2: <a href="tel:+918600314710">8600314710</a></p>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <h2>Our Location</h2>
        <div class="map-container">
            <img src="{{ asset('images/carferry/misc/map.jpg') }}" alt="Ferry Service Locations Map">
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Generate random captcha
    function generateCaptcha() {
        const num1 = Math.floor(Math.random() * 10);
        const num2 = Math.floor(Math.random() * 10);
        const answer = num1 + num2;

        document.getElementById('captchaQuestion').textContent = num1 + ' + ' + num2 + ' = ?';
        document.getElementById('captchaAnswer').value = answer;
    }

    // Generate captcha on page load
    generateCaptcha();

    // Simple form validation
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        const captchaInput = document.getElementById('captcha').value;
        const captchaAnswer = document.getElementById('captchaAnswer').value;

        if (captchaInput !== captchaAnswer) {
            e.preventDefault();
            alert('Please enter the correct answer to the math question.');
            generateCaptcha();
        }
    });
</script>
@endpush
