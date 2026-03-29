<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Setting;
use App\Models\Admin;
use App\Services\FacultySyncService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\HealthProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private function isStudentAssistantAccount(User $user): bool
    {
        $userType = strtolower(trim((string) ($user->user_type ?? '')));
        return in_array($userType, ['assistant', 'student assistant', 'student_assistant'], true);
    }

    private function isSuperadminAccount(User $user): bool
    {
        return User::normalizeRole($user->user_role) === User::ROLE_SUPERADMIN;
    }

    private function findLinkedAdminProfile(User $user): ?Admin
    {
        return $this->findLinkedAdminProfileByEmails([
            trim((string) ($user->email ?? '')),
        ]);
    }

    private function findLinkedAdminProfileByEmails(array $emails): ?Admin
    {
        if (!Schema::hasTable('admins')) {
            return null;
        }

        $emails = array_values(array_filter(array_unique(array_map(static function ($value) {
            return trim((string) $value);
        }, $emails))));

        if ($emails === []) {
            return null;
        }

        $query = Admin::query();

        $query->where(function ($builder) use ($emails) {
            if (Admin::hasColumn('email')) {
                $builder->orWhereIn('email', $emails);
            }

            if (Admin::hasColumn('email_address')) {
                $builder->orWhereIn('email_address', $emails);
            }
        });

        return $query->first();
    }

    private function splitDisplayName(string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['', '', ''];
        }

        $suffixes = ['jr', 'jr.', 'sr', 'sr.', 'ii', 'iii', 'iv', 'v'];
        $parts = preg_split('/\s+/', $name) ?: [$name];
        $suffix = '';

        if (count($parts) > 1) {
            $lastPart = strtolower((string) end($parts));
            if (in_array($lastPart, $suffixes, true)) {
                $suffix = (string) array_pop($parts);
            }
        }

        $parts = array_values($parts);
        $firstName = $parts[0] ?? '';
        $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        return [$firstName, $lastName, $suffix];
    }

    private function buildCmsAdminProfile(User $user): array
    {
        $isStudentAssistant = $this->isStudentAssistantAccount($user);
        $isSuperadmin = $this->isSuperadminAccount($user);
        $linkedAdmin = $isSuperadmin ? $this->findLinkedAdminProfile($user) : null;

        $birthday = $linkedAdmin?->birthday;
        $age = null;
        if ($birthday) {
            try {
                $age = Carbon::parse($birthday)->age;
            } catch (\Throwable $exception) {
                $age = null;
            }
        }

        $resolvedRole = $linkedAdmin?->access_level
            ?? ($isStudentAssistant ? 'student_assistant' : User::normalizeRole($user->user_role));

        $resolvedStatus = $linkedAdmin?->status ?? ($isStudentAssistant ? null : 'active');
        $resolvedAddress = $linkedAdmin?->address;
        $resolvedContactNumber = $linkedAdmin?->contact_no ?? $linkedAdmin?->emergency_contact_no;

        return [
            'name' => $linkedAdmin?->name ?: ($user->name ?? ''),
            'email' => $linkedAdmin?->email ?: ($linkedAdmin?->email_address ?: ($user->email ?? '')),
            'suffix_name' => $linkedAdmin?->suffix_name,
            'birthday' => $birthday,
            'age' => $age,
            'address' => $resolvedAddress,
            'contact_number' => $resolvedContactNumber,
            'role' => $resolvedRole,
            'status' => $resolvedStatus,
            'source' => $isSuperadmin ? 'admins' : ($isStudentAssistant ? 'external_pending' : 'display_only'),
            'is_student_assistant' => $isStudentAssistant,
            'is_superadmin' => $isSuperadmin,
            'has_local_admin_profile' => (bool) $linkedAdmin,
        ];
    }

    private function canSignHealthClearance(): bool
    {
        $role = User::normalizeRole(optional(Auth::user())->user_role ?? '');
        return $role === User::ROLE_SUPERADMIN;
    }

    private function logActivity(string $action, string $description, ?string $module = null, ?string $eventType = null): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name ?? $user->email ?? 'Unknown User',
            'user_role' => strtolower((string) ($user->user_role ?? '')),
            'action' => $action,
            'module' => $module,
            'event_type' => $eventType,
            'description' => $description,
            'route_name' => optional(request()->route())->getName(),
            'http_method' => strtoupper((string) request()->method()),
            'request_path' => '/' . ltrim((string) request()->path(), '/'),
            'status_code' => 200,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);
    }

    // ==========================================
    //  PART 1: VIEW METHODS (Loading the Pages)
    // ==========================================

    public function dashboard()
    {
        $total = Appointment::count();
        $pending = Appointment::where('status', 'Pending')->count();
        $upcoming = Appointment::where('status', 'Approved')->count();
        $completed = Appointment::where('status', 'Completed')->count();
        $cancelled = Appointment::where('status', 'Cancelled')->count();

        $currentYear = now()->year;
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $monthlyTotalsRaw = Appointment::selectRaw('MONTH(date) as month_num, COUNT(*) as total')
            ->whereYear('date', $currentYear)
            ->groupBy('month_num')
            ->pluck('total', 'month_num');

        $monthlyCompletedRaw = Appointment::selectRaw('MONTH(date) as month_num, COUNT(*) as total')
            ->whereYear('date', $currentYear)
            ->where('status', 'Completed')
            ->groupBy('month_num')
            ->pluck('total', 'month_num');

        $monthlyTotals = [];
        $monthlyCompleted = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTotals[] = (int) ($monthlyTotalsRaw[$month] ?? 0);
            $monthlyCompleted[] = (int) ($monthlyCompletedRaw[$month] ?? 0);
        }

        $recentAppointments = Appointment::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'total',
            'pending',
            'upcoming',
            'completed',
            'cancelled',
            'recentAppointments',
            'currentYear',
            'monthLabels',
            'monthlyTotals',
            'monthlyCompleted'
        ));
    }

    public function apiTesting(Request $request, FacultySyncService $facultySyncService)
    {
        $search = trim((string) $request->query('search', ''));
        $source = trim((string) $request->query('source', 'faculty'));
        $results = [];
        $apiResponseMeta = null;
        $errorMessage = null;
        $errorDetails = null;

        if ($search !== '') {
            $facultyEndpoint = trim((string) config('services.pupt_flss.faculty_profiles_url', ''));
            $internalAdminEndpoint = url('/api/external/admins');
            $configuredTempEndpoint = trim((string) config('services.temp_api_testing.url', ''));

            if ($source === 'admin_api') {
                $endpoint = $internalAdminEndpoint;
            } elseif ($source === 'custom' && $configuredTempEndpoint !== '') {
                $endpoint = $configuredTempEndpoint;
            } else {
                $source = 'faculty';
                $endpoint = $configuredTempEndpoint !== '' ? $configuredTempEndpoint : $facultyEndpoint;
            }

            if ($endpoint === '') {
                $errorMessage = 'Temporary API testing URL is not configured yet.';
            } else {
                try {
                    if ($source === 'admin_api') {
                        $results = $this->searchLocalAdminsForApiTesting($search);
                        $apiResponseMeta = [
                            'status' => 200,
                            'ok' => true,
                            'endpoint' => $internalAdminEndpoint,
                            'result_count' => count($results),
                            'auth_mode' => 'internal-query',
                            'source' => $source,
                        ];

                        if (empty($results)) {
                            $errorMessage = 'No matching records were found for the current search.';
                        }

                        return view('admin.api-testing', [
                            'search' => $search,
                            'source' => $source,
                            'results' => $results,
                            'apiResponseMeta' => $apiResponseMeta,
                            'errorMessage' => $errorMessage,
                            'errorDetails' => $errorDetails,
                        ]);
                    }

                    $client = Http::timeout((int) config('services.temp_api_testing.timeout', 20))
                        ->acceptJson();

                    $apiKey = trim((string) config('services.temp_api_testing.api_key', ''));
                    $apiHeader = trim((string) config('services.temp_api_testing.header', 'X-External-Api-Key'));
                    $authMode = 'none';

                    if ($source === 'admin_api') {
                        $internalApiKey = trim((string) config('services.external_admin_profile.api_key', ''));
                        $internalApiHeader = trim((string) config('services.external_admin_profile.header', 'X-External-Api-Key'));
                        if ($internalApiKey !== '') {
                            $client = $client->withHeaders([$internalApiHeader => $internalApiKey]);
                            $authMode = 'internal-api-key';
                        }
                    } elseif ($apiKey !== '') {
                        $client = $client->withHeaders([$apiHeader => $apiKey]);
                        $authMode = 'custom-header';
                    } elseif ($facultyEndpoint !== '' && $endpoint === $facultyEndpoint) {
                        $client = $client->withHeaders($facultySyncService->generateHmacHeaders('GET', $endpoint));
                        $authMode = 'faculty-hmac';
                    }

                    $response = $client->get($endpoint, [
                        'search' => $search,
                        'query' => $search,
                        'q' => $search,
                    ]);

                    $payload = $response->json();
                    $results = $this->normalizeApiTestingResults($payload, $search);
                    $apiResponseMeta = [
                        'status' => $response->status(),
                        'ok' => $response->successful(),
                        'endpoint' => $endpoint,
                        'result_count' => count($results),
                        'auth_mode' => $authMode,
                        'source' => $source,
                    ];

                    if (!$response->successful()) {
                        $errorMessage = 'The API request returned an error response.';
                        $errorDetails = trim((string) $response->body());
                    } elseif (empty($results)) {
                        $errorMessage = 'No matching records were found for the current search.';
                    }
                } catch (\Throwable $exception) {
                    $errorMessage = 'Unable to reach the external API right now: ' . $exception->getMessage();
                    $errorDetails = $exception->getMessage();
                }
            }
        }

        return view('admin.api-testing', [
            'search' => $search,
            'source' => $source,
            'results' => $results,
            'apiResponseMeta' => $apiResponseMeta,
            'errorMessage' => $errorMessage,
            'errorDetails' => $errorDetails,
        ]);
    }

    private function searchLocalAdminsForApiTesting(string $search): array
    {
        if (!Schema::hasTable('admins')) {
            return [];
        }

        $query = Admin::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                foreach (['admin_id', 'name', 'first_name', 'last_name', 'email', 'email_address', 'office', 'access_level', 'role'] as $column) {
                    if (!Admin::hasColumn($column)) {
                        continue;
                    }

                    $builder->orWhere($column, 'like', '%' . $search . '%');
                }
            });
        }

        $orderColumn = 'admin_id';
        foreach (['name', 'first_name', 'admin_id'] as $candidateColumn) {
            if (Admin::hasColumn($candidateColumn)) {
                $orderColumn = $candidateColumn;
                break;
            }
        }

        $records = $query->orderBy($orderColumn)->limit(20)->get()->map(function (Admin $admin) {
            $fields = $admin->toArray();
            $name = trim((string) ($fields['name'] ?? trim(($fields['first_name'] ?? '') . ' ' . ($fields['last_name'] ?? ''))));

            return [
                'identifier' => (string) ($fields['admin_id'] ?? 'N/A'),
                'name' => $name !== '' ? $name : 'N/A',
                'email' => (string) ($fields['email'] ?? $fields['email_address'] ?? 'N/A'),
                'birthday' => (string) ($fields['birthday'] ?? 'N/A'),
                'role' => (string) ($fields['access_level'] ?? $fields['role'] ?? 'N/A'),
                'office' => (string) ($fields['office'] ?? 'N/A'),
                'contact_number' => (string) ($fields['contact_no'] ?? $fields['emergency_contact_no'] ?? 'N/A'),
                'address' => (string) ($fields['address'] ?? 'N/A'),
                'status' => (string) ($fields['status'] ?? 'N/A'),
                'suffix_name' => (string) ($fields['suffix_name'] ?? 'N/A'),
                'fields' => $fields,
            ];
        })->values()->all();

        return $records;
    }

    private function normalizeApiTestingResults($payload, string $search): array
    {
        if (!is_array($payload)) {
            return [];
        }

        $records = $payload['data'] ?? $payload['results'] ?? $payload['records'] ?? $payload;
        if (!is_array($records)) {
            return [];
        }

        $items = array_is_list($records) ? $records : [$records];
        $needle = strtolower($search);
        $normalized = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $profile = isset($item['profile']) && is_array($item['profile'])
                ? $item['profile']
                : [];
            $profileAddress = isset($profile['address']) && is_array($profile['address'])
                ? $profile['address']
                : [];
            $name = trim((string) ($item['name'] ?? trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''))));
            $email = trim((string) ($item['email'] ?? $item['email_address'] ?? ''));
            $identifier = trim((string) ($item['faculty_code'] ?? $item['faculty_id'] ?? $item['id'] ?? $item['admin_id'] ?? $item['student_id'] ?? $item['employee_id'] ?? ''));
            $birthday = trim((string) ($item['birthday'] ?? $profile['birthday'] ?? $item['dob'] ?? $item['date_of_birth'] ?? ''));
            $role = trim((string) ($item['faculty_type'] ?? $item['role'] ?? $item['access_level'] ?? $item['designation'] ?? ''));
            $office = trim((string) ($item['office'] ?? $item['offices'] ?? $item['department'] ?? ''));
            $contactNumber = trim((string) ($item['contact_no'] ?? $item['contact_number'] ?? $item['phone'] ?? $item['mobile'] ?? ''));
            $address = trim((string) ($item['address'] ?? $item['home_address'] ?? $this->formatApiTestingAddress($profileAddress)));
            $status = trim((string) ($item['status'] ?? ''));

            $haystack = strtolower(implode(' ', array_filter([
                $name,
                $email,
                $identifier,
                json_encode($item),
            ])));

            if ($needle !== '' && !str_contains($haystack, $needle)) {
                continue;
            }

            $normalized[] = [
                'identifier' => $identifier !== '' ? $identifier : 'N/A',
                'name' => $name !== '' ? $name : 'N/A',
                'email' => $email !== '' ? $email : 'N/A',
                'birthday' => $birthday !== '' ? $birthday : 'N/A',
                'role' => $role !== '' ? $role : 'N/A',
                'office' => $office !== '' ? $office : 'N/A',
                'contact_number' => $contactNumber !== '' ? $contactNumber : 'N/A',
                'address' => $address !== '' ? $address : 'N/A',
                'status' => $status !== '' ? $status : 'N/A',
                'suffix_name' => trim((string) ($item['suffix_name'] ?? '')) ?: 'N/A',
                'fields' => $item,
            ];
        }

        return array_slice($normalized, 0, 20);
    }

    private function formatApiTestingAddress(array $address): string
    {
        $parts = array_values(array_filter(array_map(static function ($value) {
            return trim((string) $value);
        }, [
            $address['house_num'] ?? null,
            $address['street'] ?? null,
            $address['barangay'] ?? null,
            $address['city'] ?? null,
            $address['province'] ?? null,
            $address['country'] ?? null,
            $address['zipcode'] ?? null,
        ])));

        return implode(', ', $parts);
    }

    public function viewHealth()
    {
        // Kukunin natin ang lahat ng records mula sa health_profile table
        $records = HealthProfile::with('user')->latest()->get();
        
        return view('admin.health_records', compact('records'));
    }

    public function showHealth($id)
    {

        $profile = HealthProfile::with('user')->findOrFail($id);
        

        $calculatedAge = Carbon::parse($profile->user->DOB)->age;

        return view('admin.show_health', compact('profile', 'calculatedAge'));
    }

