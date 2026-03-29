<?php

use App\Http\Controllers\AdminAssistantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MedicalConditionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StudentAssistantController;
use App\Http\Controllers\WalkInController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// --- PUBLIC ROUTES (No login required) ---
Route::get('/', function () {
    return view('landing');
})->name('landing');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/auth/callback', [LoginController::class, 'handleIdpCallback'])->name('auth.callback');
Route::post('/login-action', [LoginController::class, 'login']);
Route::post('/register-action', [RegisterController::class, 'register']);

// --- PROTECTED ROUTES (Login required) ---
Route::middleware([\Illuminate\Auth\Middleware\Authenticate::class, 'audit'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Student-only routes
    Route::middleware('role:student')->group(function () {
        Route::post('/student/skip-barcode', function () {
            session(['barcode_skipped' => true]);
            return response()->json(['status' => 'success']);
        });

        Route::get('/student/home', function () {
            return view('student.home');
        });

        // 1. Route para ipakita ang blankong form
        Route::get('/student/health-form', [AppointmentController::class, 'showHealthForm'])->name('health.form');
        Route::get('/student/health-form-legacy', function () {
            return redirect()->route('health.form');
        })->name('student.health.form');

        // 2. Route para i-save ang data (Dito galing ang form submit)
        Route::post('/student/store-health-form', [AppointmentController::class, 'storeHealthForm'])->name('store.health.form');

        // 3. Route para sa Printable View (Dito tayo pupunta pagkatapos mag-save)
        Route::get('/student/print-health-form', [AppointmentController::class, 'printHealthForm'])->name('print.health.form');

        Route::get('/student/booking', [AppointmentController::class, 'create'])->name('student.booking');
        Route::get('/student/account', [AppointmentController::class, 'account']);
        Route::get('/student/faq', [AppointmentController::class, 'faq']);
        Route::get('/student/history', [AppointmentController::class, 'history']);
        Route::post('/student/appointments/store', [AppointmentController::class, 'store']);
        Route::get('/student/appointments/availability', [AppointmentController::class, 'availability'])->name('student.appointments.availability');
        Route::post('/student/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
        Route::post('/student/update-contact', [AppointmentController::class, 'updateContact'])->name('student.updateContact');

        Route::get('/student/barcode-register', [AppointmentController::class, 'barcodeRegister'])->name('barcode.register');
        Route::post('/student/barcode-register', [AppointmentController::class, 'storeBarcode'])->name('barcode.store');
        Route::post('/student/barcode-validate', [AppointmentController::class, 'validateBarcodeScan'])->name('barcode.validate');
        Route::post('/student/reset-barcode', [AppointmentController::class, 'resetBarcode'])->name('barcode.reset');
    });

    Route::get('/account', [AppointmentController::class, 'index'])->name('account');
Route::get('/health-records', [AdminController::class, 'viewHealth'])
    ->middleware('role:superadmin,admin')
    ->name('admin.health_records');
Route::get('/health-profile/{id}', [AdminController::class, 'showHealth'])
    ->middleware('role:superadmin,admin')
    ->name('admin.show_health');
Route::get('/health-profile/{id}/sign', [AdminController::class, 'showSignPage'])
    ->middleware('role:superadmin')
    ->name('admin.sign_page');
Route::put('/health-profile/{id}/update', [AdminController::class, 'updateClearance'])
    ->middleware('role:superadmin')
    ->name('admin.update_clearance');

    // Legacy barcode endpoints kept for compatibility
    Route::get('/barcode-register', [AppointmentController::class, 'barcodeRegister'])->name('barcode.legacy.register');
    Route::post('/barcode-store', [AppointmentController::class, 'storeBarcode'])->name('barcode.legacy.store');
    Route::post('/barcode-validate', [AppointmentController::class, 'validateBarcodeScan'])->name('barcode.legacy.validate');
    Route::post('/barcode-reset', [AppointmentController::class, 'resetBarcode'])->name('barcode.legacy.reset');

    Route::get('/fetch-user/{student_id}', [AppointmentController::class, 'fetchUser']);

    // Shared admin workspace routes (Super Admin/Admin)
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::post('/admin/assistant/intent', [AdminAssistantController::class, 'handle'])->name('admin.assistant.intent');
        Route::post('/assistant/intent', [AdminAssistantController::class, 'handle'])->name('assistant.intent');

        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/appointments', [AdminController::class, 'appointments'])->name('admin.appointments');
        Route::get('/admin/appointments/{id}/{status}', [AdminController::class, 'updateStatus'])->name('admin.appointments.status');
        Route::post('/admin/appointments/{id}/reschedule', [AdminController::class, 'reschedule'])->name('admin.appointments.reschedule');

        Route::get('/admin/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');

        Route::get('/admin/walkin', [WalkInController::class, 'index'])->name('walkin.index');
        Route::get('/admin/walkin/get-student', [WalkInController::class, 'getStudent'])->name('walkin.getStudent');
        Route::post('/admin/walkin/register', [WalkInController::class, 'registerStudent'])->name('walkin.registerStudent');
        Route::get('/admin/walkin/form/{student_id}', [WalkInController::class, 'showWalkinForm'])->name('walkin.form');
        Route::post('/admin/walkin/store', [WalkInController::class, 'store'])->name('walkin.store');

        Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
        Route::get('/admin/reports/mar', [ReportsController::class, 'marReport'])->name('reports.mar');
        Route::get('/admin/reports/inventory-summary', [AdminController::class, 'inventorySummary'])->name('reports.inventory-summary');
        Route::get('/admin/reports/export-hub', [ReportsController::class, 'exportHub'])->name('reports.exportHub');
        Route::get('/admin/reports/print-reports', [ReportsController::class, 'printReport'])->name('reports.print');
        Route::get('/admin/api-testing', [AdminController::class, 'apiTesting'])->name('admin.api-testing');
        Route::get('/admin/activity-logs', [AdminController::class, 'indexLogs'])
            ->middleware('role:superadmin')
            ->name('admin.logs');
    });

    // Super Admin-only routes
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::put('/admin/settings/update', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
        Route::put('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

        Route::post('/admin/inventory/store', [AdminController::class, 'storeItem'])->name('admin.inventory.store');
        Route::put('/admin/inventory/{id}', [AdminController::class, 'updateItem'])->name('admin.inventory.update');
        Route::delete('/admin/inventory/{id}', [AdminController::class, 'deleteItem'])->name('admin.inventory.delete');

        Route::get('/admin/reports/manage-mar', [ReportsController::class, 'manageMar'])->name('admin.reports.manage-mar');
        Route::put('/admin/conditions/{id}', [ReportsController::class, 'update'])->name('conditions.update');
        Route::post('/admin/medical-conditions', [MedicalConditionController::class, 'store'])->name('conditions.store');
        Route::delete('/admin/medical-conditions/{id}', [MedicalConditionController::class, 'destroy'])->name('conditions.destroy');

        Route::get('/admin/student-assistants', [StudentAssistantController::class, 'index'])->name('admin.student-assistants.index');
        Route::post('/admin/student-assistants', [StudentAssistantController::class, 'store'])->name('admin.student-assistants.store');
        Route::put('/admin/student-assistants/{assistant}', [StudentAssistantController::class, 'update'])->name('admin.student-assistants.update');
        Route::delete('/admin/student-assistants/{assistant}', [StudentAssistantController::class, 'destroy'])->name('admin.student-assistants.destroy');
    });

    // Admin prefixed entry points (same modules, different UI context)
    Route::middleware('role:admin')->prefix('assistant')->name('assistant.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
        Route::get('/appointments/{id}/{status}', [AdminController::class, 'updateStatus'])->name('appointments.status');
        Route::post('/appointments/{id}/reschedule', [AdminController::class, 'reschedule'])->name('appointments.reschedule');
        Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');

        Route::get('/walkin', [WalkInController::class, 'index'])->name('walkin.index');
        Route::get('/walkin/get-student', [WalkInController::class, 'getStudent'])->name('walkin.getStudent');
        Route::post('/walkin/register', [WalkInController::class, 'registerStudent'])->name('walkin.registerStudent');
        Route::get('/walkin/form/{student_id}', [WalkInController::class, 'showWalkinForm'])->name('walkin.form');
        Route::post('/walkin/store', [WalkInController::class, 'store'])->name('walkin.store');

        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/mar', [ReportsController::class, 'marReport'])->name('reports.mar');
        Route::get('/reports/inventory-summary', [AdminController::class, 'inventorySummary'])->name('reports.inventory-summary');
        Route::get('/reports/export-hub', [ReportsController::class, 'exportHub'])->name('reports.exportHub');
        Route::get('/reports/print-reports', [ReportsController::class, 'printReport'])->name('reports.print');
        Route::get('/api-testing', [AdminController::class, 'apiTesting'])->name('api-testing');
    });
}); // End protected routes

// Temporary dev login helper (debug mode only)
Route::get('/dev-login/{id}', function ($id) {
    if (!config('app.debug')) {
        abort(404);
    }

    $user = User::find($id);
    if ($user) {
        $originalRole = strtolower((string) ($user->user_role ?? ''));
        $normalizedRole = User::normalizeRole($user->user_role);
        if ($normalizedRole !== $originalRole) {
            $user->user_role = $normalizedRole;
            $user->save();
        }

        Auth::login($user);

        if ($normalizedRole === User::ROLE_SUPERADMIN) {
            return redirect('/admin/dashboard')->with('success', 'Logged in as ' . $user->name);
        }
        if ($normalizedRole === User::ROLE_ADMIN) {
            return redirect('/assistant/dashboard')->with('success', 'Logged in as ' . $user->name);
        }

        return redirect('/student/account')->with('success', 'Logged in as ' . $user->name);
    }

    return 'User not found!';
});

