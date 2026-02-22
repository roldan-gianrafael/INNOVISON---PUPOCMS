@extends('layouts.admin')

@section('title', 'Manage MAR Conditions')

@push('styles')
<style>
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 24px; }
    h2 { color: #8B0000; font-size: 22px; margin-bottom: 20px; border-bottom: 2px solid #8B0000; padding-bottom: 10px; }
    h3 { color: #334155; font-size: 18px; margin-top: 10px; }

    .mar-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .mar-table th { background: #f8fafc; color: #64748b; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 13px; }
    .mar-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
    
    .manage-section { background: #fdfdfd; border: 1px dashed #cbd5e1; padding: 20px; border-radius: 10px; }
    .btn-save { background: #22c55e; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
    .form-control { padding: 8px; border: 1px solid #ddd; border-radius: 5px; width: 100%; }
</style>
@endpush

@section('content')

<div style="margin-bottom: 20px;">
    <a href="{{ route('reports.mar') }}" style="text-decoration: none; color: #64748b; font-size: 14px;">← Back to MAR Report</a>
</div>

<div class="card manage-section">
    <h3>Manage Medical Conditions (Sub-categories)</h3>
    <p style="font-size: 13px; color: #64748b;">Add or remove medical conditions under Categories A-E.</p>
    
    <form action="{{ route('conditions.store') }}" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
        @csrf
        <select name="category_id" class="form-control" style="flex: 1;" required>
            <option value="">Select Category</option>
            @foreach($categoryList as $c)
                <option value="{{ $c->id }}">Category {{ $c->code }} - {{ $c->name }}</option>
            @endforeach
        </select>
        <input type="text" name="name" class="form-control" style="flex: 2;" placeholder="Condition Name (e.g. Fever)" required>
        <button type="submit" class="btn-save">Add New</button>
    </form>

    <table class="mar-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Condition Name</th>
                <th style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allConditions as $cond)
            <tr>
                <td><strong>Category {{ $cond->category->code }}</strong></td>
                <td>{{ $cond->name }}</td>
                <td style="text-align: center;">
                    <form action="{{ route('conditions.destroy', $cond->id) }}" method="POST" onsubmit="return confirm('Delete this condition?')">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-weight: bold;">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #94a3b8; padding: 20px;">No conditions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection