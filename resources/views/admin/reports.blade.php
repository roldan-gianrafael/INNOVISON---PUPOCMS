@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>

/* --- DASHBOARD CONTAINER --- */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
}


/* --- FRAME / CARD --- */
.report-frame {
    border: 2px solid #800000;
    border-radius: 15px;
    padding: 40px 60px;
    background: #ffffff;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
}


/* --- BUTTON GROUP --- */
.buttons {
    display: flex;
    flex-direction: column;
    gap: 20px;
}


/* --- BUTTON STYLE --- */
.btn {
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 10px;
    font-weight: 600;
    background-color: maroon;
    color: whitesmoke;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    font-size: 16px;
}


/* --- HOVER EFFECT --- */
.btn:hover {

    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0,0,0,0.2);
    background-color: #a00000;
}


/* --- CLICK EFFECT --- */
.btn:active {

    transform: scale(0.95);

}


/* --- TITLE --- */
.report-title {

    margin-bottom: 25px;
    font-size: 22px;
    font-weight: bold;
    color: maroon;

}


</style>
@endpush

@section('content')

<div class="dashboard-container">

    <div class="report-frame">

        <div class="report-title">
            Reports
        </div>

        <div class="buttons">

            <a href="{{ route('reports.mar') }}" class="btn">
                MAR
            </a>

            <a href="#" class="btn">
                Appointment Report
            </a>

            <a href="#" class="btn">
                Inventory Report
            </a>

        </div>

    </div>

</div>

@endsection