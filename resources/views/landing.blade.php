<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< ours
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PUP Taguig Online Clinic</title>
    <style>
        :root {
            --pup-maroon: #7b1113;
            --pup-maroon-deep: #5b0c0e;
            --pup-gold: #d4af37;
            --text-light: #fff8f3;
            --text-soft: rgba(255, 248, 243, 0.82);
            --line: rgba(255, 248, 243, 0.2);
            --glass: rgba(255, 248, 243, 0.08);
            --glass-strong: rgba(255, 248, 243, 0.12);
            --shadow: 0 22px 60px rgba(25, 8, 8, 0.32);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-light);
            background:
                linear-gradient(rgba(45, 10, 12, 0.7), rgba(28, 8, 8, 0.78)),
                url('{{ asset('images/PUPBG.jpg') }}') center center / cover no-repeat fixed;
        }

        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 24px 0;
        }

        .content {
            width: min(1100px, 100%);
            margin: auto;
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 42px;
        }

        .brand-logos {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .brand-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
            border-radius: 50%;
            background: transparent;
            padding: 0;
            border: none;
            box-shadow: none;
        }

        .brand-logo--clinic {
            width: 82px;
            height: 82px;
            mix-blend-mode: multiply;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--pup-gold);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
        }

        .brand h1 {
            margin: 0;
            font-size: clamp(34px, 5vw, 60px);
            line-height: 1.02;
            letter-spacing: -0.04em;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.22);
        }

        .brand p {
            margin: 14px 0 0;
            max-width: 640px;
            color: var(--text-soft);
            font-size: 16px;
            line-height: 1.7;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 28px;
        }

        .role-card {
            display: block;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            min-height: 280px;
            padding: 32px 30px;
            border-radius: 28px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, var(--glass-strong), var(--glass));
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
            transition: transform 0.22s ease, background 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        }

        .role-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(140deg, rgba(212, 175, 55, 0.12), transparent 52%);
            opacity: 0.9;
            pointer-events: none;
        }

        .role-card:hover,
        .role-card:focus-visible {
            transform: translateY(-6px);
            background: linear-gradient(180deg, rgba(123, 17, 19, 0.92), rgba(91, 12, 14, 0.94));
            border-color: rgba(212, 175, 55, 0.62);
            box-shadow: 0 28px 60px rgba(35, 7, 8, 0.42);
            outline: none;
        }

        .role-icon {
            width: 58px;
            height: 58px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: rgba(255, 248, 243, 0.16);
            border: 1px solid rgba(255, 248, 243, 0.18);
            color: var(--pup-gold);
            font-size: 24px;
            font-weight: 800;
            position: relative;
            z-index: 1;
        }

        .role-card h2 {
            margin: 26px 0 12px;
            font-size: clamp(30px, 4vw, 42px);
            letter-spacing: -0.04em;
            position: relative;
            z-index: 1;
        }

        .role-card p {
            margin: 0;
            max-width: 420px;
            color: var(--text-soft);
            font-size: 15px;
            line-height: 1.75;
            position: relative;
            z-index: 1;
        }

        .role-link {
            display: inline-block;
            margin-top: 26px;
            color: var(--pup-gold);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }

        .footer {
            background: rgba(17, 24, 39, 0.92);
            border-top: 2px solid var(--pup-maroon);
            text-align: center;
            padding: 14px 16px;
            font-size: 13px;
            color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            margin-top: auto;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer .sep {
            color: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 820px) {
            body {
                background-attachment: scroll;
            }

            .page {
                padding: 28px 16px 0;
            }

            .brand {
                margin-bottom: 30px;
            }

            .brand-logo {
                width: 58px;
                height: 58px;
            }

            .brand-logo--clinic {
                width: 70px;
                height: 70px;
            }

            .brand p {
                font-size: 15px;
            }

            .cards {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .role-card {
                min-height: auto;
                padding: 26px 22px;
                border-radius: 22px;
            }

            .role-card h2 {
                margin-top: 20px;
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="content" aria-label="PUP Taguig online clinic role selection">
            <header class="brand">
                <div class="brand-logos">
                    <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo" class="brand-logo">
                    <img src="{{ asset('images/clinic_logo.png') }}" alt="Clinic Logo" class="brand-logo brand-logo--clinic">
                </div>
                <p class="eyebrow">Polytechnic University of the Philippines</p>
                <h1>PUP Taguig Online Clinic</h1>
                <p>Choose the portal that matches your access level and continue into the clinic system.</p>
            </header>

            <div class="cards">
                <a href="{{ route('login') }}" class="role-card" aria-label="Open student portal">
                    <div class="role-icon">S</div>
                    <h2>Student</h2>
                    <p>Access appointments, health forms, account details, and the services built for enrolled students.</p>
                    <span class="role-link">Open Student Portal</span>
                </a>

                <a href="{{ route('login') }}" class="role-card" aria-label="Open admin portal">
                    <div class="role-icon">A</div>
                    <h2>Admin</h2>
                    <p>Manage appointments, monitor records, review clinic activity, and handle administrative workflows.</p>
                    <span class="role-link">Open Admin Portal</span>
                </a>
            </div>

        </section>

        <footer class="footer">
            <span>&copy; 1998-{{ now()->year }} <strong>Polytechnic University of the Philippines</strong></span>
            <span class="sep">|</span>
            <a href="https://www.pup.edu.ph/terms/" target="_blank" rel="noopener noreferrer">Terms of Use</a>
            <span class="sep">|</span>
            <a href="https://www.pup.edu.ph/privacy/" target="_blank" rel="noopener noreferrer">Privacy Statement</a>
        </footer>
    </main>
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
</body>
</html>
