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
            'notes' => 'nullable|string',
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

        // Create appointment
        Appointment::create([
            'user_id'    => $user->id,
            'student_id' => $request->input('student_id', '2025-0000-TG-0'),
            'name'       => $user->name,
            'email'      => $user->email,
            'date'       => $request->date,
            'time'       => $request->time,
            'service'    => $request->service,
            'status'     => 'Pending',
            'notes'      => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Appointment request submitted! Please wait for admin approval.');
    }

    // -------------------------------
    // 4. STUDENT ACCOUNT
    // -------------------------------
    public function account()
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
    $approvedCount = $appointments->where('status', 'Approved')->count();
    $completedCount = $appointments->where('status', 'Completed')->count();
    $cancelledCount = $appointments->where('status', 'Cancelled')->count();

    // --- Notifications ---
    $notifications = [];
    foreach ($appointments as $appt) {
        $timeAgo = $appt->updated_at ? $appt->updated_at->diffForHumans() : 'Just now';
        $dateStr = $appt->date ? date('M d', strtotime($appt->date)) : 'N/A';

        if ($appt->status == 'Approved') {
            $notifications[] = [
                'type' => 'success',
                'icon' => '✅',
                'message' => "Your {$appt->service} on {$dateStr} has been APPROVED.",
                'time' => $timeAgo
            ];
        } elseif ($appt->status == 'Cancelled') {
            $notifications[] = [
                'type' => 'danger',
                'icon' => '❌',
                'message' => "Your {$appt->service} on {$dateStr} was CANCELLED.",
                'time' => $timeAgo
            ];
        }
    }
    $notifications = collect($notifications);

    return view('student.account', compact(
        'user', 'appointments', 'pendingCount', 'approvedCount', 'completedCount', 'cancelledCount', 'notifications'
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
    // 7. Update Contact

    public function updateContact(Request $request)
{
    // 1. Get the logged-in user or guest
    $user = Auth::user();
    
    if (!$user) {
        $user = User::where('email', 'guest@pup.edu.ph')->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Guest user not found.');
        }
    }

    // 2. Validate the contact number
    $request->validate([
        'contact_number' => 'required|string|max:20', // adjust max length if needed
    ]);

    // 3. Update the contact number
    $user->contact_number = $request->contact_number;
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
// 9. BARCODE REGISTER PAGE
// -------------------------------
public function barcodeRegister()
{
    // Use logged-in user or temporary guest
    $user = Auth::user() ?? User::firstOrCreate(
        ['email' => 'guest@pup.edu.ph'],
        [
            'name' => 'Tero Student',
            'email' => 'guest@pup.edu.ph',
            'password' => bcrypt('password'),
            'is_admin' => 0,
            'student_id' => '2025-0000-TG-0', // temporary student ID
        ]
    );

    // Pass user to Blade
    return view('student.barcode-register', compact('user'));
}


// -------------------------------
// SAVE BARCODE
// -------------------------------
public function storeBarcode(Request $request)
{
    // Validate that barcode is present
    $request->validate([
        'barcode' => 'required|string|max:255'
    ]);

    $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

    if (!$user) {
        return redirect()->back()->with('error', 'User not found!');
    }

    // Save barcode to database
    $user->barcode = $request->barcode;
    $user->save();

    return redirect()->back()->with('success', 'Barcode registered successfully!');
}


// -------------------------------
// FETCH USER USING STUDENT ID
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

    return response()->json([
        'success' => false
    ]);
}


/////

// -------------------------------
// RESET BARCODE (for testing)
// -------------------------------
public function resetBarcode()
{
    // Get current student or guest
    $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }

    // Clear barcode
    $user->barcode = null;
    $user->save();

    return redirect()->back()->with('success', 'Barcode reset successfully! You can scan again.');
}


}
