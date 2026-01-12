<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - PUPT Admin</title>
    
    <style>
        :root{
            --accent:#ff5a4d;
            --bg:#f3f6f5;
            --dark:#0f1b26; 
            --pup-maroon: #70131B;
            --text:#243437;
            --card:#ffffff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial;
            background: var(--bg);
            color: var(--text);
            /* --- 1. FREEZE THE PAGE --- */
            height: 100vh; 
            overflow: hidden; 
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER (Fixed at Top) --- */
        .admin-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-shrink: 0; /* Prevents header from shrinking */
            z-index: 100;
        }

        .admin-user { display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%; background: var(--accent);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px;
        }

        /* --- DROPDOWN MENU STYLES (NEW) --- */
        .profile-dropdown {
            display: none; /* Hidden by default */
            position: absolute;
            top: 50px;
            right: 0;
            background: white;
            width: 180px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            z-index: 1000;
        }

        .profile-dropdown a {
            display: block;
            padding: 12px 20px;
            color: #334155;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
            border-bottom: 1px solid #f8fafc;
        }

        .profile-dropdown a:hover { background: #f8fafc; color: var(--pup-maroon); }
        .profile-dropdown a.logout-link { color: #ef4444; font-weight: 600; border-bottom: none; }
        .profile-dropdown a.logout-link:hover { background: #fee2e2; }


        /* --- LAYOUT CONTAINER --- */
        .admin-layout {
            display: flex;
            flex: 1; /* Takes up all remaining height */
            overflow: hidden; /* Keeps sidebar and main inside */
        }

        /* --- SIDEBAR (Fixed Left) --- */
        .sidebar {
            width: 240px;
            background: var(--pup-maroon);
            color: #ffffff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0; /* Prevents sidebar from shrinking */
            overflow-y: auto; /* Allows sidebar to scroll if menu is too long */
        }
        
        /* Sidebar Logo */
        .sidebar-logo {
            display: flex; align-items: center; gap: 12px; margin-bottom: 24px;
            padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .sidebar-logo img { width: 40px; height: 40px; border-radius: 50%; background: #fff; padding: 2px; }
        .sidebar-logo-title { font-weight: 800; font-size: 14px; margin: 0; line-height: 1.2; }
        .sidebar-logo-sub { color: #e0e0e0; font-size: 11px; margin: 2px 0 0; }
        
        /* Sidebar Menu */
        .sidebar h4 { color: #ffc107; margin: 16px 0 12px; font-size: 11px; letter-spacing: 0.8px; text-transform: uppercase; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px; padding: 10px 12px;
            border-radius: 8px; color: #f0f0f0; text-decoration: none;
            margin-bottom: 6px; font-weight: 600; transition: all 0.2s ease;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.15); color: #fff; }
        .sidebar-icon { font-size: 18px; width: 24px; text-align: center; }

        /* --- MAIN CONTENT (SCROLLABLE) --- */
        .main {
            flex: 1; /* Takes remaining width */
            padding: 28px;
            overflow-y: auto; /* --- 2. ONLY THIS PART SCROLLS --- */
            background: var(--bg);
        }

        /* --- COMMON UTILS --- */
        .card { background: var(--card); border-radius: 14px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .btn { padding: 8px 14px; border-radius: 6px; border: none; font-weight: 700; cursor: pointer; }
        
        /* Inject Page Specific Styles */
        @stack('styles')
    </style>
</head>
<body>

<header class="admin-header">
  <div class="header-left">
    <div style="font-size: 20px; font-weight: 800; color: #20343a; letter-spacing: -0.5px; display: flex; align-items: center; gap: 8px;">
        Welcome, <span style="color: #8B0000;">Nurse Joyce!</span> 👋
    </div>
    <div style="font-size: 12px; color: #64748b; font-weight: 500;">
        Have a great day at the clinic.
    </div>
  </div>

  <div style="position: relative;">
      
      <div class="admin-user" onclick="toggleProfileMenu()">
        <div style="text-align: right; margin-right: 8px;">
            <div style="font-size:14px; font-weight:700; color: #333;">Nurse Joyce</div>
            <div style="font-size: 11px; color: #64748b;">Admin</div>
        </div>
        <div class="user-avatar" style="background: #8B0000;">J</div>
      </div>

      <div id="profileDropdown" class="profile-dropdown">
        <a href="#">👤 Edit Profile</a>
        <a href="#">⚙️ Settings</a>
        <a href="{{ url('/') }}" class="logout-link">🚪 Logout</a>
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

@stack('scripts')

<script>
    function toggleProfileMenu() {
        const menu = document.getElementById('profileDropdown');
        if (menu.style.display === 'block') {
            menu.style.display = 'none';
        } else {
            menu.style.display = 'block';
        }
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('profileDropdown');
        const trigger = document.querySelector('.admin-user');
        
        if (menu.style.display === 'block' && !menu.contains(event.target) && !trigger.contains(event.target)) {
            menu.style.display = 'none';
        }
    });
</script>

</body>
</html>