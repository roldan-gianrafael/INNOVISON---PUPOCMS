<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Role</title>
    <style>
        :root {
            --maroon: #70131B;
            --maroon-strong: #8f2230;
            --gold: #facc15;
            --white: #ffffff;
            --ink: #1f2937;
            --muted: rgba(255, 255, 255, 0.78);
            --line: rgba(255, 255, 255, 0.18);
        }

        * { box-sizing: border-box; }

        html, body {
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
            text-decoration: none;
        }

        .assistant-shell {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(20px, 4vw, 48px) 18px;
        }

        .assistant-panel {
            width: min(1040px, 100%);
            min-height: min(620px, calc(100vh - 72px));
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid var(--line);
            border-radius: 26px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            box-shadow: 0 28px 68px rgba(15, 23, 42, 0.34);
        }

        .assistant-side {
            position: relative;
            display: grid;
            padding: clamp(24px, 4.4vw, 48px);
            align-content: space-between;
            justify-items: start;
        }

        .assistant-side::after {
            content: "";
            position: absolute;
            top: 34px;
            bottom: 34px;
            width: 1px;
            background: linear-gradient(180deg, transparent, rgba(250, 204, 21, 0.40), transparent);
            right: -1px;
        }

        .student-side {
            background:
                linear-gradient(145deg, rgba(112, 19, 27, 0.68), rgba(77, 13, 23, 0.52)),
                rgba(112, 19, 27, 0.34);
            border-right: 1px solid rgba(250, 204, 21, 0.14);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
        }

        .admin-side {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 230, 0.94));
            border-left: 1px solid rgba(112, 19, 27, 0.12);
            color: var(--ink);
        }

        .admin-side::after {
            background: linear-gradient(180deg, transparent, rgba(112, 19, 27, 0.22), transparent);
            right: auto;
            left: -1px;
        }

        .side-header {
            max-width: 520px;
        }

        .side-kicker {
            margin: 0 0 16px;
            color: var(--gold);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .admin-side .side-kicker {
            color: var(--maroon);
        }

        .side-title {
            margin: 0 0 12px;
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .admin-side .side-title {
            color: var(--maroon);
        }

        .side-description {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
            max-width: 480px;
        }

        .admin-side .side-description {
            color: #64748b;
        }

        .side-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.08));
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            margin-bottom: 20px;
        }

        .admin-side .side-icon {
            background: linear-gradient(135deg, var(--maroon), var(--maroon-strong));
            border: none;
            color: #ffffff;
        }

        .side-icon svg {
            width: 28px;
            height: 28px;
            stroke-width: 1.8;
        }

        .side-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 24px;
            border-radius: 12px;
            background: rgba(250, 204, 21, 0.18);
            border: 1px solid rgba(250, 204, 21, 0.30);
            color: var(--gold);
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.01em;
            margin-top: 28px;
        }

        .admin-side .side-cta {
            background: rgba(112, 19, 27, 0.08);
            border-color: rgba(112, 19, 27, 0.12);
            color: var(--maroon);
        }

        .side-cta:hover {
            opacity: 0.9;
        }

        .side-cta svg {
            width: 16px;
            height: 16px;
            stroke-width: 2.2;
        }

        @media (max-width: 768px) {
            .assistant-panel {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .assistant-side::after {
                display: none;
            }

            .admin-side::after {
                display: none;
            }

            .student-side {
                border-right: none;
                border-bottom: 1px solid rgba(250, 204, 21, 0.14);
            }

            .admin-side {
                border-left: none;
                border-top: 1px solid rgba(112, 19, 27, 0.12);
            }

            .side-title {
                font-size: 28px;
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
        $showStudentSide = $isStudentAssistant || $normalizedUserRole === \App\Models\User::ROLE_STUDENT;
        $showAdminSide = $isStudentAssistant || in_array($normalizedUserRole, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_SUPERADMIN], true);
    @endphp

    <div class="assistant-shell">
        <div class="assistant-panel">
            {{-- Student Side (Maroon) --}}
            @if($showStudentSide)
                <a href="{{ route('assistant.enter-student') }}" class="assistant-side student-side">
                    <div class="side-header">
                        <div class="side-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <p class="side-kicker">Student Portal</p>
                        <h2 class="side-title">Student Side</h2>
                        <p class="side-description">
                            Continue into the student portal to view health record status, booking pages, and student-facing account screens.
                        </p>
                    </div>
                    <span class="side-cta">
                        Enter Student Portal
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </span>
                </a>
            @endif

            {{-- Admin Side (White) --}}
            @if($showAdminSide)
                <a href="{{ route('assistant.enter-admin') }}" class="assistant-side admin-side">
                    <div class="side-header">
                        <div class="side-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15a.75.75 0 0 1 .75.75v11.25a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V3.75A.75.75 0 0 1 4.5 3ZM9 21v-5.25m6 5.25v-5.25" />
                            </svg>
                        </div>
                        <p class="side-kicker">Admin Portal</p>
                        <h2 class="side-title">Admin Side</h2>
                        <p class="side-description">
                            Open the clinic operations dashboard to handle appointments, walk-ins, reports, and inventory workflows.
                        </p>
                    </div>
                    <span class="side-cta">
                        Enter Admin Portal
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </span>
                </a>
            @endif
        </div>
    </div>
    @include('partials.system_footer')
</body>
</html>
