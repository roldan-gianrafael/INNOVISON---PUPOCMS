<?php

use App\Http\Controllers\Api\AdminProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('external.api')->group(function () {
    Route::get('/external/admins', [AdminProfileController::class, 'index']);
    Route::get('/external/admins/options', [AdminProfileController::class, 'options']);
    Route::get('/external/admins/{admin_id}', [AdminProfileController::class, 'externalShow']);
    Route::put('/external/admins/{admin_id}', [AdminProfileController::class, 'externalUpdate']);
    Route::get('/external/admin-profile', [AdminProfileController::class, 'lookup']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/profile/{admin_id}', [AdminProfileController::class, 'show']);
    Route::post('/admin/profile/update', [AdminProfileController::class, 'update']);
});
