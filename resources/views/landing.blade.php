<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PUP Taguig Medical Clinic</title>
    <style>
        :root {
            --maroon: #70131B;
            --maroon-strong: #8f2230;
            --maroon-deep: #33080d;
            --gold: #facc15;
            --gold-soft: #fff1a8;
            --white: #ffffff;
            --ink: #1f2937;
            --muted: rgba(255, 255, 255, 0.78);
            --line: rgba(255, 255, 255, 0.18);
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
            color: var(--white);
            background:
                linear-gradient(135deg, rgba(51, 8, 13, 0.92), rgba(112, 19, 27, 0.78)),
                url('{{ asset('images/PUPBG.jpg') }}') center center / cover no-repeat fixed;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 18% 18%, rgba(250, 204, 21, 0.20), transparent 28%),
                radial-gradient(circle at 88% 12%, rgba(255, 255, 255, 0.12), transparent 24%),
                linear-gradient(180deg, rgba(15, 23, 42, 0.08), rgba(15, 23, 42, 0.32));
            pointer-events: none;
        }

        a {
            color: inherit;
        }

        .landing-shell {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(20px, 4vw, 48px) 18px;
        }

        .landing-panel {
            width: min(1120px, 100%);
            min-height: min(690px, calc(100vh - 72px));
            display: grid;
            grid-template-columns: minmax(0, 1.12fr) minmax(360px, 0.88fr);
            border: 1px solid var(--line);
            border-radius: 30px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.38);
        }

        .info-column {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 34px;
            padding: clamp(30px, 5vw, 58px);
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.045));
        }

        .info-column::after {
            content: "";
            position: absolute;
            right: 0;
            top: 34px;
            bottom: 34px;
            width: 1px;
            background: linear-gradient(180deg, transparent, rgba(250, 204, 21, 0.34), transparent);
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            border-radius: 18px;
            border: 1px solid rgba(250, 204, 21, 0.24);
            background: rgba(255, 255, 255, 0.14);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
        }

        .brand-badge img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .brand-meta {
            display: grid;
            gap: 3px;
        }

        .brand-kicker {
            margin: 0;
            color: var(--gold);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .brand-name {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.92);
        }

        .hero-copy {
            max-width: 650px;
        }

        .hero-copy h1 {
            margin: 0;
            max-width: 620px;
            font-size: clamp(42px, 6vw, 68px);
            line-height: 0.98;
            font-weight: 950;
            letter-spacing: 0;
            color: #ffffff;
        }

        .hero-copy p {
            margin: 18px 0 0;
            max-width: 560px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.75;
        }

        .trust-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 58px;
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(15, 23, 42, 0.18);
        }

        .trust-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--maroon);
            background: linear-gradient(135deg, var(--gold), var(--gold-soft));
            flex: 0 0 auto;
        }

        .trust-icon svg {
            width: 17px;
            height: 17px;
            stroke-width: 2.3;
        }

        .trust-text {
            display: grid;
            gap: 2px;
            min-width: 0;
        }

        .trust-title {
            font-size: 13px;
            font-weight: 900;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .trust-copy {
            font-size: 11px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.62);
        }

        .login-column {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(24px, 4vw, 44px);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 230, 0.94));
            color: var(--ink);
        }

        .login-card {
            width: min(390px, 100%);
            display: grid;
            gap: 22px;
        }

        .logo-stack {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .logo-frame {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 94px;
            height: 94px;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid rgba(112, 19, 27, 0.10);
            box-shadow: 0 16px 32px rgba(112, 19, 27, 0.10);
        }

        .logo-frame img {
            width: 76px;
            height: 76px;
            object-fit: contain;
        }

        .logo-frame--clinic {
            width: 126px;
            height: 126px;
            border-color: rgba(250, 204, 21, 0.34);
        }

        .logo-frame--clinic img {
            width: 108px;
            height: 108px;
        }

        .login-copy {
            display: grid;
            gap: 10px;
            text-align: center;
        }

        .login-copy h2 {
            margin: 0;
            color: var(--maroon);
            font-size: 28px;
            line-height: 1.08;
            font-weight: 950;
            letter-spacing: 0;
        }

        .login-copy p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        .portal-btn {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 58px;
            width: 100%;
            padding: 0 24px;
            border-radius: 999px;
            border: 1px solid var(--maroon);
            background: linear-gradient(135deg, var(--maroon), var(--maroon-strong));
            color: #ffffff;
            font-size: 15px;
            font-weight: 950;
            letter-spacing: 0.01em;
            text-decoration: none;
            box-shadow:
                0 0 0 4px rgba(112, 19, 27, 0.10),
                0 18px 34px rgba(112, 19, 27, 0.22);
            transition: color .12s ease, transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .portal-btn::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg,
                    rgba(255, 248, 196, 0) 0%,
                    rgba(255, 239, 181, 0.16) 22%,
                    rgba(255, 239, 181, 0.58) 48%,
                    rgba(255, 239, 181, 0.16) 72%,
                    rgba(255, 248, 196, 0) 100%);
            transform: translateX(-135%);
            transition: transform 1.4s ease;
            pointer-events: none;
        }

        .portal-btn:hover,
        .portal-btn:focus-visible {
            transform: translateY(-1px);
            background: linear-gradient(135deg, var(--gold), var(--gold-soft));
            border-color: var(--gold);
            color: var(--maroon);
            box-shadow:
                0 0 0 4px rgba(250, 204, 21, 0.18),
                0 22px 42px rgba(112, 19, 27, 0.20);
            outline: none;
        }

        .portal-btn:hover::after,
        .portal-btn:focus-visible::after {
            transform: translateX(135%);
        }

        .portal-btn svg,
        .portal-btn span {
            position: relative;
            z-index: 1;
        }

        .portal-btn svg {
            width: 19px;
            height: 19px;
            flex: 0 0 auto;
            stroke-width: 2.2;
        }

        .access-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 14px;
            border-radius: 16px;
            border: 1px solid rgba(112, 19, 27, 0.10);
            background: rgba(112, 19, 27, 0.045);
            color: #64748b;
            font-size: 12px;
            line-height: 1.55;
        }

        .access-note svg {
            width: 18px;
            height: 18px;
            color: var(--maroon);
            flex: 0 0 auto;
            margin-top: 1px;
        }

        .notice {
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid rgba(220, 38, 38, 0.16);
            background: #fff1f2;
            color: #be123c;
            font-size: 13px;
            line-height: 1.55;
            text-align: center;
            font-weight: 700;
        }

        .system-foot {
            margin: 0;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.6;
        }

        @media (max-width: 920px) {
            .landing-shell {
                align-items: flex-start;
                padding: 18px;
            }

            .landing-panel {
                min-height: 0;
                grid-template-columns: 1fr;
                border-radius: 24px;
            }

            .info-column::after {
                top: auto;
                right: 24px;
                left: 24px;
                bottom: 0;
                width: auto;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(250, 204, 21, 0.34), transparent);
            }

            .hero-copy h1 {
                font-size: clamp(36px, 9vw, 54px);
            }

            .login-column {
                padding: 28px 20px 34px;
            }
        }

        @media (max-width: 640px) {
            .info-column {
                padding: 24px 18px;
            }

            .trust-grid {
                grid-template-columns: 1fr;
            }

            .logo-frame {
                width: 72px;
                height: 72px;
                border-radius: 18px;
            }

            .logo-frame img {
                width: 58px;
                height: 58px;
            }

            .logo-frame--clinic {
                width: 92px;
                height: 92px;
            }

            .logo-frame--clinic img {
                width: 78px;
                height: 78px;
            }
        }
    </style>
