<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WalkInController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\MedicalConditionController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root route: serve the landing page
Route::get('/', function () {
    return view('landing');
});

// Student Home
Route::get('/student/home', function () {
    return view('student.home');
});

// ==========================================
// 1. STUDENT ROUTES
// ==========================================
Route::get('/student/booking', [AppointmentController::class, 'create'])->name('student.booking');
Route::get('/student/account', [AppointmentController::class, 'account']);
Route::get('/student/faq', [AppointmentController::class, 'faq']);
Route::get('/student/history', [AppointmentController::class, 'history']);
Route::post('/student/appointments/store', [AppointmentController::class, 'store']);
Route::post('/student/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
Route::post('/student/update-contact', [AppointmentController::class, 'updateContact']);

// --- Student Barcode ---
Route::get('student/barcode-register', [AppointmentController::class, 'barcodeRegister'])->name('barcode.register');
Route::post('student/barcode-register', [AppointmentController::class, 'storeBarcode'])->name('barcode.store');
Route::get('/fetch-user/{student_id}', [AppointmentController::class, 'fetchUser']);
Route::post('/student/reset-barcode', [AppointmentController::class, 'resetBarcode'])->name('barcode.reset');


// ==========================================
// 2. ADMIN GENERAL ROUTES
// ==========================================
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// --- Appointments Management ---
Route::get('/admin/appointments', [AdminController::class, 'appointments'])->name('admin.appointments');
Route::get('/admin/appointments/{id}/{status}', [AdminController::class, 'updateStatus']);
Route::post('/admin/appointments/{id}/reschedule', [AdminController::class, 'reschedule']);

// --- Inventory Management ---
Route::get('/admin/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
Route::post('/admin/inventory/store', [AdminController::class, 'storeItem']);
Route::put('/admin/inventory/{id}', [AdminController::class, 'updateItem']);
Route::delete('/admin/inventory/{id}', [AdminController::class, 'deleteItem']);

// --- Settings & Profile ---
Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
Route::put('/admin/settings/update', [AdminController::class, 'updateSettings']);
Route::put('/admin/profile/update', [AdminController::class, 'updateProfile']);


// ==========================================
// 3. ADMIN WALK-IN ROUTES
// ==========================================
Route::get('/admin/walkin', [WalkInController::class, 'index'])->name('walkin.index');
Route::get('/admin/walkin/get-student', [WalkInController::class, 'getStudent'])->name('walkin.getStudent');
Route::post('/admin/walkin/register', [WalkInController::class, 'registerStudent'])->name('walkin.registerStudent');
Route::get('/admin/walkin/form/{student_id}', [WalkInController::class, 'showWalkinForm'])->name('walkin.form');
Route::post('/admin/walkin/store', [WalkInController::class, 'store'])->name('walkin.store');


// ==========================================
// 4. REPORT & EXPORT ROUTES (CENTRALIZED)
// ==========================================
Route::prefix('admin/reports')->group(function () {
    
    // Main Reports Dashboard
    Route::get('/', [AdminController::class, 'reports'])->name('admin.reports');

    // EXPORT HUB - Ang bago nating page
    Route::get('/export-hub', [ReportsController::class, 'exportHub'])->name('reports.exportHub');

    // MAR Report View & Management
    Route::get('/mar', [ReportsController::class, 'marReport'])->name('reports.mar');
    Route::get('/manage-mar', [ReportsController::class, 'manageMar'])->name('admin.reports.manage-mar');
    
    // Inventory Summary View
    Route::get('/inventory-summary', [AdminController::class, 'inventorySummary'])->name('reports.inventory-summary');

    // --- DOWNLOAD/EXPORT ACTIONS ---
    // Gagamitin natin ang mga ito sa Export Hub cards
    Route::get('/mar/export', [ReportsController::class, 'exportExcel'])->name('reports.mar.export');
    Route::get('/appointments/export', [AdminController::class, 'exportReports'])->name('admin.export.appointments');
    Route::get('/inventory/export', [AdminController::class, 'exportInventory'])->name('admin.export.inventory');
});


// ==========================================
// 5. MEDICAL CONDITIONS ACTIONS
// ==========================================
Route::post('/admin/medical-conditions', [MedicalConditionController::class, 'store'])->name('conditions.store');
Route::delete('/admin/medical-conditions/{id}', [MedicalConditionController::class, 'destroy'])->name('conditions.destroy');
Route::put('/admin/medical-conditions/{id}', [ReportsController::class, 'update'])->name('conditions.update');