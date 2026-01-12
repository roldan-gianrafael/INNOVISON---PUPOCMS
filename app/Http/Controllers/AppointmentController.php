<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment; 
use App\Models\User; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // --- 1. HOMEPAGE ---
    public function index()
    {
        if (Auth::check()) {
            $userId = Auth::id();
        } else {
            $guest = User::where('email', 'guest@pup.edu.ph')->first();
            $userId = $guest ? $guest->id : 999;
        }

        $myAppointments = Appointment::where('user_id', $userId)
                                     ->orderBy('date', 'desc')
                                     ->get();

        $upcoming = $myAppointments->where('status', 'Approved');
        $pending  = $myAppointments->where('status', 'Pending');
        $history  = $myAppointments->whereIn('status', ['Completed', 'Cancelled']);

        return view('student.home', compact('upcoming', 'pending', 'history'));
    }

    // --- 2. BOOKING FORM ---
    public function create()
    {
        $user = Auth::user();

        if (!$user) {
            $user = User::where('email', 'guest@pup.edu.ph')->first();
            if (!$user) {
                $user = new User();
                $user->name = 'Guest Student';
                $user->email = 'guest@pup.edu.ph';
                $user->id = 999; 
            }
        }
        
        $appointments = Appointment::where('user_id', $user->id)
                                   ->whereIn('status', ['Pending', 'Approved'])
                                   ->orderBy('date', 'asc')
                                   ->get();

        return view('student.booking', compact('user', 'appointments'));
    }

    // --- 3. STORE APPOINTMENT ---
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'service' => 'required',
            'notes' => 'nullable|string',
        ]);

        // 1. CHECK FOR OVERLAPPING SCHEDULES
        $exists = Appointment::where('date', $request->date)
                             ->where('time', $request->time)
                             ->where('status', '!=', 'Cancelled')
                             ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Sorry, that time slot is already taken.');
        }

        // 2. MAX PATIENTS CHECK
        $dailyCount = Appointment::where('date', $request->date)->where('status', '!=', 'Cancelled')->count();
        if ($dailyCount >= 10) {
            return redirect()->back()->withInput()->with('error', 'Fully booked for this date.');
        }

        // 3. CREATE OR UPDATE USER
        if (!Auth::check()) {
            // Find the guest user
            $user = User::firstOrCreate(
                ['email' => 'guest@pup.edu.ph'], 
                [
                    'password' => bcrypt('password'),
                    'is_admin' => 0
                ]
            );

            // FORCE UPDATE THE NAME (This fixes the Admin side)
            $user->name = 'Altheno Mari Tero';
            $user->save();

            $userId = $user->id;
            $userName = $user->name;
            $userEmail = $user->email;
        } else {
            $userId = Auth::id();
            $userName = Auth::user()->name;
            $userEmail = Auth::user()->email;
        }

        $studentId = $request->input('student_id', '2025-0000-TG-0');

        Appointment::create([
            'user_id'    => $userId,
            'student_id' => $studentId,
            'name'       => $userName, // Now this will use the updated name
            'email'      => $userEmail,
            'date'       => $request->date,
            'time'       => $request->time,
            'service'    => $request->service,
            'status'     => 'Pending',
            'notes'      => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Appointment request submitted! Please wait for admin approval.');
    }

    // --- 4. MY ACCOUNT (Fixed Missing Approved Count) ---
    public function account()
    {
        $user = Auth::user();
        
        // Guest Fallback
        if(!$user) {
             $user = User::where('email', 'guest@pup.edu.ph')->first();
             if(!$user) {
                 $user = new User();
                 $user->name = 'Altheno Mari Tero'; // <--- CHANGED HERE
                 $user->email = 'guest@pup.edu.ph';
                 $user->id = 999;
             }
        }

        $appointments = Appointment::where('user_id', $user->id)->latest()->get();
        
        // --- CALCULATE STATS ---
        $cancelledCount = $appointments->where('status', 'Cancelled')->count();
        $completedCount = $appointments->where('status', 'Completed')->count();
        $pendingCount   = $appointments->where('status', 'Pending')->count();
        $approvedCount  = $appointments->where('status', 'Approved')->count(); // <--- MATH IS DONE HERE

        // --- NOTIFICATIONS ---
        $notifications = [];
        foreach($appointments as $appt) {
            $timeAgo = $appt->updated_at ? $appt->updated_at->diffForHumans() : 'Just now';
            $dateStr = $appt->date ? date('M d', strtotime($appt->date)) : 'N/A';

            if($appt->status == 'Approved') {
                $notifications[] = [
                    'type' => 'success',
                    'icon' => '✅',
                    'message' => "Your {$appt->service} on {$dateStr} has been APPROVED.",
                    'date' => $timeAgo,
                    'time' => $timeAgo
                ];
            }
            elseif($appt->status == 'Cancelled') {
                $notifications[] = [
                    'type' => 'danger',
                    'icon' => '❌',
                    'message' => "Your {$appt->service} on {$dateStr} was Cancelled.",
                    'date' => $timeAgo,
                    'time' => $timeAgo
                ];
            }
        }
        $notifications = collect($notifications);

        // --- PASS VARIABLES TO VIEW ---
        return view('student.account', compact(
            'user', 'appointments', 'cancelledCount', 'completedCount', 
            'pendingCount', 'approvedCount', 'notifications'
        ));
    }

    // --- 5. OTHER PAGES ---
    public function cancel($id)
    {
        if (Auth::check()) {
            $userId = Auth::id();
        } else {
            $guest = User::where('email', 'guest@pup.edu.ph')->first();
            $userId = $guest ? $guest->id : 999;
        }

        $appointment = Appointment::where('id', $id)->where('user_id', $userId)->first();

        if ($appointment) {
            $appointment->status = 'Cancelled';
            $appointment->save();
            return redirect()->back()->with('success', 'Appointment cancelled.');
        }

        return redirect()->back()->with('error', 'Appointment not found.');
    }

    public function faq() 
    {
        // 1. Get User (Standard Logic)
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('email', 'guest@pup.edu.ph')->first();
            if (!$user) {
                $user = new User();
                $user->name = 'Guest Student'; 
                $user->email = 'guest@pup.edu.ph';
                $user->id = 999; 
            }
        }

        // 2. Fetch Appointments to Calculate Stats
        $appointments = Appointment::where('user_id', $user->id)->get();

        $pendingCount   = $appointments->where('status', 'Pending')->count();
        $upcomingCount  = $appointments->where('status', 'Approved')->count(); // Approved means Upcoming
        $completedCount = $appointments->where('status', 'Completed')->count();
        $cancelledCount = $appointments->where('status', 'Cancelled')->count();

        // 3. Return View with Data
        return view('student.faq', compact(
            'user', 
            'pendingCount', 
            'upcomingCount', 
            'completedCount', 
            'cancelledCount'
        ));
    }
    
    public function history()
    {
        // 1. Identify the User (Logged in or Guest)
        $user = Auth::user();
        
        if(!$user) {
             $user = User::where('email', 'guest@pup.edu.ph')->first();
             // Safe fallback if guest doesn't exist yet
             if(!$user) {
                 $user = new User();
                 $user->id = 999; 
             }
        }

        // 2. Fetch all appointments for this user, sorted by newest first
        $appointments = Appointment::where('user_id', $user->id)
                                   ->orderBy('date', 'desc')
                                   ->orderBy('time', 'desc')
                                   ->get();

        // 3. Send data to the view
        return view('student.history', compact('appointments'));
    }
}