<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Setting;
use App\Services\FacultySyncService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\HealthProfile;
use Carbon\Carbon;

class AdminController extends Controller
{
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
        $results = [];
        $apiResponseMeta = null;
        $errorMessage = null;

        if ($search !== '') {
            $configuredTempEndpoint = trim((string) config('services.temp_api_testing.url', ''));
            $facultyEndpoint = trim((string) config('services.pupt_flss.faculty_profiles_url', ''));
            $endpoint = $configuredTempEndpoint !== '' ? $configuredTempEndpoint : $facultyEndpoint;

            if ($endpoint === '') {
                $errorMessage = 'Temporary API testing URL is not configured yet.';
            } else {
                try {
                    $client = Http::timeout((int) config('services.temp_api_testing.timeout', 20))
                        ->acceptJson();

                    $apiKey = trim((string) config('services.temp_api_testing.api_key', ''));
                    $apiHeader = trim((string) config('services.temp_api_testing.header', 'X-External-Api-Key'));
                    if ($apiKey !== '') {
                        $client = $client->withHeaders([$apiHeader => $apiKey]);
                    } elseif ($facultyEndpoint !== '' && $endpoint === $facultyEndpoint) {
                        $client = $client->withHeaders($facultySyncService->generateHmacHeaders());
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
                        'auth_mode' => $apiKey !== ''
                            ? 'custom-header'
                            : (($facultyEndpoint !== '' && $endpoint === $facultyEndpoint) ? 'faculty-hmac' : 'none'),
                    ];

                    if (!$response->successful()) {
                        $errorMessage = 'The API request returned an error response.';
                    } elseif (empty($results)) {
                        $errorMessage = 'No matching records were found for the current search.';
                    }
                } catch (\Throwable $exception) {
                    $errorMessage = 'Unable to reach the external API right now: ' . $exception->getMessage();
                }
            }
        }

        return view('admin.api-testing', [
            'search' => $search,
            'results' => $results,
            'apiResponseMeta' => $apiResponseMeta,
            'errorMessage' => $errorMessage,
        ]);
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

            $name = trim((string) ($item['name'] ?? trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''))));
            $email = trim((string) ($item['email'] ?? $item['email_address'] ?? ''));
            $identifier = trim((string) ($item['id'] ?? $item['admin_id'] ?? $item['student_id'] ?? $item['employee_id'] ?? ''));
            $birthday = trim((string) ($item['birthday'] ?? $item['dob'] ?? $item['date_of_birth'] ?? ''));
            $role = trim((string) ($item['faculty_type'] ?? $item['role'] ?? $item['access_level'] ?? $item['designation'] ?? ''));
            $office = trim((string) ($item['office'] ?? $item['offices'] ?? $item['department'] ?? ''));
            $contactNumber = trim((string) ($item['contact_no'] ?? $item['contact_number'] ?? $item['phone'] ?? $item['mobile'] ?? ''));
            $address = trim((string) ($item['address'] ?? $item['home_address'] ?? ''));
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
                'fields' => $item,
            ];
        }

        return array_slice($normalized, 0, 20);
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
        return view('admin.settings', compact('admin', 'settings'));
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
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:6|confirmed',
    ]);
    
    /** @var \App\Models\User $user */
    $user = Auth::user();
    

    $passwordChanged = $request->filled('password') ? ' (Password was also updated)' : '';
    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }
    
    $user->save();

    // --- LOGS CODES ---
    \App\Models\ActivityLog::create([
        'user_id'     => $user->id,
        'user_name'   => $user->name,
        'action'      => 'Security Update', 
        'description' => "User updated admin profile info: Name/Email{$passwordChanged}.",
        'ip_address'  => $request->ip(),
        'user_agent'  => $request->userAgent(),
    ]);

    return redirect()->back()->with('success', 'Profile updated successfully.');
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
