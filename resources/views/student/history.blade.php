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
          padding: 8px 14px; 
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
      
      .empty-state { text-align: center; padding: 40px; color: #889; }
    </style>
@endpush

@section('content')
    <div class="container" style="padding-top: 40px; padding-bottom: 60px;">
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
                        <span>{{ $appt->name }} ({{ $appt->student_id }})</span>
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
                <div class="empty-state">
                  <p>You have no appointment history.</p>
                  <a href="{{ url('/student/booking') }}" class="btn-outline">Book your first appointment</a>
                </div>
            @endforelse
        </div>
        
      </section>
    </div>
@endsection