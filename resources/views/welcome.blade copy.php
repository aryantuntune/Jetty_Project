<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        /* Header */
     .top-bar {
    background-color: #3087FF;
    color: white;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    padding: 10px 0;
   
}

/* Hover effect for Contact link */
.top-bar a:hover {
    color: #FFD700;
    text-decoration: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .top-bar {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}

        /* Navbar */
       .navbar-custom {
    display: flex;
    justify-content: center; /* Centers the links horizontally */
    gap: 30px; /* Space between links */
    padding: 15px 0;
    background-color: #f8f9fa; /* optional: light background */
}

.navbar-custom a {
    font-weight: 500;
    color: #000;
    text-decoration: none;
    transition: color 0.3s;
}

.navbar-custom a:hover {
    color: #3087FF;
}

        /* Hero Section */
  .hero {
    position: relative;
    height: 90vh;
    display: flex;
    align-items: center;
    justify-content: left;
    padding-left: 60px;
    overflow: hidden;
    background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover;
    color: white;
}

.hero-text h1 {
    font-size: 4rem;
    font-weight: bold;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.6); /* Makes text readable */
}
/* Hero Booking Button */
.btn-booking {
    background: linear-gradient(45deg, #FFD700, #FF8C00);
    color: #fff;
    font-weight: bold;
    padding: 15px 40px;
    font-size: 1.4rem;
    border-radius: 50px;
    text-decoration: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    display: inline-block;
    animation: bounce 2s infinite, grow 1s infinite alternate;
    transition: all 0.3s ease;
}

/* Hover effect */
.btn-booking:hover {
    transform: scale(1.2);
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
}

/* Bounce animation */
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-15px);
    }
    60% {
        transform: translateY(-7px);
    }
}

/* Grow animation */
@keyframes grow {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
    </style>
</head>
<body>

    <!-- Top bar -->
    <!-- Top Bar -->
<div class="top-bar d-flex align-items-center justify-content-between px-4 mx-auto mt-5" style="max-width: 1200px; border-radius: 15px 15px 15px 15px;">
    <!-- Contact Link -->
    <a href="contact-us" class="d-flex align-items-center gap-2 text-white fw-bold">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-chat-left-text" viewBox="0 0 16 16">
            <path d="M14 1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4.414A1 1 0 0 0 4 14.414L1.707 12.12a1 1 0 0 1-.293-.707V2a1 1 0 0 1 1-1h12zM14 0H2a2 2 0 0 0-2 2v9.586l3.293 3.293A1 1 0 0 0 4.414 15H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
            <path d="M3 3h10v1H3V3zm0 2h10v1H3V5zm0 2h7v1H3V7z"/>
        </svg>
        Contact Us
    </a>

    <!-- Logo -->
    <div class="logo">
        <img src="https://biltrax.nitsonline.in/images/logo.png" alt="Logo" height="40">
    </div>

    <!-- Ferry Services Button -->
    <a href="ferry-services" class="btn btn-light rounded-pill fw-bold">Ferry Services</a>
</div>

    <!-- Navbar -->
    <nav class="navbar-custom">
        <a href="#">HOME</a>
        <a href="#">FERRY SERVICES</a>
        <a href="#">ABOUT US</a>
        <a href="#">FERRY LOCATIONS</a>
        <a href="#">CONTACT US</a>
    </nav>

    <!-- Hero Section -->
  <section class="hero">
    <div class="hero-text">
        <h1>Ready to <br> Begin Your <br> Journey ?</h1>
          <!-- Booking Button -->
     <a href="{{ route('booking.form') }}" target="_blank" class="btn btn-booking mt-4 text-dark fw-bold">Book Now</a>

    </div>
</section>






</body>
</html>
