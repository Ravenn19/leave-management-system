<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;

// Test route
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!']);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    Route::get('/my-leave-requests', [LeaveRequestController::class, 'myRequests']);
    Route::get('/leave-requests/{id}', [LeaveRequestController::class, 'show']);
    Route::delete('/leave-requests/{id}/cancel', [LeaveRequestController::class, 'cancel']);

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/leave-requests', [LeaveRequestController::class, 'allRequests']);
        Route::put('/admin/leave-requests/{id}/approve', [LeaveRequestController::class, 'approve']);
        Route::put('/admin/leave-requests/{id}/reject', [LeaveRequestController::class, 'reject']);
        Route::get('/admin/statistics', [LeaveRequestController::class, 'statistics']);
    });
});
