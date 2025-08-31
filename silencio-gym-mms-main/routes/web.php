<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RfidController;
use App\Http\Controllers\MembershipController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// RFID routes (public access for hardware integration)
Route::prefix('rfid')->name('rfid.')->group(function () {
    Route::post('tap', [RfidController::class, 'handleCardTap'])->name('tap')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('active-members', [RfidController::class, 'getActiveMembers'])->name('active-members')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('logs', [RfidController::class, 'getRfidLogs'])->name('logs')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    
    // RFID Automation routes (protected by auth)
    Route::post('start', [RfidController::class, 'startRfidReader'])->name('start');
    Route::post('stop', [RfidController::class, 'stopRfidReader'])->name('stop');
    Route::get('status', [RfidController::class, 'getRfidStatus'])->name('status');
});

Route::middleware(['auth', 'start.rfid'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/stats', [DashboardController::class, 'getDashboardStats'])->name('dashboard.stats');
    
    // Member routes
    Route::resource('members', MemberController::class);
    Route::get('members/{member}/profile', [MemberController::class, 'profile'])->name('members.profile');
    Route::get('members/{member}/membership-history', [MemberController::class, 'membershipHistory'])->name('members.membership-history');
    
    // Membership routes
    Route::prefix('membership')->name('membership.')->group(function () {
        Route::get('plans', [MembershipController::class, 'index'])->name('plans.index');
        Route::get('manage-member', [MembershipController::class, 'manageMember'])->name('manage-member');
        Route::post('calculate-price', [MembershipController::class, 'calculatePrice'])->name('calculate-price');
        Route::post('process-payment', [MembershipController::class, 'processPayment'])->name('process-payment');
        Route::get('payments', [MembershipController::class, 'payments'])->name('payments');
        Route::get('payments/export/csv', [MembershipController::class, 'exportToCsv'])->name('payments.export_csv');
        Route::patch('payments/{payment}/status', [MembershipController::class, 'updatePaymentStatus'])->name('payments.update-status');
        Route::get('payments/{payment}/details', [MembershipController::class, 'getPaymentDetails'])->name('payments.details');
        Route::get('payments/{payment}/print', [MembershipController::class, 'printReceipt'])->name('payments.print');
    });
    
    // RFID Monitoring Panel
    Route::get('rfid-monitor', function () {
        return view('rfid-monitor');
    })->name('rfid-monitor');
});