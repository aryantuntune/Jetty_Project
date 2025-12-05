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
            rgba(0,0,0,0.20),  /* lighter */
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
      transform: translateX(8px);   /* Left→Right transition */
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

<!-- 🔵 VIDEO BACKGROUND -->
<div class="video-bg">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
    </video>
</div>

<!-- 🔵 NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top"> 
    <div class="container">
      <a class="navbar-brand text-white" href="{{ url('/ticket-entry') }}">
        <!-- Add logo if required -->
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

<!-- 🔵 HERO WITH TITLE & BUTTON -->
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
