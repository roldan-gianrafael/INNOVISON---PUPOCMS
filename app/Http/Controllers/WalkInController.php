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
    public function index(Request $request)
{

    $mode = $request->query('mode', 'scan'); 
    $walkins = Appointment::latest()->take(10)->get();
    
    return view('admin.walkin', compact('walkins', 'mode'));
}

    // 2. SHOW WALKIN FORM
    public function showWalkinForm(Request $request, $student_id)
    {
        $student = User::where('student_id', $student_id)->firstOrFail();
        $user_source = $request->query('source', 'walkin');

        $latestAppointment = null;

        // Kukuha lang tayo ng data kung ang source link ay 'online'
        if ($user_source === 'online') {
            $latestAppointment = Appointment::where('student_id', $student_id)
                                            ->where('status', 'Approved')
                                            ->latest()
                                            ->first();

            if ($latestAppointment) {
                $appointmentDate = \Carbon\Carbon::parse($latestAppointment->date)->startOfDay();
                $today = \Carbon\Carbon::today();

                if ($appointmentDate->gt($today)) {
                    return redirect()->back()->with('error', 'Consultation denied. This appointment is scheduled for ' . $appointmentDate->format('F d, Y') . '.');
                }
            }
        }

        $items = \App\Models\Item::where('category', 'Medicine')
                                 ->where('quantity', '>', 0)
                                 ->get();

        $conditions = \App\Models\MedicalConditions::with('category')->get();

        return view('admin.walkin-form', compact('student', 'items', 'conditions', 'latestAppointment', 'user_source'));
    }

    // 3. GET STUDENT INFO
    public function getStudent(Request $request)
{
    $barcode = $request->student_id; 
    
    $student = User::where('barcode', $barcode)
                   ->orWhere('student_id', $barcode)
                   ->first();

    if ($student) {
        // --- SMART REDIRECT LOGIC ---
        // Check kung may Approved appointment siya NGAYONG ARAW
        $hasOnlineAppt = Appointment::where('student_id', $student->student_id)
                                    ->where('status', 'Approved')
                                    ->whereDate('date', now()->format('Y-m-d'))
                                    ->exists();

        // Kung may online appt, dagdagan natin ng ?source=online yung URL
        $source = $hasOnlineAppt ? 'online' : 'walkin';

        return response()->json([
            'status' => 'found',
            'redirect_url' => route('walkin.form', [
                'student_id' => $student->student_id, 
                'source' => $source // Ito ang magsasabi sa form kung online or walkin
            ])
        ]);
    } else {
        return response()->json([
            'status' => 'not_found',
            'scanned_barcode' => $barcode
        ]);
    }
}

    // 4. REGISTER STUDENT
    public function registerStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email',
            'password'   => 'required|min:6',
            'user_role'  => 'required',
        ]);

        $existingUser = User::where('student_id', $request->student_id)
                            ->orWhere('email', $request->email)
                            ->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'This account already exists.',
                'redirect_url' => route('walkin.form', ['student_id' => $existingUser->student_id])
            ], 409);
        }

        $user = User::create([
            'student_id' => $request->student_id,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'name'       => $request->first_name . ' ' . $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'user_role'  => $request->user_role, 
            'barcode'    => $request->barcode,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully!',
            'redirect_url' => route('walkin.form', ['student_id' => $user->student_id])
        ]);
    }

    // 5. FINAL STORE
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
            
            // --- SMART CHECK: May Approved Online Appointment ba siya NGAYONG ARAW? ---
            $existingAppt = Appointment::where('student_id', $student->student_id)
                                        ->where('status', 'Approved')
                                        ->whereDate('date', now()->format('Y-m-d'))
                                        ->first();

            if ($existingAppt) {
                // Kung may Approved Online: I-update lang yung record to 'Completed'
                $existingAppt->status = 'Completed';
                $existingAppt->service = $request->service; 
                $existingAppt->save();
                
                $finalSource = 'online';
            } else {
                // Kung wala: Gawa ng bagong 'walkin' record sa Appointment Table
                $appointment = new Appointment();
                $appointment->user_id    = $student->id;
                $appointment->student_id = $student->student_id;
                $appointment->name       = $student->first_name . ' ' . $student->last_name;
                $appointment->email      = $student->email; 
                $appointment->service    = $request->service;
                $appointment->remarks    = $request->remarks;
                $appointment->status     = 'Completed';
                $appointment->date       = now()->format('Y-m-d');
                $appointment->time       = now()->format('H:i:s'); 
                $appointment->type       = 'walkin';
                $appointment->user_type  = Appointment::normalizeUserType($student->user_role ?? $student->user_type);
                $appointment->save();
                
                $finalSource = 'walkin';
            }

            // --- MEDICINE LOGIC ---
            $medicineName = 'None';
            if ($request->filled('item_id')) {
                $item = \App\Models\Item::find($request->item_id);
                $medicineName = $item ? $item->name : 'None';

                // Bawasan ang inventory kung may quantity na binigay
                if ($item && $request->issued_quantity > 0) {
                    $item->decrement('quantity', $request->issued_quantity);
                }
            }

            // --- SAVE TO CONSULTATIONS TABLE ---
            \App\Models\Consultation::create([
                'name'                 => $student->first_name . ' ' . $student->last_name,
                'consultation_date'    => now()->format('Y-m-d'),
                'user_role'            => $student->user_role, 
                'user_type'            => $finalSource, 
                'service'              => $request->service,
                'medical_condition_id' => $request->condition_id,
                'temperature'          => $request->temp,
                'medicine'             => $medicineName,
                'medicine_quantity'    => $request->input('issued_quantity') ?? 0, // Fallback to 0 to avoid SQL error
                'comments'             => $request->remarks,
            ]);
        });

        // Redirect logic
        if ($request->input('user_type') === 'online') {
            return redirect('/admin/appointments')->with('success', 'Online consultation completed!');
        }

        return redirect()->route('walkin.index')->with('consultation_done', true);
    }
}
