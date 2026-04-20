@extends('layouts.student')

@section('title', 'My History')

@push('styles')
    <style>
      /* --- PAGE SPECIFIC STYLES --- */
      .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
      
      .card-history {
          background: #fff; 
          padding: 24px; 
          border-radius: 12px; 
          box-shadow: 0 8px 26px rgba(16,24,28,0.06); 
          min-height: 400px;
          border: 1px solid #eef2f3;
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
      
      .history-grid { display: grid; gap: 16px; margin-top: 20px; }
      
      .apt-card {
          padding: 16px;
          border-radius: 10px;
          background: #fff;
          border: 1px solid #eef2f3;
          display: flex;
          flex-direction: column;
          gap: 10px; 
          transition: box-shadow 0.2s; 
      }
      .apt-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-color: #e0e6e8; }
      
      .apt-header { display: flex; justify-content: space-between; align-items: flex-start; }
      .apt-service { font-size: 16px; font-weight: 700; color: #20343a; }
      .apt-date { font-weight: 600; color: #8B0000; }
      
      .apt-details { display: flex; gap: 20px; color: #5f7174; font-size: 14px; margin-top: 4px; }
      .apt-notes { background: #f3f6f5; padding: 10px; border-radius: 6px; font-size: 14px; color: #445; margin-top: 4px; }
      
      .status-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
      
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
    </style>
@endpush

@section('content')
    <div class="container" style="padding-top: 5px; padding-bottom: 60px;">
      <div class="page-header">
        <div>
          <h1 style="margin:0; font-size: 28px; color: #600000;">Appointment History</h1>
          <p class="small" style="color:#6b7b7d; margin-top:4px; font-size:14px;">View and manage your past and upcoming consultations.</p>
        </div>
      </div>

      <section class="card-history">
        
        <div class="history-grid">
            @forelse($appointments as $appt)
                <div class="apt-card">
                  <div class="apt-header">
                    <div>
                      <div class="apt-service">{{ $appt->service }}</div>
                      <div class="apt-details">
                        <span>{{ $appt->name }} ({{ $appt->student_number ?: optional(optional($appt->user)->healthProfile)->student_number ?: optional($appt->user)->student_number ?: ($studentContext['student_number'] ?? $appt->student_id) }})</span>
                        <span>{{ $appt->email }}</span>
                      </div>
                    </div>
                    <div style="text-align:right">
                      <div class="apt-date">{{ \Carbon\Carbon::parse($appt->date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($appt->time)->format('g:i A') }}</div>
                      <div style="margin-top:6px">
                          <span class="status-badge" 
                                style="background: {{ $appt->status == 'Pending' ? '#fff3cd' : ($appt->status == 'Approved' ? '#d4edda' : '#eee') }};
                                       color: {{ $appt->status == 'Pending' ? '#856404' : ($appt->status == 'Approved' ? '#155724' : '#555') }};">
                              {{ $appt->status }}
                          </span>
                      </div>
                    </div>
                  </div>
                  
                  @if($appt->notes)
                      <div class="apt-notes"><strong>Notes:</strong> {{ $appt->notes }}</div>
                  @endif
                  
                  <div style="margin-top:10px; display:flex; justify-content:flex-end; gap:8px">
    
    @if($appt->status == 'Pending' || $appt->status == 'Approved')
        <form action="{{ url('/student/appointments/' . $appt->id . '/cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
            @csrf
            <button type="submit" class="btn-outline" style="border-color:#8B0000; color:#8B0000; cursor:pointer;">
                Cancel Appointment
            </button>
        </form>
    @else
        <button disabled class="btn-outline" style="border-color:#eee; color:#aaa; cursor:not-allowed; opacity: 0.6;">
            {{ $appt->status }}
        </button>
    @endif

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
