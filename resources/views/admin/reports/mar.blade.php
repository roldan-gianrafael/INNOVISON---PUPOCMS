@extends('layouts.admin')

@section('title', 'MAR Report')

@section('content')
<h2>Medical Accomplishment Report</h2>

<form method="GET" class="mb-3">
    <label for="month">Filter by Month:</label>
    <input type="month" name="month" id="month" value="{{ $month ?? '' }}">
    <button class="btn" type="submit" style="background:#70131B;color:white;">Filter</button>
   
</form>

<<table border="1" cellpadding="10" width="100%">
    <thead>
        <tr>
            <th>Category</th>
            <th>Student Count</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $category => $data)
    <tr>
        <td>{{ $category }}</td>
        <td>{{ $data['Student'] }}</td>
        <td>{{ $data['Total'] }}</td>
    </tr>
@endforeach
    </tbody>
</table>
@endsection