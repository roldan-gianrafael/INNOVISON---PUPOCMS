<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Workspace | PUP Taguig Medical Clinic</title>
    <style>
        :root {
            --maroon: #70131b;
            --maroon-strong: #8f2230;
            --maroon-deep: #33080d;
            --gold: #facc15;
            --gold-soft: #fff1a8;
            --white: #ffffff;
            --ink: #1f2937;
            --muted: #64748b;
            --line: rgba(255, 255, 255, 0.18);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
        }

        body {
            overflow-x: hidden;
            background:
                linear-gradient(135deg, rgba(51, 8, 13, 0.92), rgba(112, 19, 27, 0.78)),
                url('{{ asset('images/PUPBG.jpg') }}') center / cover no-repeat fixed;
            color: var(--white);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body::before {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 18% 18%, rgba(250, 204, 21, 0.20), transparent 28%),
                radial-gradient(circle at 88% 12%, rgba(255, 255, 255, 0.12), transparent 24%),
                linear-gradient(180deg, rgba(15, 23, 42, 0.08), rgba(15, 23, 42, 0.32));
            content: "";
            pointer-events: none;
        }

        .workspace-shell {
            position: relative;
            z-index: 1;
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: clamp(24px, 5vw, 52px) 18px;
        }

        .workspace-panel {
            display: grid;
            grid-template-columns: minmax(0, 1.06fr) minmax(320px, 0.94fr);
            width: min(1040px, 100%);
            min-height: min(620px, calc(100vh - 72px));
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 28px 68px rgba(15, 23, 42, 0.34);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .workspace-info {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 30px;
            padding: clamp(28px, 4.4vw, 48px);
            border-right: 1px solid rgba(250, 204, 21, 0.14);
            background:
                linear-gradient(145deg, rgba(112, 19, 27, 0.68), rgba(77, 13, 23, 0.52)),
                rgba(112, 19, 27, 0.34);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
        }

        .workspace-info::after {
            position: absolute;
            top: 34px;
            right: 0;
            bottom: 34px;
            width: 1px;
            background: linear-gradient(180deg, transparent, rgba(250, 204, 21, 0.40), transparent);
            content: "";
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-badge {
            display: grid;
            width: 58px;
            height: 58px;
            flex: 0 0 auto;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
        }

        .brand-badge img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .brand-kicker,
        .brand-name {
            margin: 0;
            letter-spacing: 0;
        }

        .brand-kicker {
            margin-bottom: 4px;
            color: var(--gold);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .brand-name {
            max-width: 360px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.35;
        }

        .hero-copy h1 {
            max-width: 560px;
            margin: 0 0 18px;
            color: #ffffff;
            font-size: clamp(38px, 5vw, 60px);
            font-weight: 950;
            letter-spacing: 0;
            line-height: 1.02;
        }

        .hero-copy p {
            max-width: 570px;
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 16px;
            line-height: 1.7;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .feature-item {
            display: flex;
            min-height: 72px;
            align-items: center;
            gap: 12px;
            padding: 13px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
        }

        .feature-icon {
            display: inline-flex;
            width: 38px;
            height: 38px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(250, 204, 21, 0.16);
            color: var(--gold);
        }

        .feature-icon svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }

        .feature-text strong,
        .feature-text span {
            display: block;
        }

        .feature-text strong {
            margin-bottom: 3px;
            color: #ffffff;
            font-size: 13px;
        }

        .feature-text span {
            color: rgba(255, 255, 255, 0.68);
            font-size: 11px;
        }

        .workspace-choice {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(30px, 4vw, 48px);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 230, 0.95));
            color: var(--ink);
        }

        .choice-card {
            width: min(390px, 100%);
        }

        .logo-stack {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 24px;
        }

        .logo-frame {
            display: grid;
            width: 82px;
            height: 82px;
            place-items: center;
            border: 1px solid rgba(112, 19, 27, 0.14);
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(112, 19, 27, 0.10);
        }

        .logo-frame img {
            width: 66px;
            height: 66px;
            object-fit: contain;
        }

        .choice-copy {
            margin-bottom: 26px;
            text-align: center;
        }

        .choice-copy small {
            display: block;
            margin-bottom: 8px;
            color: var(--maroon);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .choice-copy h2 {
            margin: 0 0 10px;
            color: var(--maroon);
            font-size: 29px;
            font-weight: 950;
            letter-spacing: 0;
        }

        .choice-copy p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        .workspace-actions {
            display: grid;
            gap: 13px;
        }

        .workspace-button {
            position: relative;
            isolation: isolate;
            display: flex;
            min-height: 58px;
            width: 100%;
            overflow: hidden;
            align-items: center;
            justify-content: center;
            gap: 11px;
            padding: 0 24px;
            border: 1px solid var(--maroon);
            border-radius: 999px;
            background: var(--maroon);
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 900;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(112, 19, 27, 0.16);
            transition: color .24s ease, border-color .24s ease, box-shadow .24s ease, transform .24s ease;
        }

        .workspace-button::before {
            position: absolute;
            z-index: -1;
            inset: 0;
            background: var(--gold);
            content: "";
            transform: translateX(-102%);
            transition: transform .32s ease;
        }

        .workspace-button:hover,
        .workspace-button:focus-visible {
            border-color: var(--gold);
            color: var(--maroon);
            box-shadow: 0 16px 30px rgba(112, 19, 27, 0.20);
            outline: none;
            transform: translateY(-1px);
        }

        .workspace-button:hover::before,
        .workspace-button:focus-visible::before {
            transform: translateX(0);
        }

        .workspace-button svg {
            width: 20px;
            height: 20px;
            flex: 0 0 auto;
            stroke-width: 2.2;
        }

        .account-note {
            margin: 22px 0 0;
            color: #7c8797;
            font-size: 12px;
            line-height: 1.55;
            text-align: center;
        }

        @media (max-width: 820px) {
            .workspace-panel {
                grid-template-columns: 1fr;
                min-height: 0;
            }

            .workspace-info {
                border-right: 0;
                border-bottom: 1px solid rgba(250, 204, 21, 0.14);
            }

            .workspace-info::after {
                top: auto;
                right: 24px;
                bottom: 0;
                left: 24px;
                width: auto;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(250, 204, 21, 0.34), transparent);
            }

            .hero-copy h1 {
                font-size: clamp(36px, 9vw, 52px);
            }
        }

        @media (max-width: 560px) {
            .workspace-shell {
                padding: 14px;
            }

            .workspace-panel {
                border-radius: 20px;
            }

            .workspace-info,
            .workspace-choice {
                padding: 24px 18px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .logo-frame {
                width: 72px;
                height: 72px;
            }

            .logo-frame img {
                width: 58px;
                height: 58px;
            }

            .choice-copy h2 {
                font-size: 25px;
            }
        }
    </style>
</head>
<body>
    @php
        $normalizedUserRole = \App\Models\User::normalizeRole($user->user_role ?? '');
        $rawUserRole = strtolower(trim((string) ($user->user_role ?? '')));
        $userType = strtolower(trim((string) ($user->user_type ?? '')));
        $isStudentAssistant = in_array($userType, ['assistant', 'student assistant', 'student_assistant'], true)
            || in_array($rawUserRole, ['student_assistant', 'studentassistant', 'assistant'], true);
        $showStudentWorkspace = $isStudentAssistant || $normalizedUserRole === \App\Models\User::ROLE_STUDENT;
        $showAdminWorkspace = $isStudentAssistant
            || in_array($normalizedUserRole, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_SUPERADMIN], true);
    @endphp

    <main class="workspace-shell">
        <section class="workspace-panel" aria-label="Student Assistant workspace selection">
            <div class="workspace-info">
                <div class="brand-row">
                    <span class="brand-badge">
                        <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo">
                    </span>
                    <div>
                        <p class="brand-kicker">Medical Clinic</p>
                        <p class="brand-name">Polytechnic University of the Philippines Taguig</p>
                    </div>
                </div>

                <div class="hero-copy">
                    <h1>PUP Taguig Medical Clinic</h1>
                    <p>
                        Continue to the workspace you need. Your Student Assistant account can access
                        student services and authorized clinic operations from one secure gateway.
                    </p>
                </div>

                <div class="feature-grid" aria-label="Available workspace capabilities">
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="7" r="4" stroke="currentColor"/>
                                <path d="M5 21v-2a7 7 0 0 1 14 0v2" stroke="currentColor" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="feature-text">
                            <strong>Student Services</strong>
                            <span>Appointments and health records</span>
                        </span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 5h16v14H4zM8 9h8M8 13h8M8 17h5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="feature-text">
                            <strong>Clinic Operations</strong>
                            <span>Authorized staff workflows</span>
                        </span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3l7 4v5c0 4.5-2.9 7.4-7 9-4.1-1.6-7-4.5-7-9V7l7-4z" stroke="currentColor" stroke-linejoin="round"/>
                                <path d="M9 12l2 2 4-5" stroke="currentColor" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="feature-text">
                            <strong>Role Protected</strong>
                            <span>Access follows assigned permissions</span>
                        </span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 3v3M17 3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v14H4V7a2 2 0 0 1 2-2z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="feature-text">
                            <strong>Unified Access</strong>
                            <span>Switch without signing in again</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="workspace-choice">
                <div class="choice-card">
                    <div class="logo-stack" aria-label="PUP and clinic logos">
                        <span class="logo-frame">
                            <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo">
                        </span>
                        <span class="logo-frame">
                            <img src="{{ asset('images/clinic_logo_transparent.png') }}" alt="Clinic Logo">
                        </span>
                    </div>

                    <div class="choice-copy">
                        <small>Student Assistant Access</small>
                        <h2>Choose Your Workspace</h2>
                        <p>Select the area where you want to continue.</p>
                    </div>

                    <div class="workspace-actions">
                        @if($showStudentWorkspace)
                            <a class="workspace-button" href="{{ route('assistant.enter-student') }}">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="7" r="4" stroke="currentColor"/>
                                    <path d="M5 21v-2a7 7 0 0 1 14 0v2" stroke="currentColor" stroke-linecap="round"/>
                                </svg>
                                <span>Student Workspace</span>
                            </a>
                        @endif

                        @if($showAdminWorkspace)
                            <a class="workspace-button" href="{{ route('assistant.enter-admin') }}">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3.75 21h16.5M4.5 3h15a.75.75 0 0 1 .75.75v11.25a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V3.75A.75.75 0 0 1 4.5 3ZM9 21v-5.25m6 5.25v-5.25" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Admin Workspace</span>
                            </a>
                        @endif
                    </div>

                    <p class="account-note">
                        Signed in as {{ $user->name ?? 'Student Assistant' }}
                    </p>
                </div>
            </div>
        </section>
    </main>

    @include('partials.system_footer')
</body>
</html>
