<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emergency Backup Login | PUP Taguig Clinic</title>
    <style>
        :root {
            --accent: #8B0000;
            --accent-dark: #600000;
            --accent-gold: #facc15;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --text-dark: #20343a;
            --text-light: #64748b;
            --error-bg: #fee2e2;
            --error-text: #b91c1c;
            --warn-bg: #fff7ed;
            --warn-text: #9a3412;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background:
                linear-gradient(rgba(15, 27, 38, 0.76), rgba(15, 27, 38, 0.86)),
                url('{{ asset("images/PUPBG.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            color: var(--white);
        }
        .logo-header {
            background: var(--accent);
            padding: 16px 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 10;
        }
        .logo-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon { width: 48px; height: 48px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2); object-fit: cover; }
        .logo-text { color: var(--white); line-height: 1.1; }
        .logo-title { font-weight: 800; font-size: 18px; letter-spacing: 0.5px; }
        .logo-subtitle { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
        .lp-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .login-box {
            width: 100%;
            max-width: 440px;
            background: var(--glass-bg);
            border-radius: 20px;
            padding: 38px;
            color: var(--text-dark);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }
        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(139, 0, 0, 0.08);
            color: var(--accent);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 16px;
        }
        .login-box h1 {
            color: var(--accent);
            font-size: 28px;
            line-height: 1.1;
            margin-bottom: 10px;
        }
        .login-box p {
            color: var(--text-light);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 22px;
        }
        .alert-error,
        .alert-warn {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
            line-height: 1.5;
            border: 1px solid transparent;
        }
        .alert-error { background: var(--error-bg); color: var(--error-text); border-color: #fecaca; }
        .alert-warn { background: var(--warn-bg); color: var(--warn-text); border-color: #fed7aa; }
        .form-group { margin-bottom: 14px; text-align: left; }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.08);
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            border: none;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .btn-submit:hover {
            background: var(--accent-dark);
            transform: translateY(-1px);
        }
        .emergency-note {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid rgba(148, 163, 184, 0.22);
            color: var(--text-light);
            font-size: 12px;
            line-height: 1.6;
        }
        .emergency-note strong {
            display: block;
            color: #7f1d1d;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 11px;
        }
        .lp-foot {
            background: rgba(17, 24, 39, 0.92);
            border-top: 2px solid var(--accent);
            text-align: center;
            padding: 14px 16px;
            font-size: 13px;
            color: #f8fafc;
        }
        @media (max-width: 640px) {
            .login-box { padding: 28px 20px; }
            .login-box h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <header class="logo-header">
        <a href="{{ url('/') }}" class="logo-brand" aria-label="Clinic home">
            <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Logo" class="logo-icon">
            <div class="logo-text">
                <div class="logo-title">PUP Taguig Clinic Management System</div>
                <div class="logo-subtitle">Emergency Backup Access</div>
            </div>
        </a>
    </header>

    <main class="lp-container">
        <section class="login-box" aria-labelledby="emergency-login-title">
            <div class="login-badge">Local Fallback</div>
            <h1 id="emergency-login-title">Emergency Login</h1>
            <p>Use this only when the external IdP or One Portal is unavailable. Access is limited to the configured clinic admin or nurse emergency account.</p>

            @if($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('status'))
                <div class="alert-warn">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('system-admin.emergency-login.submit') }}" autocomplete="off">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn-submit">Sign In</button>
            </form>

            <div class="emergency-note">
                <strong>Emergency use only</strong>
                This page is intentionally unlinked from the normal navigation and should stay hidden.
            </div>
        </section>
    </main>

    <footer class="lp-foot">
        PUP Taguig Clinic Management System
    </footer>
</body>
</html>
