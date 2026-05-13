<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Role</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #111827;
            background:
                radial-gradient(circle at top right, rgba(250, 204, 21, 0.20), transparent 26%),
                radial-gradient(circle at bottom left, rgba(112, 19, 27, 0.10), transparent 30%),
                linear-gradient(135deg, #fffdf6 0%, #fff8fb 42%, #ffffff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }

        .chooser-shell {
            width: min(980px, 100%);
        }

        .chooser-card {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            padding: 34px 34px 38px;
            background: rgba(255, 255, 255, 0.90);
            border: 1px solid rgba(112, 19, 27, 0.10);
            box-shadow:
                0 24px 60px rgba(15, 23, 42, 0.12),
                0 0 0 1px rgba(255,255,255,0.72) inset;
        }

        .chooser-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 28px;
            right: 28px;
            height: 5px;
            border-radius: 999px;
            background: linear-gradient(90deg, #70131B 0%, #9f1239 48%, #facc15 100%);
        }

        .chooser-card::after {
            content: "";
            position: absolute;
            right: -70px;
            top: -80px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(112, 19, 27, 0.12) 0%, rgba(112, 19, 27, 0) 72%);
            pointer-events: none;
        }

        .chooser-hero-mark {
            position: absolute;
            top: -14px;
            right: 10px;
            width: 170px;
            height: 170px;
            color: rgba(112, 19, 27, 0.09);
            transform: rotate(-12deg);
            pointer-events: none;
        }

        .chooser-hero-mark svg {
            width: 100%;
            height: 100%;
            stroke-width: 1.7;
        }

        .chooser-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(112, 19, 27, 0.08);
            color: #70131B;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 12px;
        }

        .chooser-title {
            margin: 0;
            color: #70131B;
            font-size: clamp(32px, 5vw, 46px);
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .chooser-copy {
            margin: 12px 0 0;
            color: #475569;
            font-size: 16px;
            line-height: 1.7;
            max-width: 680px;
            position: relative;
            z-index: 1;
        }

        .chooser-kicker,
        .chooser-title {
            position: relative;
            z-index: 1;
        }

        .chooser-steps {
            position: relative;
            z-index: 1;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .chooser-step {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.84);
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #334155;
            font-size: 12px;
            font-weight: 800;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
        }

        .chooser-step-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #70131B;
            box-shadow: 0 0 0 4px rgba(112, 19, 27, 0.12);
        }

        .chooser-grid {
            margin-top: 34px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .chooser-link {
            text-decoration: none;
            color: inherit;
        }

        .chooser-option {
            position: relative;
            min-height: 280px;
            border-radius: 28px;
            padding: 28px 26px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            border: 1px solid rgba(112, 19, 27, 0.12);
            background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(255,249,247,0.96));
            box-shadow:
                0 18px 36px rgba(15, 23, 42, 0.10),
                0 0 0 1px rgba(255,255,255,0.52) inset;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .chooser-option::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg,
                    rgba(255, 248, 196, 0) 0%,
                    rgba(255, 239, 181, 0.16) 22%,
                    rgba(255, 239, 181, 0.56) 48%,
                    rgba(255, 239, 181, 0.16) 72%,
                    rgba(255, 248, 196, 0) 100%);
            transform: translateX(-135%);
            transition: transform 1.25s ease;
            pointer-events: none;
        }

        .chooser-option:hover {
            transform: translateY(-3px);
            border-color: #facc15;
            box-shadow:
                0 0 0 3px rgba(250, 204, 21, 0.16),
                0 24px 46px rgba(112, 19, 27, 0.14);
        }

        .chooser-option:hover::after {
            transform: translateX(135%);
        }

        .chooser-icon {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #70131B, #8f2230);
            color: #ffffff;
            box-shadow:
                0 16px 28px rgba(112, 19, 27, 0.22),
                0 0 0 1px rgba(255,255,255,0.18) inset;
            animation: floatCard 3.8s ease-in-out infinite;
        }

        .chooser-chip {
            position: absolute;
            top: 18px;
            right: 18px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 11px;
            border-radius: 999px;
            background: rgba(112, 19, 27, 0.08);
            color: #70131B;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .chooser-icon svg {
            width: 30px;
            height: 30px;
            stroke-width: 1.9;
        }

        .chooser-option:nth-child(2) .chooser-icon {
            background: linear-gradient(135deg, #8b0000, #c2410c);
        }

        .chooser-heading {
            margin: 0 0 10px;
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.03em;
        }

        .chooser-description {
            margin: 0;
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
            max-width: 360px;
        }

        .chooser-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 999px;
            background: rgba(112, 19, 27, 0.08);
            color: #70131B;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.02em;
            width: fit-content;
        }

        .chooser-option:nth-child(2) .chooser-chip {
            background: rgba(194, 65, 12, 0.10);
            color: #9a3412;
        }

        .chooser-cta svg {
            width: 16px;
            height: 16px;
            stroke-width: 2.1;
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @media (max-width: 760px) {
            .chooser-card {
                padding: 28px 22px 30px;
                border-radius: 24px;
            }

            .chooser-grid {
                grid-template-columns: 1fr;
            }

            .chooser-option {
                min-height: 230px;
            }

            .chooser-hero-mark {
                width: 128px;
                height: 128px;
                top: 6px;
                right: -4px;
            }
        }
    </style>
</head>
<body>
    <div class="chooser-shell">
        <section class="chooser-card">
            <div class="chooser-hero-mark" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75V6A2.25 2.25 0 0 0 15 3.75H4.5A2.25 2.25 0 0 0 2.25 6v12A2.25 2.25 0 0 0 4.5 20.25H15A2.25 2.25 0 0 0 17.25 18v-.75m0-10.5h3.375c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125H17.25m0-10.5v10.5m0-10.5h-7.5m7.5 10.5h-7.5" />
                </svg>
            </div>
            <div class="chooser-kicker">Student Assistant Portal</div>
            <h1 class="chooser-title">Choose your role</h1>
            <p class="chooser-copy">
                Welcome back, {{ $user->name }}. Select where you want to continue today. You can enter the student side for student-facing actions or go to the admin side for clinic operations.
            </p>
            <div class="chooser-steps">
                <span class="chooser-step"><span class="chooser-step-dot"></span>Student-facing access</span>
                <span class="chooser-step"><span class="chooser-step-dot"></span>Clinic operations access</span>
            </div>

            <div class="chooser-grid">
                <a href="{{ route('assistant.enter-student') }}" class="chooser-link">
                    <article class="chooser-option">
                        <span class="chooser-chip">Portal Choice</span>
                        <div class="chooser-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a8.97 8.97 0 0 0 3.74-.8 4.5 4.5 0 0 0-7.48-2.33m3.74 3.13a9.06 9.06 0 0 1-6 0m6 0a8.96 8.96 0 0 1-6 0m0 0a8.97 8.97 0 0 1-3.74-.8 4.5 4.5 0 0 1 7.48-2.33M12 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="chooser-heading">Student Side</h2>
                            <p class="chooser-description">
                                Continue into the student portal to view health record status, booking pages, and student-facing account screens.
                            </p>
                        </div>
                        <span class="chooser-cta">
                            Enter Student Side
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </span>
                    </article>
                </a>

                <a href="{{ route('assistant.enter-admin') }}" class="chooser-link">
                    <article class="chooser-option">
                        <span class="chooser-chip">Portal Choice</span>
                        <div class="chooser-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.983 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.072M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.552-.645-6.46-1.766l-.084-.049a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="chooser-heading">Admin Side</h2>
                            <p class="chooser-description">
                                Open the clinic operations dashboard to handle appointments, walk-ins, reports, and inventory workflows.
                            </p>
                        </div>
                        <span class="chooser-cta">
                            Enter Admin Side
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </span>
                    </article>
                </a>
            </div>
        </section>
    </div>
</body>
</html>
