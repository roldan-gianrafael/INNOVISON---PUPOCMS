@extends('layouts.admin')

@section('title', 'MAR Report')

@push('styles')
<style>
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 24px; }
    h2 { color: #8B0000; font-size: 22px; margin-bottom: 20px; border-bottom: 2px solid #8B0000; padding-bottom: 10px; }
    h3 { color: #334155; font-size: 18px; margin-top: 30px; }

    /* Stats Widget */
    .stats-container { display: flex; gap: 20px; margin-bottom: 25px; }
    .stat-card { background: #8B0000; color: white; padding: 15px 25px; border-radius: 10px; flex: 1; }
    .stat-card span { display: block; font-size: 12px; opacity: 0.8; text-transform: uppercase; }
    .stat-card strong { font-size: 24px; }

    /* Table Styling */
    .mar-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .mar-table th { background: #f8fafc; color: #64748b; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 13px; }
    .mar-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
    .category-row { background: #fff5f5; font-weight: 700; color: #8B0000; }
    
    /* CRUD Form */
    .manage-section { background: #fdfdfd; border: 1px dashed #cbd5e1; padding: 20px; border-radius: 10px; margin-top: 40px; }
    .form-group { margin-bottom: 15px; }
    .btn-save { background: #70131B; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
    .btn-delete { color: #ef4444; text-decoration: none; font-size: 12px; margin-left: 10px; }
</style>
@endpush

@section('content')
@php
    $role = \App\Models\User::normalizeRole(optional(auth()->user())->user_role ?? '');
    $isAdminLike = $role === \App\Models\User::ROLE_SUPER_ADMIN;
    $reportsHomeUrl = $role === 'student_assistant' ? url('/assistant/reports') : url('/admin/reports');
@endphp



 <a href="{{ $reportsHomeUrl }}" style="color: #64748b; text-decoration: none;">&larr; Back to Reports</a>



<div class="stats-container">
    <div class="stat-card">
        <span>Total Consultations Today</span>
        <strong>{{ $totalToday ?? 0 }}</strong>
    </div>
    <div class="stat-card" style="background: #8f2230;">
        <span>Current Filter Month</span>
        <strong>{{ date('F Y', strtotime($month)) }}</strong>
    </div>
</div>

@if($isAdminLike)
    <a href="{{ route('admin.reports.manage-mar', ['month' => $month]) }}" 
       class="btn-filter" 
       style="background:#70131B; color:white; padding:8px 15px; border-radius:6px; text-decoration:none; font-size:14px;">
       Manage MAR
    </a>
@endif
<div class="card">
    <h2>Medical Accomplishment Report</h2>

    <form method="GET" class="filter-box" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="month" name="month" class="form-control" value="{{ $month }}">
        <button class="btn-filter" type="submit" style="background:#8B0000; color:white; padding:8px 15px; border-radius:6px; border:none;">Generate</button>
    </form>
   

    

    <table class="mar-table">
        <thead>
            <tr>
                <th>Service Category / Conditions</th>
                <th style="text-align: center;">Student</th>
                <th style="text-align: center;">Faculty</th>
                <th style="text-align: center;">Admin</th>
                <th style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
    <tr style="background-color: #f8f9fa; font-weight: bold;">
        <td colspan="5">Category {{ $cat->code }} - {{ $cat->name }}</td>
    </tr>

    @foreach($cat->medicalConditions as $condition)
        @php
            // Bilangin ang consultations per role gamit ang 'user_type' column sa consultations table
            $stu = $condition->consultations->where('user_type', 'Student')->count();
            $fac = $condition->consultations->where('user_type', 'Faculty')->count();
            $adm = $condition->consultations->where('user_type', 'Staff')->count(); // o 'Admin' depende sa nakasave
            $total = $stu + $fac + $adm;
        @endphp
        <tr>
            <td style="padding-left: 30px;">{{ $condition->name }}</td>
            <td class="text-center">{{ $stu }}</td>
            <td class="text-center">{{ $fac }}</td>
            <td class="text-center">{{ $adm }}</td>
            <td class="text-center">{{ $total }}</td>
        </tr>
    @endforeach
@endforeach
        </tbody>
    </table>
</div>



@endsection
