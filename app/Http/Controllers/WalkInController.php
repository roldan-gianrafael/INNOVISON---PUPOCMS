<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class WalkInController extends Controller
{
    // -------------------------------
    // 1. INDEX PAGE
    // -------------------------------
    public function index()
    {
        // Optional: fetch latest walk-ins to display in table
        $walkins = Appointment::latest()->take(10)->get();

        return view('admin.walkin.index', compact('walkins'));
    }

    // -------------------------------
    // 2. GET STUDENT INFO (AJAX)
    // -------------------------------
    public function getStudent(Request $request)
    {
        $student_id = $request->student_id;
        $student = User::where('student_id', $student_id)->first();

        return response()->json([
            'student' => $student
        ]);
    }

    // -------------------------------
    // 3. REGISTER STUDENT
    // -------------------------------
    public function register(Request $request)
    {
        $request->validate([
            'student_id' => 'required|unique:users,student_id',
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = new User();
        $user->student_id = $request->student_id;
        $user->name       = $request->name;
        $user->email      = $request->email;
        $user->password   = Hash::make($request->password);

        // Save attachment if uploaded
        if($request->hasFile('attachment')){
            $path = $request->file('attachment')->store('students', 'public');
            $user->attachment = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully!',
            'student' => $user
        ]);
    }

    // -------------------------------
    // 4. STORE WALK-IN
    // -------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,student_id',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'service'    => 'nullable|string|max:255', // optional
            'date'       => 'nullable|date',
            'time'       => 'nullable',
        ]);

        $student = User::where('student_id', $request->student_id)->first();

        if(!$student){
            return response()->json(['success'=>false,'message'=>'Student not found!'], 404);
        }

        $appointment = new Appointment();
        $appointment->user_id   = $student->id;
        $appointment->student_id= $student->student_id;
        $appointment->name      = $student->name;
        $appointment->email     = $student->email;
        $appointment->service   = $request->service ?? 'Walk-in';
        $appointment->date      = $request->date ?? now()->format('Y-m-d');
        $appointment->time      = $request->time ?? now()->format('H:i:s');
        $appointment->status    = 'Pending';

        // Save attachment if uploaded
        if($request->hasFile('attachment')){
            $path = $request->file('attachment')->store('walkins', 'public');
            $appointment->attachment = $path;
        }

        $appointment->save();

        return response()->json([
            'success'     => true,
            'message'     => 'Walk-in added successfully!',
            'student'     => $student,
            'appointment' => $appointment
        ]);
    }
    public function registerStudent(Request $request)
{
    $request->validate([
        'student_id' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6',
        'barcode' => 'nullable'
    ]);

    // Check if student_id or email already exists
    $existingUser = \App\Models\User::where('student_id', $request->student_id)
                        ->orWhere('email', $request->email)
                        ->first();

    if ($existingUser) {
        return response()->json([
            'success' => false,
            'message' => 'Student already exists in the system!',
            'student' => $existingUser
        ]);
    }

    // Create user
    $user = \App\Models\User::create([
        'student_id' => $request->student_id,
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'name'       => $request->first_name . ' ' . $request->last_name,
        'email'      => $request->email,
        'password'   => bcrypt($request->password),
        'barcode'    => $request->barcode,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Student registered successfully!',
        'student' => $user
    ]);
}
    
}
