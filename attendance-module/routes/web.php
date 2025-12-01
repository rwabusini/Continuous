<?php

declare(strict_types=1);

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TrainerSessionController;
use App\Http\Middleware\AttachRiskContext;
use App\Http\Middleware\EnsureSessionIsActive;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:lms'])->group(function () {
    Route::get('/trainer/sessions/{session}', [TrainerSessionController::class, 'show'])
        ->middleware('can:manage,session');

    Route::post('/trainer/sessions/{session}/challenge', [TrainerSessionController::class, 'challenge'])
        ->middleware('can:manage,session');

    Route::middleware([EnsureSessionIsActive::class, AttachRiskContext::class])->group(function () {
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/sessions/{session}/challenge', [AttendanceController::class, 'submitChallenge']);
        Route::post('/sessions/{session}/beacon', [AttendanceController::class, 'beacon']);
    });
});
