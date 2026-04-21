<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - PUP Taguig Clinic</title>
    <script
        src="{{ asset('js/sienna-accessibility-custom.umd.js') }}?v={{ filemtime(public_path('js/sienna-accessibility-custom.umd.js')) }}"
        data-asw-position="bottom-right"
        data-asw-offset="24,12"
        data-asw-size="small"
        defer
    ></script>
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
        @keyframes accessibilityPulseRing {
            0% {
                transform: scale(1);
                opacity: 0.95;
            }
            70% {
                transform: scale(1.22);
                opacity: 0;
            }
            100% {
                transform: scale(1.22);
                opacity: 0;
            }
        }

        @keyframes accessibilityRingColorShift {
            0% {
                border-color: rgb(255, 0, 0);
                box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.2);
            }
            33% {
                border-color: rgb(255, 215, 0);
                box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
            }
            66% {
                border-color: rgb(0, 191, 255);
                box-shadow: 0 0 0 2px rgba(0, 191, 255, 0.2);
            }
            100% {
                border-color: rgb(255, 0, 0);
                box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.2);
            }
        }

        :where(.asw-menu-btn) {
            position: fixed;
            left: auto !important;
            right: 24px !important;
            top: auto !important;
            bottom: 12px !important;
            overflow: visible !important;
            background: #800000 !important;
            background-image: none !important;
            border: 2px solid #5f0012 !important;
            outline: none !important;
            box-shadow: 0 10px 24px rgba(128, 0, 0, 0.28) !important;
        }

        :where(.asw-menu-btn)::after {
            content: "";
            position: absolute;
            inset: -6px;
            border: 3px solid rgb(255, 0, 0);
            border-radius: 999px;
            pointer-events: none;
            animation:
                accessibilityPulseRing 1.9s ease-out infinite,
                accessibilityRingColorShift 3.2s linear infinite;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.2);
        }

        :where(.asw-menu-btn:hover),
        :where(.asw-menu-btn:focus-visible) {
            background: #800000 !important;
            background-image: none !important;
            border-color: #5f0012 !important;
            outline: none !important;
        }

        :where(.asw-menu-btn svg) {
            fill: #ffffff !important;
            stroke: none !important;
            transform-origin: center;
        }

        :where(.asw-menu-btn svg path:not([fill="none"])) {
            fill: #ffffff !important;
            stroke: none !important;
        }

        :where(.asw-menu-btn svg path[fill="none"]) {
            stroke: none !important;
        }

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

        :where(
            [class*="sienna"][role="dialog"],
            [class*="sienna"][role="menu"],
            [id*="sienna"][role="dialog"],
            [id*="sienna"][role="menu"],
            [class*="sienna-menu"],
            [class*="sienna-panel"],
            [id*="sienna-menu"],
            [id*="sienna-panel"]
        ) {
            background: linear-gradient(180deg, #7f1d2d 0%, #4b5563 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
            color: #f8fafc !important;
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.35) !important;
        }

        :where(
            [class*="sienna-menu"],
            [class*="sienna-panel"],
            [id*="sienna-menu"],
            [id*="sienna-panel"]
        ) :is(header, [class*="header"], [class*="title"], [class*="top"]):first-child {
            background: linear-gradient(135deg, #8b0000 0%, #6b7280 100%) !important;
            color: #ffffff !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.16) !important;
        }

        :where(
            [class*="sienna-menu"],
            [class*="sienna-panel"],
            [id*="sienna-menu"],
            [id*="sienna-panel"]
        ) :is(button, [role="button"], input, select) {
            background: rgba(255, 255, 255, 0.12) !important;
            border-color: rgba(255, 255, 255, 0.22) !important;
            color: #f8fafc !important;
        }

        :where(
            [class*="sienna-menu"],
            [class*="sienna-panel"],
            [id*="sienna-menu"],
            [id*="sienna-panel"]
        ) :is(button, [role="button"]):hover {
            background: rgba(255, 255, 255, 0.2) !important;
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

        .nav-list li.nav-dropdown {
            position: relative;
        }

        .nav-list-divider {
            width: 1px;
            height: 24px;
            background: rgba(255, 255, 255, 0.28);
            margin: 0 6px;
            flex: 0 0 auto;
        }

        .nav-dropdown-toggle {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            font-size: 15px;
            padding: 5px 0;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
            font-family: inherit;
        }

        .nav-dropdown-toggle:hover,
        .nav-dropdown-toggle[aria-expanded="true"],
        .nav-dropdown-toggle.active {
            color: #ffc107;
        }

        .nav-dropdown-toggle:focus-visible {
            outline: 2px solid #ffc107;
            outline-offset: 3px;
            border-radius: 6px;
        }

        .nav-dropdown-caret {
            width: 9px;
            height: 9px;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: rotate(45deg) translateY(-1px);
            transition: transform 0.2s ease;
        }

        .nav-dropdown.is-open .nav-dropdown-caret {
            transform: rotate(225deg) translateY(-1px);
        }

        .nav-dropdown-menu {
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            min-width: 220px;
            padding: 10px;
            margin: 0;
            list-style: none;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(139, 0, 0, 0.12);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.16);
            display: none;
            z-index: 40;
        }

        .nav-dropdown.is-open .nav-dropdown-menu {
            display: block;
        }

        .nav-dropdown-menu li {
            width: 100%;
        }

        .nav-dropdown-menu li + li {
            margin-top: 4px;
        }

        .nav-dropdown-menu a {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px !important;
            border-radius: 10px;
            color: #1f2937 !important;
            font-size: 14px !important;
            font-weight: 600 !important;
        }

        .nav-dropdown-menu a::after {
            display: none !important;
        }

        .nav-dropdown-menu a:hover,
        .nav-dropdown-menu a.active {
            background: #fff7ed;
            color: #8b0000 !important;
        }

        .notif-toggle-btn {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.42);
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            flex: 0 0 auto;
            overflow: visible;
            transition: all 0.2s ease;
        }

        .notif-toggle-btn:hover,
        .notif-toggle-btn[aria-expanded="true"] {
            background: rgba(255, 255, 255, 0.24);
            border-color: rgba(255, 255, 255, 0.65);
        }

        .notif-toggle-btn svg {
            width: 18px;
            height: 18px;
            display: block;
            flex: 0 0 auto;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: #f59e0b;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.16);
        }

        .notif-badge.is-hidden {
            display: none;
        }

        .notif-dropdown-menu {
            min-width: 320px;
            padding: 0;
            overflow: hidden;
        }

        .notif-fab-wrap {
            position: fixed;
            right: 24px;
            bottom: 84px;
            z-index: 1100;
        }

        .notif-fab {
            width: 54px;
            height: 54px;
            border-radius: 999px;
            border: 2px solid rgba(255, 255, 255, 0.92);
            background: linear-gradient(135deg, #8b0000 0%, #6b0011 100%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            box-shadow: 0 16px 28px rgba(107, 0, 17, 0.34);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .notif-fab:hover,
        .notif-fab[aria-expanded="true"] {
            transform: translateY(-2px);
            box-shadow: 0 18px 30px rgba(107, 0, 17, 0.4);
            background: linear-gradient(135deg, #9f0712 0%, #7f0014 100%);
        }

        .notif-fab svg {
            width: 22px;
            height: 22px;
            display: block;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transform-origin: top center;
        }

        .notif-fab:hover svg,
        .notif-fab:focus-visible svg {
            animation: notifBellRing 0.6s ease-in-out;
        }

        .notif-fab .notif-badge {
            top: -6px;
            right: -4px;
        }

        @keyframes notifBellRing {
            0% { transform: rotate(0deg); }
            15% { transform: rotate(14deg); }
            30% { transform: rotate(-12deg); }
            45% { transform: rotate(10deg); }
            60% { transform: rotate(-8deg); }
            75% { transform: rotate(5deg); }
            100% { transform: rotate(0deg); }
        }

        .notif-dropdown-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-bottom: 1px solid #eef2f7;
            background: #fffaf5;
        }

        .notif-dropdown-title {
            font-size: 14px;
            font-weight: 800;
            color: #7c2d12;
        }

        .notif-read-all {
            background: none;
            border: none;
            color: #8b0000;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            padding: 0;
        }

        .notif-read-all:hover {
            text-decoration: underline;
        }

        .notif-dropdown-list {
            max-height: 360px;
            overflow-y: auto;
            background: #fff;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .notif-dropdown-item {
            display: flex !important;
            gap: 10px;
            align-items: flex-start !important;
            justify-content: flex-start !important;
            padding: 12px 14px !important;
            border-radius: 0 !important;
            border-bottom: 1px solid #f1f5f9;
            position: relative;
        }

        .notif-dropdown-item:last-child {
            border-bottom: none;
        }

        .notif-dropdown-item.unread {
            background: #fffaf0;
        }

        .notif-item-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #8b0000;
            margin-top: 6px;
            flex: 0 0 auto;
        }

        .notif-item-content {
            flex: 1;
            min-width: 0;
        }

        .notif-item-message {
            font-size: 13px;
            line-height: 1.45;
            color: #1f2937;
        }

        .notif-dropdown-item.unread .notif-item-message {
            font-weight: 800;
        }

        .notif-item-time {
            display: block;
            margin-top: 4px;
            font-size: 11px;
            color: #94a3b8;
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
        .nav-list li .accessibility-toggle-btn,
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

        .nav-list li .theme-toggle-btn,
        .nav-list li .accessibility-toggle-btn {
            font-family: inherit;
            width: 36px;
            height: 36px;
            min-height: 36px;
            padding: 0;
            border-radius: 50%;
            font-size: 0;
            line-height: 0;
        }

        .nav-list li .theme-toggle-btn svg,
        .nav-list li .accessibility-toggle-btn svg {
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
        .nav-list li .accessibility-toggle-btn:hover,
        .nav-list li .logout-btn:hover {
            background: rgba(255, 255, 255, 0.24);
            border-color: rgba(255, 255, 255, 0.65);
            color: #fff;
            transform: translateY(-1px);
            text-decoration: none;
            opacity: 1;
        }

        .nav-list li .theme-toggle-btn:focus-visible,
        .nav-list li .accessibility-toggle-btn:focus-visible,
        .nav-list li .logout-btn:focus-visible {
            outline: 2px solid #ffc107;
            outline-offset: 2px;
        }

        /* --- 3. MOBILE MENU --- */
        @media (max-width: 768px) {
            .nav-toggle {
                margin-left: auto;
            }

            .main-nav {
                width: 0;
            }

            .nav-list {
                display: none;
                position: absolute;
                top: var(--header-height);
                left: 0;
                right: 0;
                flex-direction: column;
                gap: 16px;
                padding: 14px 16px;
                background: #ffffff;
                border-bottom: 1px solid var(--border);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
                max-height: calc(100vh - var(--header-height) - 10px);
                overflow-y: auto;
            }

            .nav-list.show {
                display: flex;
            }

            .nav-list li {
                width: 100%;
            }

            .nav-list li a:not(.logout-btn) {
                color: #1f2937;
                width: 100%;
                display: block;
                padding: 8px 0;
            }

            .nav-list-divider {
                width: 100%;
                height: 1px;
                margin: 2px 0 8px;
                background: #e5e7eb;
            }

            .nav-dropdown-toggle {
                color: #1f2937;
                width: 100%;
                justify-content: space-between;
                padding: 8px 0;
            }

            .nav-dropdown-menu {
                position: static;
                min-width: 100%;
                margin-top: 8px;
                box-shadow: none;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                background: #f8fafc;
            }

            .nav-dropdown-menu a {
                padding: 10px 12px !important;
            }

            .notif-dropdown-menu {
                min-width: 100%;
            }

            .nav-list li a:not(.logout-btn)::after {
                display: none;
            }

            .nav-list li .theme-toggle-btn,
            .nav-list li .accessibility-toggle-btn {
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
        html[data-theme="dark"] .nav-list li .accessibility-toggle-btn,
        html[data-theme="dark"] .nav-list li .logout-btn {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.4);
            color: #f8fafc;
        }

        html[data-theme="dark"] .nav-list li .theme-toggle-btn:hover,
        html[data-theme="dark"] .nav-list li .accessibility-toggle-btn:hover,
        html[data-theme="dark"] .nav-list li .logout-btn:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        html[data-theme="dark"] .nav-list-divider {
            background: rgba(255, 255, 255, 0.16);
        }

        html[data-theme="dark"] .nav-dropdown-menu {
            background: #171d27;
            border-color: #2f3847;
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.38);
        }

        html[data-theme="dark"] .nav-dropdown-menu a {
            color: #e5eaf3 !important;
        }

        html[data-theme="dark"] .nav-dropdown-menu a:hover,
        html[data-theme="dark"] .nav-dropdown-menu a.active {
            background: rgba(139, 0, 0, 0.22);
            color: #ffd166 !important;
        }

        html[data-theme="dark"] .notif-toggle-btn {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.4);
            color: #f8fafc;
        }

        html[data-theme="dark"] .notif-dropdown-header {
            background: rgba(139, 0, 0, 0.18);
            border-bottom-color: #2f3847;
        }

        html[data-theme="dark"] .notif-dropdown-title,
        html[data-theme="dark"] .notif-read-all {
            color: #ffd7b5;
        }

        html[data-theme="dark"] .notif-dropdown-list {
            background: #171d27;
        }

        html[data-theme="dark"] .notif-dropdown-item {
            border-bottom-color: #253041;
        }

        html[data-theme="dark"] .notif-dropdown-item.unread {
            background: rgba(139, 0, 0, 0.14);
        }

        html[data-theme="dark"] .notif-item-message {
            color: #e5eaf3;
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
            .notif-fab-wrap {
                right: 18px;
                bottom: 76px;
            }

            .notif-fab {
                width: 50px;
                height: 50px;
            }

            html[data-theme="dark"] .nav-list {
                background: #1a1018 !important;
                border: 1px solid rgba(255, 255, 255, 0.12);
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.5);
            }

            html[data-theme="dark"] .nav-list li a:not(.logout-btn) {
                color: #f3f4f6 !important;
            }

            html[data-theme="dark"] .nav-dropdown-toggle {
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
                    <span class="brand-badges">
                        <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo" class="brand-img">
                        <img src="{{ asset('images/clinic_logo.png') }}" alt="Clinic Logo" class="brand-img brand-img--clinic">
                    </span>
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
                @php
                    $isMyAccountSection = Request::is('student/account') || Request::is('student/history') || Request::is('student/barcode-register') || Request::is('student/print-health-form');
                    $layoutNotifications = collect($notifications ?? [])->take(5);
                    $notificationCount = $layoutNotifications->where('is_unread', true)->count();
                @endphp
                <ul class="nav-list">
                    <li><a href="{{ url('/student/home') }}" class="{{ Request::is('student/home') ? 'active' : '' }}">Home</a></li>
                    <li><a href="{{ url('/student/booking') }}" class="{{ Request::is('student/booking') ? 'active' : '' }}">Appointments</a></li>
                    <li><a href="{{ url('/student/home') }}#about">About Us</a></li>
                    <li><a href="{{ url('/student/faq') }}" class="{{ Request::is('student/faq') ? 'active' : '' }}">FAQs</a></li>
                    <li class="nav-list-divider" aria-hidden="true"></li>
                    <li class="nav-dropdown {{ $isMyAccountSection ? 'is-open-on-route' : '' }}" data-nav-dropdown>
                        <button
                            type="button"
                            class="nav-dropdown-toggle {{ $isMyAccountSection ? 'active' : '' }}"
                            aria-expanded="{{ $isMyAccountSection ? 'true' : 'false' }}"
                            aria-haspopup="true"
                        >
                            <span>My Account</span>
                            <span class="nav-dropdown-caret" aria-hidden="true"></span>
                        </button>
                        <ul class="nav-dropdown-menu">
                            <li><a href="{{ url('/student/account?view=profile') }}" class="{{ Request::is('student/account') && request('view', 'profile') === 'profile' ? 'active' : '' }}">Profile</a></li>
                            <li><a href="{{ url('/student/account?view=notifications') }}" class="{{ Request::is('student/account') && request('view') === 'notifications' ? 'active' : '' }}">Notifications @if($notificationCount > 0)<span style="margin-left:6px; font-weight:800; color:#8b0000;">({{ $notificationCount }})</span>@endif</a></li>
                            <li><a href="{{ url('/student/history') }}" class="{{ Request::is('student/history') ? 'active' : '' }}">Appointment History</a></li>
                            <li><a href="{{ url('/student/account?view=health-record') }}" class="{{ Request::is('student/account') && request('view') === 'health-record' ? 'active' : '' }}">Health Record</a></li>
                            <li><a href="{{ url('/student/barcode-register') }}" class="{{ Request::is('student/barcode-register') ? 'active' : '' }}">Scan / Bio</a></li>
                        </ul>
                    </li>
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
                            <input type="hidden" name="portal_guard" value="student">
                        </form>
                    </li>
                    
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @include('partials.post_login_terms_gate')

    @if($notificationCount > 0)
        <div class="notif-fab-wrap">
            <a
                href="{{ url('/student/account?view=notifications') }}"
                class="notif-fab"
                aria-label="Notifications"
                title="Notifications"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"></path>
                    <path d="M10 17a2 2 0 0 0 4 0"></path>
                </svg>
                <span class="notif-badge">{{ $notificationCount }}</span>
            </a>
        </div>
    @endif

    @if(
        Request::is('student/account') ||
        Request::is('student/booking') ||
        Request::is('student/faq') ||
        Request::is('student/health-form')
    )
        @include('partials.student_voice_input_support')
    @endif

    @stack('scripts')
    
    <script>
        (function() {
            const navToggle = document.querySelector('.nav-toggle');
            const navList = document.querySelector('.nav-list');
            const navDropdowns = document.querySelectorAll('[data-nav-dropdown]');
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const storageKey = 'student_theme';

            function forceAccessibilityButtonTheme() {
                document.querySelectorAll('.asw-menu-btn').forEach((button) => {
                    button.style.setProperty('background', '#800000', 'important');
                    button.style.setProperty('background-image', 'none', 'important');
                    button.style.setProperty('border', '2px solid #5f0012', 'important');
                    button.style.setProperty('outline', 'none', 'important');
                    button.style.setProperty('box-shadow', '0 10px 24px rgba(128, 0, 0, 0.28)', 'important');
                    button.querySelectorAll('svg').forEach((icon) => {
                        icon.style.setProperty('fill', '#ffffff', 'important');
                        icon.style.setProperty('stroke', 'none', 'important');
                        icon.style.setProperty('background', 'transparent', 'important');
                    });
                    button.querySelectorAll('svg path:not([fill="none"])').forEach((path) => {
                        path.style.setProperty('fill', '#ffffff', 'important');
                        path.style.setProperty('stroke', 'none', 'important');
                    });
                    button.querySelectorAll('svg path[fill="none"]').forEach((path) => {
                        path.style.setProperty('stroke', 'none', 'important');
                    });
                });
            }

            if (navToggle && navList) {
                const closeMobileMenu = () => navList.classList.remove('show');
                const closeDropdowns = (exceptDropdown = null) => {
                    navDropdowns.forEach((dropdown) => {
                        if (dropdown === exceptDropdown) {
                            return;
                        }

                        dropdown.classList.remove('is-open');
                        const toggle = dropdown.querySelector('.nav-dropdown-toggle, .notif-toggle-btn');
                        if (toggle) {
                            toggle.setAttribute('aria-expanded', 'false');
                        }
                    });
                };

                navToggle.addEventListener('click', () => {
                    navList.classList.toggle('show');
                });

                navDropdowns.forEach((dropdown) => {
                    const toggle = dropdown.querySelector('.nav-dropdown-toggle, .notif-toggle-btn');
                    if (!toggle) {
                        return;
                    }

                    toggle.addEventListener('click', (event) => {
                        event.preventDefault();
                        const isOpen = dropdown.classList.contains('is-open');
                        closeDropdowns(isOpen ? null : dropdown);
                        dropdown.classList.toggle('is-open', !isOpen);
                        toggle.setAttribute('aria-expanded', (!isOpen).toString());
                    });
                });

                navList.addEventListener('click', (event) => {
                    const target = event.target.closest('a, button');
                    if (!target) {
                        return;
                    }

                    if (target.classList.contains('nav-dropdown-toggle') || target.classList.contains('notif-toggle-btn')) {
                        return;
                    }

                    closeDropdowns();
                    closeMobileMenu();
                });

                document.addEventListener('click', (event) => {
                    const clickedInsideMenu = navList.contains(event.target);
                    const clickedToggle = navToggle.contains(event.target);
                    if (!clickedInsideMenu && !clickedToggle) {
                        closeDropdowns();
                        closeMobileMenu();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeDropdowns();
                        closeMobileMenu();
                    }
                });

                window.addEventListener('resize', () => {
                    if (window.innerWidth > 768) {
                        closeMobileMenu();
                    }
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

            function initAccessibilityLaunch() {
                function findSiennaTrigger() {
                    const selectorMatches = [
                        '#sienna-accessibility-button',
                        '.sienna-accessibility-button',
                        '.sienna-accessibility-trigger',
                        '[data-sienna-accessibility-trigger]',
                        'button[aria-label*="accessibility" i]',
                        'button[title*="accessibility" i]',
                        '[role="button"][aria-label*="accessibility" i]'
                    ];

                    for (const selector of selectorMatches) {
                        const candidate = document.querySelector(selector);
                        if (candidate) {
                            return candidate;
                        }
                    }

                    const fallbackCandidates = Array.from(document.querySelectorAll('button, [role="button"], div'))
                        .filter((element) => {
                            const label = [
                                element.getAttribute('aria-label'),
                                element.getAttribute('title'),
                                element.textContent
                            ].join(' ').toLowerCase();

                            const style = window.getComputedStyle(element);
                            const looksFloating = style.position === 'fixed' || style.position === 'sticky';

                            return looksFloating && label.includes('access');
                        });

                    return fallbackCandidates[0] || null;
                }

                function showSiennaTrigger() {
                    const trigger = findSiennaTrigger();
                    if (!trigger) {
                        return;
                    }

                    trigger.style.removeProperty('opacity');
                    trigger.style.removeProperty('pointer-events');
                    trigger.removeAttribute('aria-hidden');
                    trigger.style.position = 'fixed';
                    trigger.style.left = 'auto';
                    trigger.style.right = '24px';
                    trigger.style.top = 'auto';
                    trigger.style.bottom = '12px';
                }

                function themeSiennaMenu() {
                    const candidates = document.querySelectorAll('[class*="sienna"], [id*="sienna"]');
                    candidates.forEach((element) => {
                        const style = window.getComputedStyle(element);
                        const role = (element.getAttribute('role') || '').toLowerCase();
                        const isTrigger = element === findSiennaTrigger();
                        const looksPanel =
                            !isTrigger &&
                            (
                                role === 'dialog' ||
                                role === 'menu' ||
                                ((style.position === 'fixed' || style.position === 'absolute') && element.clientWidth >= 220 && element.clientHeight >= 180)
                            );

                        if (!looksPanel) {
                            return;
                        }

                        element.style.background = 'linear-gradient(180deg, #7f1d2d 0%, #4b5563 100%)';
                        element.style.border = '1px solid rgba(255,255,255,0.18)';
                        element.style.color = '#f8fafc';
                        element.style.boxShadow = '0 18px 38px rgba(15, 23, 42, 0.35)';

                        const header = element.querySelector('header, [class*="header"], [class*="title"], [class*="top"]');
                        if (header) {
                            header.style.background = 'linear-gradient(135deg, #8b0000 0%, #6b7280 100%)';
                            header.style.color = '#ffffff';
                            header.style.borderBottom = '1px solid rgba(255,255,255,0.16)';
                        }

                        element.querySelectorAll('button, [role="button"], input, select').forEach((control) => {
                            control.style.background = 'rgba(255,255,255,0.12)';
                            control.style.borderColor = 'rgba(255,255,255,0.22)';
                            control.style.color = '#f8fafc';
                        });
                    });
                }

                function injectSiennaShadowStyles() {
                    const hosts = Array.from(document.querySelectorAll('body *')).filter((element) => element.shadowRoot);

                    hosts.forEach((host) => {
                        const shadowRoot = host.shadowRoot;
                        if (!shadowRoot || shadowRoot.getElementById('customSiennaTheme')) {
                            return;
                        }

                        const text = shadowRoot.textContent || '';
                        const html = shadowRoot.innerHTML || '';
                        const combined = (text + ' ' + html).toLowerCase();
                        if (!combined.includes('access') && !combined.includes('sienna')) {
                            return;
                        }

                        const style = document.createElement('style');
                        style.id = 'customSiennaTheme';
                        style.textContent = `
                            :host, * {
                                --sienna-primary: #7f1d2d !important;
                                --sienna-secondary: #4b5563 !important;
                            }
                            header,
                            [class*="header"],
                            [class*="title"],
                            [class*="top"] {
                                background: linear-gradient(135deg, #8b0000 0%, #6b7280 100%) !important;
                                color: #ffffff !important;
                                border-bottom: 1px solid rgba(255,255,255,0.16) !important;
                            }
                            [role="dialog"],
                            [role="menu"],
                            .menu,
                            .panel,
                            .popover,
                            .container {
                                background: linear-gradient(180deg, #7f1d2d 0%, #4b5563 100%) !important;
                                color: #f8fafc !important;
                                border-color: rgba(255,255,255,0.18) !important;
                            }
                            button,
                            [role="button"],
                            input,
                            select {
                                background: rgba(255,255,255,0.12) !important;
                                color: #f8fafc !important;
                                border-color: rgba(255,255,255,0.22) !important;
                            }
                        `;

                        shadowRoot.appendChild(style);
                    });
                }

                showSiennaTrigger();
                themeSiennaMenu();
                injectSiennaShadowStyles();
                forceAccessibilityButtonTheme();

                const observer = new MutationObserver(() => {
                    showSiennaTrigger();
                    themeSiennaMenu();
                    injectSiennaShadowStyles();
                    forceAccessibilityButtonTheme();
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }

            initAccessibilityLaunch();

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
