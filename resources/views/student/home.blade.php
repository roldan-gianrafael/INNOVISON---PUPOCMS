@extends('layouts.student')

@section('title', 'Home')

@push('styles')
<style>
    /* --- CRITICAL FIX: REMOVE TOP PADDING FOR HOME PAGE ONLY --- */
    main { padding-top: 0 !important; }

    /* --- HERO SECTION STYLES --- */
    .PUPBG { position:relative; min-height:520px; display:flex; align-items:center; }
    .PUPBG::before {
        content:""; position:absolute; inset:0;
        /* Background Image with Overlay */
        background: linear-gradient(180deg,rgba(15,27,38,0.72),rgba(15,27,38,0.72)), url('{{ asset("images/PUPBG.jpg") }}') center/cover no-repeat;
        z-index:0;
    }
    .PUPBG-overlay { position:absolute; inset:0; background:linear-gradient(180deg,rgba(0,0,0,0.15),rgba(0,0,0,0.45)); z-index:1; }
    .PUPBG-inner { position:relative; z-index:2; padding:90px 0; color:#fff; text-align:center; }
    
    .kicker { letter-spacing:2px; font-weight:700; margin:0 0 12px 0; opacity:0.95; color:#ff7b73; }
    .PUPBG-title { font-size:56px; margin:6px 0 18px 0; line-height:1.05; font-weight:800; text-shadow:0 2px 10px rgba(0,0,0,0.5); color: #fff; }
    .PUPBG-lead { color:#bfcfd6; margin:0 auto 28px; max-width:720px; font-size:18px; }

    .hero-actions { display:flex; gap:16px; justify-content:center; margin-top:18px; }
    .btn { display:inline-block; padding:12px 24px; border-radius:8px; text-decoration:none; font-weight:700; cursor: pointer; border: none; }
    .btn-primary { background:#8B0000; color:#fff; border:2px solid rgba(0,0,0,0.08); box-shadow:0 6px 18px rgba(0,0,0,0.25); }
    .btn-secondary { background:#fff; color:#15222a; border:0; }

    /* --- WELCOME SECTION --- */
    .welcome { padding:64px 0; background:#fff; }
    .welcome-inner { display:flex; gap:40px; align-items:center; }
    .welcome-text { flex:1; }
    .welcome-text h2 { margin-top:0; font-size: 28px; color: #2d3748; }
    .welcome-text p { color:#666; line-height:1.6; }
    .btn-outline { display:inline-block; padding:10px 14px; border-radius:8px; border:1px solid #8B0000; color:#8B0000; text-decoration:none; margin-top:14px; }
    .welcome-art { width:320px; flex:0 0 320px; }

    /* --- TESTIMONIALS / COMMENTS --- */
    .comments-section { padding:56px 0; background:#f3f6f5; }
    .comments-section .section-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:22px; }
    .comments-section h3 { margin:0; font-size:22px; color: #2d3748; }
    .comments-section p.lead { margin:6px 0 0; color:#5b6d70; }
    
    .comments-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-top:18px; }
    .comment-card { background:#ffffff; padding:18px; border-radius:12px; box-shadow:0 8px 28px rgba(16,24,28,0.06); display:flex; gap:12px; align-items:flex-start; transition:transform 220ms ease; }
    .comment-card:hover { transform:translateY(-8px); box-shadow:0 20px 48px rgba(16,24,28,0.14); }
    
    .avatar { width:56px; height:56px; border-radius:50%; flex:0 0 56px; overflow:hidden; fill: #cbd5e0; }
    .comment-body { flex:1; }
    .comment-body h4 { margin:0; font-size:15px; color: #2d3748; }
    .comment-meta { color:#7f8b8e; font-size:13px; font-weight: normal; }
    .comment-body p { margin:10px 0 0; color:#345; line-height:1.5; font-size: 14px; }
    .comment-footer { display:flex; align-items:center; gap:8px; margin-top:12px; }
    .comment-chip { background:rgba(15,27,38,0.04); padding:6px 10px; border-radius:999px; font-size:13px; color:#334; }

    /* --- FOOTER STYLES --- */
    .site-footer { background:#0f1b26; color:#c9d6df; padding:48px 0 20px; font-size:15px; margin-top: 0; }
    .footer-grid { display:grid; grid-template-columns:1.4fr 1fr 1fr 1fr; gap:32px; align-items:start; padding-bottom:18px; }
    
    .brand { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .brand-logo img { width:56px; height:56px; border-radius:50%; object-fit:cover; border: 2px solid #8B0000; }
    .brand-name { font-weight:700; color:#fff; }
    .brand-sub { display:block; font-size:12px; color:#91a0ad; font-weight:600; }
    .brand-desc { color:#9fb0bd; max-width:420px; line-height:1.6; margin:12px 0; }
    
    .social { display:flex; gap:10px; margin-top:8px; }
    .social-link svg { width:18px; height:18px; fill:rgba(255,255,255,0.92); }

    .site-footer h4 { color:#fff; margin:0 0 12px 0; font-size:16px; }
    .footer-links { list-style:none; padding:0; margin:0; }
    .footer-links li { margin:10px 0; color:#b9c8d2; }
    .footer-links a { color:#b9c8d2; text-decoration:none; }
    .footer-links a:hover { color:#fff; text-decoration:underline; }

    .contact-list { list-style:none; padding:0; margin:0; }
    .contact-list li { display:flex; align-items:flex-start; gap:12px; color:#b9c8d2; margin:12px 0; line-height:1.4; }
    .contact-icon { width:20px; height:20px; fill:#ffb3a9; flex:0 0 20px; margin-top:4px; }
    .footer-bottom { border-top:1px solid rgba(255,255,255,0.04); padding-top:22px; text-align:center; color:rgba(255,255,255,0.6); font-size:14px; margin-top:18px; }

    /* Responsive */
    @media (max-width:900px){
        .comments-grid { grid-template-columns:repeat(2,1fr); }
        .footer-grid { grid-template-columns:repeat(2,1fr); }
        .PUPBG-title { font-size:36px; }
    }
    @media (max-width:600px){
        .comments-grid { grid-template-columns:1fr; }
        .footer-grid { grid-template-columns:1fr; }
    }
</style>
@endpush

@section('content')
    <svg style="display: none;">
      <symbol id="avatar-placeholder" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="12" fill="#e2e8f0"/>
        <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-5.33 0-8 2.67-8 4v2h16v-2c0-1.33-2.67-4-8-4z" fill="#cbd5e0"/>
      </symbol>
    </svg>

    <section class="PUPBG">
      <div class="PUPBG-overlay"></div>
      <div class="container PUPBG-inner">
        <p class="kicker">PUP TAGUIG HEALTH SERVICES</p>
        <h1 class="PUPBG-title">Your Health, Our Priority</h1>
        <p class="PUPBG-lead">Access quality healthcare services online, anytime, anywhere.</p>

        <div class="hero-actions">
          <a href="{{ url('/student/booking') }}" class="btn btn-primary">Book Appointment</a>
          <a href="{{ url('/student/history') }}" class="btn btn-secondary">View Appointments</a>
        </div>
      </div>
    </section>

    <section id="about" class="container" style="padding: 60px 20px; text-align: center; max-width: 800px; margin: 0 auto; scroll-margin-top: 100px;">
    
    <h2 style="color: #20343a; font-weight: 800; margin-bottom: 16px;">Welcome to the Official PUPT Clinic</h2>
    
    <p style="color: #64748b; line-height: 1.8; margin-bottom: 24px;">
        Welcome to the official website for the PUP - Taguig Branch school clinic. If you ever feel unwell,
        you can make a consultation request here online. Additionally, you can check and evaluate all the
        information and status related to your appointment.
    </p>

    <a href="#" style="color: #8B0000; font-weight: 700; text-decoration: none; border: 1px solid #8B0000; padding: 10px 24px; border-radius: 6px; display: inline-block; transition: 0.2s;">
        Learn More
    </a>

</section>  

    <section class="comments-section">
      <div class="container">
        <div class="section-head">
          <div>
            <h3>What people are saying</h3>
            <p class="lead">Recent feedback from students and staff about our clinic services.</p>
          </div>
        </div>

        <div class="comments-grid">
          <article class="comment-card" tabindex="0">
            <svg class="avatar" role="img" aria-label="User avatar"><use href="#avatar-placeholder"></use></svg>
            <div class="comment-body">
              <h4>Jane D. <span class="comment-meta">· Student · 2 days ago</span></h4>
              <p>Quick and easy booking process — staff were helpful and the consultation was very informative. Highly recommended.</p>
              <div class="comment-footer"><span class="comment-chip">General Consultation</span></div>
            </div>
          </article>

          <article class="comment-card" tabindex="0">
            <svg class="avatar" role="img" aria-label="User avatar"><use href="#avatar-placeholder"></use></svg>
            <div class="comment-body">
              <h4>Mark R. <span class="comment-meta">· Faculty · 1 week ago</span></h4>
              <p>Received my medical certificate quickly. The online system saved me a lot of time.</p>
              <div class="comment-footer"><span class="comment-chip">Medical Certificates</span></div>
            </div>
          </article>

          <article class="comment-card" tabindex="0">
            <svg class="avatar" role="img" aria-label="User avatar"><use href="#avatar-placeholder"></use></svg>
            <div class="comment-body">
              <h4>Anna L. <span class="comment-meta">· Student · 3 weeks ago</span></h4>
              <p>Staff were friendly and the mental health support session was very helpful. Thank you!</p>
              <div class="comment-footer"><span class="comment-chip">Mental Health</span></div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <footer class="site-footer">
      <div class="footer-top">
        <div class="container footer-grid">
          
          <div class="footer-col footer-brand">
            <div class="brand">
              <div class="brand-logo">
                <img src="{{ asset('images/pup_logo.png') }}" alt="PUP Taguig logo" />
              </div>
              <div>
                <div class="brand-name">PUP TAGUIG <span class="brand-sub">ONLINE CLINIC</span></div>
              </div>
            </div>
            <p class="brand-desc">Providing quality healthcare services to the PUP Taguig community.</p>

            <div class="social">
              <a class="social-link" href="#" aria-label="facebook">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 5 3.66 9.12 8.44 9.88v-6.99H7.9v-2.9h2.54V9.41c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.45h-1.25c-1.23 0-1.61.77-1.61 1.56v1.88h2.74l-.44 2.9h-2.3V21.9C18.34 21.12 22 17 22 12z"/></svg>
              </a>
              <a class="social-link" href="#" aria-label="twitter">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 5.92c-.63.28-1.3.47-2 .55.72-.43 1.27-1.1 1.53-1.9-.68.4-1.43.68-2.23.84A3.49 3.49 0 0015.5 5c-1.92 0-3.48 1.57-3.48 3.5 0 .27.03.54.09.8-2.89-.14-5.45-1.53-7.16-3.64-.3.52-.47 1.12-.47 1.76 0 1.21.62 2.28 1.56 2.91-.58-.02-1.12-.18-1.6-.44v.04c0 1.7 1.2 3.12 2.8 3.45-.29.08-.6.12-.92.12-.23 0-.46-.02-.68-.06.46 1.44 1.8 2.49 3.38 2.52A7.03 7.03 0 012 19.54 9.93 9.93 0 008.56 21c6.54 0 10.12-5.42 10.12-10.12 0-.15 0-.3-.01-.45.7-.5 1.3-1.12 1.78-1.83-.64.28-1.32.48-2.03.57z"/></svg>
              </a>
            </div>
          </div>

          <div class="footer-col">
            <h4>Quick Links</h4>
            <ul class="footer-links">
              <li><a href="{{ url('/student/home') }}">Home</a></li>
              <li><a href="#">About Us</a></li>
              <li><a href="{{ url('/student/booking') }}">Book Appointment</a></li>
              <li><a href="{{ url('/student/faq') }}">FAQ</a></li>
            </ul>
          </div>

          <div class="footer-col">
            <h4>Services</h4>
            <ul class="footer-links">
              <li>General Consultation</li>
              <li>Mental Health Support</li>
              <li>Prescription Services</li>
              <li>Medical Certificates</li>
            </ul>
          </div>

          <div class="footer-col">
            <h4>Contact Us</h4>
            <ul class="contact-list">
              <li>
                <svg viewBox="0 0 24 24" class="contact-icon" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1112 6a2.5 2.5 0 010 5.5z"/></svg>
                General Santos Ave, Taguig City
              </li>
              <li>
                <svg viewBox="0 0 24 24" class="contact-icon" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm-1 4l-7 4-7-4V6l7 4 7-4v2z"/></svg>
                pupt_clinic@pup.edu.ph
              </li>
              <li>
                <svg viewBox="0 0 24 24" class="contact-icon" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 011 1v3.5a1 1 0 01-1 1A17 17 0 013 5a1 1 0 011-1H7.5a1 1 0 011 1c0 1.24.2 2.45.57 3.57a1 1 0 01-.24 1.02l-2.2 2.2z"/></svg>
                (02) 8837-5858
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <div class="container">© 2025 PUP Taguig Online Clinic. All rights reserved.</div>
      </div>
    </footer>
@endsection