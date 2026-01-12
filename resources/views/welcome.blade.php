<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PUP Taguig - Online Clinic</title>
    <style>
        /* --- 1. GLOBAL RESET & VARIABLES --- */
        :root {
            --accent: #8B0000;      /* PUP Maroon */
            --accent-dark: #600000;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --text-dark: #20343a;
            --text-light: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        
        body {
            /* --- BACKGROUND IMAGE FIX --- */
            background: linear-gradient(rgba(15, 27, 38, 0.75), rgba(15, 27, 38, 0.85)), 
                        url('{{ asset("images/PUPBG.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            color: var(--white);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- 2. LOGO HEADER (Maroon Strip) --- */
        .logo-header {
            background-color: var(--accent);
            padding: 16px 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 10;
        }
        
        .logo-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.2);
            object-fit: cover;
        }
        
        .logo-text { color: var(--white); line-height: 1.1; }
        .logo-title { font-weight: 800; font-size: 18px; letter-spacing: 0.5px; }
        .logo-subtitle { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }

        /* --- 3. MAIN CONTENT (Centered) --- */
        .lp-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .lp-hero { margin-bottom: 48px; max-width: 680px; animation: fadeIn 0.8s ease-out; }
        
        .lp-hero h1 { 
            font-size: 42px; 
            font-weight: 800; 
            color: var(--white); 
            margin-bottom: 16px; 
            text-shadow: 0 4px 12px rgba(0,0,0,0.4); 
            letter-spacing: -0.5px;
        }
        
        .lp-sub { 
            font-size: 18px; 
            color: #e2e8f0; 
            line-height: 1.6; 
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        /* --- 4. ROLE SELECTION CARDS --- */
        .role-grid {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
            justify-content: center;
            animation: slideUp 0.8s ease-out;
        }

        /* The Card Buttons */
        .role-card {
            background: var(--glass-bg);
            border-radius: 20px;
            padding: 40px 32px;
            width: 280px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            border: 1px solid rgba(255,255,255,0.4);
            position: relative;
            overflow: hidden;
        }

        /* Hover Effect: Lift and Glow - UPDATED TO MAROON */
        .role-card:hover {
            transform: translateY(-12px);
            background: var(--accent); /* Change to Maroon */
            box-shadow: 0 20px 50px rgba(139, 0, 0, 0.4); /* Reddish shadow */
            border-color: var(--accent);
        }
        
        /* Change text color to white on hover */
        .role-card:hover .card-title,
        .role-card:hover .card-sub {
            color: #ffffff;
        }

        /* Change icon background to blend in on hover */
        .role-card:hover .card-icon {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        
        .role-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: var(--accent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .role-card:hover::before { opacity: 1; }

        .card-icon { 
            font-size: 48px; 
            margin-bottom: 8px; 
            background: #f1f5f9;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .card-title { font-size: 22px; font-weight: 800; color: var(--text-dark); transition: color 0.3s; }
        .card-sub { font-size: 14px; color: var(--text-light); line-height: 1.5; transition: color 0.3s; }

        /* --- 5. FOOTER --- */
        .lp-foot {
            text-align: center;
            padding: 24px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            border-top: none;
            margin-bottom: 12px;
        }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Responsive */
        @media (max-width: 600px) {
            .lp-hero h1 { font-size: 32px; }
            .role-grid { gap: 16px; }
            .role-card { width: 100%; max-width: 320px; }
        }
    </style>
</head>
<body>

  <div class="logo-header">
    <div class="logo-brand">
      <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo" class="logo-icon">
      <div class="logo-text">
        <div class="logo-title">PUP TAGUIG</div>
        <div class="logo-subtitle">ONLINE CLINIC</div>
      </div>
    </div>
  </div>

  <main class="lp-container">
    <header class="lp-hero">
      <h1>Welcome to the PUP TAGUIG Online Clinic</h1>
      <p class="lp-sub">Your holistic health partner. Select your portal below to access services, book consultations, and manage records.</p>
    </header>

    <section class="role-section">
      <div class="role-grid">
        
        <a href="{{ url('/student/home') }}" class="role-card">
          <div class="card-icon">🎓</div>
          <div class="card-body">
            <div class="card-title">Student Portal</div>
            <div class="card-sub">Book appointments, view medical history, and check request status.</div>
          </div>
        </a>

        <a href="{{ url('/admin/dashboard') }}" class="role-card">
          <div class="card-icon">🩺</div>
          <div class="card-body">
            <div class="card-title">Faculty / Admin</div>
            <div class="card-sub">Manage clinic schedules, approve requests, and maintain patient records.</div>
          </div>
        </a>

      </div>
    </section>
  </main>

  <footer class="lp-foot">© <span id="year">2026</span> PUP Taguig — Online Clinic System</footer>

</body>
</html>