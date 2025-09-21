<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberRegistrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GenealogyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Admin\ReferralReportController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\Admin\LoanApprovalController;
use App\Http\Controllers\Admin\LoanManagementController;
use App\Http\Controllers\Admin\CashInApprovalController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\Admin\MembershipCodeController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Member\RewardHistoryController;
use App\Http\Controllers\Member\TicketController as MemberTicketController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\LoanPaymentController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\Admin\MemberApprovalController;
use App\Http\Controllers\GuestRegistrationController;
use App\Http\Controllers\Auth\SmsForgotPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordSmsController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Admin\ReferralBonusController;
use App\Http\Controllers\MemberProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderReportController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AdminWalletController;

        // ✅ Welcome Page
        Route::get('/', function () {
            try {
                $featuredProducts = \App\Models\Product::where('active', 1)
                    ->with('category')
                    ->inRandomOrder()
                    ->take(15)
                    ->get();
            } catch (\Exception $e) {
                // Fallback if products table doesn't exist or has issues
                $featuredProducts = collect();
            }
            return view('welcome', compact('featuredProducts'));
        })->name('welcome');

             // ✅ Authentication Routes
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');

        // ✅ Social Login Routes
        Route::get('/auth/{provider}', [\App\Http\Controllers\Auth\SocialLoginController::class, 'redirectToProvider'])->name('social.redirect');
        Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialLoginController::class, 'handleProviderCallback'])->name('social.callback');

        // ✅ AliExpress OAuth Routes
        Route::get('/aliexpress/auth', [\App\Http\Controllers\Auth\AliexpressAuthController::class, 'redirectToProvider'])->name('aliexpress.redirect');
        Route::get('/aliexpress/callback', [\App\Http\Controllers\Auth\AliexpressAuthController::class, 'handleProviderCallback'])->name('aliexpress.callback');

        // ✅ OTP Login Routes
        Route::post('/auth/otp/send', [\App\Http\Controllers\Auth\OtpLoginController::class, 'sendOtp'])->name('otp.send');
        Route::post('/auth/otp/verify', [\App\Http\Controllers\Auth\OtpLoginController::class, 'verifyOtp'])->name('otp.verify');
        Route::post('/auth/otp/resend', [\App\Http\Controllers\Auth\OtpLoginController::class, 'resendOtp'])->name('otp.resend');

        // ✅ Push Notification Routes (Public - for device registration)
        Route::middleware('auth')->prefix('api/notifications')->name('notifications.')->group(function () {
            Route::post('/register-token', [\App\Http\Controllers\PushNotificationController::class, 'registerToken'])->name('register');
            Route::post('/unregister-token', [\App\Http\Controllers\PushNotificationController::class, 'unregisterToken'])->name('unregister');
            Route::post('/test', [\App\Http\Controllers\PushNotificationController::class, 'sendTestNotification'])->name('test');
            Route::get('/tokens', [\App\Http\Controllers\PushNotificationController::class, 'getUserTokens'])->name('tokens');
            Route::delete('/tokens/{tokenId}', [\App\Http\Controllers\PushNotificationController::class, 'removeToken'])->name('tokens.remove');
        });



    // 🛒 Cart & Order Routes (Member Only)
