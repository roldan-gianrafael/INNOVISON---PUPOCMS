<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        $selectedDate = Carbon::parse($request->date)->startOfDay();
        $today = Carbon::today();
        if ($selectedDate->lt($today)) {
            return redirect()->back()->withInput()->with('error', 'Past dates are not available.');
        }

        if ($selectedDate->isWeekend()) {
            return redirect()->back()->withInput()->with('error', 'Appointments are available from Monday to Friday only.');
        }

        $selectedDateTime = Carbon::parse($request->date . ' ' . $request->time);
        if ($selectedDateTime->lt(Carbon::now())) {
            return redirect()->back()->withInput()->with('error', 'Please choose a future appointment time.');
        }

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
        $appointment->type            = 'online';
        $appointment->user_type       = Appointment::normalizeUserType($user->user_role);
        $appointment->save(); // Dito lang dapat magtatapos ang command.

        return redirect()->back()
            ->with('success', 'Appointment request submitted! Please wait for admin approval.')
            ->with('appointment_confirmation', [
                'service' => $appointment->service,
                'date' => Carbon::parse($appointment->date)->format('M d, Y'),
                'time' => Carbon::parse($appointment->time)->format('g:i A'),
                'status' => $appointment->status,
            ]);
    }

    public function availability(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $selectedDate = Carbon::parse($request->date)->startOfDay();
        $today = Carbon::today();

        if ($selectedDate->lt($today)) {
            return response()->json([
                'date' => $selectedDate->toDateString(),
                'available' => false,
                'reason' => 'past_date',
                'message' => 'Past dates are not available.',
                'slots' => [],
            ]);
        }

        if ($selectedDate->isWeekend()) {
            return response()->json([
                'date' => $selectedDate->toDateString(),
                'available' => false,
                'reason' => 'weekend',
                'message' => 'Appointments are available from Monday to Friday only.',
                'slots' => [],
            ]);
        }

        $takenTimes = Appointment::whereDate('date', $selectedDate->toDateString())
            ->where('status', '!=', 'Cancelled')
            ->pluck('time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->values()
            ->all();

        $dailyBookedCount = Appointment::whereDate('date', $selectedDate->toDateString())
            ->where('status', '!=', 'Cancelled')
            ->count();

        if ($dailyBookedCount >= 10) {
            return response()->json([
                'date' => $selectedDate->toDateString(),
                'available' => false,
                'reason' => 'fully_booked',
                'message' => 'This date is fully booked.',
                'slots' => [],
            ]);
        }

        $takenLookup = array_fill_keys($takenTimes, true);
        $slots = [];
        $now = Carbon::now();
        $dailyCount = 0;

        for ($hour = 8; $hour <= 19; $hour++) {
            foreach ([0, 30] as $minute) {
                if ($hour === 19 && $minute > 0) {
                    continue;
                }

                $slotTime = Carbon::createFromTime($hour, $minute, 0);
                $slotValue = $slotTime->format('H:i');
                $slotDateTime = Carbon::parse($selectedDate->toDateString() . ' ' . $slotValue);
                $isTaken = isset($takenLookup[$slotValue]);
                $isPastTime = $slotDateTime->lt($now);
                $isAvailable = !$isTaken && !$isPastTime;

                if ($isAvailable) {
                    $dailyCount++;
                }

                $slots[] = [
                    'value' => $slotValue,
                    'label' => $slotTime->format('g:i A'),
                    'available' => $isAvailable,
                ];
            }
        }

        return response()->json([
            'date' => $selectedDate->toDateString(),
            'available' => $dailyCount > 0,
            'reason' => $dailyCount > 0 ? null : 'fully_booked',
            'message' => $dailyCount > 0 ? null : 'No available time slots for this date.',
            'slots' => $slots,
        ]);
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


    // ---------------------------------------------------------
// HEALTH FORM FUNCTIONS
// ---------------------------------------------------------

public function showHealthForm()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    
    if ($user->is_health_profile_completed) {
        return redirect()->route('print.health.form')->with('info', 'You have already completed your health profile.');
    }

    $calculatedAge = null;
    if ($user->DOB) {
        $calculatedAge = \Carbon\Carbon::parse($user->DOB)->age;
    }
    return view('student.health_form', compact('user', 'calculatedAge'));
}


public function storeHealthForm(Request $request)
{
    // 1. VALIDATION: Siguraduhin na lahat ng fields ay tama ang format
    $request->validate([
        'school_year'       => 'required|string',
        'home_address'      => 'required|string|max:255',
        'student_photo'     => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        'age'               => 'required|numeric|min:15|max:100',
        'sex'               => 'required|string',
        'civil_status'      => 'required|string',
        'course_college'    => 'required|string',
        'blood_type'        => 'nullable|string|max:10',
        'guardian_name'     => 'required|string|max:255',
        'cellphone'         => 'required|string|max:20',
        
        // Medical History
        'medical_history'   => 'nullable|array', 
        'has_illness'       => 'required|string',
        'has_disability'    => 'required|string',
        
        // Allergies
        'medicine_allergies' => 'nullable|array',
        
        // Signature
        'digital_signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    /** @var \App\Models\User $user */
    $user = Auth::user();

    try {
        // 2. FILE HANDLING: I-save ang Photo at Signature sa storage
        // Gagamit ng 'public' disk para ma-access ng 'storage:link'
        $photoPath = $request->file('student_photo')->store('health_profiles/photos', 'public');
        $sigPath = $request->file('digital_signature')->store('health_profiles/signatures', 'public');

        // 3. LOGIC: I-save ang data sa health_profiles table
        \App\Models\HealthProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'school_year'        => $request->school_year,
                'home_address'       => $request->home_address,
                'student_photo'      => $photoPath,
                'age'                => $request->age,
                'sex'                => $request->sex,
                'civil_status'       => $request->civil_status,
                'course_college'     => $request->course_college,
                'blood_type'         => $request->blood_type,
                'guardian_name'      => $request->guardian_name,
                'landline'           => $request->landline,
                'cellphone'          => $request->cellphone,

                // Part II
                'has_illness'        => $request->has_illness,
                'medical_history'    => $request->medical_history, 
                'other_illness'      => $request->other_illness,
                'has_disability'     => $request->has_disability,
                'disability_type'    => $request->disability_type,

                // Section 3
                'food_allergies'      => $request->food_allergies,
                'no_allergies'        => $request->has('no_allergies'),
                'medicine_allergies'  => $request->medicine_allergies,
                'other_med_allergies' => $request->other_med_allergies,

                // Part III: COVID Vax
                'vaccine_history' => [
                    'dose1'    => ['date' => $request->vax_date_1, 'brand' => $request->vax_brand_1],
                    'dose2'    => ['date' => $request->vax_date_2, 'brand' => $request->vax_brand_2],
                    'booster1' => ['date' => $request->booster_date_1, 'brand' => $request->booster_brand_1],
                    'booster2' => ['date' => $request->booster_date_2, 'brand' => $request->booster_brand_2],
                ],

                'digital_signature'  => $sigPath,
                'is_smoker'          => $request->smoking ?? 'No',
                'is_drinker'         => $request->alcohol ?? 'No',
            ]
        );

        // 4. UPDATE USER STATUS
        $user->is_health_profile_completed = 1;
        $user->save();

        // 5. ACTIVITY LOG
        \App\Models\ActivityLog::create([
            'user_id'     => $user->id,
            'user_name'   => $user->name,
            'action'      => 'Health Profile Completed',
            'description' => "Student completed the detailed Health Information Form (HIF).",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        // Eto yung binago natin para diretso agad sa Print Form pagkatapos mag-submit
        return redirect()->route('print.health.form')->with('success', 'Health Profile saved! You can now print your form.');

    } catch (\Exception $e) {
   
        return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

public function printHealthForm()
{
    $user = Auth::user();
    // Kunin ang profile record
    $profile = \App\Models\HealthProfile::where('user_id', $user->id)->first();

    if (!$profile) {
        return redirect()->route('health.form')->with('error', 'Please fill up the form first.');
    }

    return view('student.print_health_form', compact('profile'));
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

