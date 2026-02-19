<?php
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WalkInController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarcodeController;

// Root route: serve the landing page
Route::get('/', function () {
    return view('landing');
});

Route::get('/student/home', function () {
    return view('student.home');
});

// Student Routes
Route::get('/student/booking', [AppointmentController::class, 'create'])->name('student.booking');
Route::get('/student/account', [AppointmentController::class, 'account']);
Route::get('/student/faq', [AppointmentController::class, 'faq']);
Route::get('/student/history', [AppointmentController::class, 'history']);
Route::post('/student/appointments/store', [AppointmentController::class, 'store']);
Route::post('/student/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
Route::post('/student/update-contact', [AppointmentController::class, 'updateContact']);

// --- ADMIN ROUTES ---
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

// Show the Appointments List
Route::get('/admin/appointments', [AdminController::class, 'appointments']);

// Handle Status Updates (Approve/Cancel)
Route::get('/admin/appointments/{id}/{status}', [AdminController::class, 'updateStatus']);

// Handle Reschedule Logic
Route::post('/admin/appointments/{id}/reschedule', [AdminController::class, 'reschedule']);

// Inventory Routes
Route::get('/admin/inventory', [AdminController::class, 'inventory']);
Route::post('/admin/inventory/store', [AdminController::class, 'storeItem']);
Route::put('/admin/inventory/{id}', [AdminController::class, 'updateItem']);
Route::delete('/admin/inventory/{id}', [AdminController::class, 'deleteItem']);

// Reports Routes
Route::get('/admin/reports', [AdminController::class, 'reports']);
Route::get('/admin/reports/export', [AdminController::class, 'exportReports']);
Route::get('/admin/reports/export-inventory', [AdminController::class, 'exportInventory']);

// Settings Routes
Route::get('/admin/settings', [AdminController::class, 'settings']);
Route::put('/admin/settings/update', [AdminController::class, 'updateSettings']);
Route::put('/admin/profile/update', [AdminController::class, 'updateProfile']);

/// FOR Student BARCODE CONTROLLER

// Show barcode register page
Route::get('student/barcode-register', [AppointmentController::class, 'barcodeRegister'])->name('barcode.register');

// Save barcode
Route::post('student/barcode-register', [AppointmentController::class, 'storeBarcode'])->name('barcode.store');

// Fetch student info
Route::get('/fetch-user/{student_id}', [AppointmentController::class, 'fetchUser']);

// Reset barcode (for testing)
Route::post('/student/reset-barcode', [AppointmentController::class, 'resetBarcode'])->name('barcode.reset');


//// FOR ADMIN WALKIN REGISTRATION AND SCANNING
//// ADMIN WALK-IN ROUTES
    Route::prefix('admin/walkin')->group(function () {

    // Show Walk-in page (scanner + registration)
    Route::get('/', [WalkInController::class, 'index'])->name('walkin.index');

    // Fetch student info by student_id (AJAX)
    Route::get('/get-student', [WalkInController::class, 'getStudent'])->name('walkin.getStudent');

    // Store walk-in appointment
    Route::post('/store', [WalkInController::class, 'store'])->name('walkin.store');

    
// Register a new student from admin walk-in page
Route::post('/admin/walkin/register-student', [WalkInController::class, 'registerStudent'])
    ->name('walkin.registerStudent');

    });


