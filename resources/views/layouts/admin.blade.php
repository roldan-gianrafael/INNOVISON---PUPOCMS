<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PUPT Admin</title>
    <script
        src="{{ asset('js/sienna-accessibility-custom.umd.js') }}"
        data-asw-position="bottom-right"
        data-asw-offset="24,12"
        defer
    ></script>
    <script>
        (function() {
            try {
                var savedTheme = localStorage.getItem('admin_theme');
                var theme = savedTheme === 'light' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', theme);
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@600;700&display=swap" rel="stylesheet">

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

        @keyframes accessibilityIconWave {
            0%, 100% {
                transform: rotate(0deg);
            }
            20% {
                transform: rotate(10deg);
            }
            40% {
                transform: rotate(-8deg);
            }
            60% {
                transform: rotate(10deg);
            }
            80% {
                transform: rotate(-4deg);
            }
        }

        :where(.asw-menu-btn) {
            position: fixed;
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
            transform-origin: 50% 28%;
            animation: accessibilityIconWave 2.4s ease-in-out infinite;
        }

        :where(.asw-menu-btn svg path:not([fill="none"])) {
            fill: #ffffff !important;
            stroke: none !important;
        }

        :where(.asw-menu-btn svg path[fill="none"]) {
            stroke: none !important;
        }

        :root {
            --bg: #2a0e16;
            --bg-grad-1: #5b1a2a;
            --bg-grad-2: #3a111c;
            --surface: #5a1d2a;
            --surface-soft: #733242;
            --stroke: #8d4c5b;
            --stroke-strong: #a86373;
            --text: #fff0f4;
            --muted: #f0c5cf;
            --pup-maroon: #800000;
            --pup-maroon-dark: #5f0012;
            --pup-maroon-soft: #f1d9df;
            --pup-gold: #ffb81c;
            --danger: #ff9cb0;
            --shadow-soft: 0 14px 30px rgba(22, 5, 10, 0.32);
            --radius-xl: 22px;
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        * { box-sizing: border-box; }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: "Manrope", "Segoe UI", "Inter", "Helvetica Neue", Arial, sans-serif;
            background:
                radial-gradient(circle at -10% -10%, rgba(255, 255, 255, 0.09) 0%, transparent 42%),
                radial-gradient(circle at 110% 120%, rgba(255, 184, 28, 0.08) 0%, transparent 36%),
                linear-gradient(180deg, var(--bg-grad-2) 0%, var(--bg) 80%);
            color: var(--text);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            position: sticky;
            top: 0;
            background: linear-gradient(180deg, rgba(80, 18, 31, 0.96) 0%, rgba(59, 13, 23, 0.94) 100%);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            padding: 14px clamp(16px, 3vw, 30px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
            z-index: 70;
        }

        .header-left {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-brand-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            background: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.32);
            padding: 2px;
            flex-shrink: 0;
        }

        .header-copy {
            min-width: 0;
        }

        .header-kicker {
            margin: 0 0 4px;
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .header-title {
            margin: 0;
            font-family: "Outfit", "Manrope", sans-serif;
            font-size: clamp(18px, 2vw, 24px);
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--text);
        }

        .header-title span {
            color: var(--pup-gold);
        }

        .header-subtitle {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 500;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .sidebar-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--stroke);
            background: var(--surface);
            color: var(--text);
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        }

        .profile-wrap {
            position: relative;
        }

        .admin-user {
            border: 1px solid rgba(255, 255, 255, 0.24);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .admin-user:hover {
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: var(--shadow-soft);
            transform: translateY(-1px);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(145deg, var(--pup-maroon), var(--pup-maroon-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 0.02em;
        }

        .admin-user-meta {
            text-align: right;
        }

        .admin-user-name {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--text);
        }

        .admin-user-role {
            margin-top: 2px;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .profile-dropdown {
            display: none;
            position: absolute;
            top: 54px;
            right: 0;
            background: #f4dde4;
            width: 190px;
            box-shadow: var(--shadow-soft);
            border-radius: 12px;
            border: 1px solid #cb97a3;
            overflow: hidden;
            z-index: 1000;
        }

        .profile-dropdown a {
            display: block;
            padding: 12px 16px;
            color: #4b0f19;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s ease, color 0.2s ease;
            border-bottom: 1px solid #e4bdc7;
        }

        .profile-dropdown a:hover {
            background: #edd0d8;
            color: #5c0e1a;
        }

        .profile-dropdown a.logout-link {
            color: var(--danger);
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 1.2;
            padding: 12px 14px;
        }

        .profile-dropdown a.logout-link:hover {
            background: #f4cfd8;
        }

        .admin-layout {
            display: flex;
            flex: 1;
            min-height: 0;
            gap: 20px;
            padding: clamp(14px, 2.4vw, 24px);
            overflow: hidden;
        }

        .sidebar {
            width: 86px;
            background: linear-gradient(180deg, #2a1318 0%, #1a0b0f 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-soft);
            padding: 20px 14px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.28s ease;
        }

        .sidebar:hover {
            width: 258px;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.16);
            min-width: 210px;
        }

        .sidebar-logo img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid rgba(255, 255, 255, 0.28);
            object-fit: cover;
            padding: 2px;
        }

        .sidebar-logo-title {
            margin: 0;
            font-family: "Outfit", "Manrope", sans-serif;
            font-size: 14px;
            letter-spacing: 0.02em;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .sidebar-logo-sub {
            margin: 3px 0 0;
            color: rgba(255, 255, 255, 0.68);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sidebar-logo-text {
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .sidebar h4 {
            color: rgba(255, 255, 255, 0.62);
            margin: 0 0 12px;
            font-size: 10px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            font-weight: 800;
            white-space: nowrap;
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.01em;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
            min-width: 210px;
            white-space: nowrap;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateX(1px);
        }

        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.26);
            color: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
        }

        .sidebar-short {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
            flex-shrink: 0;
            letter-spacing: 0.1em;
            color: #ffffff;
            font-family: "Outfit", "Manrope", sans-serif;
        }

        .sidebar-nav a.active .sidebar-short {
            border-color: rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }

        .sidebar-label {
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .sidebar:hover .sidebar-logo-text,
        .sidebar:hover h4,
        .sidebar:hover .sidebar-label {
            opacity: 1;
            transform: translateX(0);
        }

        .sidebar-logout {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.16);
        }

        .sidebar-logout a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-decoration: none;
            min-width: 210px;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
            margin-bottom: 0;
            color: rgba(255, 255, 255, 0.95);
            background: rgba(0, 0, 0, 0.18);
            line-height: 1.2;
        }

        .sidebar-logout a:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateX(1px);
        }

        .sidebar-logout .sidebar-short {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.26);
            background: rgba(255, 255, 255, 0.12);
        }

        .main {
            flex: 1;
            min-width: 0;
            overflow-y: auto;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 14px;
            color: #3c0f18;
            background: linear-gradient(180deg, rgba(140, 72, 89, 0.22) 0%, rgba(98, 33, 47, 0.26) 100%);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: var(--radius-xl);
        }

        .main table {
            width: 100%;
        }

        .main::-webkit-scrollbar,
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .main::-webkit-scrollbar-track,
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .main::-webkit-scrollbar-thumb,
        .sidebar::-webkit-scrollbar-thumb {
            background: #b17382;
            border-radius: 999px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--stroke);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-soft);
            margin-bottom: 18px;
        }

        .btn {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid transparent;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: filter 0.2s ease, transform 0.2s ease;
        }

        .btn:hover {
            filter: brightness(0.98);
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--pup-maroon);
            color: #fff;
        }

        .btn-outline {
            background: var(--surface);
            border-color: var(--stroke-strong);
            color: var(--text);
        }

        @media (max-width: 1024px) {
            .admin-layout {
                gap: 16px;
                padding: 14px;
            }
        }

        @media (max-width: 860px) {
            .sidebar-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .admin-layout {
                padding: 12px;
            }

            .sidebar {
                position: fixed;
                top: 76px;
                left: 12px;
                bottom: 12px;
                width: min(86vw, 300px);
                z-index: 60;
                transform: translateX(-115%);
                transition: transform 0.22s ease;
            }

            .sidebar:hover {
                width: min(86vw, 300px);
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            body.sidebar-open::before {
                content: "";
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.28);
                z-index: 55;
            }

            .main {
                width: 100%;
            }

            .header-subtitle {
                display: none;
            }

            .header-brand-avatar {
                width: 44px;
                height: 44px;
            }

            .admin-user-meta {
                display: none;
            }

            .sidebar-logo {
                min-width: 0;
            }

            .sidebar-nav a {
                min-width: 0;
            }

            .sidebar .sidebar-logo-text,
            .sidebar h4,
            .sidebar .sidebar-label {
                opacity: 1;
                transform: translateX(0);
            }

            .main table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (max-width: 560px) {
            .header-kicker {
                display: none;
            }

            .header-title {
                font-size: 18px;
            }
        }

        @stack('styles')
    </style>

    <style>
        /* Global Admin Theme Overrides */
        .main :where(.card, .panel, .stat-card-mini, .modal-box) {
            background: linear-gradient(165deg, #f2dee4 0%, #eacbd3 100%) !important;
            border: 1px solid #cc97a3 !important;
            border-radius: 14px !important;
            box-shadow: 0 10px 24px rgba(47, 8, 16, 0.16) !important;
            color: #3b0f18 !important;
        }

        .main :where(h1, h2, h3, h4, h5) {
            color: #4b0f17;
            letter-spacing: -0.01em;
        }

        .main :where(a):not(.report-card) {
            color: #70131B;
        }

        .main :where(a:hover):not(.report-card) {
            color: #5a0f16;
        }

        /* Keep Reports cards high-contrast even on hover */
        .main .report-grid .report-card {
            background: #6f1422 !important;
            border: 1px solid #8f3444 !important;
            color: #ffffff !important;
        }

        .main .report-grid .report-card:hover {
            background: #7d1a2a !important;
            color: #ffffff !important;
            filter: none !important;
        }

        .main .report-grid .report-card .report-label {
            color: #f2d4dc !important;
        }

        .main .report-grid .report-card .report-main-title {
            color: #ffffff !important;
        }

        .main .report-grid .report-card .report-badge {
            background: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
        }

        .main :where(table th) {
            border-bottom-color: #ca97a2 !important;
            color: #61202d !important;
            letter-spacing: 0.06em;
            background: rgba(255, 255, 255, 0.24) !important;
        }

        .main :where(table td) {
            border-bottom-color: #d7b0b9 !important;
            color: #43111a !important;
        }

        .main :where(.form-control, .form-input, .input-month, input, select, textarea) {
            border-color: #c88f9b !important;
            border-radius: 10px !important;
            background: #fff6f8 !important;
            color: #41111b !important;
        }

        .main :where(.form-control, .form-input, .input-month, input, select, textarea):focus {
            outline: none;
            border-color: #70131B !important;
            box-shadow: 0 0 0 3px rgba(112, 19, 27, 0.15);
        }

        .main :where(.btn-save, .btn-add, .btn-add-walkin, .btn-filter, .btn-change, .btn-generate, .btn-primary, .btn-complete) {
            background: #720f1e !important;
            color: #ffffff !important;
            border: 1px solid #720f1e !important;
        }

        .main :where(.btn-save:hover, .btn-add:hover, .btn-add-walkin:hover, .btn-filter:hover, .btn-change:hover, .btn-generate:hover, .btn-primary:hover, .btn-complete:hover) {
            background: #590b17 !important;
            border-color: #590b17 !important;
            color: #ffffff !important;
        }

        .main :where(.btn-edit, .btn-view, .btn-outline) {
            background: #f7e2e8 !important;
            color: #65101d !important;
            border: 1px solid #d6a2ad !important;
        }

        .main :where(.btn-edit:hover, .btn-view:hover, .btn-outline:hover) {
            background: #efd0d8 !important;
        }

        .main :where(.btn-delete, .btn-cancel) {
            background: #f6d9e2 !important;
            color: #b42339 !important;
            border: 1px solid #e6a8b7 !important;
        }

        .main :where(.status.completed) {
            background: #f3d2dc !important;
            color: #781826 !important;
        }

        .main :where(.switch-row input) {
            accent-color: #70131B !important;
        }

        .main :where(.notification-toast) {
            background: linear-gradient(145deg, #7f1d2d, #5a0f16) !important;
        }

        .main :where(.btn-toast-action) {
            background: rgba(255, 255, 255, 0.18) !important;
            border-color: rgba(255, 255, 255, 0.38) !important;
        }

        .assistant-launch {
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-radius: 12px;
            padding: 9px 12px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            letter-spacing: 0.03em;
        }

        .assistant-launch:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.42);
        }

        .theme-toggle-admin {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
            padding: 0;
        }

        .theme-toggle-admin:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.42);
            transform: translateY(-1px);
        }

        .theme-toggle-admin:focus-visible {
            outline: 2px solid var(--pup-gold);
            outline-offset: 2px;
        }

        .theme-toggle-admin svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            display: block;
        }

        .accessibility-launch-admin {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
            padding: 0;
        }

        .accessibility-launch-admin:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.42);
            transform: translateY(-1px);
        }

        .accessibility-launch-admin:focus-visible {
            outline: 2px solid var(--pup-gold);
            outline-offset: 2px;
        }

        .accessibility-launch-admin svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            display: block;
        }

        html[data-theme="light"] body {
            background:
                radial-gradient(circle at -10% -10%, rgba(128, 0, 0, 0.06) 0%, transparent 42%),
                radial-gradient(circle at 110% 120%, rgba(128, 0, 0, 0.08) 0%, transparent 36%),
                linear-gradient(180deg, #faf2f5 0%, #f4e6eb 80%);
            color: #3f111b;
        }

        html[data-theme="light"] .admin-header {
            background: linear-gradient(180deg, rgba(255, 245, 248, 0.98) 0%, rgba(245, 226, 233, 0.96) 100%);
            border-bottom-color: rgba(128, 0, 0, 0.18);
        }

        html[data-theme="light"] .header-kicker,
        html[data-theme="light"] .header-subtitle {
            color: #7d4b5a;
        }

        html[data-theme="light"] .header-title {
            color: #4f1220;
        }

        html[data-theme="light"] .header-title span {
            color: #8b0000;
        }

        html[data-theme="light"] .assistant-launch,
        html[data-theme="light"] .accessibility-launch-admin,
        html[data-theme="light"] .theme-toggle-admin,
        html[data-theme="light"] .sidebar-toggle {
            background: rgba(128, 0, 0, 0.08);
            border-color: rgba(128, 0, 0, 0.24);
            color: #5f0012;
        }

        html[data-theme="light"] .assistant-launch:hover,
        html[data-theme="light"] .accessibility-launch-admin:hover,
        html[data-theme="light"] .theme-toggle-admin:hover,
        html[data-theme="light"] .sidebar-toggle:hover {
            background: rgba(128, 0, 0, 0.14);
            border-color: rgba(128, 0, 0, 0.34);
        }

        html[data-theme="light"] .admin-user {
            border-color: rgba(128, 0, 0, 0.24);
            background: rgba(255, 255, 255, 0.78);
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

        html[data-theme="light"] .admin-user-name {
            color: #4f1220;
        }

        html[data-theme="light"] .admin-user-role {
            color: #7d4b5a;
        }

        html[data-theme="light"] .sidebar {
            background: linear-gradient(180deg, #f9edf1 0%, #efd8df 100%);
            border-color: rgba(128, 0, 0, 0.18);
        }

        html[data-theme="light"] .sidebar-logo {
            border-bottom-color: rgba(128, 0, 0, 0.18);
        }

        html[data-theme="light"] .sidebar-logo-title {
            color: #5a1421;
        }

        html[data-theme="light"] .sidebar-logo-sub,
        html[data-theme="light"] .sidebar h4 {
            color: rgba(90, 20, 33, 0.72);
        }

        html[data-theme="light"] .sidebar-nav a {
            color: #5a1421;
        }

        html[data-theme="light"] .sidebar-nav a:hover {
            background: rgba(128, 0, 0, 0.08);
            border-color: rgba(128, 0, 0, 0.18);
        }

        html[data-theme="light"] .sidebar-nav a.active {
            background: rgba(128, 0, 0, 0.13);
            border-color: rgba(128, 0, 0, 0.26);
            color: #4a0f1a;
        }

        html[data-theme="light"] .sidebar-short {
            color: #5a1421;
            border-color: rgba(128, 0, 0, 0.24);
            background: rgba(128, 0, 0, 0.08);
        }

        html[data-theme="light"] .sidebar-logout {
            border-top-color: rgba(128, 0, 0, 0.18);
        }

        html[data-theme="light"] .sidebar-logout a {
            background: rgba(128, 0, 0, 0.08);
            border-color: rgba(128, 0, 0, 0.2);
            color: #561320;
        }

        html[data-theme="light"] .sidebar-logout a:hover {
            background: rgba(128, 0, 0, 0.14);
            border-color: rgba(128, 0, 0, 0.28);
        }

        html[data-theme="light"] .main {
            color: #3c0f18;
            background: linear-gradient(180deg, rgba(242, 220, 227, 0.96) 0%, rgba(234, 208, 217, 0.96) 100%);
            border-color: rgba(128, 0, 0, 0.16);
        }

        html[data-theme="light"] .profile-dropdown {
            background: #fff4f7;
            border-color: #d5a4af;
        }

        html[data-theme="light"] .profile-dropdown a {
            color: #5b1623;
            border-bottom-color: #e7c2ca;
        }

        html[data-theme="light"] .profile-dropdown a:hover {
            background: #f8e4ea;
            color: #56111d;
        }

        html[data-theme="light"] .profile-dropdown a.logout-link {
            color: #a2263f;
        }

        .assistant-panel {
            position: fixed;
            right: 20px;
            bottom: 18px;
            width: min(420px, calc(100vw - 24px));
            background: #f2dde3;
            border: 1px solid #bf8592;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(112, 19, 27, 0.14);
            z-index: 1200;
            display: none;
            overflow: hidden;
        }

        .assistant-panel.open {
            display: block;
        }

        .assistant-head {
            padding: 12px 14px;
            background: linear-gradient(145deg, #7f1d2d, #5a0f16);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .assistant-head-title {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
        }

        .assistant-head-sub {
            margin: 2px 0 0;
            font-size: 11px;
            opacity: 0.86;
        }

        .assistant-close {
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
        }

        .assistant-messages {
            max-height: 300px;
            overflow-y: auto;
            padding: 12px;
            background: #f8eaee;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .assistant-bubble {
            padding: 9px 11px;
            border-radius: 11px;
            font-size: 13px;
            line-height: 1.4;
            border: 1px solid #efe1e5;
            max-width: 92%;
            white-space: pre-wrap;
        }

        .assistant-bubble.user {
            margin-left: auto;
            background: #efd2d9;
            border-color: #d49faa;
            color: #5b0e1a;
        }

        .assistant-bubble.assistant {
            background: #f3dde4;
            border-color: #dbb1bb;
            color: #3e0f18;
        }

        .assistant-controls {
            border-top: 1px solid #c7909e;
            padding: 10px;
            display: flex;
            gap: 8px;
            align-items: center;
            background: #f2dce3;
        }

        .assistant-mic,
        .assistant-send {
            border: 1px solid #6e1220;
            background: #6e1220;
            color: #ffffff;
            padding: 8px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            min-width: 68px;
        }

        .assistant-mic.listening {
            background: #70131B;
            color: #ffffff;
            border-color: #70131B;
        }

        .assistant-mic:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .assistant-input {
            flex: 1;
            border: 1px solid #c18895;
            border-radius: 10px;
            padding: 9px 10px;
            font-size: 13px;
            color: #40111b;
            background: #fff6f8;
        }

        .assistant-input:focus {
            outline: none;
            border-color: #70131B;
            box-shadow: 0 0 0 3px rgba(112, 19, 27, 0.14);
        }

        .assistant-note {
            margin: 0;
            padding: 8px 11px 11px;
            font-size: 11px;
            color: #5d2833;
            border-top: 1px solid #d9b0bb;
            background: #ebd2d9;
        }

        @media (max-width: 860px) {
            .assistant-panel {
                right: 12px;
                bottom: 10px;
            }

            .assistant-launch {
                display: none;
            }
        }
    </style>
</head>
<body>
@php
    $authUser = auth()->user();
    $currentRole = \App\Models\User::normalizeRole(optional($authUser)->user_role ?? '');
    $isStudentAssistant = $currentRole === \App\Models\User::ROLE_ADMIN;
    $isAdminLike = $currentRole === \App\Models\User::ROLE_SUPERADMIN;
    $dashboardUrl = $isStudentAssistant ? url('/assistant/dashboard') : url('/admin/dashboard');
    $appointmentsUrl = $isStudentAssistant ? url('/assistant/appointments') : url('/admin/appointments');
    $inventoryUrl = $isStudentAssistant ? url('/assistant/inventory') : url('/admin/inventory');
    $reportsUrl = $isStudentAssistant ? url('/assistant/reports') : url('/admin/reports');
    $apiTestingUrl = $isStudentAssistant ? url('/assistant/api-testing') : url('/admin/api-testing');
    $settingsUrl = url('/admin/settings');
    $assistantAccountsUrl = url('/admin/student-assistants');
    $walkinUrl = $isStudentAssistant ? url('/assistant/walkin') : url('/admin/walkin');
    $assistantEndpoint = $isStudentAssistant ? route('assistant.intent') : route('admin.assistant.intent');
    $displayName = optional($authUser)->name ?? 'Clinic User';
    $welcomeName = in_array($displayName, ['Admin Account', 'Super Admin Account'], true) ? 'Nurse Joyce' : $displayName;
    $avatarInitial = strtoupper(substr($displayName, 0, 1));
    $brandLogo = asset('images/clinic_logo.png');
    $roleLabelMap = [
        'superadmin' => 'Super Admin',
        'admin' => 'Admin',
        'super_admin' => 'Super Admin (Legacy)',
        'student_assistant' => 'Admin (Legacy)',
    ];
    $displayRole = $roleLabelMap[$currentRole] ?? ucfirst($currentRole ?: 'user');
@endphp

<header class="admin-header">
    <div class="header-left">
        <img src="{{ $brandLogo }}" alt="Clinic Logo" class="header-brand-avatar">
        <div class="header-copy">
            <p class="header-kicker">{{ $isStudentAssistant ? 'Clinic Assistant Console' : 'Clinic Administration' }}</p>
            <h1 class="header-title">Welcome back, <span>{{ $welcomeName }}</span></h1>
            <p class="header-subtitle">Monitor operations and patient flow in one clear workspace.</p>
        </div>
    </div>

    <div class="header-right">
        <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar" onclick="toggleSidebar()">&#9776;</button>
        <button type="button" class="theme-toggle-admin" id="adminThemeToggle" aria-pressed="false" aria-label="Theme mode" title="Theme mode">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
            </svg>
        </button>
        <button type="button" class="assistant-launch" id="assistantLaunchBtn" onclick="toggleAssistantPanel()">AI Assistant</button>

        <div class="profile-wrap">
            <button type="button" class="admin-user" onclick="toggleProfileMenu()">
                <div class="admin-user-meta">
                    <div class="admin-user-name">{{ $displayName }}</div>
                    <div class="admin-user-role">{{ $displayRole }}</div>
                </div>
                <div class="user-avatar">{{ $avatarInitial }}</div>
            </button>

            <div id="profileDropdown" class="profile-dropdown">
                @if($isAdminLike)
                    <a href="{{ $settingsUrl }}">Settings</a>
                @endif
                <a href="#" class="logout-link" onclick="event.preventDefault(); document.getElementById('layoutLogoutForm').submit();">Logout</a>
            </div>
        </div>
    </div>
</header>

<div class="admin-layout">
  
  <aside class="sidebar" id="adminSidebar">
    <div class="sidebar-logo">
      <img src="{{ $brandLogo }}" alt="Clinic Logo">
      <div class="sidebar-logo-text">
        <div class="sidebar-logo-title">PUP TAGUIG</div>
        <div class="sidebar-logo-sub">{{ $isStudentAssistant ? 'Clinic Assistant' : 'Clinic Admin' }}</div>
      </div>
    </div>
    
    <h4>Main Menu</h4>
    <nav class="sidebar-nav">
      <a href="{{ $dashboardUrl }}" class="{{ (Request::is('admin/dashboard') || Request::is('assistant/dashboard')) ? 'active' : '' }}">
        <span class="sidebar-short">DB</span><span class="sidebar-label">Dashboard</span>
      </a>
      <a href="{{ $appointmentsUrl }}" class="{{ (Request::is('admin/appointments*') || Request::is('assistant/appointments*')) ? 'active' : '' }}">
        <span class="sidebar-short">AP</span><span class="sidebar-label">Appointments</span>
      </a>
      <a href="{{ $inventoryUrl }}" class="{{ (Request::is('admin/inventory*') || Request::is('assistant/inventory*')) ? 'active' : '' }}">
        <span class="sidebar-short">IN</span><span class="sidebar-label">Inventory</span>
      </a>
      <a href="{{ $reportsUrl }}" class="{{ (Request::is('admin/reports*') || Request::is('assistant/reports*')) ? 'active' : '' }}">
        <span class="sidebar-short">RP</span><span class="sidebar-label">Reports</span>
      </a>
      <a href="{{ $apiTestingUrl }}" class="{{ (Request::is('admin/api-testing*') || Request::is('assistant/api-testing*')) ? 'active' : '' }}">
        <span class="sidebar-short">FT</span><span class="sidebar-label">For API Testing</span>
      </a>
      <a href="{{ $walkinUrl }}" class="{{ (Request::is('admin/walkin*') || Request::is('assistant/walkin*')) ? 'active' : '' }}">
        <span class="sidebar-short">WK</span><span class="sidebar-label">Walk-in</span>
      </a>
      <a href="{{ route('admin.health_records') }}" class="{{ Request::is('admin/health-records*') ? 'active' : '' }}">
    <span class="sidebar-short">HR</span>
    <span class="sidebar-label">Student Health Form</span>
    </a>
      @if($isAdminLike)
          <a href="{{ $assistantAccountsUrl }}" class="{{ Request::is('admin/student-assistants*') ? 'active' : '' }}">
            <span class="sidebar-short">SA</span><span class="sidebar-label">Student Assistants</span>
          </a>
          <a href="{{ route('admin.logs') }}" class="{{ Request::is('admin/activity-logs*') ? 'active' : '' }}">
            <span class="sidebar-short">LG</span><span class="sidebar-label">Audit Trail</span>
          </a>
          <a href="{{ $settingsUrl }}" class="{{ Request::is('admin/settings*') ? 'active' : '' }}">
            <span class="sidebar-short">ST</span><span class="sidebar-label">Settings</span>
          </a>
      @endif
    </nav>

    <div class="sidebar-logout">
      <a href="#" onclick="event.preventDefault(); document.getElementById('layoutLogoutForm').submit();">
        <span class="sidebar-short">LO</span><span class="sidebar-label">Logout</span>
      </a>
    </div>
  </aside>

    <main class="main">
        @yield('content')
    </main>

</div>

<form id="layoutLogoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
</form>

<section id="assistantPanel" class="assistant-panel" aria-live="polite">
    <div class="assistant-head">
        <div>
            <p class="assistant-head-title">Clinic AI Assistant</p>
            <p class="assistant-head-sub">Voice commands and basic clinical triage guidance</p>
        </div>
        <button type="button" class="assistant-close" aria-label="Close assistant" onclick="closeAssistantPanel()">x</button>
    </div>

    <div id="assistantMessages" class="assistant-messages">
        <div class="assistant-bubble assistant">Try: "generate MAR", "open appointments", or ask a symptom question.</div>
    </div>

    <div class="assistant-controls">
        <button type="button" id="assistantMicBtn" class="assistant-mic">Mic</button>
        <input type="text" id="assistantInput" class="assistant-input" placeholder="Type command or question..." maxlength="500">
        <button type="button" id="assistantSendBtn" class="assistant-send">Send</button>
    </div>

    <p class="assistant-note">Medical responses are for initial triage support only, not a confirmed diagnosis. For emergencies, call local emergency services immediately.</p>
</section>

@include('partials.post_login_terms_gate')
@include('partials.student_voice_input_support')

@stack('scripts')

<script>
    const assistantEndpoint = @json($assistantEndpoint);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function toggleSidebar() {
        document.body.classList.toggle('sidebar-open');
    }

    function applyAdminTheme(theme) {
        const normalizedTheme = theme === 'light' ? 'light' : 'dark';
        const toggle = document.getElementById('adminThemeToggle');
        const moonIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79z"></path></svg>';
        const sunIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>';

        document.documentElement.setAttribute('data-theme', normalizedTheme);

        if (!toggle) {
            return;
        }

        const isDark = normalizedTheme === 'dark';
        toggle.innerHTML = isDark ? moonIcon : sunIcon;
        toggle.setAttribute('aria-label', isDark ? 'Dark mode enabled' : 'Light mode enabled');
        toggle.setAttribute('title', isDark ? 'Dark mode' : 'Light mode');
        toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
    }

    function initThemeToggle() {
        const toggle = document.getElementById('adminThemeToggle');
        const storageKey = 'admin_theme';
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';

        applyAdminTheme(currentTheme);

        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', function () {
            const activeTheme = document.documentElement.getAttribute('data-theme');
            const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';
            applyAdminTheme(nextTheme);
            try {
                localStorage.setItem(storageKey, nextTheme);
            } catch (error) {
                console.warn('Theme preference was not saved.', error);
            }
        });
    }

    function initAccessibilityLaunch() {
        const launchButton = document.getElementById('adminAccessibilityLaunch');
        forceAccessibilityButtonTheme();
        if (!launchButton) {
            return;
        }

        function findSiennaTrigger() {
            const selectorMatches = [
                '#sienna-accessibility-button',
                '.sienna-accessibility-button',
                '.sienna-accessibility-trigger',
                '[data-sienna-accessibility-trigger]',
                'button[aria-label*="accessibility" i]:not(#adminAccessibilityLaunch)',
                'button[title*="accessibility" i]:not(#adminAccessibilityLaunch)',
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
                    if (element.id === 'adminAccessibilityLaunch') {
                        return false;
                    }

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

        function hideSiennaTrigger() {
            const trigger = findSiennaTrigger();
            if (!trigger) {
                return;
            }

            trigger.style.position = 'fixed';
            trigger.style.left = '-9999px';
            trigger.style.opacity = '0';
            trigger.style.pointerEvents = 'none';
            trigger.setAttribute('aria-hidden', 'true');
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

        launchButton.addEventListener('click', function () {
            const trigger = findSiennaTrigger();
            if (!trigger) {
                console.warn('Accessibility widget trigger not found yet.');
                return;
            }

            trigger.click();
        });

        hideSiennaTrigger();
        themeSiennaMenu();
        injectSiennaShadowStyles();
        forceAccessibilityButtonTheme();

        const observer = new MutationObserver(function () {
            hideSiennaTrigger();
            themeSiennaMenu();
            injectSiennaShadowStyles();
            forceAccessibilityButtonTheme();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    function toggleProfileMenu() {
        const menu = document.getElementById('profileDropdown');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    function toggleAssistantPanel() {
        const panel = document.getElementById('assistantPanel');
        panel.classList.toggle('open');
    }

    function closeAssistantPanel() {
        const panel = document.getElementById('assistantPanel');
        panel.classList.remove('open');
    }

    function appendAssistantMessage(role, text) {
        const messages = document.getElementById('assistantMessages');
        if (!messages) return;
        const bubble = document.createElement('div');
        bubble.className = 'assistant-bubble ' + (role === 'user' ? 'user' : 'assistant');
        bubble.textContent = text;
        messages.appendChild(bubble);
        messages.scrollTop = messages.scrollHeight;
    }

    async function sendAssistantQuery(rawText) {
        const text = (rawText || '').trim();
        if (!text) return;

        appendAssistantMessage('user', text);

        try {
            const response = await fetch(assistantEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ text })
            });

            if (!response.ok) {
                throw new Error('Assistant request failed');
            }

            const payload = await response.json();
            if (payload.message) {
                appendAssistantMessage('assistant', payload.message);
            }

            if (payload.type === 'action' && payload.action?.kind === 'redirect' && payload.action?.url) {
                setTimeout(function () {
                    window.location.href = payload.action.url;
                }, 650);
            }
        } catch (error) {
            appendAssistantMessage('assistant', 'Unable to process right now. Please try again.');
        }
    }

    function initAssistantUi() {
        const panel = document.getElementById('assistantPanel');
        const micBtn = document.getElementById('assistantMicBtn');
        const sendBtn = document.getElementById('assistantSendBtn');
        const input = document.getElementById('assistantInput');

        if (!panel || !sendBtn || !input || !micBtn) return;

        sendBtn.addEventListener('click', function () {
            const value = input.value;
            input.value = '';
            sendAssistantQuery(value);
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const value = input.value;
                input.value = '';
                sendAssistantQuery(value);
            }
        });

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            micBtn.disabled = true;
            micBtn.title = 'Voice recognition is not supported in this browser.';
            return;
        }

        let isListening = false;
        const recognition = new SpeechRecognition();
        recognition.lang = 'en-US';
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        recognition.onstart = function () {
            isListening = true;
            micBtn.classList.add('listening');
            micBtn.textContent = 'Listening';
        };

        recognition.onend = function () {
            isListening = false;
            micBtn.classList.remove('listening');
            micBtn.textContent = 'Mic';
        };

        recognition.onerror = function () {
            appendAssistantMessage('assistant', 'Mic capture failed. You can type your command instead.');
        };

        recognition.onresult = function (event) {
            const transcript = event.results?.[0]?.[0]?.transcript || '';
            if (transcript) {
                sendAssistantQuery(transcript);
            }
        };

        micBtn.addEventListener('click', function () {
            if (isListening) {
                recognition.stop();
                return;
            }
            panel.classList.add('open');
            recognition.start();
        });
    }

    function forceAccessibilityButtonTheme() {
        document.querySelectorAll('.asw-menu-btn').forEach(function (button) {
            button.style.setProperty('background', '#800000', 'important');
            button.style.setProperty('background-image', 'none', 'important');
            button.style.setProperty('border', '2px solid #5f0012', 'important');
            button.style.setProperty('outline', 'none', 'important');
            button.style.setProperty('box-shadow', '0 10px 24px rgba(128, 0, 0, 0.28)', 'important');
            button.querySelectorAll('svg').forEach(function (icon) {
                icon.style.setProperty('fill', '#ffffff', 'important');
                icon.style.setProperty('stroke', 'none', 'important');
                icon.style.setProperty('background', 'transparent', 'important');
            });
            button.querySelectorAll('svg path:not([fill="none"])').forEach(function (path) {
                path.style.setProperty('fill', '#ffffff', 'important');
                path.style.setProperty('stroke', 'none', 'important');
            });
            button.querySelectorAll('svg path[fill="none"]').forEach(function (path) {
                path.style.setProperty('stroke', 'none', 'important');
            });
        });
    }

    document.addEventListener('click', function (event) {
        const menu = document.getElementById('profileDropdown');
        const trigger = document.querySelector('.admin-user');
        const sidebar = document.getElementById('adminSidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const panel = document.getElementById('assistantPanel');
        const assistantLaunch = document.getElementById('assistantLaunchBtn');

        if (menu.style.display === 'block' && trigger && !menu.contains(event.target) && !trigger.contains(event.target)) {
            menu.style.display = 'none';
        }

        if (
            document.body.classList.contains('sidebar-open') &&
            sidebar &&
            sidebarToggle &&
            !sidebar.contains(event.target) &&
            !sidebarToggle.contains(event.target)
        ) {
            document.body.classList.remove('sidebar-open');
        }

        if (
            panel &&
            panel.classList.contains('open') &&
            assistantLaunch &&
            !panel.contains(event.target) &&
            !assistantLaunch.contains(event.target)
        ) {
            panel.classList.remove('open');
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 860) {
            document.body.classList.remove('sidebar-open');
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        initAssistantUi();
        initThemeToggle();
        initAccessibilityLaunch();
    });
</script>

</body>
</html>
