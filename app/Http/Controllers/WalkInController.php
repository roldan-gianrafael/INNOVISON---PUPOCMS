<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use App\Services\PuptasWebhookService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalkInController extends Controller
{
    private function normalizeLookupName(?string $value): string
    {
        $value = Str::upper((string) $value);
        $value = preg_replace('/[^A-Z\s]/', ' ', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
    }

    private function namesRoughlyMatch(?string $extractedName, User $student): bool
    {
        $needle = $this->normalizeLookupName($extractedName);
        if ($needle === '') {
            return true;
        }

        $candidates = array_filter([
            $student->name ?? '',
            trim(implode(' ', array_filter([
                $student->first_name ?? '',
                $student->middle_name ?? '',
                $student->last_name ?? '',
            ]))),
            trim(implode(' ', array_filter([
                $student->first_name ?? '',
                $student->last_name ?? '',
            ]))),
        ]);

        foreach ($candidates as $candidate) {
            $normalizedCandidate = $this->normalizeLookupName($candidate);
            if ($normalizedCandidate === '') {
                continue;
            }

            if ($normalizedCandidate === $needle) {
                return true;
            }

            if (Str::contains($normalizedCandidate, $needle) || Str::contains($needle, $normalizedCandidate)) {
                return true;
            }

            $needleParts = array_values(array_filter(explode(' ', $needle)));
            $candidateParts = array_values(array_filter(explode(' ', $normalizedCandidate)));

            if (count($needleParts) >= 2 && count($candidateParts) >= 2) {
                $firstMatches = ($needleParts[0] ?? null) === ($candidateParts[0] ?? null);
                $lastMatches = ($needleParts[count($needleParts) - 1] ?? null) === ($candidateParts[count($candidateParts) - 1] ?? null);

                if ($firstMatches && $lastMatches) {
                    return true;
                }
            }
        }

        return false;
    }

    private function findUserByIdentifier(string $identifier): ?User
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        return User::with('healthProfile')
            ->where(function ($query) use ($identifier) {
                if (\Schema::hasColumn('users', 'student_number')) {
                    $query->orWhere('student_number', $identifier);
                }

                $query->orWhere('barcode', $identifier)
                    ->orWhere('student_id', $identifier);
            })
            ->first();
    }

    private function resolveUniqueStudentId(string $seed): string
    {
        $candidate = trim($seed) !== '' ? trim($seed) : ('idp-' . Str::lower(Str::random(12)));
        $base = $candidate;
        $counter = 1;

        while (User::where('student_id', $candidate)->exists()) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    private function resolveLocalUserFromApplicant(array $applicant): User
    {
        $studentNumber = trim((string) data_get($applicant, 'student_number'));
        $idpUserId = trim((string) data_get($applicant, 'idp_user_id'));
        $email = trim((string) data_get($applicant, 'email'));

        $user = User::query()
            ->when($idpUserId !== '', fn ($query) => $query->orWhere('student_id', $idpUserId))
            ->when($studentNumber !== '' && \Schema::hasColumn('users', 'student_number'), fn ($query) => $query->orWhere('student_number', $studentNumber))
            ->when($email !== '', fn ($query) => $query->orWhere('email', $email))
            ->first();

        $firstName = trim((string) data_get($applicant, 'first_name'));
        $lastName = trim((string) data_get($applicant, 'last_name'));
        $fullName = trim(implode(' ', array_filter([$firstName, $lastName])));

        if (!$user) {
            $user = new User();
            $user->student_id = $this->resolveUniqueStudentId($idpUserId !== '' ? $idpUserId : $studentNumber);
            $user->password = Hash::make(Str::random(40));
            $user->user_role = User::ROLE_STUDENT;
            $user->status = \Schema::hasColumn('users', 'status') ? 'active' : null;
        }

        if ($studentNumber !== '' && \Schema::hasColumn('users', 'student_number')) {
            $user->student_number = $studentNumber;
        }

        if ($idpUserId !== '' && trim((string) $user->student_id) === '') {
            $user->student_id = $this->resolveUniqueStudentId($idpUserId);
        }

        if ($firstName !== '') {
            $user->first_name = $firstName;
        }

        if ($lastName !== '') {
            $user->last_name = $lastName;
        }

        if ($fullName !== '') {
            $user->name = $fullName;
        }

        if ($email !== '') {
            $user->email = $email;
        } elseif (!$user->exists || trim((string) $user->email) === '') {
            $seed = $studentNumber !== '' ? $studentNumber : ($idpUserId !== '' ? $idpUserId : Str::lower(Str::random(8)));
            $user->email = Str::slug($seed, '.') . '@idp.local';
        }

        $user->save();

        return $user;
    }

    private function resolveAssistedEmail(string $referenceId): string
    {
        $base = Str::slug($referenceId, '.');
        $base = $base !== '' ? $base : ('assisted.' . Str::lower(Str::random(8)));

        $email = $base . '@assisted.local';
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = $base . '.' . $counter . '@assisted.local';
            $counter++;
        }

        return $email;
    }

    private function inAssistantWorkspace(Request $request): bool
    {
        return $request->is('assistant/*');
    }

    private function walkinRouteName(Request $request, string $suffix): string
    {
        if ($this->inAssistantWorkspace($request)) {
            return 'assistant.walkin.' . $suffix;
        }

        return 'walkin.' . $suffix;
    }

    private function adminBasePrefix(Request $request): string
    {
        return $this->inAssistantWorkspace($request) ? '/assistant' : '/admin';
    }

    private function extractOpenAiOutputText(array $payload): string
    {
        $outputText = trim((string) data_get($payload, 'output_text', ''));
        if ($outputText !== '') {
            return $outputText;
        }

        $parts = [];
        foreach ((array) data_get($payload, 'output', []) as $output) {
            foreach ((array) data_get($output, 'content', []) as $content) {
                $text = trim((string) data_get($content, 'text', ''));
                if ($text !== '') {
                    $parts[] = $text;
                }
            }
        }

        return trim(implode("\n", $parts));
    }

    private function decodeAiVerificationText(string $text): ?array
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        $text = preg_replace('/^```json\s*/i', '', $text) ?? $text;
        $text = preg_replace('/^```\s*/', '', $text) ?? $text;
        $text = preg_replace('/\s*```$/', '', $text) ?? $text;

        $decoded = json_decode($text, true);
        if (!is_array($decoded)) {
            return null;
        }

        return [
            'student_number' => trim((string) ($decoded['student_number'] ?? '')),
            'first_name' => trim((string) ($decoded['first_name'] ?? '')),
            'surname' => trim((string) ($decoded['surname'] ?? '')),
            'full_name' => trim((string) ($decoded['full_name'] ?? '')),
            'confidence_note' => trim((string) ($decoded['confidence_note'] ?? '')),
        ];
    }

    // 1. INDEX PAGE
    public function index(Request $request)
{

    $mode = $request->query('mode', '');
    $walkins = Appointment::latest()->take(10)->get();
    
    return view('admin.walkin', compact('walkins', 'mode'));
}

    // 2. SHOW WALKIN FORM
    public function showWalkinForm(Request $request, $student_id)
    {
        $student = $this->findUserByIdentifier((string) $student_id);
        abort_if(!$student, 404);
        $user_source = $request->query('source', 'walkin');

        $latestAppointment = null;

        // Kukuha lang tayo ng data kung ang source link ay 'online'
        if ($user_source === 'online') {
            $latestAppointment = Appointment::query()
                ->where('status', 'Approved')
                ->where(function ($query) use ($student) {
                    $query->where('student_id', $student->student_id);

                    if (!empty($student->student_number)) {
                        $query->orWhere('student_number', $student->student_number);
                    }
                })
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

        $consultationDob = (string) ($student->healthProfile->birthday ?? $student->DOB ?? '');
        if ($consultationDob !== '') {
            try {
                $consultationDob = \Carbon\Carbon::parse($consultationDob)->format('Y-m-d');
            } catch (\Throwable $exception) {
                $consultationDob = '';
            }
        }

        $consultationHeight = $student->healthProfile->height ?? $student->height ?? null;
        $consultationWeight = $student->healthProfile->weight ?? $student->weight ?? null;

        return view('admin.consult-form', compact(
            'student',
            'items',
            'conditions',
            'latestAppointment',
            'user_source',
            'consultationDob',
            'consultationHeight',
            'consultationWeight'
        ));
    }

    // 3. GET STUDENT INFO
    public function getStudent(Request $request, PuptasWebhookService $puptasWebhookService)
    {
        $lookup = trim((string) $request->student_id);
        $lookupName = trim((string) $request->student_name);

        $student = $this->findUserByIdentifier($lookup);
        $lookupMessage = 'No patient matched that student number in local records or PUPTAS.';
        $lookupStatus = null;

        if (!$student && $lookup !== '') {
            $lookupResult = $puptasWebhookService->fetchApplicantByStudentNumberDetailed($lookup);
            $lookupStatus = $lookupResult['status'] ?? null;
            $lookupMessage = trim((string) ($lookupResult['message'] ?? '')) ?: $lookupMessage;
            $applicant = $lookupResult['data'] ?? null;

            if (is_array($applicant)) {
                $student = $this->resolveLocalUserFromApplicant($applicant);
            }
        }

        if ($student) {
            if (!$this->namesRoughlyMatch($lookupName, $student)) {
                return response()->json([
                    'status' => 'name_mismatch',
                    'lookup_status' => $lookupStatus,
                    'message' => 'The student number matched a record, but the extracted name does not match our saved name yet.',
                    'candidate' => [
                        'student_number' => $student->student_number ?: $student->student_id,
                        'name' => trim((string) ($student->name ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')))),
                    ],
                ]);
            }

            return response()->json([
                'status' => 'found',
                'redirect_url' => route($this->walkinRouteName($request, 'form'), [
                    'student_id' => $student->student_number ?: $student->student_id,
                    'source' => 'walkin'
                ])
            ]);
        }

        return response()->json([
            'status' => 'not_found',
            'scanned_barcode' => $lookup,
            'lookup_status' => $lookupStatus,
            'message' => $lookupMessage,
        ]);
    }

    public function verifyStudentIdWithAi(Request $request)
    {
        $request->validate([
            'image_data' => 'required|string',
        ]);

        $apiKey = trim((string) config('services.openai.api_key'));
        if ($apiKey === '') {
            return response()->json([
                'status' => 'unavailable',
                'message' => 'AI verification is not configured yet. Please add OPENAI_API_KEY first.',
            ], 422);
        }

        $imageData = trim((string) $request->input('image_data'));
        if (!Str::startsWith($imageData, 'data:image/')) {
            return response()->json([
                'status' => 'invalid_image',
                'message' => 'The captured ID image is invalid. Please capture the card again.',
            ], 422);
        }

        $model = trim((string) config('services.openai.model', ''));
        if ($model === '') {
            $model = 'gpt-4.1-mini';
        }

        $prompt = <<<'PROMPT'
You are reading a school ID card for clinic intake.
Extract only the printed identity fields and return strict JSON with these keys:
student_number, first_name, surname, full_name, confidence_note

Rules:
- The card layout is vertical:
  1. given/first name on an upper line
  2. surname on the line below
  3. student number below the name lines
- Student number format may look like: 2025-00523-TG-0
- Preserve hyphens in the student number.
- If a field is unclear, return an empty string for that field.
- full_name should combine first_name and surname when both are readable.
- confidence_note should be a short plain-English note about the extraction quality.
- Return JSON only. No markdown fence. No explanation.
PROMPT;

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(30)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => $model,
                    'input' => [[
                        'role' => 'user',
                        'content' => [
                            ['type' => 'input_text', 'text' => $prompt],
                            ['type' => 'input_image', 'image_url' => $imageData, 'detail' => 'high'],
                        ],
                    ]],
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'api_error',
                    'message' => 'AI verification could not finish right now.',
                    'details' => $response->json() ?: $response->body(),
                ], $response->status());
            }

            $payload = $response->json() ?: [];
            $text = $this->extractOpenAiOutputText($payload);
            $decoded = $this->decodeAiVerificationText($text);

            if (!$decoded) {
                Log::warning('AI ID verification returned non-JSON text.', ['text' => $text]);

                return response()->json([
                    'status' => 'parse_error',
                    'message' => 'AI verification returned an unreadable response. Please try again.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'student_number' => $decoded['student_number'],
                'student_name' => trim($decoded['full_name'] !== '' ? $decoded['full_name'] : trim($decoded['first_name'] . ' ' . $decoded['surname'])),
                'first_name' => $decoded['first_name'],
                'surname' => $decoded['surname'],
                'confidence_note' => $decoded['confidence_note'],
            ]);
        } catch (\Throwable $exception) {
            Log::error('AI ID verification failed.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'server_error',
                'message' => 'AI verification failed. Please try again or use manual confirmation.',
            ], 500);
        }
    }

    // 4. REGISTER STUDENT
    public function registerStudent(Request $request)
    {
        $request->validate([
            'student_number' => 'required',
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'nullable|email',
            'password'   => 'nullable|min:6',
            'user_role'  => 'required',
            'dob'        => 'nullable|date',
            'gender'     => 'nullable|string|max:50',
            'contact_no' => 'nullable|string|max:20',
        ]);

        $email = trim((string) $request->email);
        $password = trim((string) $request->password);

        $studentNumber = trim((string) $request->student_number);

        $existingUser = User::query()
                            ->where(function ($query) use ($studentNumber) {
                                if (\Schema::hasColumn('users', 'student_number')) {
                                    $query->orWhere('student_number', $studentNumber);
                                }

                                $query->orWhere('student_id', $studentNumber);
                            })
                            ->when($email !== '', function ($query) use ($email) {
                                $query->orWhere('email', $email);
                            })
                            ->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'This account already exists.',
                'redirect_url' => route($this->walkinRouteName($request, 'form'), ['student_id' => $existingUser->student_number ?: $existingUser->student_id])
            ], 409);
        }

        if ($email === '') {
            $email = $this->resolveAssistedEmail($studentNumber);
        }

        if ($password === '') {
            $password = Str::random(12);
        }

        $user = User::create([
            'student_id' => $this->resolveUniqueStudentId('assisted-' . Str::slug($studentNumber, '-')),
            'student_number' => $studentNumber,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'name'       => $request->first_name . ' ' . $request->last_name,
            'email'      => $email,
            'password'   => Hash::make($password),
            'user_role'  => $request->user_role, 
            'barcode'    => $request->barcode,
            'DOB'        => $request->input('dob'),
            'gender'     => $request->input('gender'),
            'contact_no' => $request->input('contact_no'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully!',
            'redirect_url' => route($this->walkinRouteName($request, 'form'), ['student_id' => $user->student_number ?: $user->student_id])
        ]);
    }

    // 5. FINAL STORE
    public function store(Request $request)
    {
        $request->validate([
            'student_number' => 'required',
            'service'      => 'required',
            'remarks'      => 'required',
            'condition_id' => 'required|exists:medical_conditions,id',
            'dob'          => 'nullable|date',
            'height'       => 'nullable|numeric|min:0|max:400',
            'weight'       => 'nullable|numeric|min:0|max:1000',
            'temp'         => 'nullable|numeric|min:30|max:45',
            'bp'           => 'nullable|string|max:20',
            'pulse_rate'   => 'nullable|integer|min:0|max:300',
            'respiratory_rate' => 'nullable|integer|min:0|max:120',
            'covid_status' => 'required|in:Yes,No',
            'reason_for_visit' => 'nullable|string|max:255',
        ]);

        $student = $this->findUserByIdentifier((string) $request->student_number);

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        if ($request->filled('dob')) {
            $student->DOB = $request->input('dob');
        }

        if ($request->filled('height')) {
            $student->height = $request->input('height');
        }

        if ($request->filled('weight')) {
            $student->weight = $request->input('weight');
        }

        $student->save();

        if ($student->healthProfile) {
            if ($request->filled('dob')) {
                $student->healthProfile->birthday = $request->input('dob');
            }

            if ($request->filled('height')) {
                $student->healthProfile->height = (string) $request->input('height');
            }

            if ($request->filled('weight')) {
                $student->healthProfile->weight = (string) $request->input('weight');
            }

            $student->healthProfile->save();
        }

        DB::transaction(function () use ($request, $student) {
            $requestedSource = trim((string) $request->input('user_type', 'walkin'));
            $isOnlineSource = $requestedSource === 'online';
            $finalSource = 'walkin';

            if ($isOnlineSource) {
                $existingAppt = Appointment::where('student_id', $student->student_id)
                    ->where('status', 'Approved')
                    ->whereDate('date', now()->format('Y-m-d'))
                    ->first();

                if (!$existingAppt && !empty($student->student_number)) {
                    $existingAppt = Appointment::where('student_number', $student->student_number)
                        ->where('status', 'Approved')
                        ->whereDate('date', now()->format('Y-m-d'))
                        ->first();
                }

                if ($existingAppt) {
                    $existingAppt->status = 'Completed';
                    $existingAppt->service = $request->service;
                    $existingAppt->save();
                    $finalSource = 'online';
                }
            }

            if ($finalSource !== 'online') {
                $appointment = new Appointment();
                $appointment->user_id    = $student->id;
                $appointment->student_id = $student->student_id;
                $appointment->student_number = $student->student_number ?? null;
                $appointment->name       = $student->first_name . ' ' . $student->last_name;
                $appointment->email      = $student->email; 
                $appointment->service    = $request->service;
                $appointment->remarks    = $request->input('reason_for_visit') ?: $request->remarks;
                $appointment->status     = 'Completed';
                $appointment->date       = now()->format('Y-m-d');
                $appointment->time       = now()->format('H:i:s'); 
                $appointment->type       = 'walkin';
                $appointment->user_type  = Appointment::normalizeUserType($student->user_role ?? $student->user_type);
                $appointment->save();
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
                'blood_pressure'       => $request->bp,
                'pulse_rate'           => $request->input('pulse_rate'),
                'respiratory_rate'     => $request->input('respiratory_rate'),
                'covid_status'         => $request->input('covid_status'),
                'reason_for_visit'     => $request->input('reason_for_visit'),
                'medicine'             => $medicineName,
                'medicine_quantity'    => $request->input('issued_quantity') ?? 0, // Fallback to 0 to avoid SQL error
                'comments'             => $request->remarks,
            ]);
        });

        // Redirect logic
        if ($request->input('user_type') === 'online') {
            return redirect($this->adminBasePrefix($request) . '/appointments')
                ->with('success', 'Online consultation completed!');
        }

        return redirect()->route($this->walkinRouteName($request, 'index'))->with('consultation_done', true);
    }
}