Route::middleware(['auth', 'can:member-only'])->group(function () {
    // View Cart
    Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');

    // Add to Cart
    Route::post('/shop/order/{product}', [ShopController::class, 'order'])->name('shop.order');

    // Update Quantity (AJAX PATCH)
Route::patch('/shop/cart/{id}/update', [ShopController::class, 'updateQuantity'])->name('shop.cart.update');

    // Remove Item
    Route::delete('/shop/cart/{id}', [ShopController::class, 'remove'])->name('shop.cart.remove');

    // Checkout
    Route::get('/shop/checkout', [ShopController::class, 'checkoutPage'])->name('shop.checkout.page');
    Route::post('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');


});



        // ✅ Registration Routes
        Route::get('/register-member', [MemberRegistrationController::class, 'create'])->name('member.register');
        Route::post('/register-member', [MemberRegistrationController::class, 'store'])->name('member.store');


        // Password Reset Flow
        Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

        Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

        Route::get('password/reset', [ResetPasswordController::class, 'showLinkRequestForm']);
        Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail']);
        Route::get('password/sms', [ForgotPasswordSmsController::class, 'showSmsForm']);

        // ✅ SMS Password Reset Flow
        Route::get('/forgot-password-sms', [ForgotPasswordSmsController::class, 'showForm'])->name('password.sms.request');
        Route::post('/forgot-password-sms', [ForgotPasswordSmsController::class, 'sendSmsResetLink'])->name('password.sms.send');


        // ✅ SMS Password Reset Routes
        Route::get('/password/sms', [SmsForgotPasswordController::class, 'showRequestForm'])->name('password.sms.request');
        Route::post('/password/sms', [SmsForgotPasswordController::class, 'sendSmsResetLink'])->name('password.sms.send');

        Route::get('/password/sms/verify', [SmsForgotPasswordController::class, 'showVerifyForm'])->name('password.sms.verify.form');
        Route::post('/password/sms/verify', [SmsForgotPasswordController::class, 'verifyCode'])->name('password.sms.verify');

        Route::get('/password/sms/reset', [SmsForgotPasswordController::class, 'showResetForm'])->name('password.sms.reset.form');
        Route::post('/password/sms/reset', [SmsForgotPasswordController::class, 'reset'])->name('password.sms.reset');
        Route::get('password/reset', [ResetPasswordController::class, 'showLinkRequestForm']);


        // ✅ Unified Protected Routes (must be authenticated)
        Route::middleware('auth')->group(function () {

            // ✅ Role-Based Redirect from /home
            Route::get('/home', function () {
                $user = auth()->user();

                switch ($user->role) {
                    case 'Admin':
                        return redirect()->route('admin.dashboard');
                    case 'Staff':
                        return redirect()->route('staff.dashboard');
                    default:
                        return redirect()->route('member.dashboard');
                }
            })->name('home');

            // ✅ Optional /dashboard can just redirect to /home
            Route::get('/dashboard', function () {
                return redirect()->route('home');
            });


Route::get('storage/photos/{filename}', function ($filename) {
    $path = storage_path('photos/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return response($file)->header("Content-Type", $type);
});


            // ✅ Member Registration Routes (Authenticated Users Only)
            Route::middleware(['auth'])->group(function () {
                Route::get('/member/register', [\App\Http\Controllers\MemberRegistrationController::class, 'create'])
                    ->name('member.register.form');

                Route::post('/member/register', [\App\Http\Controllers\MemberRegistrationController::class, 'store'])
                    ->name('member.register.store');

                // Merchant Registration Routes
                Route::get('/member/merchant-register', [\App\Http\Controllers\MemberRegistrationController::class, 'create'])
                    ->name('member.merchant.register.form');

                Route::post('/member/merchant-register', [\App\Http\Controllers\MemberRegistrationController::class, 'storeMerchant'])
                    ->name('member.merchant.register');
            });

Route::get('/check-mobile', [MemberRegistrationController::class, 'checkMobile']);


            // ✅ Role Dashboards
            Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');


        //    Route::view('/member/dashboard', 'dashboard.member')->name('member.dashboard');
            Route::get('/staff/dashboard', [\App\Http\Controllers\Staff\StaffDashboardController::class, 'index'])->name('staff.dashboard');

        Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');

        // Member Membership Code Request Routes
        Route::middleware(['auth', 'can:member-only'])->prefix('member')->name('member.')->group(function () {
            Route::get('/membership-code-request', [\App\Http\Controllers\Member\MembershipCodeRequestController::class, 'create'])->name('membership-code-request.create');
            Route::post('/membership-code-request', [\App\Http\Controllers\Member\MembershipCodeRequestController::class, 'store'])->name('membership-code-request.store');
            Route::get('/membership-code-requests', [\App\Http\Controllers\Member\MembershipCodeRequestController::class, 'index'])->name('membership-code-request.index');
        });


            // ✅ Member Utilities
            Route::get('/genealogy', [GenealogyController::class, 'index'])->name('genealogy.index');
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

            // Move the pending route group before resource routes to avoid conflicts
            Route::middleware(['auth', 'can:admin-only'])->prefix('admin')->name('admin.')->group(function () {
                Route::get('members/pending', [MemberApprovalController::class, 'index'])->name('members.pending');
                Route::post('members/approve', [MemberApprovalController::class, 'approve'])->name('members.approve');
                Route::post('members/reject/{id}', [MemberApprovalController::class, 'reject'])->name('members.reject');

                // Push Notification Admin Routes (Legacy - keeping for API compatibility)
                Route::prefix('notifications')->name('notifications.')->group(function () {
                    Route::post('/send-to-user', [\App\Http\Controllers\PushNotificationController::class, 'sendToUser'])->name('send.user');
                    Route::post('/broadcast', [\App\Http\Controllers\PushNotificationController::class, 'sendBroadcast'])->name('broadcast');
                    Route::post('/cleanup-expired', [\App\Http\Controllers\PushNotificationController::class, 'cleanupExpiredTokens'])->name('cleanup');
                });

                // Admin Notification Management UI Routes
                Route::prefix('notifications')->name('notifications.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
                    Route::get('/push', [\App\Http\Controllers\Admin\NotificationController::class, 'pushNotifications'])->name('push');
                    Route::get('/sms', [\App\Http\Controllers\Admin\NotificationController::class, 'smsBlasting'])->name('sms');
                    Route::get('/sms-history', [\App\Http\Controllers\Admin\NotificationController::class, 'smsHistory'])->name('sms.history');
                    Route::get('/device-tokens', [\App\Http\Controllers\Admin\NotificationController::class, 'deviceTokens'])->name('devices');

                    // POST routes for sending notifications
                    Route::post('/send-push', [\App\Http\Controllers\Admin\NotificationController::class, 'sendPushNotification'])->name('send.push');
                    Route::post('/send-sms', [\App\Http\Controllers\Admin\NotificationController::class, 'sendSmsBlast'])->name('send.sms');

                    // AJAX routes
                    Route::post('/test-push', [\App\Http\Controllers\Admin\NotificationController::class, 'testPushNotification'])->name('test.push');
                    Route::post('/test-sms', [\App\Http\Controllers\Admin\NotificationController::class, 'testSms'])->name('test.sms');
                    Route::get('/sms-balance', [\App\Http\Controllers\Admin\NotificationController::class, 'getSmsBalance'])->name('sms.balance');
                    Route::post('/cleanup-tokens', [\App\Http\Controllers\Admin\NotificationController::class, 'cleanupTokens'])->name('cleanup.tokens');

                    // SMS Log Management
                    Route::post('/sms/{id}/retry', [\App\Http\Controllers\Admin\NotificationController::class, 'retrySms'])->name('sms.retry');
                    Route::delete('/sms/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'deleteSmsLog'])->name('sms.delete');

                    // Notification Management Routes
                    Route::post('/{id}/mark-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('mark-as-read');
                    Route::post('/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
                });
            });

            Route::middleware('auth')->prefix('admin')->group(function () {
                Route::resource('members', App\Http\Controllers\MembersController::class);
            });


        Route::middleware('auth')->group(function () {
            Route::resource('admin/members', \App\Http\Controllers\MembersController::class);
        });


            // Route::resource('admin/loans', \App\Http\Controllers\LoanController::class);

        // ✅ Wallet Management
        Route::middleware('auth')->group(function () {
            Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
            Route::post('/wallet/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');
            Route::post('/wallet/cashin', [WalletController::class, 'cashin'])->name('wallet.cashin');

            // Payment Request QR Code Routes
            Route::get('/payment-request/{walletId}', [WalletController::class, 'showPaymentRequest'])->name('payment.request');
            Route::post('/payment-request/{walletId}/send', [WalletController::class, 'processPaymentRequest'])->name('payment.request.send');
        });


// Main wallet default (optional)
Route::get('/wallet/history', [WalletController::class, 'history'])->name('wallet.history');

// With type param
Route::get('/wallet/history/{type}', [WalletController::class, 'historyByType'])->name('wallet.history.type');

// ✅ Keep only this route
Route::post('/wallet/transfer-cashback', [WalletController::class, 'transferCashbackToMain'])
    ->name('wallet.transfer.cashback')
    ->middleware('auth');
// Cashback history as separate route
Route::get('/wallet/cashback-history', [WalletController::class, 'cashbackHistory'])->name('wallet.cashback-history');



// Admin Wallet Top-Up
Route::post('/admin/wallet/topup', [WalletController::class, 'adminTopUp'])->name('admin.wallet.topup');

Route::prefix('admin')->middleware(['auth', 'can:admin-only'])->group(function () {
    Route::get('/wallet/topup', [AdminWalletController::class, 'topupForm'])->name('admin.wallet.topup');
    Route::post('/wallet/topup', [AdminWalletController::class, 'processTopup'])->name('admin.wallet.topup.store');
});



        // ✅ Loan Routes (Authenticated Users Only)
        Route::middleware('auth')->group(function () {

            // 🔹 Loan: Create Request
            Route::post('/loan/request', [LoanController::class, 'requestLoan'])->name('loan.request');

            // 🔹 Loan: View All Requests (History)
            Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');

            // 🔹 Loan: Cancel a Request (Only if Pending)
            Route::delete('/loans/{id}/cancel', [LoanController::class, 'cancel'])->name('loans.cancel');

            // 🔹 Loan: View Specific Loan with Payment Breakdown
            Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');

            // 🔹 Loan: Redirect to Latest Loan
            Route::get('/loans/latest', function () {
                $member = Auth::user()->member;

                if (!$member) {
                    return redirect()->route('loans.index')->with('error', 'Member profile not found.');
                }

                $latestLoan = $member->loans()->latest()->first();

                if (!$latestLoan) {
                    return redirect()->route('loans.index')->with('info', 'You have no loans yet.');
                }

                return redirect()->route('loans.show', $latestLoan->id);
            })->name('loans.latest');

            // 🔹 Loan Payments: Mark as Paid (Manual by Admin or System)
            Route::post('/loan-payments/{id}/mark-paid', [LoanPaymentController::class, 'markAsPaid'])
                ->name('loan-payments.mark-paid');

            // 🔹 Loan Payments: Pay Now (Multiple Payment Methods)
            Route::post('/loan-payments/{id}/pay-now', [LoanPaymentController::class, 'payNow'])
                ->name('loan-payments.pay-now');

            // 🔹 Loan Payments: Show Payment Modal
            Route::get('/loan-payments/{id}/payment-modal', [LoanPaymentController::class, 'showPaymentModal'])
                ->name('loan-payments.payment-modal');
        });



        // ✅ Member Name Lookup by Mobile Number
        Route::get('/api/member-name/{mobile}', function ($mobile) {
            $member = \App\Models\Member::where('mobile_number', $mobile)->first();
            if (!$member) return response()->json(['message' => 'Not found'], 404);
            return response()->json(['full_name' => $member->full_name]);
        });

        // ✅ Admin Cash In Approvals
        Route::prefix('admin')->middleware('auth', 'admin')->group(function () {
            Route::get('/cashin-approvals', [CashInApprovalController::class, 'index'])->name('admin.cashin.index');
            Route::post('/cashin-approvals/{id}/approve', [CashInApprovalController::class, 'approve'])->name('admin.cashin.approve');
            Route::post('/cashin-approvals/{id}/reject', [CashInApprovalController::class, 'reject'])->name('admin.cashin.reject');
            Route::post('/cashin-approvals/{id}/reviewed', [CashInApprovalController::class, 'markAsReviewed'])->name('admin.cashin.reviewed');

        });



        // ✅ Admin Referral Report
        Route::middleware(['auth', 'admin']) // or just 'auth' if no admin middleware
            ->prefix('admin')
            ->group(function () {
                Route::get('/referral-report', [ReferralReportController::class, 'index'])->name('referral.report');
            });


        //  ✅ Loan Management - New Implementation
        Route::middleware(['auth', 'can:admin-only'])->prefix('admin')->name('admin.')->group(function () {
            // Loan Management Routes
            Route::get('/loans/management', [LoanManagementController::class, 'index'])->name('loans.management');
            Route::get('/loans/reports', [LoanManagementController::class, 'reports'])->name('loans.reports');
            Route::get('/loans/{loan}', [LoanManagementController::class, 'show'])->name('loans.show');
            Route::post('/loans/{loan}/approve', [LoanManagementController::class, 'approve'])->name('loans.approve');
            Route::post('/loans/{loan}/reject', [LoanManagementController::class, 'reject'])->name('loans.reject');

            // Payment Management
            Route::post('/payment/{id}/verify', [LoanPaymentController::class, 'verifyPayment'])->name('payment.verify');
            Route::post('/payment/store', [LoanPaymentController::class, 'storeManual'])->name('payment.store');
            Route::post('/payment/{id}/mark-paid', [LoanPaymentController::class, 'markAsPaid'])->name('payment.markAsPaid');
        });

        // ✅ Membership Code Management

        Route::prefix('admin')->middleware(['auth', 'can:admin-only'])->group(function () {
            Route::get('/codes', [MembershipCodeController::class, 'index'])->name('admin.codes.index');
            Route::post('/codes', [MembershipCodeController::class, 'store'])->name('admin.codes.store');
        });

        // ✅ Admin Membership Code Management
        Route::prefix('admin')->middleware(['auth', 'can:admin-only'])->group(function () {
            Route::get('/membership-codes', [\App\Http\Controllers\Admin\MembershipCodeController::class, 'index'])->name('admin.membership-codes.index');
            Route::post('/membership-codes/generate', [\App\Http\Controllers\Admin\MembershipCodeController::class, 'generate'])->name('admin.membership-codes.generate');

            // Admin Membership Code Request Management
            Route::get('/membership-code-requests', [\App\Http\Controllers\Admin\MembershipCodeRequestController::class, 'index'])->name('admin.membership-code-requests.index');
            Route::put('/membership-code-requests/{membershipCodeRequest}', [\App\Http\Controllers\Admin\MembershipCodeRequestController::class, 'update'])->name('admin.membership-code-requests.update');
            Route::get('/membership-code-requests/codes', [\App\Http\Controllers\Admin\MembershipCodeRequestController::class, 'getAvailableCodes'])->name('admin.membership-code-requests.codes');
            Route::put('/membership-code-requests/{membershipCodeRequest}/assign-codes', [\App\Http\Controllers\Admin\MembershipCodeRequestController::class, 'assignCodes'])->name('admin.membership-code-requests.assign-codes');
        });

        // ✅ Admin Reward Management
        Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('rewards', [RewardController::class, 'index'])->name('rewards.index');
            Route::get('rewards/create', [RewardController::class, 'create'])->name('rewards.create');
            Route::post('rewards', [RewardController::class, 'store'])->name('rewards.store');
            Route::post('rewards/{id}/pick', [RewardController::class, 'pickWinner'])->name('rewards.pick');
            Route::get('rewards/winners', [RewardController::class, 'winners'])->name('rewards.winners');
        });


        Route::middleware(['auth'])->prefix('member')->name('member.')->group(function () {
            Route::get('/rewards', [RewardHistoryController::class, 'index'])->name('rewards.index');
        });

        Route::patch('/admin/rewards/status/{id}', [RewardController::class, 'updateStatus'])->name('admin.rewards.status.update');

        // Ticket Management

        // Member routes
        Route::middleware(['auth'])->prefix('member')->name('member.')->group(function () {
            Route::get('/tickets', [MemberTicketController::class, 'index'])->name('tickets.index');
            Route::post('/tickets', [MemberTicketController::class, 'store'])->name('tickets.store');
        });

        // Admin routes
        Route::middleware(['auth', 'can:admin-only'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
            Route::put('/tickets/{ticket}', [AdminTicketController::class, 'update'])->name('tickets.update');
        });

        // Admin Ticket Replies
        Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
            Route::get('/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
            Route::get('/tickets/{id}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
            Route::post('/tickets/{id}/reply', [\App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('tickets.reply');
            Route::put('/tickets/{id}', [\App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('tickets.update');
        });


        Route::prefix('member')->name('member.')->middleware(['auth'])->group(function () {
            Route::get('/tickets', [\App\Http\Controllers\Member\TicketController::class, 'index'])->name('tickets.index');
            Route::get('/tickets/{id}', [\App\Http\Controllers\Member\TicketController::class, 'show'])->name('tickets.show');
            Route::post('/tickets/{id}/reply', [\App\Http\Controllers\Member\TicketController::class, 'reply'])->name('tickets.reply');
        });

        //loan payments admin - moved to LoanManagementController


        //load payments member
        Route::post('/member/payments/{id}/pay', [LoanPaymentController::class, 'payNow'])->name('member.payment.payNow');
        Route::middleware(['auth', 'can:member-only'])->group(function () {
            Route::get('/member/loans/{loan}/payments', [LoanPaymentController::class, 'viewPayments'])
                ->name('member.loans.payments');
        });
        Route::middleware(['auth', 'can:member-only'])->group(function () {
            Route::get('loans/payment-history', [LoanController::class, 'paymentHistory'])->name('member.payment-history');
        });

// ✅ Admin-only Routes
});

// ✅ 3-Tier Registration System (Public)
Route::get('/register', [GuestRegistrationController::class, 'create'])->name('guest.register');
Route::post('/register', [GuestRegistrationController::class, 'store'])->name('guest.register.store');

// ✅ Admin Registration (Public)
Route::get('/register/admin', [GuestRegistrationController::class, 'createAdmin'])->name('admin.register');
Route::post('/register/admin', [GuestRegistrationController::class, 'storeAdmin'])->name('admin.register.store');

// ✅ Member Referral Registration (Public)
Route::get('/register/ref/{sponsor_id}', [GuestRegistrationController::class, 'createWithReferral'])->name('member.register.referral');
Route::post('/register/ref/{sponsor_id}', [GuestRegistrationController::class, 'storeWithReferral'])->name('member.register.referral.store');

// ✅ Legacy Routes (for backward compatibility)
Route::get('/join', [GuestRegistrationController::class, 'create'])->name('join');
Route::post('/join', [GuestRegistrationController::class, 'store']);

// ✅ Legacy Referral Route (for backward compatibility)
Route::get('/join/{sponsor_id}', [GuestRegistrationController::class, 'createWithReferral'])->name('join.referral');
Route::post('/join/{sponsor_id}', [GuestRegistrationController::class, 'storeWithReferral'])->name('join.referral.store');

// ✅ Welcome Page Guest Registration (Public)
Route::get('/welcome.php', [GuestRegistrationController::class, 'welcome'])->name('welcome.register');
Route::post('/welcome.php', [GuestRegistrationController::class, 'store'])->name('welcome.register.store');

// ✅ Offline Page Route
Route::get('/offline', function () {
    return view('offline');
})->name('offline');



// Referral Bonuses Management

// ✅ Member Referral Summary
Route::middleware(['auth'])->group(function () {
    Route::get('/member/referral-summary', [\App\Http\Controllers\Member\ReferralController::class, 'summary'])->name('member.referral.summary');
    Route::get('/member/referral-level/{level}', [\App\Http\Controllers\Member\ReferralController::class, 'levelDetails'])->name('member.referral.level');
});

// ✅ Member or public view (optional)
Route::get('/referral-bonuses', [ReferralBonusController::class, 'index'])->name('referral.bonuses');

// ✅ Admin-only view
Route::get('/admin/referral-bonuses', [ReferralBonusController::class, 'index'])
    ->middleware(['auth', 'can:admin-only'])
    ->name('admin.referral-bonuses');

// ✅ Export to CSV or Excel
Route::get('/admin/referral-bonuses/export', [ReferralBonusController::class, 'export'])
    ->middleware(['auth', 'can:admin-only'])
    ->name('admin.referral-bonuses.export');


// 🛒 Shop & Cart - Member Only
Route::middleware(['auth', 'can:member-only'])->group(function () {
    // Shop browsing (MemberProductController)
    Route::get('/shop', [MemberProductController::class, 'index'])->name('shop.index');
    Route::get('/shop/{product}', [MemberProductController::class, 'show'])->name('shop.show');

    // Cart & Order (ShopController)
    Route::post('/shop/order/{product}', [ShopController::class, 'order'])->name('shop.order');
    Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
    Route::delete('/shop/cart/{id}', [ShopController::class, 'remove'])->name('shop.cart.remove');
});

// 🏷️ Promo Code Validation (AJAX)
Route::post('/shop/validate-promo', [ShopController::class, 'validatePromoCode'])
    ->name('shop.validate-promo')
    ->middleware(['auth', 'can:member-only']);

// 🛍️ Member-side product listing (if separate from shop)
Route::get('/products', [MemberProductController::class, 'index'])->name('products.index');

// 🛠️ Admin: Product Management
Route::prefix('admin')->middleware(['auth', 'can:admin-only'])->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
    Route::post('products/preview-cashback', [ProductController::class, 'previewCashback'])
        ->name('products.preview-cashback');
});

// 🛠️ Staff: Product Management (Staff can only manage their own products)
Route::prefix('staff')->middleware(['auth', 'staff'])->name('staff.')->group(function () {
    Route::resource('products', \App\Http\Controllers\Staff\ProductController::class);
    Route::post('products/preview-cashback', [\App\Http\Controllers\Staff\ProductController::class, 'previewCashback'])
        ->name('products.preview-cashback');
    Route::patch('products/{product}/toggle-status', [\App\Http\Controllers\Staff\ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');
});

// 🛒 Member Order Routes
Route::middleware(['auth'])->prefix('member')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
});



// 🛠️ Admin Settings Management
Route::prefix('admin/settings')->name('admin.settings.')->middleware('auth')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::put('/', [SettingsController::class, 'update'])->name('update');


});



// 🛠️ Admin Order Report & Detail

Route::prefix('admin')->middleware(['auth', 'can:admin-only'])->name('admin.')->group(function () {
    // 🧾 Order Report & Detail
    Route::get('orders/report', [OrderReportController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderReportController::class, 'show'])->name('orders.show');

    // 📄 Invoice
    Route::get('orders/invoice/{order}', [OrderReportController::class, 'invoice'])->name('orders.invoice');

    Route::post('/orders/{id}/update-status', [OrderReportController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/order-items/{id}/update-status', [OrderReportController::class, 'updateItemStatus'])->name('orders.updateItemStatus');
});


// 🛒 Member Order Routes
Route::middleware(['auth', 'can:member-only'])->group(function () {
    Route::get('/my-orders', [OrderController::class, 'index'])->name('member.orders');
    Route::get('/my-orders/{id}', [OrderController::class, 'show'])->name('member.orders.show');
    Route::post('/my-orders/{id}/cancel', [OrderController::class, 'cancel'])->name('member.orders.cancel');
    Route::get('/my-orders/{id}/track', [OrderController::class, 'track'])->name('member.orders.track');
    Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
    // Removed conflicting root route that was overriding the welcome page
    // Route::get('/', [OrderController::class, 'index'])->name('');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('member.orders.show.alt');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('member.orders.cancel.alt');
    Route::get('/orders/{id}/track', [OrderController::class, 'track'])->name('member.orders.track.alt');


});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('product-categories', \App\Http\Controllers\Admin\CategoryController::class)
         ->names('admin.categories');
});

// ✅ Admin Referral Configuration Management
Route::prefix('admin')->middleware(['auth', 'can:admin-only'])->name('admin.')->group(function () {
    Route::resource('referral-configurations', \App\Http\Controllers\Admin\ReferralConfigurationController::class);
    Route::post('referral-configurations/{referralConfiguration}/activate', [\App\Http\Controllers\Admin\ReferralConfigurationController::class, 'activate'])
        ->name('referral-configurations.activate');
    Route::post('referral-configurations/preview', [\App\Http\Controllers\Admin\ReferralConfigurationController::class, 'preview'])
        ->name('referral-configurations.preview');
});
