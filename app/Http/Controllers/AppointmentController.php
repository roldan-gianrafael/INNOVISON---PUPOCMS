<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // -------------------------------
    // 1. STUDENT DASHBOARD
    // -------------------------------
    public function index()
    {
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

        if (!$user) {
            $user = User::create([
                'first_name' => 'Guest',
                'last_name' => 'Student',
                'name' => 'Guest Student',
                'email' => 'guest@pup.edu.ph',
                'password' => bcrypt('password'),
                'is_admin' => 0,
            ]);
        }

        $appointments = Appointment::where('user_id', $user->id)->get();
        $upcoming = $appointments->where('status', 'Approved');
        $pending = $appointments->where('status', 'Pending');
        $history = $appointments->whereIn('status', ['Completed', 'Cancelled']);

        return view('student.home', compact('upcoming', 'pending', 'history'));
    }

    // -------------------------------
    // 2. BOOKING FORM
    // -------------------------------
    public function create()
    {
        $user = Auth::user() ?? User::firstOrCreate(
            ['email' => 'guest@pup.edu.ph'],
            [
                'first_name' => 'Guest',
                'last_name' => 'Student',
                'name' => 'Guest Student',
                'password' => bcrypt('password'),
                'is_admin' => 0
            ]
        );

        $appointments = Appointment::where('user_id', $user->id)
                                   ->whereIn('status', ['Pending', 'Approved'])
                                   ->orderBy('date', 'asc')
                                   ->get();

        return view('student.booking', compact('user', 'appointments'));
    }

    // -------------------------------
    // 3. STORE APPOINTMENT
    // -------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'service' => 'required',
            'remarks' => 'nullable|string',
        ]);

        // Prevent overlapping appointments
        $exists = Appointment::where('date', $request->date)
                             ->where('time', $request->time)
                             ->where('status', '!=', 'Cancelled')
                             ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'That time slot is already taken.');
        }

        // Max 10 appointments per day
        $dailyCount = Appointment::where('date', $request->date)
                                 ->where('status', '!=', 'Cancelled')
                                 ->count();
        if ($dailyCount >= 10) {
            return redirect()->back()->withInput()->with('error', 'Fully booked for this date.');
        }

        // Get or create user
        $user = Auth::user() ?? User::firstOrCreate(
            ['email' => 'guest@pup.edu.ph'],
            [
                'first_name' => 'Guest',
                'last_name' => 'Student',
                'name' => 'Guest Student',
                'password' => bcrypt('password'),
                'is_admin' => 0
            ]
        );

        // Create appointment for student side
  

        $appointment = new Appointment();
        $appointment->user_id    = $user->id;
        $appointment->student_id = $request->input('student_id', '2025-0000-TG-0');
        $appointment->name       = $user->name;
        $appointment->email      = $user->email;
        $appointment->date       = $request->date;
        $appointment->time       = $request->time;
        $appointment->service    = $request->service;
        $appointment->status     = 'Pending';
        $appointment->remarks    = $request->remarks; 
        $appointment->user_type = $request->input('user_type', 'online');
        $appointment->save(); // Dito lang dapat magtatapos ang command.

        return redirect()->back()->with('success', 'Appointment request submitted! Please wait for admin approval.');
    }
    // -------------------------------
    // 4. STUDENT ACCOUNT
    // -------------------------------
   public function account()
{
    // 1. Kunin ang logged-in user. Kung walang session, redirect sa login page.
    $user = Auth::user();

    if (!$user) {
        // Imbis na gumawa ng guest, force login natin para stable ang testing
        return redirect('/login-as-student')->with('error', 'Please login first.');
    }

    // 2. Kunin ang appointments ng SPECIFIC user na naka-login
    $appointments = Appointment::where('user_id', $user->id)
                                ->orderBy('updated_at', 'desc')
                                ->get();

    // 3. Stats calculation
    $pendingCount   = $appointments->where('status', 'Pending')->count();
    $approvedCount  = $appointments->where('status', 'Approved')->count();
    $completedCount = $appointments->where('status', 'Completed')->count();
    $cancelledCount = $appointments->where('status', 'Cancelled')->count();

    // 4. Notification Logic
    $notifications = [];
    foreach ($appointments as $appt) {
        $timeAgo = $appt->updated_at ? $appt->updated_at->diffForHumans() : 'Just now';
        $dateStr = $appt->date ? date('M d', strtotime($appt->date)) : 'N/A';

        if ($appt->status == 'Approved') {
            $notifications[] = [
                'type'    => 'success',
                'icon'    => '✅',
                'message' => "Your {$appt->service} on {$dateStr} has been APPROVED.",
                'time'    => $timeAgo
            ];
        } elseif ($appt->status == 'Cancelled') {
            $notifications[] = [
                'type'    => 'danger',
                'icon'    => '❌',
                'message' => "Your {$appt->service} on {$dateStr} was CANCELLED.",
                'time'    => $timeAgo
            ];
        }
    }
    
    $notifications = collect($notifications);

    // 5. Return view user
    return view('student.account', compact(
        'user', 
        'appointments', 
        'pendingCount', 
        'approvedCount', 
        'completedCount', 
        'cancelledCount', 
        'notifications'
    ));
}


    // -------------------------------
    // 5. CANCEL APPOINTMENT
    // -------------------------------
    public function cancel($id)
    {
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();
        $appointment = Appointment::where('id', $id)->where('user_id', $user->id)->first();

        if ($appointment) {
            $appointment->status = 'Cancelled';
            $appointment->save();
            return redirect()->back()->with('success', 'Appointment cancelled.');
        }

        return redirect()->back()->with('error', 'Appointment not found.');
    }

    // -------------------------------
    // 6. FAQ PAGE
    // -------------------------------
    public function faq()
    {
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

        if (!$user) {
            $user = User::create([
                'first_name' => 'Guest',
                'last_name' => 'Student',
                'name' => 'Guest Student',
                'email' => 'guest@pup.edu.ph',
                'password' => bcrypt('password'),
                'is_admin' => 0,
            ]);
        }

        $appointments = Appointment::where('user_id', $user->id)->get();
        $pendingCount = $appointments->where('status', 'Pending')->count();
        $upcomingCount = $appointments->where('status', 'Approved')->count();
        $completedCount = $appointments->where('status', 'Completed')->count();
        $cancelledCount = $appointments->where('status', 'Cancelled')->count();

        return view('student.faq', compact('user', 'pendingCount', 'upcomingCount', 'completedCount', 'cancelledCount'));
    }

    //-------------------------------
    // 7. UPDATE CONTACT
    //-------------------------------
    public function updateContact(Request $request)
    {
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User session not found.');
        }

        $validated = $request->validate([
            'contact_number' => ['required', 'regex:/^[0-9]{10,13}$/'],
        ], [
            'contact_number.regex' => 'Contact number must be 10 to 13 digits.',
        ]);

        $user->contact_number = $validated['contact_number'];
        $user->save();

        return redirect()->back()->with('success', 'Contact number updated successfully.');
    }

    // -------------------------------
    // 8. APPOINTMENT HISTORY
    // -------------------------------
    public function history()
    {
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

        if (!$user) {
            $user = User::create([
                'first_name' => 'Guest',
                'last_name' => 'Student',
                'name' => 'Guest Student',
                'email' => 'guest@pup.edu.ph',
                'password' => bcrypt('password'),
                'is_admin' => 0,
            ]);
        }

        $appointments = Appointment::where('user_id', $user->id)
                                   ->orderBy('date', 'desc')
                                   ->orderBy('time', 'desc')
                                   ->get();

        return view('student.history', compact('appointments'));
    }

    // -------------------------------
    // 9. BARCODE REGISTER PAGE
    // -------------------------------
    public function barcodeRegister()
    {
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();
        return view('student.barcode-register', compact('user'));
    }

    // -------------------------------
    // SAVE BARCODE
    // -------------------------------
    public function storeBarcode(Request $request)
    {
        // Mas safe itong validation para hindi mag-crash ang app kung may duplicate
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();
        
        $request->validate([
            'barcode' => 'required|string|max:255|unique:users,barcode,' . $user->id
        ]);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found!');
        }

        $user->barcode = $request->barcode;
        $user->save();

        return redirect()->back()->with('success', 'Barcode registered successfully!');
    }

    // -------------------------------
    // FETCH USER USING STUDENT ID (Bridge for Walk-in)
    // -------------------------------
    public function fetchUser($student_id)
    {
        $user = User::where('student_id', $student_id)->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'name' => $user->name,
                'student_id' => $user->student_id,
                'barcode' => $user->barcode
            ]);
        }

        return response()->json(['success' => false]);
    }

    // -------------------------------
    // RESET BARCODE (for testing)
    // -------------------------------
    public function resetBarcode()
    {
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $user->barcode = null;
        $user->save();

        return redirect()->back()->with('success', 'Barcode reset successfully! You can scan again.');
    }
} 

