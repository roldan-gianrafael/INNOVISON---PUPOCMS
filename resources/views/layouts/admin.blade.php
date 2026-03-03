<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PUPT Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f4f6f8;
            --bg-grad-1: #f8fafc;
            --bg-grad-2: #f2f4f7;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --stroke: #e4e8ef;
            --stroke-strong: #d5dbe5;
            --text: #111827;
            --muted: #6b7280;
            --pup-maroon: #70131B;
            --pup-maroon-dark: #5a0f16;
            --pup-maroon-soft: #f9edef;
            --danger: #d83a52;
            --shadow-soft: 0 10px 28px rgba(15, 23, 42, 0.06);
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
                radial-gradient(circle at 0% -20%, var(--bg-grad-1) 0%, transparent 50%),
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
            background: rgba(244, 246, 248, 0.88);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--stroke);
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
            color: var(--pup-maroon);
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
            border: 1px solid var(--stroke);
            background: var(--surface);
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
            border-color: var(--stroke-strong);
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
            background: var(--surface);
            width: 190px;
            box-shadow: var(--shadow-soft);
            border-radius: 12px;
            border: 1px solid var(--stroke);
            overflow: hidden;
            z-index: 1000;
        }

        .profile-dropdown a {
            display: block;
            padding: 12px 16px;
            color: #1f2937;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s ease, color 0.2s ease;
            border-bottom: 1px solid var(--surface-soft);
        }

        .profile-dropdown a:hover {
            background: var(--surface-soft);
            color: var(--pup-maroon);
        }

        .profile-dropdown a.logout-link {
            color: var(--danger);
            border-bottom: none;
        }

        .profile-dropdown a.logout-link:hover {
            background: #fff1f4;
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
            width: 92px;
            background: linear-gradient(180deg, #771822 0%, #631018 100%);
            border: 1px solid rgba(255, 255, 255, 0.14);
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
            width: 270px;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            min-width: 220px;
        }

        .sidebar-logo img {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid rgba(255, 255, 255, 0.35);
            object-fit: cover;
            padding: 4px;
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
            color: rgba(255, 255, 255, 0.75);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .sidebar-logo-text {
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .sidebar h4 {
            color: rgba(255, 255, 255, 0.72);
            margin: 0 0 12px;
            font-size: 10px;
            letter-spacing: 0.12em;
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
            padding: 11px 12px;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.01em;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
            min-width: 220px;
            white-space: nowrap;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.13);
            border-color: rgba(255, 255, 255, 0.24);
            transform: translateX(1px);
        }

        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.34);
            color: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
        }

        .sidebar-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.34);
            background: rgba(255, 255, 255, 0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            flex-shrink: 0;
            letter-spacing: 0.04em;
            color: #ffffff;
        }

        .sidebar-nav a.active .sidebar-icon {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.2);
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
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar-logout a {
            margin-bottom: 0;
            color: rgba(255, 255, 255, 0.95);
        }

        .sidebar-logout .sidebar-icon {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.15);
        }

        .main {
            flex: 1;
            min-width: 0;
            overflow-y: auto;
            padding: 2px;
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
            background: #d6dce5;
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
        .main :where(.card, .panel, .report-card, .stat-card-mini, .modal-box) {
            border: 1px solid #eadde1 !important;
            border-radius: 14px !important;
            box-shadow: 0 8px 24px rgba(112, 19, 27, 0.06) !important;
        }

        .main :where(h2, h3) {
            color: #4b0f17;
            letter-spacing: -0.01em;
        }

        .main :where(a) {
            color: #70131B;
        }

        .main :where(a:hover) {
            color: #5a0f16;
        }

        .main :where(table th) {
            border-bottom-color: #eddde2 !important;
            color: #6b7280 !important;
            letter-spacing: 0.06em;
        }

        .main :where(table td) {
            border-bottom-color: #f4ebee !important;
        }

        .main :where(.form-control, .form-input, .input-month, input, select, textarea) {
            border-color: #dcc9ce !important;
            border-radius: 10px !important;
            background: #ffffff;
        }

        .main :where(.form-control, .form-input, .input-month, input, select, textarea):focus {
            outline: none;
            border-color: #70131B !important;
            box-shadow: 0 0 0 3px rgba(112, 19, 27, 0.15);
        }

        .main :where(.btn-save, .btn-add, .btn-add-walkin, .btn-filter, .btn-change, .btn-generate, .btn-primary, .btn-complete) {
            background: #70131B !important;
            color: #ffffff !important;
            border: 1px solid #70131B !important;
        }

        .main :where(.btn-save:hover, .btn-add:hover, .btn-add-walkin:hover, .btn-filter:hover, .btn-change:hover, .btn-generate:hover, .btn-primary:hover, .btn-complete:hover) {
            background: #5a0f16 !important;
            border-color: #5a0f16 !important;
            color: #ffffff !important;
        }

        .main :where(.btn-edit, .btn-view, .btn-outline) {
            background: #fff3f5 !important;
            color: #70131B !important;
            border: 1px solid #f1d8dd !important;
        }

        .main :where(.btn-edit:hover, .btn-view:hover, .btn-outline:hover) {
            background: #fbe8ed !important;
        }

        .main :where(.btn-delete, .btn-cancel) {
            background: #fff1f4 !important;
            color: #b42339 !important;
            border: 1px solid #f8c7d2 !important;
        }

        .main :where(.status.completed) {
            background: #f9eaed !important;
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
            border: 1px solid #e9d9de;
            background: #fff3f6;
            color: #70131B;
            border-radius: 12px;
            padding: 9px 12px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            letter-spacing: 0.03em;
        }

        .assistant-launch:hover {
            background: #fbe8ee;
            border-color: #e8cdd5;
        }

        .assistant-panel {
            position: fixed;
            right: 20px;
            bottom: 18px;
            width: min(420px, calc(100vw - 24px));
            background: #ffffff;
            border: 1px solid #e6d5db;
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
            background: #fff;
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
            background: #fff3f6;
            border-color: #ead2d9;
            color: #70131B;
        }

        .assistant-bubble.assistant {
            background: #f9fafb;
            border-color: #e5e7eb;
            color: #1f2937;
        }

        .assistant-controls {
            border-top: 1px solid #eee2e5;
            padding: 10px;
            display: flex;
            gap: 8px;
            align-items: center;
            background: #fff;
        }

        .assistant-mic,
        .assistant-send {
            border: 1px solid #e4ccd3;
            background: #fff3f6;
            color: #70131B;
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
            border: 1px solid #dcc9ce;
            border-radius: 10px;
            padding: 9px 10px;
            font-size: 13px;
            color: #111827;
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
            color: #6b7280;
            border-top: 1px solid #f3e8eb;
            background: #fffdfd;
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

<header class="admin-header">
    <div class="header-left">
        <p class="header-kicker">Clinic Administration</p>
        <h1 class="header-title">Welcome back, <span>Nurse Joyce</span></h1>
        <p class="header-subtitle">Monitor operations and patient flow in one clear workspace.</p>
    </div>

    <div class="header-right">
        <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar" onclick="toggleSidebar()">&#9776;</button>
        <button type="button" class="assistant-launch" id="assistantLaunchBtn" onclick="toggleAssistantPanel()">AI Assistant</button>

        <div class="profile-wrap">
            <button type="button" class="admin-user" onclick="toggleProfileMenu()">
                <div class="admin-user-meta">
                    <div class="admin-user-name">Nurse Joyce</div>
                    <div class="admin-user-role">Admin</div>
                </div>
                <div class="user-avatar">J</div>
            </button>

            <div id="profileDropdown" class="profile-dropdown">
                <a href="#">Edit Profile</a>
                <a href="#">Settings</a>
                <a href="{{ url('/') }}" class="logout-link">Logout</a>
            </div>
        </div>
    </div>
</header>

<div class="admin-layout">
  
  <aside class="sidebar">
    <div class="sidebar-logo">
      <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo">
      <div class="sidebar-logo-text">
        <div class="sidebar-logo-title">PUP TAGUIG</div>
        <div class="sidebar-logo-sub">Clinic Admin</div>
      </div>
    </div>
    
    <h4>Main Menu</h4>
    <nav class="sidebar-nav">
      <a href="{{ url('/admin/dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
        <span class="sidebar-icon">🏠</span> Dashboard
      </a>
      <a href="{{ url('/admin/appointments') }}" class="{{ Request::is('admin/appointments') ? 'active' : '' }}">
        <span class="sidebar-icon">📅</span> Appointments
      </a>
      <a href="{{ url('/admin/inventory') }}" class="{{ Request::is('admin/inventory') ? 'active' : '' }}">
        <span class="sidebar-icon">📦</span> Inventory
      </a>
      <a href="{{ url('/admin/reports') }}" class="{{ Request::is('admin/reports') ? 'active' : '' }}">
        <span class="sidebar-icon">📊</span> Reports
      </a>
      <a href="{{ url('/admin/settings') }}" class="{{ Request::is('admin/settings') ? 'active' : '' }}">
        <span class="sidebar-icon">⚙️</span> Settings
      </a>
      

     

      <a href="{{ url('/') }}" style="margin-top: 40px; background: rgba(0,0,0,0.2);">
        <span class="sidebar-icon">🚪</span> Logout
      </a>
    </nav>
  </aside>

    <main class="main">
        @yield('content')
    </main>

</div>

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

@stack('scripts')

<script>
    const assistantEndpoint = @json(route('admin.assistant.intent'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function toggleSidebar() {
        document.body.classList.toggle('sidebar-open');
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
    });
</script>

</body>
</html>
