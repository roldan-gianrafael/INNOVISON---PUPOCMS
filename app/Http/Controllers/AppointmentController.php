<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Appointment;
use App\Models\HealthProfile;
use App\Models\User;
use App\Services\PuptasWebhookService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    private function fetchPuptasApplicantForUser(User $user): ?array
    {
        $puptasService = app(PuptasWebhookService::class);

        $studentNumber = trim((string) ($user->student_number ?? ''));
        if ($studentNumber !== '') {
            $applicantByStudentNumber = $puptasService->fetchApplicantByStudentNumber($studentNumber);
            if (is_array($applicantByStudentNumber) && !empty($applicantByStudentNumber)) {
                return $applicantByStudentNumber;
            }
        }

        $idpUserId = trim((string) ($user->student_id ?? ''));
        if ($idpUserId !== '') {
            $applicantByIdpUserId = $puptasService->fetchApplicantByIdpUserId($idpUserId);
            if (is_array($applicantByIdpUserId) && !empty($applicantByIdpUserId)) {
                return $applicantByIdpUserId;
            }
        }

        return null;
    }

    private function normalizeSexValue(?string $value): string
    {
        $value = strtolower(trim((string) $value));

        return match ($value) {
            'male' => 'Male',
            'female' => 'Female',
            default => '',
        };
    }

    private function isPuptasApplicant(array $applicantData): bool
    {
        if (empty($applicantData)) {
            return false;
        }

        if (is_array(data_get($applicantData, 'application'))) {
            return true;
        }

        if (trim((string) data_get($applicantData, 'medical_process_status')) !== '') {
            return true;
        }

        return trim((string) data_get($applicantData, 'student_number')) === '';
    }

    private function resolveSchoolYear(?array $applicantData, User $user): string
    {
        $now = now();
        $calendarYear = (int) $now->format('Y');
        $academicStartMonth = 7;

        $isApplicant = is_array($applicantData) && $this->isPuptasApplicant($applicantData);

        if ($isApplicant) {
            return $calendarYear . '-' . ($calendarYear + 1);
        }

        $startYear = ((int) $now->format('n')) >= $academicStartMonth
            ? $calendarYear
            : ($calendarYear - 1);

        return $startYear . '-' . ($startYear + 1);
    }

    private function normalizeMeasurement(?string $value, string $unit): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $normalizedUnit = strtolower(trim($unit));
        $normalizedValue = preg_replace('/\s+/', ' ', $value) ?? $value;

        if (!str_contains(strtolower($normalizedValue), $normalizedUnit)) {
            $normalizedValue .= ' ' . $normalizedUnit;
        }

        return $normalizedValue;
    }

    private function normalizeDoctorName(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = preg_replace('/^(dr\.?\s*)+/i', '', $value) ?? $value;

        return 'Dr. ' . trim($value);
    }

    private function buildHealthFormPrefill(User $user, ?Admin $linkedAdminProfile = null, ?HealthProfile $healthProfile = null): array
    {
        $linkedAdminProfile = $linkedAdminProfile ?: $this->resolveLinkedAdminProfile($user);
        $applicantData = $this->fetchPuptasApplicantForUser($user);

        $calculatedAge = null;
        if (!empty($user->DOB)) {
            try {
                $calculatedAge = \Carbon\Carbon::parse($user->DOB)->age;
            } catch (\Throwable $exception) {
                $calculatedAge = null;
            }
        }

        $resolvedSex = $this->normalizeSexValue(
            data_get($applicantData, 'sex')
            ?: (optional($healthProfile)->sex ?? $user->gender ?? optional($linkedAdminProfile)->gender ?? '')
        );

        $resolvedCivilStatus = trim((string) (optional($healthProfile)->civil_status ?? optional($linkedAdminProfile)->civil_status ?? ''));
        $resolvedCivilStatus = in_array($resolvedCivilStatus, ['Single', 'Married'], true) ? $resolvedCivilStatus : 'Single';

        $resolvedBirthday = (string) (
            $user->DOB
            ?: optional($linkedAdminProfile)->birthday
            ?: data_get($applicantData, 'birthday')
            ?: ''
        );

        if ($resolvedBirthday !== '') {
            try {
                $resolvedBirthday = \Carbon\Carbon::parse($resolvedBirthday)->format('Y-m-d');
            } catch (\Throwable $exception) {
                $resolvedBirthday = '';
            }
        }

        $resolvedAge = optional($healthProfile)->age ?? $calculatedAge;
        if ($resolvedBirthday !== '') {
            try {
                $resolvedAge = \Carbon\Carbon::parse($resolvedBirthday)->age;
            } catch (\Throwable $exception) {
                // keep existing resolved age
            }
        }

        $resolvedAddress = trim(implode(', ', array_filter([
            data_get($applicantData, 'street_address'),
            data_get($applicantData, 'barangay'),
            data_get($applicantData, 'city'),
            data_get($applicantData, 'province'),
        ])));

        return [
            'full_name' => trim(implode(' ', array_filter([
                data_get($applicantData, 'first_name') ?: data_get($applicantData, 'firstname'),
                data_get($applicantData, 'middle_name') ?: data_get($applicantData, 'middlename'),
                data_get($applicantData, 'last_name') ?: data_get($applicantData, 'lastname'),
            ]))) ?: trim((string) $user->name),
            'student_id' => (string) (optional($healthProfile)->student_id ?? $user->student_id ?? ''),
            'student_number' => (string) (
                data_get($applicantData, 'student_number')
                ?: (optional($healthProfile)->student_number ?? $user->student_number ?: ($user->student_id ?: ''))
            ),
            'email' => (string) (
                optional(optional($healthProfile)->user)->email
                ?? data_get($applicantData, 'email')
                ?: ($user->email ?? optional($linkedAdminProfile)->email ?? '')
            ),
            'course_college' => trim((string) (
                optional($healthProfile)->course_college
                ?? trim(implode(' - ', array_filter([
                    data_get($applicantData, 'program.code'),
                    data_get($applicantData, 'program.name'),
                ])))
                ?: trim(implode(' - ', array_filter([
                    data_get($applicantData, 'strand'),
                    data_get($applicantData, 'track'),
                ])))
                ?: ($user->course ?? '')
            )),
            'home_address' => trim((string) (
                optional($healthProfile)->home_address
                ?: ($resolvedAddress !== '' ? $resolvedAddress : trim((string) (optional($linkedAdminProfile)->address ?? '')))
            )),
            'zipcode' => trim((string) (optional($healthProfile)->zipcode ?? data_get($applicantData, 'postal_code') ?? '')),
            'school_year' => (string) (optional($healthProfile)->school_year ?? $this->resolveSchoolYear($applicantData, $user)),
            'height' => (string) (optional($healthProfile)->height ?? $user->height ?? ''),
            'weight' => (string) (optional($healthProfile)->weight ?? $user->weight ?? ''),
            'birthday' => $resolvedBirthday,
            'age' => $resolvedAge,
            'sex' => $resolvedSex,
            'civil_status' => $resolvedCivilStatus,
            'blood_type' => (string) (optional($healthProfile)->blood_type ?? 'Not Known'),
            'contact_number' => trim((string) ($user->contact_no ?? data_get($applicantData, 'contactnumber') ?? '')),
            'guardian_name' => trim((string) (optional($healthProfile)->guardian_name ?? optional($linkedAdminProfile)->emergency_contact_person ?? '')),
            'cellphone' => trim((string) (
                optional($healthProfile)->cellphone
                ?: (optional($linkedAdminProfile)->emergency_contact_no ?? '')
            )),
            'landline' => (string) (optional($healthProfile)->landline ?? ''),
            'office' => trim((string) (optional($linkedAdminProfile)->office ?? '')),
            'access_level' => trim((string) (optional($linkedAdminProfile)->access_level ?? '')),
        ];
    }

    private function hasSubmittedHealthProfile(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->relationLoaded('healthProfile')) {
            return $user->healthProfile !== null;
        }

        return HealthProfile::query()->where('user_id', $user->id)->exists();
    }

    private function resolveLinkedAdminProfile(?User $user): ?Admin
    {
        if (!$user || !Admin::hasColumn('email')) {
            return null;
        }

        $email = trim(strtolower((string) $user->email));
        if ($email === '') {
            return null;
        }

        return Admin::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();
    }

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
        $appointment->user_id = $user->id;
        $appointment->student_id = $request->input('student_id', '2025-0000-TG-0');
        $appointment->student_number = $request->input('student_number', (string) ($user->student_number ?? ''));
        $appointment->name = $user->name;
        $appointment->email = $user->email;
        $appointment->date = $request->date;
        $appointment->time = $request->time;
        $appointment->service = $request->service;
        $appointment->status = 'Pending';
        $appointment->remarks = $request->remarks; 
        $appointment->type = 'online';
        $appointment->user_type = Appointment::normalizeUserType($user->user_role);
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

    $user->load('healthProfile');

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
    
    $hasSubmittedHealthProfile = $this->hasSubmittedHealthProfile($user);
    $healthProfileStatus = optional($user->healthProfile)->clearance_status;

    $notifications = collect($notifications);

    if ($hasSubmittedHealthProfile) {
        $notifications->prepend([
            'type' => $healthProfileStatus === 'Issued' ? 'success' : 'warning',
            'icon' => $healthProfileStatus === 'Issued' ? 'OK' : '...',
            'message' => $healthProfileStatus === 'Issued'
                ? 'Your health profile has been approved by the clinic.'
                : 'Your health profile was submitted and is awaiting medical review.',
            'time' => 'Health form status',
        ]);
    }

    // 5. Return view user
    $linkedAdminProfile = $this->resolveLinkedAdminProfile($user);
    $accountProfileData = $this->buildHealthFormPrefill($user, $linkedAdminProfile, $user->healthProfile);

    return view('student.account', compact(
        'user', 
        'appointments', 
        'pendingCount', 
        'approvedCount', 
        'completedCount', 
        'cancelledCount', 
        'notifications',
        'linkedAdminProfile',
        'hasSubmittedHealthProfile',
        'accountProfileData'
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

    $linkedAdminProfile = $this->resolveLinkedAdminProfile($user);

    // 2. I-validate ang lahat ng fields (Contact, Year, Section, etc.)
    $validated = $request->validate([
        'contact_no' => ['required', 'regex:/^[0-9]{10,13}$/'],
        'year'       => empty($linkedAdminProfile) ? ['required', 'string', 'max:10'] : ['nullable', 'string', 'max:10'],
        'section'    => empty($linkedAdminProfile) ? ['required', 'string', 'max:10'] : ['nullable', 'string', 'max:10'],
        'height'     => ['nullable', 'numeric'],
        'weight'     => ['nullable', 'numeric'],
        'admin_profile_id' => ['nullable', 'integer', 'exists:admins,admin_id'],
        'first_name' => ['nullable', 'string', 'max:255'],
        'middle_name' => ['nullable', 'string', 'max:255'],
        'last_name' => ['nullable', 'string', 'max:255'],
        'suffix_name' => ['nullable', 'string', 'max:50'],
        'email' => ['nullable', 'email', 'max:255'],
        'birthday' => ['nullable', 'date'],
        'age' => ['nullable', 'integer', 'min:0'],
        'gender' => ['nullable', 'string', 'max:255'],
        'civil_status' => ['nullable', 'string', 'max:255'],
        'address' => ['nullable', 'string', 'max:1000'],
        'emergency_contact_person' => ['nullable', 'string', 'max:255'],
        'emergency_contact_no' => ['nullable', 'string', 'max:255'],
        'office' => ['nullable', 'string', 'max:255'],
    ], [
        'contact_no.regex' => 'Contact number must be 10 to 13 digits.',
    ]);

    // 3. I-track ang changes para sa Log (Optional: Para alam ng Admin kung ano ang binago)
    $oldYear = $user->year;
    $newYear = $validated['year'] ?? $user->year;

    // 4. I-save ang mga bagong data
    $user->contact_no = $validated['contact_no'];
    $user->year = $validated['year'] ?? $user->year;
    $user->section = $validated['section'] ?? $user->section;
    $user->height = $validated['height'] ?? $user->height;
    $user->weight = $validated['weight'] ?? $user->weight;
    if ($linkedAdminProfile && isset($validated['admin_profile_id']) && (int) $validated['admin_profile_id'] === (int) $linkedAdminProfile->admin_id) {
        $linkedAdminProfile->first_name = $validated['first_name'] ?? $linkedAdminProfile->first_name;
        $linkedAdminProfile->middle_name = $validated['middle_name'] ?? $linkedAdminProfile->middle_name;
        $linkedAdminProfile->last_name = $validated['last_name'] ?? $linkedAdminProfile->last_name;
        $linkedAdminProfile->suffix_name = $validated['suffix_name'] ?? $linkedAdminProfile->suffix_name;
        $linkedAdminProfile->email = $validated['email'] ?? $linkedAdminProfile->email;
        $linkedAdminProfile->birthday = $validated['birthday'] ?? $linkedAdminProfile->birthday;
        $linkedAdminProfile->age = $validated['age'] ?? $linkedAdminProfile->age;
        $linkedAdminProfile->gender = $validated['gender'] ?? $linkedAdminProfile->gender;
        $linkedAdminProfile->civil_status = $validated['civil_status'] ?? $linkedAdminProfile->civil_status;
        $linkedAdminProfile->address = $validated['address'] ?? $linkedAdminProfile->address;
        $linkedAdminProfile->emergency_contact_person = $validated['emergency_contact_person'] ?? $linkedAdminProfile->emergency_contact_person;
        $linkedAdminProfile->emergency_contact_no = $validated['emergency_contact_no'] ?? $linkedAdminProfile->emergency_contact_no;
        $linkedAdminProfile->office = $validated['office'] ?? $linkedAdminProfile->office;
        $linkedAdminProfile->save();

        $user->first_name = $validated['first_name'] ?? $user->first_name;
        $user->last_name = $validated['last_name'] ?? $user->last_name;
        $user->name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name;
        $user->email = $validated['email'] ?? $user->email;
    }

    $user->save();

    // 5. SYSTEM LOG ---
    \App\Models\ActivityLog::create([
        'user_id'     => $user->id,
        'user_name'   => $user->name,
        'action'      => 'Profile Update',
        'description' => "Updated profile: Year ($oldYear to $newYear), Section, Medical Info, and linked designee info when available.",
        'ip_address'  => $request->ip(),
        'user_agent'  => $request->userAgent(),
    ]);

    return redirect()->back()->with('success', 'Profile details updated successfully.');
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
                'student_number' => $user->student_number,
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
    
    if ($this->hasSubmittedHealthProfile($user)) {
        return redirect()->route('print.health.form')
            ->with('info', 'You have already submitted your health profile.');
    }

    // Resolve the linked admin profile (Required by your view to avoid Undefined Variable error)
    $linkedAdminProfile = $this->resolveLinkedAdminProfile($user);
    $healthFormPrefill = $this->buildHealthFormPrefill($user, $linkedAdminProfile);
    $calculatedAge = $healthFormPrefill['age'] ?? null;

    // Return the view with all required variables
    return view('student.health_form', compact('user', 'calculatedAge', 'linkedAdminProfile', 'healthFormPrefill'));
}

public function storeHealthForm(Request $request)
{
    // 1. VALIDATION: Siguraduhin na lahat ng fields ay tama ang format
    $request->validate([
        'student_id'        => 'nullable|string|max:255',
        'student_number'    => 'required|string|max:255',
        'school_year'       => 'required|string',
        'home_address'      => 'required|string|max:255',
        'zipcode'           => 'required|string|max:20',
        'student_photo'     => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'height'            => 'required|string|max:50',
        'weight'            => 'required|string|max:50',
        'age'               => 'required|numeric|min:15|max:100',
        'sex'               => 'required|string',
        'civil_status'      => 'required|string',
        'course_college'    => 'required|string',
        'blood_type'        => 'required|string|max:20',
        'contact_no'        => 'required|string|max:20',
        'guardian_name'     => 'required|string|max:255',
        'cellphone'         => 'required|string|max:20',
        
        // Medical History
        'medical_history'   => 'nullable|array', 
        'has_illness'       => 'required|string',
        'chest_xray_result' => 'required|file|mimes:jpeg,jpg,png,pdf|max:4096',
        'has_disability'    => 'required|string',
        'disability_type'   => 'required_if:has_disability,Yes|nullable|string|max:255',
        'pwd_id_proof'      => 'required_if:has_disability,Yes|file|mimes:jpeg,jpg,png,pdf|max:4096',
        
        // Allergies
        'food_allergies' => 'required_without:no_allergies|nullable|string|max:255',
        'medicine_allergies' => 'required_without:no_allergies|nullable|array|min:1',
        'medical_certificate' => 'required|file|mimes:jpeg,jpg,png,pdf|max:4096',
        'medical_certificate_issued_by' => 'required|string|max:255',

        // Vaccination
        'vax_date_1' => 'required|date',
        'vax_brand_1' => 'required|string|max:255',
        'vax_date_2' => 'required|date',
        'vax_brand_2' => 'required|string|max:255',
        'booster_date_1' => 'required|date',
        'booster_brand_1' => 'required|string|max:255',
        'booster_date_2' => 'required|date',
        'booster_brand_2' => 'required|string|max:255',
        
        // Signature
        'digital_signature' => 'required_without:digital_signature_drawn|nullable|image|mimes:jpeg,png,jpg|max:2048',
        'digital_signature_drawn' => 'required_without:digital_signature|nullable|string',
    ]);

    /** @var \App\Models\User $user */
    $user = Auth::user();
    $normalizedHeight = $this->normalizeMeasurement($request->input('height'), 'cm');
    $normalizedWeight = $this->normalizeMeasurement($request->input('weight'), 'kg');
    $normalizedDoctorName = $this->normalizeDoctorName($request->input('medical_certificate_issued_by'));
    $user->contact_no = $request->input('contact_no');
    $user->save();

    try {
        // 2. FILE HANDLING: I-save ang Photo at Signature sa storage
        // Gagamit ng 'public' disk para ma-access ng 'storage:link'
        $photoPath = $request->file('student_photo')->store('health_profiles/photos', 'public');
        $sigPath = null;
        if ($request->hasFile('digital_signature')) {
            $sigPath = $request->file('digital_signature')->store('health_profiles/signatures', 'public');
        } else {
            $drawnSignature = (string) $request->input('digital_signature_drawn', '');
            if (!preg_match('/^data:image\/png;base64,/', $drawnSignature)) {
                throw new \RuntimeException('The drawn signature format is invalid.');
            }

            $binarySignature = base64_decode(substr($drawnSignature, strpos($drawnSignature, ',') + 1), true);
            if ($binarySignature === false) {
                throw new \RuntimeException('The drawn signature could not be processed.');
            }

            $signatureFileName = 'health_profiles/signatures/' . uniqid('drawn-signature-', true) . '.png';
            Storage::disk('public')->put($signatureFileName, $binarySignature);
            $sigPath = $signatureFileName;
        }
        $chestXrayPath = $request->hasFile('chest_xray_result')
            ? $request->file('chest_xray_result')->store('health_profiles/chest_xray_results', 'public')
            : null;
        $pwdIdProofPath = $request->hasFile('pwd_id_proof')
            ? $request->file('pwd_id_proof')->store('health_profiles/pwd_id_proofs', 'public')
            : null;
        $medicalCertificatePath = $request->hasFile('medical_certificate')
            ? $request->file('medical_certificate')->store('health_profiles/medical_certificates', 'public')
            : null;

        // 3. LOGIC: I-save ang data sa health_profiles table
        \App\Models\HealthProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'student_id'         => $request->student_id,
                'student_number'     => $request->student_number,
                'school_year'        => $request->school_year,
                'home_address'       => $request->home_address,
                'zipcode'            => $request->zipcode,
                'student_photo'      => $photoPath,
                'height'             => $normalizedHeight,
                'weight'             => $normalizedWeight,
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
                'chest_xray_result'  => $chestXrayPath,
                'has_disability'     => $request->has_disability,
                'disability_type'    => $request->disability_type,
                'pwd_id_proof'       => $pwdIdProofPath,

                // Section 3
                'food_allergies'      => $request->food_allergies,
                'no_allergies'        => $request->has('no_allergies'),
                'medicine_allergies'  => $request->medicine_allergies,
                'other_med_allergies' => $request->other_med_allergies,
                'medical_certificate' => $medicalCertificatePath,
                'medical_certificate_issued_by' => $normalizedDoctorName,

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

        $user->is_health_profile_completed = 0;
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

        return redirect('/student/account')
            ->with('health_profile_submitted', true)
            ->with('success', 'Health Profile saved successfully.');

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

