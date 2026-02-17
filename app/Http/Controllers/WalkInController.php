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
            $scannedStudent = User::where('barcode', $request->student_id)
                                ->orWhere('student_id', $request->student_id)
                                ->first();
        }

        return view('admin.walkin.index', compact('scannedStudent'));
    }

    // Get student info by student_id (AJAX)
    public function getStudent(Request $request)
    {
        $student_id = $request->student_id;

        $student = User::where('student_id', $student_id)->first();

        if ($student) {
            return response()->json(['success' => true, 'student' => $student]);
        } else {
            // Return a default walk-in object if not found
            return response()->json([
                'success' => false,
                'student' => [
                    'student_id' => $student_id,
                    'name' => 'Walk-in Client',
                    'email' => '-'
                ]
            ]);
        }
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

    // **NEW METHOD**: Handle "Select" from modal
    public function selectStudent(Request $request)
    {
        $student_id = $request->student_id;

        $student = User::where('student_id', $student_id)->first();

        if (!$student) {
            // Optionally, create a default walk-in client if not in users
            $student = User::create([
                'student_id' => $student_id,
                'name' => 'Walk-in Client',
                'email' => '-',
            ]);
        }

        // Create walk-in appointment
        $appointment = Appointment::create([
            'user_id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'student_id' => $student->student_id,
            'service' => null, // will add in modal later
            'type' => 'walk-in',
            'status' => 'Pending',
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
            'notes' => null,
        ]);

        return response()->json([
            'success' => true,
            'student' => $student,
            'appointment' => $appointment
        ]);
    }
}
