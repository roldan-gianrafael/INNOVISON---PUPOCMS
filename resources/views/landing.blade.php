<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Online Clinic - Welcome</title>
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
  <style>
    body {
        /* This loads the image and adds a dark overlay so text is readable */
        background: linear-gradient(rgba(45, 10, 15, 0.85), rgba(45, 10, 15, 0.95)), 
                    url("{{ asset('images/PUPBG.jpg') }}");
        
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        
        /* Ensure text stays white */
        color: white;
        margin: 0;
        font-family: sans-serif;
    }
    
    /* Optional: Add a subtle shadow to the cards to make them pop against the image */
    .role-card {
        background: rgba(255, 255, 255, 0.1); /* Glass effect */
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .role-card:hover {
        background: rgba(139, 0, 0, 0.9); /* Maroon on hover */
        transform: translateY(-5px);
    }
</style>
</head>
<body>
  <div class="logo-header">
    <div class="logo-brand">
      <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo" class="logo-icon">
      <div class="logo-text">
        <div class="logo-title">PUP TAGUIG</div>
        <div class="logo-subtitle">ONLINE CLINIC</div>
      </div>
    </div>
  </div>

  <main class="lp-container">
    <header class="lp-hero" role="banner" aria-labelledby="welcomeTitle">
      <h1 id="welcomeTitle">Welcome to the Online Clinic</h1>
      <p class="lp-sub">Quick access for Students and Faculty — book consultations and manage appointments.</p>
    </header>

    <section class="role-section" aria-label="Choose your role">
      <div class="role-grid">
        
        <a href="{{ url('/student/home') }}" class="role-card" aria-label="Go to Student Dashboard" style="text-decoration: none; color: inherit; display: block;">
          <div class="card-icon">🎓</div>
          <div class="card-body">
            <div class="card-title">Student</div>
            <div class="card-sub">Access your clinic dashboard, book appointments</div>
          </div>
        </a>

        <a href="{{ url('/admin/dashboard') }}" class="role-card" aria-label="Go to Faculty/Admin Dashboard" style="text-decoration: none; color: inherit; display: block;">
          <div class="card-icon">🩺</div>
          <div class="card-body">
            <div class="card-title">Faculty</div>
            <div class="card-sub">Clinic administration and appointment management</div>
          </div>
        </a>

      </div>
    </section>

    <footer class="lp-foot">© <span id="year">2026</span> PUP Taguig — Online Clinic</footer>
  </main>

  <script src="{{ asset('scripts.js') }}" defer></script>
</body>
</html>