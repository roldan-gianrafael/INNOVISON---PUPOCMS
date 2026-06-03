<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clinic Portal</title>
    <style>
        :root {
            --maroon: #7b1113;
            --maroon-deep: #4c0a0c;
            --gold: #facc15;
            --text: #f8fafc;
            --muted: rgba(248, 250, 252, 0.78);
            --line: rgba(255, 255, 255, 0.16);
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top, rgba(250, 204, 21, 0.14), transparent 34%),
                linear-gradient(135deg, rgba(26, 6, 7, 0.88), rgba(74, 9, 12, 0.78)),
                url('{{ asset('images/PUPBG.jpg') }}') center center / cover no-repeat fixed;
        }

        .shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 28px 18px;
        }

        .panel {
            width: min(920px, 100%);
            display: grid;
            gap: 24px;
            padding: 34px;
            border: 1px solid var(--line);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.10), rgba(255, 255, 255, 0.06));
            backdrop-filter: blur(14px);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.34);
        }

        .brand {
            display: grid;
            justify-items: center;
            text-align: center;
            gap: 16px;
        }

        .brand-logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border-radius: 22px;
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 8px;
        }

        .brand-logo--clinic {
            width: 160px;
            height: 160px;
            padding: 8px;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .eyebrow {
            margin: 0;
            color: var(--gold);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.24em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(34px, 5vw, 56px);
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .lead {
            margin: 0;
            max-width: 720px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .notice {
            width: min(640px, 100%);
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid rgba(250, 204, 21, 0.18);
            background: rgba(15, 23, 42, 0.22);
            color: #fff7d6;
            font-size: 14px;
            line-height: 1.6;
            text-align: center;
        }

        .actions {
            display: grid;
            place-items: center;
            padding-top: 6px;
        }

        .portal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 54px;
            padding: 0 26px;
            border-radius: 999px;
            border: 1px solid rgba(250, 204, 21, 0.26);
            background: linear-gradient(135deg, #facc15, #f59e0b);
            color: #70131b;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.02em;
            text-decoration: none;
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.22);
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .portal-btn:hover,
        .portal-btn:focus-visible {
            transform: translateY(-1px);
            background: linear-gradient(135deg, #fff2a8, #facc15);
            box-shadow: 0 22px 42px rgba(15, 23, 42, 0.28);
            outline: none;
        }

        .portal-btn svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
        }

        .footnote {
            margin: 0;
            text-align: center;
            color: rgba(248, 250, 252, 0.64);
            font-size: 12px;
            line-height: 1.6;
        }

        @media (max-width: 720px) {
            .panel {
                padding: 24px 18px;
                border-radius: 22px;
            }

            .brand-logo {
                width: 72px;
                height: 72px;
            }

            .brand-logo--clinic {
                width: 78px;
                height: 78px;
            }

            .portal-btn {
                width: 100%;
                max-width: 360px;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="panel" aria-label="Clinic portal landing">
            <div class="brand">
                <div class="brand-logos" aria-label="PUP and clinic logos">
                    <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo" class="brand-logo">
                    <img src="{{ asset('images/clinic_logo_transparent.png') }}?v={{ filemtime(public_path('images/clinic_logo_transparent.png')) }}" alt="Clinic Logo" class="brand-logo brand-logo--clinic">
                </div>
                <div>
                    <p class="eyebrow">Medical Clinic</p>
                    <h1>PUP Taguig Clinic</h1>
                </div>
                <p class="lead">
                    A unified clinic access point for students and staff through One Portal.
                </p>
                @if($errors->has('idp'))
                    <div class="notice">{{ $errors->first('idp') }}</div>
                @endif
            </div>

            <div class="actions">
                <a class="portal-btn" href="{{ route('login.portal') }}">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 12H4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 4v16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Login via One Portal
                </a>
            </div>

            <p class="footnote">
                Use your central Identity Provider account to continue.
            </p>
        </section>
    </main>
    @include('partials.system_footer')
</body>
</html>
