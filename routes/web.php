<?php
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WalkInController;
use Illuminate\Support\Facades\Route;

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

/// Walk-in page
Route::get('/admin/walk-in', [WalkInController::class, 'index'])->name('walkin.index');

// Store walk-in appointment
Route::post('/admin/walk-in/store', [WalkInController::class, 'store'])->name('walkin.store');

// Get student info by student_id
Route::get('/admin/walk-in/student', [WalkInController::class, 'getStudent'])->name('walkin.getStudent');
Route::post('/admin/walk-in/select-student', [WalkInController::class, 'selectStudent'])->name('walkin.selectStudent');
