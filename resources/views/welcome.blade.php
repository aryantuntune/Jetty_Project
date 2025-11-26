<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ferry Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --brand:#3087FF;
      --cta1:#FFD700;
      --cta2:#FF8C00;
    }
    html,body{height:100%;}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#000;}

    /* Nav */
    .navbar{
      background: rgba(0,0,0,.35);
      backdrop-filter: blur(6px);
    }
    .navbar .nav-link{
      color:#fff; font-weight:600;
    }
    .navbar .nav-link:hover{ color:var(--cta1); }
    .navbar-brand img{ height:38px; }

    /* Hero */
    .hero{
      min-height:100vh;
      position:relative;
      display:flex;align-items:center;
      color:#fff;
      background:
        linear-gradient( to bottom right, rgba(0,0,0,.55), rgba(0,0,0,.35) ),
        url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=2000&q=80') center/cover no-repeat fixed;
    }
    .hero h1{
      font-weight:800; line-height:1.1; letter-spacing:.3px;
      text-shadow:0 6px 24px rgba(0,0,0,.5);
      font-size: clamp(2rem, 4.5vw, 4rem);
    }
    .btn-booking{
      background: linear-gradient(45deg, var(--cta1), var(--cta2));
      border:none; color:#111; font-weight:800;
      padding:14px 32px; border-radius:40px; font-size:1.15rem;
      box-shadow:0 12px 30px rgba(0,0,0,.35);
      transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn-booking:hover{ transform: translateY(-2px) scale(1.03); filter:saturate(1.1); }
    .btn-outline-light:hover{ color:#111; background:#fff; }

    /* Footer note (optional) */
    .mini-note{ opacity:.7; font-size:.9rem; }
  </style>
</head>
<body>

  <!-- Minimal Top Nav: Home + Login/Logout -->
  <nav class="navbar navbar-expand-lg fixed-top"> 
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/ticket-entry') }}">
        <!-- <img src="https://carferry.in/wp-content/uploads/2021/03/LOGO-White-224x150-1.png" alt="Logo"> -->
     
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMini">
        <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navMini">
        <ul class="navbar-nav align-items-lg-center gap-lg-3">
        <!--
          <li class="nav-item">
            <a class="nav-link" href="{{url('/ticket-entry') }}">Home</a>
          </li>

          -->
<li class="nav-item">
    <a class="nav-link" href="{{ route('customer.login') }}">Customer Login</a>
</li>

          @guest
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">Admin Login</a>
            </li>
          @endguest

          @auth
            <li class="nav-item">
              <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-outline-light btn-sm">Logout</button>
              </form>
            </li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-12 col-lg-7">
          <h1>Ready to<br>Begin Your<br>Journey?</h1>
          <p class="lead mt-3 mini-note">Fast. Safe. Scenic. Book your ferry in seconds.</p>

          {{-- <a href="{{ route('booking.form') }}" class="btn btn-booking mt-3"> --}}
               <a href="" class="btn btn-booking mt-3">
            Book Now
          </a>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
