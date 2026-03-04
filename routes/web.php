<?php
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WalkInController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\MedicalConditionController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Auth;

// --- PUBLIC ROUTES (No login required) ---
Route::get('/', function () { return view('login'); });
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login-action', [LoginController::class, 'login']);
Route::post('/register-action', [RegisterController::class, 'register']);

// --- PROTECTED ROUTES (Login required) ---
Route::middleware([\Illuminate\Auth\Middleware\Authenticate::class])->group(function () {

    // Route for Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Route for Barcode Popup
    Route::post('/student/skip-barcode', function () {
        session(['barcode_skipped' => true]);
        return response()->json(['status' => 'success']);
    });

    // Student Routes
    Route::get('/student/home', function () { return view('student.home'); });
    Route::get('/student/booking', [AppointmentController::class, 'create'])->name('student.booking');
    Route::get('/student/account', [AppointmentController::class, 'account']);
    Route::get('/student/faq', [AppointmentController::class, 'faq']);
    Route::get('/student/history', [AppointmentController::class, 'history']);
    Route::post('/student/appointments/store', [AppointmentController::class, 'store']);
    Route::post('/student/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
    Route::post('/student/update-contact', [AppointmentController::class, 'updateContact']);

    // --- ADMIN ROUTES ---
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/appointments', [AdminController::class, 'appointments'])->name('admin.appointments');
    Route::get('/admin/appointments/{id}/{status}', [AdminController::class, 'updateStatus']);
    Route::post('/admin/appointments/{id}/reschedule', [AdminController::class, 'reschedule']);

    // Inventory Routes
    Route::get('/admin/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
    Route::post('/admin/inventory/store', [AdminController::class, 'storeItem']);
    Route::put('/admin/inventory/{id}', [AdminController::class, 'updateItem']);
    Route::delete('/admin/inventory/{id}', [AdminController::class, 'deleteItem']);

    // Settings & Barcode
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/settings/update', [AdminController::class, 'updateSettings']);
    Route::put('/admin/profile/update', [AdminController::class, 'updateProfile']);
    Route::get('student/barcode-register', [AppointmentController::class, 'barcodeRegister'])->name('barcode.register');
    Route::post('student/barcode-register', [AppointmentController::class, 'storeBarcode'])->name('barcode.store');
    Route::get('/fetch-user/{student_id}', [AppointmentController::class, 'fetchUser']);
    Route::post('/student/reset-barcode', [AppointmentController::class, 'resetBarcode'])->name('barcode.reset');

    // Admin Walk-in
    Route::get('/admin/walkin', [WalkInController::class, 'index'])->name('walkin.index');
    Route::get('/admin/walkin/get-student', [WalkInController::class, 'getStudent'])->name('walkin.getStudent');
    Route::post('/admin/walkin/register', [WalkInController::class, 'registerStudent'])->name('walkin.registerStudent');
    Route::get('/admin/walkin/form/{student_id}', [WalkInController::class, 'showWalkinForm'])->name('walkin.form');
    Route::post('/admin/walkin/store', [WalkInController::class, 'store'])->name('walkin.store');

    // Main Reports Page
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');

    // MAR & Management
    Route::get('/admin/reports/mar', [ReportsController::class, 'marReport'])->name('reports.mar');
    Route::get('/admin/reports/manage-mar', [ReportsController::class, 'manageMar'])->name('admin.reports.manage-mar');

    // Inventory Summary
    Route::get('/admin/reports/inventory-summary', [AdminController::class, 'inventorySummary'])->name('reports.inventory-summary');

    // EXPORT HUB 
    Route::get('/reports/export-hub', [ReportsController::class, 'exportHub'])->name('reports.exportHub');

    // MEDICAL CONDITIONS
    Route::post('/admin/medical-conditions', [MedicalConditionController::class, 'store'])->name('conditions.store');
    Route::delete('/admin/medical-conditions/{id}', [MedicalConditionController::class, 'destroy'])->name('conditions.destroy');

    // Additional Report Routes
    Route::get('/admin/reports/export-hub', [ReportsController::class, 'exportHub'])->name('reports.exportHub');
    Route::get('/admin/reports/print-reports', [ReportsController::class, 'printReport'])->name('reports.print');

    // Barcode Registration Routes for Students (student side)
    Route::get('/barcode-register', [AppointmentController::class, 'barcodeRegister'])->name('barcode.register');
    Route::post('/barcode-store', [AppointmentController::class, 'storeBarcode'])->name('barcode.store');
    Route::post('/barcode-reset', [AppointmentController::class, 'resetBarcode'])->name('barcode.reset');

}); // End of Protected Routes

// Temporary dev login helper
Route::get('/dev-login/{id}', function($id) {
    $user = \App\Models\User::find($id);
    if ($user) {
        Auth::login($user);
        return redirect('/student/account')->with('success', 'Logged in as ' . $user->name);
    }
    return "User not found!";
});