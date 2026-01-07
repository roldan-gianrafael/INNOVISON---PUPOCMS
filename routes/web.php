<?php
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// This route serves your landing page at the main URL (http://127.0.0.1:8000)
Route::get('/', function () {
    return view('landing'); // This matches the name 'landing.blade.php'
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/student/home', function () {
    return view('student.home');
});

// Now we use the Controller to load the page so it brings the data with it
Route::get('/student/booking', [AppointmentController::class, 'create']);

Route::get('/student/account', [AppointmentController::class, 'account']);

Route::get('/student/faq', [AppointmentController::class, 'faq']);

Route::get('/student/history', [AppointmentController::class, 'history']);

// The route that handles the form submission
Route::post('/student/appointments/store', [AppointmentController::class, 'store']);

// Route to handle cancellation
Route::post('/student/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);

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
    // Export Inventory
    Route::get('/admin/reports/export-inventory', [AdminController::class, 'exportInventory']);

    // Settings Routes
    Route::get('/admin/settings', [AdminController::class, 'settings']);
    Route::put('/admin/settings/update', [AdminController::class, 'updateSettings']);
    Route::put('/admin/profile/update', [AdminController::class, 'updateProfile']);

    Route::post('/student/update-contact', [App\Http\Controllers\AppointmentController::class, 'updateContact']);
    Route::get('/student/booking', [AppointmentController::class, 'create'])->name('student.booking');