</head>
<body>
    <main class="landing-shell">
        <section class="landing-panel" aria-label="PUP medical clinic access">
            <div class="info-column">
                <div class="brand-row">
                    <div class="brand-badge">
                        <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo">
                    </div>
                    <div class="brand-meta">
                        <p class="brand-kicker">Medical Clinic</p>
                        <p class="brand-name">Polytechnic University of the Philippines Taguig</p>
                    </div>
                </div>

                <div class="hero-copy">
                    <h1>PUP Taguig Medical Clinic</h1>
                    <p>
                        A unified clinic access point for student health profiles, appointments,
                        clinic verification, and staff workflows through One Portal.
                    </p>
                </div>

                <div class="trust-grid" aria-label="Clinic system capabilities">
                    <div class="trust-item">
                        <span class="trust-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3l7 4v5c0 4.5-2.9 7.4-7 9-4.1-1.6-7-4.5-7-9V7l7-4z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12l2 2 4-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="trust-text">
                            <span class="trust-title">One Portal Integrated</span>
                            <span class="trust-copy">Central IdP access</span>
                        </span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 4h10l2 2v16H5V6l2-2z" stroke="currentColor" stroke-linejoin="round"/>
                                <path d="M8 10h8M8 14h8M8 18h5" stroke="currentColor" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="trust-text">
                            <span class="trust-title">Clinic Records</span>
                            <span class="trust-copy">Organized health data</span>
                        </span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 3v3M17 3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v14H4V7a2 2 0 0 1 2-2z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 14h3M8 18h6" stroke="currentColor" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="trust-text">
                            <span class="trust-title">Appointments</span>
                            <span class="trust-copy">Student service flow</span>
                        </span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 21s7-4.4 7-11a4 4 0 0 0-7-2.6A4 4 0 0 0 5 10c0 6.6 7 11 7 11z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 13h2l1-2 2 4 1-2h2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="trust-text">
                            <span class="trust-title">Health Profiles</span>
                            <span class="trust-copy">Clinic verification ready</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="login-column">
                <div class="login-card">
                    <div class="logo-stack" aria-label="PUP and clinic logos">
                        <span class="logo-frame">
                            <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo">
                        </span>
                        <span class="logo-frame logo-frame--clinic">
                            <img src="{{ asset('images/clinic_logo_transparent.png') }}?v={{ filemtime(public_path('images/clinic_logo_transparent.png')) }}" alt="Clinic Logo">
                        </span>
                    </div>

                    <div class="login-copy">
                        <h2>Clinic Access</h2>
                        <p>Use your institutional One Portal account to continue to the student or clinic workspace.</p>
                    </div>

                    @if($errors->has('idp'))
                        <div class="notice">{{ $errors->first('idp') }}</div>
                    @endif

                    <a class="portal-btn" href="{{ route('login.portal') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15 12H4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 4v16" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Login via One Portal</span>
                    </a>

                    <div class="access-note">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3l7 4v5c0 4.5-2.9 7.4-7 9-4.1-1.6-7-4.5-7-9V7l7-4z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.5 12.2l1.7 1.7 3.8-4.1" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Access is routed through the central Identity Provider and redirects users based on their assigned role.</span>
                    </div>

                    <p class="system-foot">PUP Taguig Clinic Management System</p>
                </div>
            </div>
        </section>
    </main>
    @include('partials.system_footer')
</body>
</html>
