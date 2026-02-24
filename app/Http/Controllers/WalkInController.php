<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WalkInController extends Controller
{
    // 1. INDEX PAGE
    public function index()
    {
        $walkins = Appointment::latest()->take(10)->get();
        return view('admin.walkin', compact('walkins'));
    }

    // 2. SHOW WALKIN FORM (The page users go to after scanning)
    public function showWalkinForm($student_id)
{
    $student = User::where('student_id', $student_id)->firstOrFail();
    
    // get medicine with stock
    $items = \App\Models\Item::where('category', 'Medicine')
                             ->where('quantity', '>', 0)
                             ->get();

    //get med conditons
    $conditions = \App\Models\MedicalConditions::with('category')->get();

    return view('admin.walkin-form', compact('student', 'items', 'conditions'));
}

    // 3. GET STUDENT INFO (Modified for Barcode Redirect)
    public function getStudent(Request $request)
{
    $barcode = $request->student_id; // This is the data from the scanner
    
    // First, try to find the user by the barcode column
    $student = User::where('barcode', $barcode)
                   ->orWhere('student_id', $barcode)
                   ->first();

    if ($student) {
        return response()->json([
            'status' => 'found',
            'redirect_url' => route('walkin.form', ['student_id' => $student->student_id])
        ]);
    } else {
        // If not found, we return the barcode so JS can pre-fill the registration form
        return response()->json([
            'status' => 'not_found',
            'scanned_barcode' => $barcode
        ]);
    }
}

    // 4. REGISTER STUDENT (Includes user_type and auto-redirect)
    // 4. REGISTER STUDENT (With "Account Already Exists" check)
public function registerStudent(Request $request)
{
    $request->validate([
        'student_id' => 'required',
        'first_name' => 'required',
        'last_name'  => 'required',
        'email'      => 'required|email',
        'password'   => 'required|min:6',
        'user_type'  => 'required',
    ]);

    // Check if ID or Email already exists
    $existingUser = User::where('student_id', $request->student_id)
                        ->orWhere('email', $request->email)
                        ->first();

    if ($existingUser) {
        return response()->json([
            'success' => false,
            'message' => 'This account already exists (ID or Email matched).',
            // Provide the link so the nurse can jump straight to the consultation
            'redirect_url' => route('walkin.form', ['student_id' => $existingUser->student_id])
        ], 409); // 409 is the "Conflict" status code
    }

    // Create user if they don't exist
    $user = User::create([
        'student_id' => $request->student_id,
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'name'       => $request->first_name . ' ' . $request->last_name,
        'email'      => $request->email,
        'password'   => Hash::make($request->password),
        'user_type'  => $request->user_type, // Change 'role' to 'user_type'
        'barcode'    => $request->barcode,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Student registered successfully!',
        'redirect_url' => route('walkin.form', ['student_id' => $user->student_id])
    ]);
}

    // 5. FINAL STORE (Saves the consultation/appointment)
    public function store(Request $request)
{
    $request->validate([
        'student_id'   => 'required',
        'service'      => 'required',
        'remarks'      => 'required',
        'condition_id' => 'required|exists:medical_conditions,id',
    ]);

    $student = User::where('student_id', $request->student_id)->first();

    if (!$student) {
        return redirect()->back()->with('error', 'Student not found.');
    }

    DB::transaction(function () use ($request, $student) {
        
        // --- 1. APPOINTMENTS TABLE (General Log Only) ---
        $appointment = new Appointment();
        $appointment->user_id      = $student->id;
        $appointment->student_id   = $student->student_id;
        $appointment->name         = $student->first_name . ' ' . $student->last_name;
        $appointment->email        = $student->email; 
        $appointment->service      = $request->service;
        $appointment->remarks      = $request->remarks;
        // ALISIN ANG condition_id, temp, at bp DITO dahil wala ito sa appointments table mo
        $appointment->status       = 'Completed';
        $appointment->date         = now()->format('Y-m-d');
        $appointment->time         = now()->format('H:i:s'); 
        $appointment->user_role    = $student->user_type; 
        $appointment->user_type    = 'walkin';
        $appointment->save();

        // --- 2. CONSULTATIONS TABLE (Medical Details) ---
        $medicineName = null;
        if ($request->item_id) {
            $item = \App\Models\Item::find($request->item_id);
            $medicineName = $item ? $item->name : null;
        }

        \App\Models\Consultation::create([
            'name'                 => $student->first_name . ' ' . $student->last_name,
            'consultation_date'    => now()->format('Y-m-d'),
            'user_type'            => $student->user_type, 
            'service'              => $request->service,
            'medical_condition_id' => $request->condition_id, // Dito lang ito dapat
            'temperature'          => $request->temp,
            'medicine'             => $medicineName,
            'medicine_quantity'    => $request->issued_quantity,
            'comments'             => $request->remarks,
        ]);

        // --- 3. INVENTORY DEDUCTION ---
        if ($request->item_id && $request->issued_quantity) {
            $item = \App\Models\Item::find($request->item_id);
            if ($item && $item->quantity >= $request->issued_quantity) {
                $item->decrement('quantity', $request->issued_quantity);
            }
        }
    });

    return redirect()->route('walkin.index')->with('consultation_done', true);
}
}