// 1. Para lumabas 'yung page (GET)
public function showSignPage($id)
{
    if (!$this->canSignHealthClearance()) {
        return redirect()->route('admin.health_records')
            ->with('error', 'Only Nurse Joyce or Super Admin can e-sign health records.');
    }

    // Ginaya ko ang variable name na $record para tugma sa blade na binigay ko kanina
    $record = HealthProfile::with('user')->findOrFail($id);
    return view('admin.sign_clearance', compact('record'));
}

// 2. Para sa pag-save ng pinirmahan (PUT)
public function updateClearance(Request $request, $id)
{
    if (!$this->canSignHealthClearance()) {
        return redirect()->route('admin.health_records')
            ->with('error', 'Only Nurse Joyce or Super Admin can e-sign health records.');
    }

    // 1. I-validate ang data
    $request->validate([
        'clearance_status' => 'required',
        'pending_reason'   => 'nullable|string',
        'verified_at'      => 'nullable|date',
    ]);

    // 2. Hanapin ang record
    $record = HealthProfile::findOrFail($id);

    // 3. Manual Assignment
    $record->clearance_status = $request->clearance_status;
    $record->pending_reason   = $request->pending_reason;

    // 4. Date Logic
    if ($request->clearance_status == 'Issued') {
        // Kapag Issued, gamitin ang nilagay na date o ang oras ngayon (now)
        $record->verified_at = $request->verified_at ?? now();
    } else {
        // Kapag Pending o Rejected, gawing NULL ang date para hindi lumitaw sa form
        $record->verified_at = null;
        
        // Optional: I-clear din ang pending_reason kung Issued na uli
        if ($request->clearance_status == 'Issued') {
            $record->pending_reason = null;
        }
    }

    // 5. I-save at i-check
    if($record->save()){
        return redirect()->route('admin.health_records')
                         ->with('success', 'Health Clearance status updated successfully!');
    } else {
        return back()->with('error', 'Failed to save to database.');
    }
}

    public function appointments()
    {
        $appointments = Appointment::latest()->get();
        return view('admin.appointments', compact('appointments'));
    }

    public function inventory()
    {
        $items = Item::all();
        return view('admin.inventory', compact('items'));
    }

    public function reports()
{
   
    $appointments = Appointment::all();
    $total = Appointment::count();
    $approved = Appointment::where('status', 'Approved')->count();
    $cancelled = Appointment::where('status', 'Cancelled')->count();
    

    $lowStockCount = Item::where('quantity', '<', 10)->count(); 
    
   
    $appointmentsToday = Appointment::where('status', 'Approved')
                                    ->whereDate('date', \Carbon\Carbon::today())
                                    ->count();


    $totalConsultations = Appointment::where('status', 'Approved')
                                     ->whereMonth('date', \Carbon\Carbon::now()->month)
                                     ->count();

    $items = Item::all();

    return view('admin.reports', compact(
        'appointments', 'total', 'approved', 'cancelled', 
        'lowStockCount', 'appointmentsToday', 'totalConsultations', 'items'
    ));
}

    public function settings()
    {
        $admin = Auth::user();
        $settings = Setting::first();
        if(!$settings) { $settings = new Setting(); }
        $cmsProfile = $admin ? $this->buildCmsAdminProfile($admin) : [];

        return view('admin.settings', compact('admin', 'settings', 'cmsProfile'));
    }

    // ==========================================
    //  PART 2: ACTION METHODS (The Real Logic)
    // ==========================================

    // --- 1. APPOINTMENT ACTIONS ---
    public function updateStatus($id, $status)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->status = $status;
            $appointment->save();

            \App\Models\ActivityLog::create([
            'user_id'     => auth()->id(), 
            'user_name'   => auth()->user()->name,
            'action'      => 'Status Updated',
            'description' => "Updated Appointment #$id status to $status",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

            return redirect()->back()->with('success', "Appointment marked as $status.");
        }
        return redirect()->back()->with('error', "Appointment not found.");
    }

    public function reschedule($id, Request $request)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->date = $request->date;
            $appointment->time = $request->time;
            $appointment->status = 'Approved';
            $appointment->save();

            // LOGS CODES
             \App\Models\ActivityLog::create([
            'user_id'     => auth()->id(),
            'user_name'   => auth()->user()->name,
            'action'      => 'Appointment Rescheduled', 
            'description' => "Rescheduled Appointment #$id to $request->date at $request->time. Status set to Approved.", 
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

            return redirect()->back()->with('success', "Appointment rescheduled successfully.");
        }
        return redirect()->back()->with('error', "Error rescheduling.");
    }

    // --- 2. INVENTORY ACTIONS ---
    public function storeItem(Request $request)
{
  
    $item = Item::create($request->all());

    // 2. LOGS CODES
    \App\Models\ActivityLog::create([
        'user_id'     => auth()->id(),
        'user_name'   => auth()->user()->name,
        'action'      => 'Inventory Update', 
        'description' => "Added new item: " . $item->item_name . " (Qty: " . $item->quantity . ")", 
   
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
    ]);

     return redirect()->back()->with('success', 'New item added to inventory.');
    }

    public function updateItem($id, Request $request)
{
    $item = Item::find($id);
    if ($item) {
      
        $oldName = $item->item_name; 
        
        $item->update($request->all());

        // LOGS CODES
        \App\Models\ActivityLog::create([
            'user_id'     => auth()->id(),
            'user_name'   => auth()->user()->name,
            'action'      => 'Inventory Edited', 
            'description' => "Updated Item: $oldName (ID: #$id). New Qty: " . $item->quantity,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Item updated successfully.');
    }
    return redirect()->back()->with('error', 'Item not found.');
}

   public function deleteItem($id)
{
  
    $item = Item::find($id);

    if ($item) {
        $itemName = $item->item_name; 

        $item->delete();

        // 3. LOGS CODES
        \App\Models\ActivityLog::create([
            'user_id'     => auth()->id(),
            'user_name'   => auth()->user()->name,
            'action'      => 'Inventory Deleted',
            'description' => "Permanently removed item: $itemName (ID: #$id) from inventory.",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Item removed.');
    }

    return redirect()->back()->with('error', 'Item not found.');
}

    // --- 3. SETTINGS & PROFILE ---
    public function updateSettings(Request $request)
    {
        $settings = Setting::first();
        if(!$settings) { $settings = new Setting(); }

        $settings->clinic_name = $request->clinic_name;
        $settings->clinic_location = $request->clinic_location;
        $settings->open_time = $request->open_time;
        $settings->close_time = $request->close_time;
        $settings->email_notifications = $request->has('email_notifications');
        $settings->auto_approve = $request->has('auto_approve');
        $settings->save();

        // --- LOGS CODES ---
    \App\Models\ActivityLog::create([
        'user_id'     => auth()->id(),
        'user_name'   => auth()->user()->name,
        'action'      => 'System Settings Updated',
        'description' => "Modified clinic configuration (Name: $request->clinic_name, Hours: $request->open_time - $request->close_time)",
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
    ]);

        return redirect()->back()->with('success', 'System settings saved.');
    }

    public function updateProfile(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
        'suffix_name' => 'nullable|string|max:50',
        'birthday' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'contact_number' => 'nullable|string|max:30',
        'role' => 'nullable|string|max:255',
        'status' => 'nullable|in:active,inactive',
        'password' => 'nullable|string|min:6|confirmed',
    ]);
    
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $isStudentAssistant = $this->isStudentAssistantAccount($user);
    $originalEmail = (string) $user->email;
    

    $passwordChanged = $request->filled('password') ? ' (Password was also updated)' : '';
    $user->name = $request->name;
    $user->email = $request->email;

    if (!$isStudentAssistant && $request->filled('role')) {
        $user->user_role = User::normalizeRole($request->role);
    }

    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }
    
    $user->save();

    $profileMessageSuffix = '';

    if ($this->isSuperadminAccount($user) && Schema::hasTable('admins')) {
        $linkedAdminProfile = $this->findLinkedAdminProfileByEmails([
            $originalEmail,
            $request->email,
        ]);

        if (!$linkedAdminProfile) {
            $linkedAdminProfile = new Admin();
        }

        [$firstName, $lastName, $suffixName] = $this->splitDisplayName((string) $request->name);

        if (Admin::hasColumn('first_name')) {
            $linkedAdminProfile->first_name = $firstName;
        }

        if (Admin::hasColumn('last_name')) {
            $linkedAdminProfile->last_name = $lastName;
        }

        if (Admin::hasColumn('suffix_name')) {
            $linkedAdminProfile->suffix_name = $request->suffix_name !== null && $request->suffix_name !== ''
                ? $request->suffix_name
                : $suffixName;
        }

        if (Admin::hasColumn('name')) {
            $linkedAdminProfile->name = $request->name;
        }

        if (Admin::hasColumn('email')) {
            $linkedAdminProfile->email = $request->email;
        }

        if (Admin::hasColumn('email_address')) {
            $linkedAdminProfile->email_address = $request->email;
        }

        if (Admin::hasColumn('birthday')) {
            $linkedAdminProfile->birthday = $request->birthday;
        }

        if (Admin::hasColumn('age')) {
            $linkedAdminProfile->age = $request->filled('birthday')
                ? Carbon::parse($request->birthday)->age
                : null;
        }

        if (Admin::hasColumn('address')) {
            $linkedAdminProfile->address = $request->address;
        }

        if (Admin::hasColumn('contact_no')) {
            $linkedAdminProfile->contact_no = $request->contact_number;
        } elseif (Admin::hasColumn('emergency_contact_no')) {
            $linkedAdminProfile->emergency_contact_no = $request->contact_number;
        }

        if (Admin::hasColumn('access_level')) {
            $linkedAdminProfile->access_level = $request->role;
        } elseif (Admin::hasColumn('role')) {
            $linkedAdminProfile->role = $request->role;
        }

        if (Admin::hasColumn('status')) {
            $linkedAdminProfile->status = $request->status;
        }

        $linkedAdminProfile->save();
        $profileMessageSuffix = ' CMS admin profile saved locally for the superadmin account.';
    } elseif ($isStudentAssistant) {
        $profileMessageSuffix = ' Student assistant profile sync is pending external API integration, so extra profile fields remain temporary.';
    } else {
        $profileMessageSuffix = ' Extra CMS profile fields are display-only for admin accounts right now and were not saved.';
    }

    // --- LOGS CODES ---
    \App\Models\ActivityLog::create([
        'user_id'     => $user->id,
        'user_name'   => $user->name,
        'action'      => 'Security Update', 
        'description' => "User updated admin profile info: Name/Email{$passwordChanged}. Source email before update: {$originalEmail}.",
        'ip_address'  => $request->ip(),
        'user_agent'  => $request->userAgent(),
    ]);

    return redirect()->back()->with('success', 'Profile updated successfully.' . $profileMessageSuffix);
}

    // --- 4. EXPORTS (CSV) ---
    public function exportReports()
{
    $appointments = Appointment::all();
    $filename = "appointments_" . date('Y-m-d_H-i-s') . ".csv";
    $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename={$filename}"];
    $columns = ['ID','Name','Email','Student ID','Service','Date','Time','Status','Notes'];

    // --- LOGS CODES ---
    \App\Models\ActivityLog::create([
        'user_id'     => auth()->id(),
        'user_name'   => auth()->user()->name,
        'action'      => 'Report Exported',
        'description' => "Downloaded appointment reports as CSV ($filename). Total records: " . $appointments->count(),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
    ]);

    $callback = function() use ($appointments, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        foreach ($appointments as $appt) {
            fputcsv($file, [
                $appt->id,
                $appt->name,
                $appt->email,
                $appt->student_id,
                $appt->service,
                $appt->date,
                $appt->time,
                $appt->status,
                $appt->notes
            ]);
        }
        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

    public function exportInventory()
{
    $items = Item::all();
    $filename = "inventory_" . date('Y-m-d_H-i-s') . ".csv";
    $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename={$filename}"];
    $columns = ['ID','Name','Category','Quantity'];

    // --- LOGS CODES ---
    \App\Models\ActivityLog::create([
        'user_id'     => auth()->id(),
        'user_name'   => auth()->user()->name,
        'action'      => 'Inventory Exported',
        'description' => "Exported full inventory list to CSV ($filename). Total items logged: " . $items->count(),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
    ]);

    $callback = function() use ($items, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        foreach ($items as $item) {
            fputcsv($file, [
                $item->id,
                $item->name,
                $item->category,
                $item->quantity
            ]);
        }
        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

    // --- 5. COMPLETE APPOINTMENT & DEDUCT INVENTORY ---
    public function completeWithMedicine(Request $request, $id)
{
    $appointment = Appointment::find($id);
    if(!$appointment) return redirect()->back()->with('error', 'Appointment not found.');

    $appointment->status = 'Completed';
    $appointment->save();

    $logDescription = "Completed Appointment #$id for {$appointment->name}.";

    if ($request->filled('item_id')) {
        $item = Item::find($request->item_id);
        if ($item && $item->quantity > 0) {
            $item->decrement('quantity', 1);
            $logDescription .= " Deducted 1 unit of {$item->name} from inventory."; 
            
            $this->logActivity('Appointment & Inventory', $logDescription); 
            return redirect()->back()->with('success', "Appointment completed and 1 {$item->name} deducted.");
        } 
    }

    $this->logActivity('Appointment Completed', $logDescription);
    return redirect()->back()->with('success', 'Appointment completed (No medicine deducted).');
}

    // 6. FOR INVENTORY SUMMARY
    public function inventorySummary()
{
    $totalItems = Item::count();
    $totalStock = Item::sum('quantity');
    $outOfStock = Item::where('quantity', 0)->count();
    $lowStockItems = Item::where('quantity', '>', 0)->where('quantity', '<', 10)->get();
    
    $categorySummary = Item::select('category', 
                        DB::raw('count(*) as count'), 
                        DB::raw('sum(quantity) as total_qty'))
                        ->groupBy('category')
                        ->get();

    // LOGS CODES
    \App\Models\ActivityLog::create([
        'user_id'     => auth()->id(),
        'user_name'   => auth()->user()->name,
        'action'      => 'Viewed Inventory Report',
        'description' => "Accessed Inventory Summary. System detected $outOfStock out-of-stock items.",
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
    ]);

    return view('admin.reports.inventory-summary', compact(
        'totalItems', 'totalStock', 'outOfStock', 'lowStockItems', 'categorySummary'
    ));
}

// 7. AUDIT TRAIL CONTROLLER
    public function indexLogs(Request $request)
    {
        $currentRole = User::normalizeRole(optional(Auth::user())->user_role ?? '');
        if ($currentRole !== User::ROLE_SUPERADMIN) {
            abort(403, 'Unauthorized');
        }

        $query = ActivityLog::query();

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('user_name', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhere('route_name', 'like', "%{$search}%")
                    ->orWhere('request_path', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $actorRole = trim((string) $request->input('actor_role', ''));
        if ($actorRole !== '') {
            $query->where('user_role', strtolower($actorRole));
        }

        $eventType = trim((string) $request->input('event_type', ''));
        if ($eventType !== '') {
            $query->where('event_type', strtolower($eventType));
        }

        $module = trim((string) $request->input('module', ''));
        if ($module !== '') {
            $query->where('module', $module);
        }

        $httpMethod = strtoupper(trim((string) $request->input('http_method', '')));
        if ($httpMethod !== '') {
            $query->where('http_method', $httpMethod);
        }

        $statusClass = trim((string) $request->input('status_class', ''));
        if ($statusClass === 'success') {
            $query->where(function ($builder) {
                $builder->whereNull('status_code')
                    ->orWhere('status_code', '<', 400);
            });
        } elseif ($statusClass === 'error') {
            $query->where('status_code', '>=', 400);
        }

        $dateFrom = trim((string) $request->input('date_from', ''));
        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        $dateTo = trim((string) $request->input('date_to', ''));
        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        $logs = (clone $query)
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $totalEvents = (clone $query)->count();
        $todayEvents = (clone $query)->whereDate('created_at', Carbon::today())->count();
        $uniqueActors = (clone $query)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
        $failedEvents = (clone $query)->where('status_code', '>=', 400)->count();

        $roleBreakdown = (clone $query)
            ->selectRaw("COALESCE(NULLIF(user_role, ''), 'unknown') as role, COUNT(*) as total")
            ->groupBy('role')
            ->orderByDesc('total')
            ->get();

        $moduleBreakdown = (clone $query)
            ->selectRaw("COALESCE(NULLIF(module, ''), 'Uncategorized') as module_name, COUNT(*) as total")
            ->groupBy('module_name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $roleOptions = ActivityLog::query()
            ->whereNotNull('user_role')
            ->where('user_role', '!=', '')
            ->distinct()
            ->orderBy('user_role')
            ->pluck('user_role');

        $eventTypeOptions = ActivityLog::query()
            ->whereNotNull('event_type')
            ->where('event_type', '!=', '')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type');

        $moduleOptions = ActivityLog::query()
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('admin.activity_logs', compact(
            'logs',
            'totalEvents',
            'todayEvents',
            'uniqueActors',
            'failedEvents',
            'roleBreakdown',
            'moduleBreakdown',
            'roleOptions',
            'eventTypeOptions',
            'moduleOptions'
        ));
    }

}
