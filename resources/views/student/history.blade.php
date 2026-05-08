@extends('layouts.student')

@section('title', 'My History')

@push('styles')
    <style>
      /* --- PAGE SPECIFIC STYLES --- */
      .page-header {
          position: relative;
          margin: -12px auto 22px;
          padding: 18px 22px;
          border-radius: 24px;
          border: 1px solid rgba(139, 0, 0, 0.12);
          background:
              radial-gradient(circle at top right, rgba(255, 244, 194, 0.68), transparent 30%),
              linear-gradient(135deg, #fffef4 0%, #fff8fb 36%, #ffffff 100%);
          box-shadow:
              0 20px 40px rgba(15, 23, 42, 0.09),
              0 0 0 1px rgba(255,255,255,0.78) inset;
          overflow: hidden;
          max-width: 980px;
      }
      .page-header::before {
          content: "";
          position: absolute;
          inset: auto -60px -80px auto;
          width: 220px;
          height: 220px;
          background: radial-gradient(circle, rgba(139, 0, 0, 0.10) 0%, rgba(139, 0, 0, 0) 70%);
          pointer-events: none;
      }
      .history-hero-icon {
          position: absolute;
          top: -12px;
          right: -8px;
          width: 180px;
          height: 180px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          color: rgba(112, 19, 27, 0.10);
          transform: rotate(-12deg);
          pointer-events: none;
          z-index: 0;
      }
      .history-hero-icon svg {
          width: 100%;
          height: 100%;
          stroke-width: 1.7;
      }
      .history-hero-kicker,
      .history-hero-title,
      .history-hero-text,
      .history-hero-steps {
          position: relative;
          z-index: 1;
      }
      .history-hero-kicker {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 6px 10px;
          border-radius: 999px;
          background: rgba(139, 0, 0, 0.08);
          color: #8B0000;
          font-size: 11px;
          font-weight: 800;
          letter-spacing: 0.08em;
          text-transform: uppercase;
          margin-bottom: 10px;
      }
      .history-hero-title {
          margin: 0;
          font-size: 28px;
          color: #8B0000;
          font-weight: 800;
          letter-spacing: -0.03em;
      }
      .history-hero-text {
          color: #6b7b7d;
          margin-top: 8px;
          font-size: 14px;
          line-height: 1.6;
          max-width: 620px;
      }
      .history-hero-steps {
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
          margin-top: 14px;
      }
      .history-hero-step {
          display: inline-flex;
          align-items: center;
          gap: 10px;
          padding: 8px 12px;
          border-radius: 14px;
          background: rgba(255, 255, 255, 0.82);
          border: 1px solid rgba(148, 163, 184, 0.18);
          color: #334155;
          font-size: 12px;
          font-weight: 700;
          box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
      }
      .history-hero-step::before {
          content: "";
          width: 8px;
          height: 8px;
          border-radius: 999px;
          background: #8B0000;
          flex: 0 0 auto;
          box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.08);
      }
      
      .card-history {
          background: #fff; 
          padding: 24px; 
          border-radius: 22px; 
          box-shadow: 0 18px 36px rgba(16,24,28,0.08); 
          min-height: 400px;
          border: 1px solid rgba(30, 41, 59, 0.08);
          position: relative;
          overflow: hidden;
          max-width: 980px;
          margin: 0 auto;
      }
      .card-history::before {
          content: "";
          position: absolute;
          top: 0;
          left: 20px;
          right: 20px;
          height: 4px;
          border-radius: 999px;
          background: linear-gradient(90deg, #8B0000 0%, #facc15 100%);
      }
      .history-summary-grid {
          display: grid;
          grid-template-columns: repeat(4, minmax(0, 1fr));
          gap: 14px;
          margin-bottom: 18px;
      }
      .history-summary-card {
          padding: 16px 16px 14px;
          border-radius: 18px;
          background: linear-gradient(180deg, #ffffff 0%, #fcfcfe 100%);
          border: 1px solid rgba(30, 41, 59, 0.08);
          box-shadow:
              0 12px 24px rgba(15, 23, 42, 0.06),
              0 0 0 1px rgba(255,255,255,0.76) inset;
      }
      .history-summary-label {
          display: block;
          font-size: 11px;
          font-weight: 800;
          letter-spacing: 0.08em;
          text-transform: uppercase;
          color: #64748b;
          margin-bottom: 8px;
      }
      .history-summary-value {
          font-size: 28px;
          line-height: 1;
          font-weight: 800;
          color: #8B0000;
          display: block;
      }
      .history-summary-note {
          margin-top: 8px;
          font-size: 12px;
          color: #64748b;
      }
      .history-toolbar {
          display: flex;
          align-items: center;
          justify-content: space-between;
          gap: 14px;
          flex-wrap: wrap;
          margin-bottom: 12px;
      }
      .history-toolbar-copy {
          color: #64748b;
          font-size: 13px;
          font-weight: 600;
      }
      .history-filter-row {
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
      }
      .history-filter-btn {
          min-height: 40px;
          padding: 0 16px;
          border-radius: 999px;
          border: 1px solid rgba(139, 0, 0, 0.16);
          background: #ffffff;
          color: #8B0000;
          font-size: 13px;
          font-weight: 800;
          cursor: pointer;
          transition: all 0.18s ease;
          box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
      }
      .history-filter-btn:hover,
      .history-filter-btn.is-active {
          transform: translateY(-1px);
          background: #8B0000;
          color: #facc15;
          border-color: #8B0000;
          box-shadow: 0 14px 24px rgba(139, 0, 0, 0.16);
      }
      
      .btn-outline { 
          border: 1px solid #8B0000; 
          color: #8B0000; 
          background: transparent; 
          padding: 7.9px 14px; 
          border-radius: 8px; 
          text-decoration: none; 
          font-weight: 600; 
          font-size: 14px; 
          display: inline-block;
      }
      .btn-outline:hover { background: #fdf2f2; }
      
      .history-grid {
          display: grid;
          gap: 18px;
          margin-top: 18px;
          position: relative;
          padding-left: 18px;
      }
      .history-grid::before {
          content: "";
          position: absolute;
          left: 6px;
          top: 6px;
          bottom: 6px;
          width: 2px;
          border-radius: 999px;
          background: linear-gradient(180deg, rgba(139, 0, 0, 0.24) 0%, rgba(250, 204, 21, 0.22) 100%);
      }
      
      .apt-card {
          position: relative;
          padding: 18px 18px 18px 22px;
          border-radius: 18px;
          background: linear-gradient(180deg, #ffffff 0%, #fcfcfe 100%);
          border: 1px solid rgba(30, 41, 59, 0.08);
          display: flex;
          flex-direction: column;
          gap: 12px; 
          transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s; 
          box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06);
      }
      .apt-card::before {
          content: "";
          position: absolute;
          left: -21px;
          top: 26px;
          width: 12px;
          height: 12px;
          border-radius: 999px;
          background: #ffffff;
          border: 3px solid #8B0000;
          box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.08);
      }
      .apt-card.status-pending { border-left: 4px solid #d97706; }
      .apt-card.status-approved { border-left: 4px solid #15803d; }
      .apt-card.status-completed { border-left: 4px solid #8B0000; }
      .apt-card.status-missed { border-left: 4px solid #c2410c; }
      .apt-card.status-cancelled { border-left: 4px solid #64748b; }
      .apt-card.status-expired { border-left: 4px solid #6b7280; }
      .apt-card.status-default { border-left: 4px solid #8B0000; }
      .apt-card:hover { box-shadow: 0 18px 34px rgba(0,0,0,0.08); border-color: rgba(139, 0, 0, 0.12); transform: translateY(-2px); }
      .apt-card.is-upcoming {
          background: linear-gradient(180deg, #fffdfa 0%, #ffffff 100%);
      }
      
      .apt-header { display: flex; justify-content: space-between; align-items: flex-start; }
      .apt-service { font-size: 17px; font-weight: 800; color: #20343a; letter-spacing: -0.01em; }
      .apt-date {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          min-height: 36px;
          padding: 0 14px;
          border-radius: 999px;
          background: rgba(139, 0, 0, 0.07);
          font-weight: 700;
          color: #8B0000;
      }
      
      .apt-meta {
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
          margin-top: 4px;
      }
      .apt-meta-pill {
          display: inline-flex;
          align-items: center;
          min-height: 32px;
          padding: 0 12px;
          border-radius: 999px;
          background: #f8fafc;
          border: 1px solid #e2e8f0;
          color: #475569;
          font-size: 12px;
          font-weight: 700;
      }
      .apt-meta-pill.is-status {
          font-weight: 800;
          text-transform: uppercase;
          letter-spacing: 0.04em;
      }
      .apt-meta-pill.is-status.status-pending { background: #fff3cd; color: #856404; border-color: rgba(217, 119, 6, 0.18); }
      .apt-meta-pill.is-status.status-approved { background: #d4edda; color: #155724; border-color: rgba(21, 128, 61, 0.18); }
      .apt-meta-pill.is-status.status-completed { background: #f3e8ea; color: #7f1d2d; border-color: rgba(139, 0, 0, 0.14); }
      .apt-meta-pill.is-status.status-missed { background: #ffedd5; color: #9a3412; border-color: rgba(194, 65, 12, 0.16); }
      .apt-meta-pill.is-status.status-cancelled { background: #e5e7eb; color: #4b5563; border-color: rgba(100, 116, 139, 0.18); }
      .apt-meta-pill.is-status.status-expired { background: #f3f4f6; color: #4b5563; border-color: rgba(107, 114, 128, 0.18); }
      .apt-meta-pill.is-status.status-default { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
      .apt-notes {
          display: inline-block;
          width: fit-content;
          max-width: min(100%, 520px);
          background: linear-gradient(180deg, #fffdf0 0%, #fff7cc 100%);
          padding: 12px 14px;
          border-radius: 14px;
          font-size: 14px;
          color: #445;
          margin-top: 2px;
          border: 1px solid rgba(245, 158, 11, 0.22);
          box-shadow:
              0 10px 20px rgba(146, 64, 14, 0.06),
              inset 0 1px 0 rgba(255,255,255,0.7);
          line-height: 1.55;
          word-break: break-word;
      }
      .apt-footer {
          display: flex;
          justify-content: space-between;
          align-items: center;
          gap: 12px;
          flex-wrap: wrap;
      }
      .apt-action-note {
          font-size: 12px;
          font-weight: 700;
          color: #94a3b8;
      }
      
      .status-badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; }
      .status-badge.status-pending { background: #fff3cd; color: #856404; }
      .status-badge.status-approved { background: #d4edda; color: #155724; }
      .status-badge.status-completed { background: #f3e8ea; color: #7f1d2d; }
      .status-badge.status-missed { background: #ffedd5; color: #9a3412; }
      .status-badge.status-cancelled { background: #e5e7eb; color: #4b5563; }
      .status-badge.status-expired { background: #f3f4f6; color: #4b5563; }
      .status-badge.status-default { background: #eee; color: #555; }
      
      .empty-state {
          min-height: 360px;
          display: flex;
          align-items: center;
          justify-content: center;
          flex-direction: column;
          gap: 18px;
          padding: 48px 24px;
          color: #667085;
          text-align: center;
      }

      .empty-illustration {
          position: relative;
          width: 230px;
          height: 220px;
          display: flex;
          align-items: flex-end;
          justify-content: center;
      }

      .empty-shadow {
          position: absolute;
          bottom: 6px;
          width: 138px;
          height: 18px;
          border-radius: 999px;
          background: rgba(127, 29, 29, 0.12);
          filter: blur(2px);
      }

      .empty-bubble {
          position: absolute;
          top: 2px;
          right: 18px;
          background: #ffffff;
          color: #8B0000;
          border: 2px solid #f3c9c9;
          border-radius: 18px;
          padding: 10px 16px;
          font-size: 16px;
          font-weight: 800;
          box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
          animation: bubbleFloat 2s ease-in-out infinite;
          min-width: 110px;
      }

      .bubble-text {
          display: inline-block;
          transition: opacity 0.18s ease, transform 0.18s ease;
      }

      .bubble-text-yay {
          position: absolute;
          inset: 10px 16px;
          opacity: 0;
          transform: translateY(4px);
      }

      .empty-bubble::after {
          content: "";
          position: absolute;
          left: 50%;
          bottom: -10px;
          width: 18px;
          height: 18px;
          background: #ffffff;
          border-right: 2px solid #f3c9c9;
          border-bottom: 2px solid #f3c9c9;
          transform: translateX(-50%) rotate(45deg);
      }

      .clinic-cartoon {
          position: relative;
          width: 132px;
          height: 172px;
          animation: cartoonBounce 2.6s ease-in-out infinite;
      }

      .cartoon-head {
          position: absolute;
          top: 12px;
          left: 34px;
          width: 64px;
          height: 64px;
          border-radius: 999px;
          background: #ffd7b5;
          border: 3px solid #7c2d12;
          z-index: 2;
      }

      .cartoon-hair {
          position: absolute;
          top: 4px;
          left: 29px;
          width: 74px;
          height: 38px;
          border-radius: 999px 999px 18px 18px;
          background: #5b2c1d;
          z-index: 3;
      }

      .cartoon-eye {
          position: absolute;
          top: 30px;
          width: 7px;
          height: 7px;
          border-radius: 999px;
          background: #3f1d12;
          z-index: 4;
      }

      .cartoon-eye.left { left: 53px; }
      .cartoon-eye.right { right: 53px; }

      .cartoon-smile {
          position: absolute;
          top: 46px;
          left: 52px;
          width: 28px;
          height: 12px;
          border-bottom: 3px solid #b45309;
          border-radius: 0 0 20px 20px;
          z-index: 4;
      }

      .cartoon-body {
          position: absolute;
          top: 74px;
          left: 28px;
          width: 76px;
          height: 70px;
          border-radius: 22px 22px 18px 18px;
          background: #ffffff;
          border: 3px solid #7c2d12;
      }

      .cartoon-cross-v,
      .cartoon-cross-h {
          position: absolute;
          background: #8B0000;
          border-radius: 999px;
          z-index: 2;
      }

      .cartoon-cross-v {
          top: 92px;
          left: 63px;
          width: 8px;
          height: 24px;
      }

      .cartoon-cross-h {
          top: 100px;
          left: 55px;
          width: 24px;
          height: 8px;
      }

      .cartoon-arm {
          position: absolute;
          top: 84px;
          width: 18px;
          height: 58px;
          border-radius: 999px;
          background: #ffd7b5;
          border: 3px solid #7c2d12;
          transform-origin: top center;
          transition: transform 0.28s ease;
      }

      .cartoon-arm.left {
          left: 12px;
          transform: rotate(18deg);
      }

      .cartoon-arm.right {
          right: 12px;
          transform: rotate(-22deg);
      }

      .cartoon-leg {
          position: absolute;
          bottom: 10px;
          width: 18px;
          height: 52px;
          border-radius: 999px;
          background: #f8fafc;
          border: 3px solid #7c2d12;
      }

      .cartoon-leg.left { left: 42px; }
      .cartoon-leg.right { right: 42px; }

      .empty-title {
          margin: 0;
          font-size: 18px;
          color: #7f1d1d;
          font-weight: 800;
      }

      .empty-state .btn-outline {
          padding: 10px 18px;
          border-radius: 999px;
          font-weight: 700;
          box-shadow: 0 10px 24px rgba(139, 0, 0, 0.08);
      }

      .empty-state.is-celebrating .cartoon-arm.left {
          transform: rotate(72deg) translateY(-10px);
      }

      .empty-state.is-celebrating .cartoon-arm.right {
          transform: rotate(-72deg) translateY(-10px);
      }

      .empty-state.is-celebrating .bubble-text-book {
          opacity: 0;
          transform: translateY(-4px);
      }

      .empty-state.is-celebrating .bubble-text-yay {
          opacity: 1;
          transform: translateY(0);
      }

      @keyframes bubbleFloat {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-8px); }
      }

      @keyframes cartoonBounce {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-10px); }
      }
      html[data-theme="dark"] .page-header {
          background: linear-gradient(180deg, #0f0f10 0%, #161618 100%) !important;
          border-color: rgba(250, 204, 21, 0.16) !important;
          box-shadow:
              0 18px 36px rgba(0, 0, 0, 0.42),
              0 0 0 1px rgba(250, 204, 21, 0.05) inset !important;
      }
      html[data-theme="dark"] .history-hero-kicker,
      html[data-theme="dark"] .history-hero-step {
          background: linear-gradient(180deg, #17171a 0%, #1d1d21 100%) !important;
          border-color: rgba(250, 204, 21, 0.14) !important;
          color: #f8fafc !important;
      }
      html[data-theme="dark"] .history-hero-title {
          color: #ffffff !important;
      }
      html[data-theme="dark"] .history-hero-text {
          color: #e5e7eb !important;
      }
      html[data-theme="dark"] .history-hero-icon {
          color: rgba(250, 204, 21, 0.08) !important;
      }
      html[data-theme="dark"] .card-history,
      html[data-theme="dark"] .history-summary-card,
      html[data-theme="dark"] .apt-card {
          background: linear-gradient(180deg, #0f0f10 0%, #161618 100%) !important;
          border-color: rgba(250, 204, 21, 0.14) !important;
          box-shadow:
              0 18px 36px rgba(0, 0, 0, 0.30),
              0 0 0 1px rgba(250, 204, 21, 0.04) inset !important;
      }
      html[data-theme="dark"] .history-summary-label,
      html[data-theme="dark"] .history-toolbar-copy,
      html[data-theme="dark"] .apt-meta-pill,
      html[data-theme="dark"] .apt-notes {
          color: #cbd5e1 !important;
      }
      html[data-theme="dark"] .history-summary-value,
      html[data-theme="dark"] .apt-service {
          color: #ffffff !important;
      }
      html[data-theme="dark"] .history-filter-btn {
          background: #17171a !important;
          color: #ffffff !important;
          border-color: rgba(250, 204, 21, 0.14) !important;
      }
      html[data-theme="dark"] .history-filter-btn:hover,
      html[data-theme="dark"] .history-filter-btn.is-active {
          background: #8B0000 !important;
          color: #facc15 !important;
          border-color: #8B0000 !important;
      }
      html[data-theme="dark"] .apt-date,
      html[data-theme="dark"] .apt-meta-pill {
          background: #17171a !important;
          border-color: rgba(250, 204, 21, 0.14) !important;
          color: #f8fafc !important;
      }
      html[data-theme="dark"] .apt-meta-pill.is-status.status-pending,
      html[data-theme="dark"] .apt-meta-pill.is-status.status-approved,
      html[data-theme="dark"] .apt-meta-pill.is-status.status-completed,
      html[data-theme="dark"] .apt-meta-pill.is-status.status-missed,
      html[data-theme="dark"] .apt-meta-pill.is-status.status-cancelled,
      html[data-theme="dark"] .apt-meta-pill.is-status.status-expired,
      html[data-theme="dark"] .apt-meta-pill.is-status.status-default {
          color: #f8fafc !important;
      }
      html[data-theme="dark"] .apt-action-note {
          color: #94a3b8 !important;
      }
      html[data-theme="dark"] .apt-notes {
          background: linear-gradient(180deg, rgba(133, 77, 14, 0.26) 0%, rgba(146, 64, 14, 0.20) 100%) !important;
          border-color: rgba(250, 204, 21, 0.18) !important;
          color: #f8fafc !important;
      }
      html[data-theme="dark"] .history-grid::before {
          background: linear-gradient(180deg, rgba(250, 204, 21, 0.20) 0%, rgba(139, 0, 0, 0.20) 100%) !important;
      }
      html[data-theme="dark"] .apt-card::before {
          background: #0f0f10 !important;
      }
      @media (max-width: 680px) {
          .page-header {
              padding: 16px 16px;
              margin: -8px auto 18px;
          }
          .history-summary-grid {
              grid-template-columns: repeat(2, minmax(0, 1fr));
          }
          .history-toolbar {
              align-items: stretch;
          }
          .history-filter-row {
              width: 100%;
          }
          .history-filter-btn {
              flex: 1 1 calc(50% - 10px);
          }
          .apt-header,
          .apt-footer {
              flex-direction: column;
              align-items: flex-start;
          }
          .history-grid {
              padding-left: 14px;
          }
          .history-hero-icon {
              top: 4px;
              right: -10px;
              width: 118px;
              height: 118px;
          }
          .history-hero-step {
              width: 100%;
              justify-content: flex-start;
          }
      }
    </style>
@endpush

@section('content')
    @php
        $totalAppointments = $appointments->count();
        $pendingAppointments = $appointments->filter(fn ($appt) => strtolower((string) $appt->status) === 'pending')->count();
        $approvedAppointments = $appointments->filter(fn ($appt) => strtolower((string) $appt->status) === 'approved')->count();
        $completedAppointments = $appointments->filter(fn ($appt) => strtolower((string) $appt->status) === 'completed')->count();
    @endphp
    <div class="container" style="padding-top: 5px; padding-bottom: 60px;">
      <div class="page-header">
        <div class="history-hero-icon" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-3.13-6.838M21 3v6h-6" />
          </svg>
        </div>
        <div class="history-hero-kicker">Clinic Timeline</div>
        <h1 class="history-hero-title">Appointment History</h1>
        <p class="history-hero-text">View and manage your past and upcoming consultations.</p>
        <div class="history-hero-steps">
          <div class="history-hero-step">
            <span>Recent Appointments</span>
          </div>
          <div class="history-hero-step">
            <span>Current Status</span>
          </div>
          <div class="history-hero-step">
            <span>Upcoming Bookings</span>
          </div>
        </div>
      </div>

      <section class="card-history">
        <div class="history-summary-grid">
          <div class="history-summary-card">
            <span class="history-summary-label">Total Appointments</span>
            <span class="history-summary-value">{{ $totalAppointments }}</span>
            <div class="history-summary-note">Your complete clinic appointment trail.</div>
          </div>
          <div class="history-summary-card">
            <span class="history-summary-label">Pending</span>
            <span class="history-summary-value">{{ $pendingAppointments }}</span>
            <div class="history-summary-note">Waiting for clinic review.</div>
          </div>
          <div class="history-summary-card">
            <span class="history-summary-label">Approved</span>
            <span class="history-summary-value">{{ $approvedAppointments }}</span>
            <div class="history-summary-note">Confirmed and scheduled.</div>
          </div>
          <div class="history-summary-card">
            <span class="history-summary-label">Completed</span>
            <span class="history-summary-value">{{ $completedAppointments }}</span>
            <div class="history-summary-note">Finished consultations on record.</div>
          </div>
        </div>

        @if($appointments->isNotEmpty())
          <div class="history-toolbar">
            <div class="history-toolbar-copy">Filter your records by appointment status.</div>
            <div class="history-filter-row" id="historyFilterRow">
              <button type="button" class="history-filter-btn is-active" data-filter="all">All</button>
              <button type="button" class="history-filter-btn" data-filter="pending">Pending</button>
              <button type="button" class="history-filter-btn" data-filter="approved">Approved</button>
              <button type="button" class="history-filter-btn" data-filter="completed">Completed</button>
              <button type="button" class="history-filter-btn" data-filter="cancelled">Cancelled</button>
            </div>
          </div>
        @endif
        
        <div class="history-grid">
            @forelse($appointments as $appt)
                @php
                    $statusNormalized = strtolower((string) $appt->status);
                    $statusClass = match (strtolower((string) $appt->status)) {
                        'pending' => 'status-pending',
                        'approved' => 'status-approved',
                        'completed' => 'status-completed',
                        'missed' => 'status-missed',
                        'cancelled' => 'status-cancelled',
                        'expired' => 'status-expired',
                        default => 'status-default',
                    };
                    $appointmentAt = \Carbon\Carbon::parse($appt->date . ' ' . $appt->time);
                    $isUpcoming = $appointmentAt->isFuture() && in_array($statusNormalized, ['pending', 'approved'], true);
                @endphp
                <div class="apt-card {{ $statusClass }} {{ $isUpcoming ? 'is-upcoming' : '' }}" data-history-status="{{ $statusNormalized }}">
                  <div class="apt-header">
                    <div>
                      <div class="apt-service">{{ $appt->service }}</div>
                      <div class="apt-meta">
                        <span class="apt-meta-pill">{{ $appt->name }}</span>
                        <span class="apt-meta-pill">{{ $appt->student_number ?: optional(optional($appt->user)->healthProfile)->student_number ?: optional($appt->user)->student_number ?: ($studentContext['student_number'] ?? $appt->student_id) }}</span>
                        <span class="apt-meta-pill">{{ $appt->email }}</span>
                        <span class="apt-meta-pill is-status {{ $statusClass }}">{{ $appt->status }}</span>
                        @if($isUpcoming)
                          <span class="apt-meta-pill">Upcoming Schedule</span>
                        @endif
                      </div>
                    </div>
                    <div class="apt-date">{{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($appt->time)->format('g:i A') }}</div>
                  </div>
                  
                  @if($appt->notes)
                      <div class="apt-notes"><strong>Notes:</strong> {{ $appt->notes }}</div>
                  @endif
                  
                  <div class="apt-footer">
                    <div style="display:flex; justify-content:flex-end; gap:8px">
                      @if($appt->status == 'Pending' || $appt->status == 'Approved')
                          <form action="{{ url('/student/appointments/' . $appt->id . '/cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                              @csrf
                              <button type="submit" class="btn-outline" style="border-color:#8B0000; color:#8B0000; cursor:pointer;">
                                  Cancel Appointment
                              </button>
                          </form>
                      @else
                          <span class="apt-action-note">No actions available</span>
                      @endif
                    </div>
                  </div>
                </div>
            @empty
                <div class="empty-state" id="emptyHistoryState">
                  <div class="empty-illustration" aria-hidden="true">
                    <div class="empty-bubble">
                        <span class="bubble-text bubble-text-book">Book Now!</span>
                        <span class="bubble-text bubble-text-yay">Yay!</span>
                    </div>
                    <div class="empty-shadow"></div>
                    <div class="clinic-cartoon">
                        <div class="cartoon-hair"></div>
                        <div class="cartoon-head"></div>
                        <div class="cartoon-eye left"></div>
                        <div class="cartoon-eye right"></div>
                        <div class="cartoon-smile"></div>
                        <div class="cartoon-arm left"></div>
                        <div class="cartoon-arm right"></div>
                        <div class="cartoon-body"></div>
                        <div class="cartoon-cross-v"></div>
                        <div class="cartoon-cross-h"></div>
                        <div class="cartoon-leg left"></div>
                        <div class="cartoon-leg right"></div>
                    </div>
                  </div>
                  <h2 class="empty-title">You have no appointment history yet</h2>
                  <a href="{{ url('/student/booking') }}" class="btn-outline empty-cta" id="emptyHistoryCta">Book your first appointment</a>
                </div>
            @endforelse
        </div>
        
      </section>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const emptyState = document.getElementById('emptyHistoryState');
    const cta = document.getElementById('emptyHistoryCta');
    const filterRow = document.getElementById('historyFilterRow');
    const historyCards = Array.from(document.querySelectorAll('.apt-card[data-history-status]'));

    if (filterRow && historyCards.length) {
        const filterButtons = Array.from(filterRow.querySelectorAll('.history-filter-btn'));

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const filter = button.dataset.filter || 'all';

                filterButtons.forEach(function (btn) {
                    btn.classList.toggle('is-active', btn === button);
                });

                historyCards.forEach(function (card) {
                    const cardStatus = card.dataset.historyStatus || '';
                    const shouldShow = filter === 'all' || cardStatus === filter;
                    card.style.display = shouldShow ? '' : 'none';
                });
            });
        });
    }

    if (!emptyState || !cta) {
        return;
    }

    const activate = () => emptyState.classList.add('is-celebrating');
    const deactivate = () => emptyState.classList.remove('is-celebrating');

    cta.addEventListener('mouseenter', activate);
    cta.addEventListener('mouseleave', deactivate);
    cta.addEventListener('focus', activate);
    cta.addEventListener('blur', deactivate);
});
</script>
@endpush
