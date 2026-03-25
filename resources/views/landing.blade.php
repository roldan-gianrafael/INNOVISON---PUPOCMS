<!doctype html>
<html lang="en">
<head>
<<<<<<< ours
<<<<<<< ours
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Online Clinic - Welcome</title>
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
=======
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PUP Online Clinic | Welcome</title>
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
>>>>>>> theirs
</head>
<body>
    <main class="landing-shell" aria-labelledby="welcomeTitle">
        <section class="landing-panel">
            <header class="landing-header">
                <div class="brand-wrap" aria-label="Polytechnic University of the Philippines Online Clinic branding">
                    <img src="{{ asset('images/pup_logo.png') }}" alt="PUP logo" class="brand-logo">
                    <img src="{{ asset('images/clinic_logo.png') }}" alt="Clinic logo" class="brand-logo clinic-logo">
                </div>
                <p class="brand-eyebrow">Polytechnic University of the Philippines</p>
                <h1 id="welcomeTitle">Online Clinic Portal</h1>
                <p class="brand-subtitle">Choose your portal to continue.</p>
            </header>

            <section class="role-grid" aria-label="Choose your portal type">
                <button class="role-card" data-target="{{ route('login') }}?role=student" aria-label="Continue as Student">
                    <span class="role-badge">Student</span>
                    <p class="role-description">Book consultations, manage appointments, and view your health profile.</p>
                    <span class="role-cta">Continue</span>
                </button>

                <button class="role-card" data-target="{{ route('login') }}?role=admin" aria-label="Continue as Admin">
                    <span class="role-badge">Admin</span>
                    <p class="role-description">Manage appointments, inventory, reports, and clinic operations.</p>
                    <span class="role-cta">Continue</span>
                </button>
            </section>
        </section>

        <footer class="landing-footer">&copy; <span id="year"></span> PUP Online Clinic</footer>
    </main>

<<<<<<< ours
    <footer class="lp-foot">&copy; <span id="year"></span> PUP Taguig - Online Clinic</footer>
  </main>

  <script src="{{ asset('scripts.js') }}" defer></script>
=======
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PUP Online Clinic | Welcome</title>
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body>
    <main class="landing-shell" aria-labelledby="welcomeTitle">
        <section class="landing-panel">
            <header class="landing-header">
                <div class="brand-wrap" aria-label="Polytechnic University of the Philippines Online Clinic branding">
                    <img src="{{ asset('images/pup_logo.png') }}" alt="PUP logo" class="brand-logo">
                    <img src="{{ asset('images/clinic_logo.png') }}" alt="Clinic logo" class="brand-logo clinic-logo">
                </div>
                <p class="brand-eyebrow">Polytechnic University of the Philippines</p>
                <h1 id="welcomeTitle">Online Clinic Portal</h1>
                <p class="brand-subtitle">Choose your portal to continue.</p>
            </header>

            <section class="role-grid" aria-label="Choose your portal type">
                <button class="role-card" data-target="{{ route('login') }}?role=student" aria-label="Continue as Student">
                    <span class="role-badge">Student</span>
                    <p class="role-description">Book consultations, manage appointments, and view your health profile.</p>
                    <span class="role-cta">Continue</span>
                </button>

                <button class="role-card" data-target="{{ route('login') }}?role=admin" aria-label="Continue as Admin">
                    <span class="role-badge">Admin</span>
                    <p class="role-description">Manage appointments, inventory, reports, and clinic operations.</p>
                    <span class="role-cta">Continue</span>
                </button>
            </section>
        </section>

        <footer class="landing-footer">&copy; <span id="year"></span> PUP Online Clinic</footer>
    </main>

    <script src="{{ asset('scripts.js') }}" defer></script>
>>>>>>> theirs
=======
    <script src="{{ asset('scripts.js') }}" defer></script>
>>>>>>> theirs
</body>
</html>
