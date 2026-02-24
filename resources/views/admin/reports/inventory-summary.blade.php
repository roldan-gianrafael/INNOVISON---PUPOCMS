@extends('layouts.admin')

@section('title', 'Inventory Summary')

@section('content')
<div style="padding: 20px;">
    <h2 style="color: #1e293b;">Inventory Summary Report</h2>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
        <div style="background: #fff; padding: 20px; border-radius: 12px; border-left: 5px solid #8B0000; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <span style="font-size: 13px; color: #64748b;">TOTAL UNIQUE ITEMS</span>
            <h3 style="margin: 5px 0 0 0;">{{ $totalItems }}</h3>
        </div>
        <div style="background: #fff; padding: 20px; border-radius: 12px; border-left: 5px solid #10b981; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <span style="font-size: 13px; color: #64748b;">TOTAL STOCK QUANTITY</span>
            <h3 style="margin: 5px 0 0 0;">{{ $totalStock }}</h3>
        </div>
        <div style="background: #fff; padding: 20px; border-radius: 12px; border-left: 5px solid #ef4444; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <span style="font-size: 13px; color: #64748b;">OUT OF STOCK</span>
            <h3 style="margin: 5px 0 0 0;">{{ $outOfStock }}</h3>
        </div>
    </div>

    <div style="background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px;">
        <h4 style="margin-top: 0;">Stock by Category</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #f1f5f9; text-align: left; color: #64748b; font-size: 12px;">
                    <th style="padding: 12px;">CATEGORY</th>
                    <th style="padding: 12px;">ITEM COUNT</th>
                    <th style="padding: 12px;">TOTAL QUANTITY</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categorySummary as $cat)
                <tr style="border-bottom: 1px solid #f8fafc;">
                    <td style="padding: 12px; font-weight: 600;">{{ $cat->category }}</td>
                    <td style="padding: 12px;">{{ $cat->count }}</td>
                    <td style="padding: 12px;">{{ $cat->total_qty }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.reports') }}" style="color: #64748b; text-decoration: none;">← Back to Reports</a>
</div>
@endsection