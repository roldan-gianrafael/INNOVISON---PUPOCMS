<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - PUP Taguig Clinic</title>
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    
    <style>
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
        .nav-list li a:not(.logout-btn):::after {
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
    </style>

    @stack('styles')
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
                    <li><a href="{{ url('/student/account') }}" class="{{ Request::is('student/account') ? 'active' : '' }}">My Account</a></li>
                    <li><a href="{{ url('/student/faq') }}" class="{{ Request::is('student/faq') ? 'active' : '' }}">FAQ</a></li>
                    
                    <li>
                        <a href="{{ url('/') }}" class="logout-btn" style="margin-left: 15px; padding: 8px 24px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.5); color: white; border-radius: 20px; transition: 0.3s;">
                            Logout
                        </a>
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
        const btn = document.querySelector('.nav-toggle');
        const menu = document.querySelector('.nav-list');
        if(btn && menu) {
            btn.addEventListener('click', () => {
                if (menu.style.display === 'flex') {
                    menu.style.display = 'none';
                } else {
                    menu.style.display = 'flex';
                    menu.style.flexDirection = 'column';
                    menu.style.position = 'absolute';
                    menu.style.top = '70px';
                    menu.style.right = '20px';
                    menu.style.background = '#8B0000';
                    menu.style.padding = '20px';
                    menu.style.borderRadius = '8px';
                    menu.style.zIndex = '1000';
                    // Reset width for mobile menu
                    menu.style.width = 'auto'; 
                }
            });
        }
    </script>
</body>
</html>