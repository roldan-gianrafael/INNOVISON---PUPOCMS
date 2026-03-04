@extends('layouts.admin')

@section('title', 'Inventory Summary')

@push('styles')
<style>
    .summary-container {
        padding: 10px 4px;
    }

    .summary-title {
        margin: 0 0 18px;
        color: #4b0f17;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.01em;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eadde1;
        border-left: 4px solid #70131B;
        padding: 18px 18px 16px;
        box-shadow: 0 8px 24px rgba(112, 19, 27, 0.06);
    }

    .summary-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .summary-value {
        margin: 6px 0 0;
        font-size: 30px;
        line-height: 1;
        color: #4b0f17;
        font-weight: 800;
    }

    .summary-table-card {
        margin-bottom: 18px;
    }

    .summary-table-title {
        margin: 0 0 10px;
        font-size: 17px;
        color: #4b0f17;
    }

    .summary-table {
        width: 100%;
        border-collapse: collapse;
    }

    .summary-table thead th {
        padding: 11px 12px;
        text-align: left;
        border-bottom: 1px solid #eddde2;
        color: #6b7280;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .summary-table td {
        padding: 12px;
        border-bottom: 1px solid #f4ebee;
        color: #334155;
        font-size: 14px;
    }

    .summary-table tbody tr:last-child td {
        border-bottom: none;
    }

    .summary-back {
        text-decoration: none;
        color: #70131B;
        font-weight: 700;
        font-size: 14px;
    }

    .summary-back:hover {
        color: #5a0f16;
    }

    @media (max-width: 900px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $role = strtolower((string) (optional(auth()->user())->user_role ?? ''));
    $reportsHomeUrl = $role === 'student_assistant' ? url('/assistant/reports') : url('/admin/reports');
@endphp
<div class="summary-container">
    <h2 class="summary-title">Inventory Summary Report</h2>

    <div class="summary-grid">
        <div class="summary-card">
            <span class="summary-label">Total Unique Items</span>
            <h3 class="summary-value">{{ $totalItems }}</h3>
        </div>
        <div class="summary-card">
            <span class="summary-label">Total Stock Quantity</span>
            <h3 class="summary-value">{{ $totalStock }}</h3>
        </div>
        <div class="summary-card">
            <span class="summary-label">Out of Stock</span>
            <h3 class="summary-value">{{ $outOfStock }}</h3>
        </div>
    </div>

    <div class="card summary-table-card">
        <h4 class="summary-table-title">Stock by Category</h4>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Item Count</th>
                    <th>Total Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categorySummary as $cat)
                <tr>
                    <td style="font-weight: 700;">{{ $cat->category }}</td>
                    <td>{{ $cat->count }}</td>
                    <td>{{ $cat->total_qty }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ $reportsHomeUrl }}" class="summary-back">&larr; Back to Reports</a>
</div>
@endsection
