<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;

class WalkInController extends Controller
{
    // Show Walk-in page
    public function index(Request $request)
{
    $scannedStudent = null;

    if ($request->filled('student_id')) {
        $scannedStudent = \App\Models\User::where('barcode', $request->student_id)
                            ->orWhere('student_id', $request->student_id)
                            ->first();
    }

    return view('admin.walkin.index', compact('scannedStudent'));
}


    // Store a walk-in appointment
    public function store(Request $request)
{
    $request->validate([
        'student_id' => 'required|string',
        'service' => 'required|string',
        'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    $user = User::where('student_id', $request->student_id)->first();
    if(!$user){
        return response()->json(['message'=>'Student not found'], 404);
    }

    $fileName = null;
    if($request->hasFile('attachment')){
        $file = $request->file('attachment');
        $fileName = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('uploads'), $fileName);
    }

    $appointment = Appointment::create([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'student_id' => $user->student_id,
        'service' => $request->service,
        'type' => 'walk-in',
        'status' => 'Approved',
        'date' => now()->toDateString(),
        'time' => now()->format('H:i:s'),
        'notes' => $fileName,
    ]);

    return response()->json([
        'student' => $user,
        'appointment' => $appointment
    ]);
}
}