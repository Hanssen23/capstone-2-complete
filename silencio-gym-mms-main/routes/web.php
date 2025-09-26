<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeAuthController;
use App\Http\Controllers\RfidController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\EmployeeDashboardController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CSRF Token refresh endpoint
Route::get('/csrf-token', function() {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

// Member self-registration
Route::get('/register', [MemberAuthController::class, 'showRegister'])->name('member.register');
Route::post('/register', [MemberAuthController::class, 'register'])->name('member.register.post');

// RFID routes (public access for hardware integration)
Route::prefix('rfid')->name('rfid.')->group(function () {
    Route::post('tap', [RfidController::class, 'handleCardTap'])->name('tap')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('active-members', [RfidController::class, 'getActiveMembers'])->name('active-members')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('logs', [RfidController::class, 'getRfidLogs'])->name('logs')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class, \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    
    // RFID Automation routes (protected by auth)
    Route::post('start', [RfidController::class, 'startRfidReader'])->name('start');
    Route::post('stop', [RfidController::class, 'stopRfidReader'])->name('stop');
    Route::get('status', [RfidController::class, 'getRfidStatus'])->name('status');
    Route::get('member-suggestions', [RfidController::class, 'getMemberSuggestions'])->name('member-suggestions');
});

Route::middleware(['auth', 'admin.only', 'prevent.member.admin', 'prevent.back', 'start.rfid'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/stats', [DashboardController::class, 'getDashboardStats'])->name('dashboard.stats');
    
    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('weekly-attendance', [AnalyticsController::class, 'getWeeklyAttendance'])->name('weekly-attendance');
        Route::get('monthly-revenue', [AnalyticsController::class, 'getMonthlyRevenue'])->name('monthly-revenue');
        Route::get('dashboard-stats', [AnalyticsController::class, 'getDashboardStats'])->name('dashboard-stats');
        Route::get('attendance-trends', [AnalyticsController::class, 'getAttendanceTrends'])->name('attendance-trends');
        Route::get('revenue-trends', [AnalyticsController::class, 'getRevenueTrends'])->name('revenue-trends');
        Route::get('rfid-activity', [AnalyticsController::class, 'getRfidActivity'])->name('rfid-activity');
    });

    // Membership Plan Management routes
    Route::prefix('membership-plans')->name('membership-plans.')->group(function () {
        Route::get('active', [MembershipPlanController::class, 'getActivePlans'])->name('active');
        Route::get('pricing', [MembershipPlanController::class, 'getPricing'])->name('pricing');
        Route::get('all', [MembershipPlanController::class, 'getAllPlans'])->name('all');
        Route::get('plan-types', [MembershipPlanController::class, 'getPlanTypes'])->name('plan-types');
        Route::get('duration-types', [MembershipPlanController::class, 'getDurationTypes'])->name('duration-types');
        Route::post('', [MembershipPlanController::class, 'store'])->name('store');
        Route::put('{plan}', [MembershipPlanController::class, 'update'])->name('update');
        Route::delete('{plan}', [MembershipPlanController::class, 'destroy'])->name('destroy');
        Route::patch('{plan}/toggle-status', [MembershipPlanController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('{plan}/usage-stats', [MembershipPlanController::class, 'getUsageStats'])->name('usage-stats');
        Route::put('plan-types', [MembershipPlanController::class, 'updatePlanTypes'])->name('update-plan-types');
        Route::put('duration-types', [MembershipPlanController::class, 'updateDurationTypes'])->name('update-duration-types');
    });
    
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
    
    // RFID System Management
    Route::get('rfid/start', [RfidController::class, 'startRfidSystem'])->name('rfid.start');
    Route::get('rfid/stop', [RfidController::class, 'stopRfidSystem'])->name('rfid.stop');
    Route::get('rfid/status', [RfidController::class, 'getRfidStatus'])->name('rfid.status');
    
    // Account Management routes (admin only)
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::put('{user}', [AccountController::class, 'update'])->name('update');
        Route::delete('{user}', [AccountController::class, 'destroy'])->name('destroy');
        Route::patch('{user}/toggle-status', [AccountController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('bulk-action', [AccountController::class, 'bulkAction'])->name('bulk-action');
    });
});

// Employee test route (for debugging)
// Temporary test route for employee debugging
Route::get('/employee/test', function () {
    $user = auth()->user();
    return response()->json([
        'authenticated' => auth()->check(),
        'user_id' => $user ? $user->id : null,
        'user_email' => $user ? $user->email : null,
        'user_role' => $user ? $user->role : null,
        'is_employee' => $user ? $user->isEmployee() : false,
        'is_admin' => $user ? $user->isAdmin() : false,
        'message' => 'Employee test route working!'
    ]);
})->middleware(['auth']);

// Employee routes with enhanced session persistence
Route::middleware(['auth', 'employee.only', 'ensure.session'])->group(function () {
    Route::get('employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    Route::get('employee/dashboard/stats', [EmployeeDashboardController::class, 'getDashboardStats'])->name('employee.dashboard.stats');
    
    // Analytics routes (same as admin)
    Route::prefix('employee/analytics')->name('employee.analytics.')->group(function () {
        Route::get('weekly-attendance', [AnalyticsController::class, 'getWeeklyAttendance'])->name('weekly-attendance');
        Route::get('monthly-revenue', [AnalyticsController::class, 'getMonthlyRevenue'])->name('monthly-revenue');
        Route::get('dashboard-stats', [AnalyticsController::class, 'getDashboardStats'])->name('dashboard-stats');
        Route::get('attendance-trends', [AnalyticsController::class, 'getAttendanceTrends'])->name('attendance-trends');
        Route::get('revenue-trends', [AnalyticsController::class, 'getRevenueTrends'])->name('revenue-trends');
        Route::get('rfid-activity', [AnalyticsController::class, 'getRfidActivity'])->name('rfid-activity');
    });

    // Membership Plan Management routes (full access - same as admin)
    Route::prefix('employee/membership-plans')->name('employee.membership-plans.')->group(function () {
        Route::get('/', function () {
            // Get plans data for the view - using full admin functionality
            $plans = \App\Models\MembershipPlan::all();
            $planTypes = config('membership.plan_types');
            $durationTypes = config('membership.duration_types');
            return view('employee.membership-plans-full', compact('plans', 'planTypes', 'durationTypes'));
        })->name('index');
        Route::get('active', [MembershipPlanController::class, 'getActivePlans'])->name('active');
        Route::get('pricing', [MembershipPlanController::class, 'getPricing'])->name('pricing');
        Route::get('all', [MembershipPlanController::class, 'getAllPlans'])->name('all');
        Route::get('plan-types', [MembershipPlanController::class, 'getPlanTypes'])->name('plan-types');
        Route::get('duration-types', [MembershipPlanController::class, 'getDurationTypes'])->name('duration-types');
        Route::get('{plan}/usage-stats', [MembershipPlanController::class, 'getUsageStats'])->name('usage-stats');
        
        // CRUD operations for employee (same as admin)
        Route::post('/', [MembershipPlanController::class, 'store'])->name('store');
        Route::put('duration-types', [MembershipPlanController::class, 'updateDurationTypes'])->name('update-duration-types');
        Route::put('plan-types', [MembershipPlanController::class, 'updatePlanTypes'])->name('update-plan-types');
        Route::put('{plan}', [MembershipPlanController::class, 'update'])->name('update');
        Route::delete('{plan}', [MembershipPlanController::class, 'destroy'])->name('destroy');
        Route::patch('{plan}/toggle-status', [MembershipPlanController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Member routes (with restrictions)
    Route::prefix('employee/members')->name('employee.members.')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('create', [MemberController::class, 'create'])->name('create');
        Route::post('/', [MemberController::class, 'store'])->name('store');
        Route::get('{member}', [MemberController::class, 'show'])->name('show');
        Route::get('{member}/edit', [MemberController::class, 'edit'])->name('edit');
        Route::put('{member}', [MemberController::class, 'update'])->name('update');
        Route::delete('{member}', [MemberController::class, 'destroy'])->name('destroy');
        Route::get('{member}/profile', [MemberController::class, 'profile'])->name('profile');
        Route::get('{member}/membership-history', [MemberController::class, 'membershipHistory'])->name('membership-history');
    });
    
    // Membership routes (full access)
    Route::prefix('employee/membership')->name('employee.membership.')->group(function () {
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
    
    // RFID Monitoring Panel (full access)
    Route::get('employee/rfid-monitor', function () {
        return view('employee.rfid-monitor');
    })->name('employee.rfid-monitor');
    
    // RFID System Management (full access)
    Route::get('employee/rfid/start', [RfidController::class, 'startRfidSystem'])->name('employee.rfid.start');
    Route::get('employee/rfid/stop', [RfidController::class, 'stopRfidSystem'])->name('employee.rfid.stop');
    Route::get('employee/rfid/status', [RfidController::class, 'getRfidStatus'])->name('employee.rfid.status');
    
    // Account Management routes (restricted - edit account only, no user type)
    Route::prefix('employee/accounts')->name('employee.accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('create', [AccountController::class, 'create'])->name('create');
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::get('{user}/edit', [AccountController::class, 'edit'])->name('edit');
        Route::put('{user}', [AccountController::class, 'update'])->name('update');
        Route::delete('{user}', [AccountController::class, 'destroy'])->name('destroy');
        Route::patch('{user}/toggle-status', [AccountController::class, 'toggleStatus'])->name('toggle-status');
    });
});


// Member routes (member guard)
Route::middleware(['auth:member', 'member.only', 'prevent.back'])->group(function () {
    Route::get('member', [MemberDashboardController::class, 'index'])->name('member.dashboard');
    Route::get('member/plans', [MemberDashboardController::class, 'plans'])->name('member.plans');
    Route::get('member/accounts', [MemberDashboardController::class, 'accounts'])->name('member.accounts');
    Route::put('member/profile', [MemberDashboardController::class, 'updateProfile'])->name('member.profile.update');
    
    // Member-side membership plan access (read-only with real-time updates)
    Route::get('member/membership-plans', [MembershipPlanController::class, 'getActivePlans'])->name('member.membership-plans');
    Route::get('member/membership-pricing', [MembershipPlanController::class, 'getPricing'])->name('member.membership-pricing');
    Route::get('member/membership-plans/stream', [MembershipPlanController::class, 'streamUpdates'])->name('member.membership-plans.stream');
});