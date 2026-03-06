<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    private function normalizeBarcodeValue(?string $value): string
    {
        return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $value ?? ''));
    }

    private function getBarcodeMismatchMessage(User $user, string $barcode): ?string
    {
        $studentId = trim((string) $user->student_id);
        if ($studentId === '') {
            return null;
        }

        $normalizedStudentId = $this->normalizeBarcodeValue($studentId);
        $normalizedBarcode = $this->normalizeBarcodeValue($barcode);

        if ($normalizedStudentId !== '' && $normalizedBarcode !== '' && $normalizedStudentId !== $normalizedBarcode) {
            return 'The scanned barcode does not match your Student ID.';
        }

        return null;
    }

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
        $appointment->user_type       = 'online';
        $appointment->user_role = Appointment::normalizeUserType($user->user_role);
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
    // 1. Kunin ang user
    $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

    if (!$user) {
        return redirect()->back()->with('error', 'User session not found.');
    }

    // 2. I-validate ang lahat ng fields (Contact, Year, Section, etc.)
    $validated = $request->validate([
        'contact_no' => ['required', 'regex:/^[0-9]{10,13}$/'],
        'year'       => ['required', 'string', 'max:10'],
        'section'    => ['required', 'string', 'max:10'],
        'height'     => ['nullable', 'numeric'],
        'weight'     => ['nullable', 'numeric'],
    ], [
        'contact_no.regex' => 'Contact number must be 10 to 13 digits.',
    ]);

    // 3. I-track ang changes para sa Log (Optional: Para alam ng Admin kung ano ang binago)
    $oldYear = $user->year;
    $newYear = $validated['year'];

    // 4. I-save ang mga bagong data
    $user->contact_no = $validated['contact_no'];
    $user->year = $validated['year'];
    $user->section = $validated['section'];
    $user->height = $validated['height'];
    $user->weight = $validated['weight'];
    $user->save();

    // 5. SYSTEM LOG ---
    \App\Models\ActivityLog::create([
        'user_id'     => $user->id,
        'user_name'   => $user->name,
        'action'      => 'Profile Update',
        'description' => "Updated profile: Year ($oldYear to $newYear), Section, and Medical Info.",
        'ip_address'  => $request->ip(),
        'user_agent'  => $request->userAgent(),
    ]);

    return redirect()->back()->with('success', 'Profile and academic details updated successfully.');
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
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found!');
        }

        $request->validate([
            'barcode' => 'required|string|max:255'
        ]);

        $barcode = trim((string) $request->barcode);
        $mismatchMessage = $this->getBarcodeMismatchMessage($user, $barcode);
        if ($mismatchMessage) {
            return redirect()->back()->withInput()->withErrors([
                'barcode' => $mismatchMessage,
            ]);
        }

        $request->merge(['barcode' => $barcode]);
        $request->validate([
            'barcode' => 'required|string|max:255|unique:users,barcode,' . $user->id
        ]);

        $user->barcode = $barcode;
        $user->save();

        return redirect()->back()->with('success', 'Barcode registered successfully!');
    }

    public function validateBarcodeScan(Request $request)
    {
        $user = Auth::user() ?? User::where('email', 'guest@pup.edu.ph')->first();

        if (!$user) {
            return response()->json([
                'valid' => false,
                'message' => 'User session not found. Please login again.',
            ], 401);
        }

        $request->validate([
            'barcode' => 'required|string|max:255',
        ]);

        $barcode = trim((string) $request->barcode);
        $mismatchMessage = $this->getBarcodeMismatchMessage($user, $barcode);
        if ($mismatchMessage) {
            return response()->json([
                'valid' => false,
                'message' => $mismatchMessage,
            ], 422);
        }

        $barcodeInUse = User::where('barcode', $barcode)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($barcodeInUse) {
            return response()->json([
                'valid' => false,
                'message' => 'This barcode is already linked to another account.',
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'barcode' => $barcode,
            'message' => 'Barcode validated. You can submit registration.',
        ]);
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
    // HEALTH FORM FUNTION
    // -------------------------------

public function showHealthForm()
{
    $user = Auth::user();
    
    // Kung tapos na siya, i-redirect na lang natin sa home para hindi paulit-ulit
    if ($user->is_health_profile_completed) {
        return redirect()->route('student.home')->with('info', 'You have already completed your health profile.');
    }

    return view('student.health_form', compact('user'));
}

public function storeHealthForm(Request $request)
{
    // 1. I-validate ang lahat ng parts (Student Info, Medical History, Social, Vaccines)
    $request->validate([
        'medical_history' => 'required|array',
        'smoking' => 'required|string',
        'alcohol' => 'required|string',
        // Dagdagan mo ang validation dito base sa iba pang fields
    ]);

    /** @var \App\Models\User $user */
    $user = Auth::user();

    // 2. LOGIC: Dito mo ise-save ang data sa isang bagong table (e.g., HealthProfile model)
    // Halimbawa: HealthProfile::create($request->all() + ['user_id' => $user->id]);

    // 3. UPDATE USER STATUS: Ito ang pinaka-importante para mawala ang modal
    $user->is_health_profile_completed = 1;
    $user->save();

    // 4. ACTIVITY LOG (Hard Mode style)
    \App\Models\ActivityLog::create([
        'user_id'     => $user->id,
        'user_name'   => $user->name,
        'action'      => 'Health Profile Completed',
        'description' => "Student completed the mandatory Health Information Form (HIF).",
        'ip_address'  => $request->ip(),
        'user_agent'  => $request->userAgent(),
    ]);

    return redirect()->route('student.home')->with('success', 'Health Profile submitted successfully! You can now access clinic services.');
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

