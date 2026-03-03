<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Online Clinic - Welcome</title>
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
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
      <p class="lp-sub">Quick access for Students and Faculty - book consultations and manage appointments.</p>
    </header>

    <section class="role-section" aria-label="Choose your role">
      <div class="role-grid">
        <button class="role-card" data-target="{{ route('login') }}?role=student" aria-label="Go to Student Login">
          <div class="card-icon" aria-hidden="true">
            <img src="{{ asset('images/students-icon.jpg') }}" alt="" class="card-icon-img">
          </div>
          <div class="card-body">
            <div class="card-title">Student</div>
            <div class="card-sub">Access your clinic dashboard and book appointments</div>
          </div>
        </button>

        <button class="role-card" data-target="{{ route('login') }}?role=faculty" aria-label="Go to Faculty/Admin Login">
          <div class="card-icon" aria-hidden="true">
            <img src="{{ asset('images/faculty-icon.webp') }}" alt="" class="card-icon-img">
          </div>
          <div class="card-body">
            <div class="card-title">Faculty</div>
            <div class="card-sub">Clinic administration and appointment management</div>
          </div>
        </button>
      </div>
    </section>

    <footer class="lp-foot">&copy; <span id="year"></span> PUP Taguig - Online Clinic</footer>
  </main>

  <script src="{{ asset('scripts.js') }}" defer></script>
</body>
</html>
