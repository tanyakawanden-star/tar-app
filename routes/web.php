<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\TravelRequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Auth scaffolding (if using Laravel UI / Breeze etc.)
Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/', [TravelRequestController::class, 'index'])->name('home');

    Route::resource('travel-requests', TravelRequestController::class);

    Route::get('travel-requests/{travelRequest}/pdf', [TravelRequestController::class, 'pdf'])
        ->name('travel-requests.pdf');

    Route::get('approvals', [ApprovalController::class, 'index'])
        ->name('approvals.index');
    Route::get('approvals/{travelRequest}', [ApprovalController::class, 'show'])
        ->name('approvals.show');
    Route::post('approvals/{travelRequest}/approve', [ApprovalController::class, 'approve'])
        ->name('approvals.approve');
    Route::post('approvals/{travelRequest}/reject', [ApprovalController::class, 'reject'])
        ->name('approvals.reject');
});
