<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - PUP Taguig Clinic</title>
    <script>
        (function() {
            try {
                var savedTheme = localStorage.getItem('student_theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = savedTheme || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    
    <style>
        img,
        svg,
        video,
        canvas {
            max-width: 100%;
            height: auto;
        }

        main {
            width: 100%;
        }

        /* --- 1. HEADER SPACING FIX --- */
        .site-header .header-inner {
            max-width: 100% !important; /* Force full width */
            width: 100%;
            padding-left: 50px;  /* Push Logo Left */
            padding-right: 50px; /* Push Menu Right */
        }

        /* --- 2. NAV HOVER EFFECTS --- */
        .nav-list {
            display: flex;
            gap: 30px; /* Increased gap between links */
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        .nav-list li a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            position: relative;
            transition: color 0.3s ease;
            padding: 5px 0;
        }

        /* Hover Effect: Turn Gold */
        .nav-list li a:not(.logout-btn):hover {
            color: #ffc107; /* PUP Gold */
        }

        /* Underline Animation */
        .nav-list li a:not(.logout-btn)::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #ffc107;
            transition: width 0.3s ease;
        }

        .nav-list li a:not(.logout-btn):hover::after {
            width: 100%;
        }

        /* Active State */
        .nav-list li a.active {
            color: #fff;
            font-weight: 700;
        }

        .nav-list li .theme-toggle-btn,
        .nav-list li .logout-btn {
            margin-left: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.45);
            color: #fff;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            line-height: 1;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
        }

        .nav-list li .theme-toggle-btn {
            font-family: inherit;
            width: 36px;
            height: 36px;
            min-height: 36px;
            padding: 0;
            border-radius: 50%;
            font-size: 0;
            line-height: 0;
        }

        .nav-list li .theme-toggle-btn svg {
            width: 18px;
            height: 18px;
            display: block;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .nav-list li .logout-btn {
            min-height: 36px;
            padding: 8px 18px;
            min-width: 96px;
            white-space: nowrap;
            opacity: 1;
        }

        .nav-list li .theme-toggle-btn:hover,
        .nav-list li .logout-btn:hover {
            background: rgba(255, 255, 255, 0.24);
            border-color: rgba(255, 255, 255, 0.65);
            color: #fff;
            transform: translateY(-1px);
            text-decoration: none;
            opacity: 1;
        }

        .nav-list li .theme-toggle-btn:focus-visible,
        .nav-list li .logout-btn:focus-visible {
            outline: 2px solid #ffc107;
            outline-offset: 2px;
        }

        /* --- 3. MOBILE MENU --- */
        @media (max-width: 768px) {
            .nav-list {
                gap: 16px;
            }

            .nav-list li .theme-toggle-btn {
                margin-left: 0;
                width: 40px;
                height: 40px;
                min-height: 40px;
            }

            .nav-list li .logout-btn {
                margin-left: 0;
                width: 100%;
            }

            main table {
                display: block;
                width: 100%;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (max-width: 1024px) {
            .site-header .header-inner {
                padding-left: 22px;
                padding-right: 22px;
            }
        }

        @media (max-width: 520px) {
            .site-header .header-inner {
                padding-left: 14px;
                padding-right: 14px;
            }
        }
    </style>

    @stack('styles')
    <style>
        html[data-theme="dark"] {
            color-scheme: dark;
            --primary: #a31b1b;
            --header-bg: #3f0b15;
            --bg-color: #0f131a;
            --card-bg: #171d27;
            --text-main: #e5eaf3;
            --text-light: #a9b4c4;
            --border: #2f3847;
            --focus-ring: rgba(163, 27, 27, 0.32);
        }

        html[data-theme="dark"] body {
            background: var(--bg-color);
            color: var(--text-main);
        }

        html[data-theme="dark"] .site-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
        }

        html[data-theme="dark"] .nav-list li a:not(.logout-btn) {
            color: rgba(243, 246, 252, 0.92);
        }

        html[data-theme="dark"] .nav-list li a:not(.logout-btn):hover {
            color: #ffd166;
        }

        html[data-theme="dark"] .nav-list li .theme-toggle-btn,
        html[data-theme="dark"] .nav-list li .logout-btn {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.4);
            color: #f8fafc;
        }

        html[data-theme="dark"] .nav-list li .theme-toggle-btn:hover,
        html[data-theme="dark"] .nav-list li .logout-btn:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        html[data-theme="dark"] h1,
        html[data-theme="dark"] h2,
        html[data-theme="dark"] h3,
        html[data-theme="dark"] h4,
        html[data-theme="dark"] h5,
        html[data-theme="dark"] h6,
        html[data-theme="dark"] .page-title,
        html[data-theme="dark"] .form-section-title,
        html[data-theme="dark"] .info-title,
        html[data-theme="dark"] .section-title,
        html[data-theme="dark"] .widget-title,
        html[data-theme="dark"] .category-title,
        html[data-theme="dark"] .apt-service,
        html[data-theme="dark"] .appt-service,
        html[data-theme="dark"] .comment-body h4,
        html[data-theme="dark"] #about h2 {
            color: #f2f6fd !important;
        }

        html[data-theme="dark"] .page-subtitle,
        html[data-theme="dark"] .small,
        html[data-theme="dark"] .input-label,
        html[data-theme="dark"] .apt-details,
        html[data-theme="dark"] .apt-time,
        html[data-theme="dark"] .appt-time,
        html[data-theme="dark"] .faq-answer,
        html[data-theme="dark"] .comment-meta,
        html[data-theme="dark"] .comment-body p,
        html[data-theme="dark"] .notif-text,
        html[data-theme="dark"] .notif-time,
        html[data-theme="dark"] .scan-helper,
        html[data-theme="dark"] #about p {
            color: var(--text-light) !important;
        }

        html[data-theme="dark"] .card,
        html[data-theme="dark"] .booking-card,
        html[data-theme="dark"] .booking-info-section,
        html[data-theme="dark"] .info-card,
        html[data-theme="dark"] .appt-item,
        html[data-theme="dark"] .card-history,
        html[data-theme="dark"] .apt-card,
        html[data-theme="dark"] .appt-card,
        html[data-theme="dark"] .widget-card,
        html[data-theme="dark"] .barcode-status-card,
        html[data-theme="dark"] .student-info-box,
        html[data-theme="dark"] .barcode-card,
        html[data-theme="dark"] .category-card,
        html[data-theme="dark"] .sidebar-widget,
        html[data-theme="dark"] .comment-card,
        html[data-theme="dark"] details[open] {
            background: var(--card-bg) !important;
            border-color: var(--border) !important;
            color: var(--text-main) !important;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
        }

        html[data-theme="dark"] .welcome {
            background: #111822 !important;
        }

        html[data-theme="dark"] .comments-section {
            background: #0d141c !important;
        }

        html[data-theme="dark"] .comment-chip {
            background: rgba(203, 213, 225, 0.15) !important;
            color: #dbe5f4 !important;
        }

        html[data-theme="dark"] .booking-form-section,
        html[data-theme="dark"] .category-header,
        html[data-theme="dark"] .notif-item,
        html[data-theme="dark"] .stat-row,
        html[data-theme="dark"] .info-title {
            border-color: var(--border) !important;
        }

        html[data-theme="dark"] .apt-notes,
        html[data-theme="dark"] .appt-notes,
        html[data-theme="dark"] .note-widget {
            background: #111a24 !important;
            border-color: #3b4657 !important;
            color: #d2dbe8 !important;
        }

        html[data-theme="dark"] .note-header {
            color: #f0be6a !important;
        }

        html[data-theme="dark"] .barcode-label,
        html[data-theme="dark"] .stat-label {
            color: #9aa6ba !important;
        }

        html[data-theme="dark"] .barcode-value,
        html[data-theme="dark"] .stat-val {
            color: #f3f7ff !important;
        }

        html[data-theme="dark"] .hero-search-input,
        html[data-theme="dark"] input,
        html[data-theme="dark"] select,
        html[data-theme="dark"] textarea,
        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .barcode-input {
            background: #0f161f !important;
            color: #e9eef8 !important;
            border-color: #3a4556 !important;
        }

        html[data-theme="dark"] input::placeholder,
        html[data-theme="dark"] textarea::placeholder {
            color: #8e9aaf !important;
        }

        html[data-theme="dark"] .form-control[readonly],
        html[data-theme="dark"] .form-control:disabled {
            background: #1a2432 !important;
            color: #9ba7ba !important;
            border-color: #334053 !important;
        }

        html[data-theme="dark"] .btn.ghost,
        html[data-theme="dark"] .btn-outline {
            color: #f6d3d3 !important;
            border-color: #b45858 !important;
            background: transparent !important;
        }

        html[data-theme="dark"] .btn-outline:hover,
        html[data-theme="dark"] .btn.ghost:hover {
            background: rgba(139, 0, 0, 0.22) !important;
        }

        html[data-theme="dark"] details {
            border-color: var(--border) !important;
        }

        html[data-theme="dark"] summary {
            color: #d7e1ee !important;
        }

        html[data-theme="dark"] details[open] summary {
            color: #ffd166 !important;
        }

        html[data-theme="dark"] .empty-state {
            color: #93a2b6 !important;
        }

        html[data-theme="dark"] #about a {
            color: #ffd8d8 !important;
            border-color: #bb5959 !important;
            background: rgba(139, 0, 0, 0.18) !important;
        }

        html[data-theme="dark"] #about a:hover {
            background: rgba(139, 0, 0, 0.28) !important;
        }

        html[data-theme="dark"] #reader {
            border-color: #4a5568 !important;
        }

        html[data-theme="dark"] #barcodeModal > div {
            background: #181f2a !important;
            color: #e5ecf7 !important;
            border: 1px solid #2f3a4a;
        }

        html[data-theme="dark"] #barcodeModal h2 {
            color: #f5f8ff !important;
        }

        html[data-theme="dark"] #barcodeModal p {
            color: #b5c1d3 !important;
        }

        html[data-theme="dark"] #barcodeModal > div > div:first-child {
            background: rgba(139, 0, 0, 0.2) !important;
        }

        html[data-theme="dark"] #barcodeModal button[onclick="closeBarcodeModal()"] {
            background: #101723 !important;
            color: #d4deec !important;
        }

        @media (max-width: 768px) {
            html[data-theme="dark"] .nav-list {
                background: #1a1018 !important;
                border: 1px solid rgba(255, 255, 255, 0.12);
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.5);
            }

            html[data-theme="dark"] .nav-list a {
                color: #f3f4f6 !important;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <div class="header-left">
                <a class="brand-link" href="{{ url('/student/home') }}">
                    <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo" class="brand-img">
                    <span class="brand-text">
                        <span class="brand-title">PUP TAGUIG</span>
                        <span class="brand-subtitle">ONLINE CLINIC</span>
                    </span>
                </a>
            </div>

            <button class="nav-toggle" aria-label="Open menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>

            <nav id="main-menu" class="main-nav">
                <ul class="nav-list">
                    <li><a href="{{ url('/student/home') }}" class="{{ Request::is('student/home') ? 'active' : '' }}">Home</a></li>

                    <li><a href="{{ url('/student/home') }}#about">About Us</a></li>
                    <li><a href="{{ url('/student/booking') }}" class="{{ Request::is('student/booking') ? 'active' : '' }}">Appointments</a></li>
                    <li><a href="{{ url('/student/barcode-register') }}" class="{{ Request::is('student/barcode-register') ? 'active' : '' }}">Register</a></li>
                   <li><a href="{{ url('/student/account') }}" class="{{ Request::is('student/account') ? 'active' : '' }}">My Account</a></li>
                    <li><a href="{{ url('/student/faq') }}" class="{{ Request::is('student/faq') ? 'active' : '' }}">FAQ</a></li>
                    <li>
                        <button type="button" id="themeToggleBtn" class="theme-toggle-btn" aria-pressed="false" aria-label="Theme mode" title="Theme mode">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                            </svg>
                        </button>
                    </li>
                    
                    <li>
                        <a href="#" class="logout-btn" 
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                    
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
    
    <script>
        (function() {
            const navToggle = document.querySelector('.nav-toggle');
            const navList = document.querySelector('.nav-list');
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const storageKey = 'student_theme';

            if (navToggle && navList) {
                navToggle.addEventListener('click', () => {
                    navList.classList.toggle('show');
                });

                navList.querySelectorAll('a, button').forEach((el) => {
                    el.addEventListener('click', () => {
                        navList.classList.remove('show');
                    });
                });
            }

            function setTheme(theme) {
                const normalizedTheme = theme === 'dark' ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', normalizedTheme);

                if (themeToggleBtn) {
                    const isDark = normalizedTheme === 'dark';
                    const moonIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79z"></path></svg>';
                    const sunIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>';
                    themeToggleBtn.innerHTML = isDark ? moonIcon : sunIcon;
                    themeToggleBtn.setAttribute('aria-label', isDark ? 'Dark mode enabled' : 'Light mode enabled');
                    themeToggleBtn.setAttribute('title', isDark ? 'Dark mode' : 'Light mode');
                    themeToggleBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                }
            }

            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            setTheme(currentTheme);

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const activeTheme = document.documentElement.getAttribute('data-theme');
                    const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';
                    setTheme(nextTheme);

                    try {
                        localStorage.setItem(storageKey, nextTheme);
                    } catch (error) {
                        console.warn('Theme preference was not saved.', error);
                    }
                });
            }
        })();
    </script>
</body>
</html>
