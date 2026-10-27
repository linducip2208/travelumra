<?php

use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\MuthawifController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PilgrimController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController as PilgrimAuthController;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('/packages/featured', [PackageController::class, 'featured']);
    Route::get('/packages/{slug}', [PackageController::class, 'show']);
    Route::post('/leads', [LeadController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/pilgrim/login', [PilgrimAuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/pilgrim/logout', [PilgrimAuthController::class, 'logout']);
    Route::get('/me', [PilgrimController::class, 'me']);
    Route::get('/me/bookings', [PilgrimController::class, 'bookings']);
    Route::get('/me/invoices', [PilgrimController::class, 'invoices']);
    Route::get('/me/itinerary/{booking}', [PilgrimController::class, 'itinerary']);
    Route::post('/me/payments/upload', [PilgrimController::class, 'uploadPayment']);
    Route::get('/me/documents', [PilgrimController::class, 'documents']);
    Route::post('/me/documents/upload', [PilgrimController::class, 'uploadDocument']);
    Route::get('/me/documents/{document}/download', [\App\Http\Controllers\SecureFileController::class, 'apiDocument']);
    Route::get('/me/payments/{payment}/download', [\App\Http\Controllers\SecureFileController::class, 'apiPayment']);
    Route::patch('/me/profile', [PilgrimController::class, 'updateProfile']);

    Route::prefix('muthawif')->group(function () {
        Route::get('/cluster', [MuthawifController::class, 'cluster']);
        Route::get('/pilgrims', [MuthawifController::class, 'pilgrims']);
        Route::post('/attendance', [MuthawifController::class, 'attendance']);
        Route::post('/reports', [MuthawifController::class, 'dailyReport']);
        Route::post('/emergency', [MuthawifController::class, 'emergency']);
    });
});
