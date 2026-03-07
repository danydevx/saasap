<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\HelpArticleController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\BillingController;
use App\Http\Controllers\Member\CheckoutController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\HelpArticleController as MemberHelpArticleController;
use App\Http\Controllers\Member\InvoiceController as MemberInvoiceController;
use App\Http\Controllers\Member\NotificationController;
use App\Http\Controllers\Member\OnboardingController;
use App\Http\Controllers\Member\PasswordController;
use App\Http\Controllers\Member\PaymentController as MemberPaymentController;
use App\Http\Controllers\Member\PlanSelectionController;
use App\Http\Controllers\Member\SupportTicketController as MemberSupportTicketController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->middleware('guest')->name('login');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::post('/pricing/select/{plan}', [PricingController::class, 'select'])->name('pricing.select');
Route::get('/plans', function () {
    return redirect('/pricing');
})->name('plans');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::get('/register', [RegisterController::class, 'showRegister'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [LoginController::class, 'store'])
    ->middleware('guest')
    ->name('login.store');

Route::post('/register', [RegisterController::class, 'register'])
    ->middleware('guest')
    ->name('register.store');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])
    ->middleware('guest')
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showVerifyCode'])
    ->middleware('guest')
    ->name('password.verify');
Route::post('/reset-password/{token}/verify-code', [PasswordResetController::class, 'verifyCode'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('password.verify-code');
Route::get('/reset-password/{token}/new-password', [PasswordResetController::class, 'showResetPasswordForm'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/reset-password/{token}', [PasswordResetController::class, 'resetPassword'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('password.update');

Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [LogoutController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'role:admin|superadmin'])->name('dashboard');

Route::get('/member', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.dashboard');

Route::get('/member/account', [AccountController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.account.show');

Route::post('/member/billing/portal', [BillingController::class, 'portal'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.billing.portal');

Route::post('/member/checkout/{plan}', [CheckoutController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.checkout.create');
Route::post('/member/checkout/coupon/validate', [CheckoutController::class, 'validateCoupon'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.checkout.coupon.validate');
Route::put('/member/checkout/coupon/clear', [CheckoutController::class, 'clearCoupon'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.checkout.coupon.clear');
Route::get('/member/checkout/success', [CheckoutController::class, 'success'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.checkout.success');
Route::get('/member/checkout/cancel', [CheckoutController::class, 'cancel'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.checkout.cancel');

Route::get('/member/plan-selection', [PlanSelectionController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.plan-selection.show');
Route::put('/member/plan-selection/clear', [PlanSelectionController::class, 'clear'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.plan-selection.clear');

Route::get('/member/profile', [UserProfileController::class, 'editMember'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.profile.edit');

Route::get('/member/password', [PasswordController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.password.edit');
Route::put('/member/password', [PasswordController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.password.update');

Route::put('/member/onboarding/complete', [OnboardingController::class, 'complete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.onboarding.complete');

Route::get('/member/notifications', [NotificationController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.notifications.index');
Route::get('/member/notifications/unread-count', [NotificationController::class, 'unreadCount'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.notifications.unread-count');
Route::put('/member/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.notifications.read');
Route::put('/member/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.notifications.read-all');

Route::get('/member/payments', [MemberPaymentController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.payments.index');

Route::get('/member/invoices', [MemberInvoiceController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.invoices.index');
Route::get('/member/invoices/{invoice}', [MemberInvoiceController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.invoices.show');
Route::get('/member/invoices/{invoice}/download', [MemberInvoiceController::class, 'download'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.invoices.download');

Route::get('/member/support', [MemberSupportTicketController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.support.index');
Route::get('/member/support/create', [MemberSupportTicketController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.support.create');
Route::post('/member/support', [MemberSupportTicketController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.support.store');
Route::get('/member/support/{ticket}', [MemberSupportTicketController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.support.show');
Route::post('/member/support/{ticket}/reply', [MemberSupportTicketController::class, 'reply'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.support.reply');

Route::get('/member/help', [MemberHelpArticleController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.help.index');
Route::get('/member/help/{slug}', [MemberHelpArticleController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.help.show');

Route::get('/profile', [UserProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('profile.edit');
Route::put('/profile', [UserProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

Route::get('/admin/profile', [UserProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('admin.profile.edit');
Route::put('/admin/profile', [UserProfileController::class, 'update'])
    ->middleware('auth')
    ->name('admin.profile.update');

Route::prefix('admin')->middleware(['auth', 'admin_or_user:1'])->group(function () {

    Route::get('/settings', [SettingController::class, 'index'])
        ->middleware('permission_or_user:settings.view,1')
        ->name('admin.settings.index');
    Route::put('/settings', [SettingController::class, 'update'])
        ->middleware('permission_or_user:settings.update,1')
        ->name('admin.settings.update');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission_or_user:users.view,1')
        ->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission_or_user:users.create,1')
        ->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission_or_user:users.create,1')
        ->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission_or_user:users.update,1')
        ->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission_or_user:users.update,1')
        ->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission_or_user:users.delete,1')
        ->name('admin.users.destroy');
    Route::put('/users/{user}/activate', [UserController::class, 'activate'])
        ->middleware('permission_or_user:users.activate,1')
        ->name('admin.users.activate');
    Route::put('/users/{user}/deactivate', [UserController::class, 'deactivate'])
        ->middleware('permission_or_user:users.deactivate,1')
        ->name('admin.users.deactivate');
    Route::post('/users/{user}/resend-verification', [UserController::class, 'resendVerification'])
        ->middleware('permission_or_user:users.resend_verification,1')
        ->name('admin.users.resend-verification');

    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    Route::get('/activity', [ActivityLogController::class, 'index'])
        ->middleware('permission_or_user:activity.view,1')
        ->name('admin.activity.index');

    Route::get('/plans', [PlanController::class, 'index'])
        ->middleware('permission_or_user:plans.view,1')
        ->name('admin.plans.index');
    Route::get('/plans/create', [PlanController::class, 'create'])
        ->middleware('permission_or_user:plans.create,1')
        ->name('admin.plans.create');
    Route::post('/plans', [PlanController::class, 'store'])
        ->middleware('permission_or_user:plans.create,1')
        ->name('admin.plans.store');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])
        ->middleware('permission_or_user:plans.update,1')
        ->name('admin.plans.edit');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])
        ->middleware('permission_or_user:plans.update,1')
        ->name('admin.plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])
        ->middleware('permission_or_user:plans.delete,1')
        ->name('admin.plans.destroy');

    Route::get('/payments', [PaymentController::class, 'index'])
        ->middleware('permission_or_user:payments.view,1')
        ->name('admin.payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])
        ->middleware('permission_or_user:payments.create,1')
        ->name('admin.payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])
        ->middleware('permission_or_user:payments.create,1')
        ->name('admin.payments.store');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])
        ->middleware('permission_or_user:payments.update,1')
        ->name('admin.payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])
        ->middleware('permission_or_user:payments.update,1')
        ->name('admin.payments.update');

    Route::get('/coupons', [CouponController::class, 'index'])
        ->middleware('permission_or_user:coupons.view,1')
        ->name('admin.coupons.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])
        ->middleware('permission_or_user:coupons.create,1')
        ->name('admin.coupons.create');
    Route::post('/coupons', [CouponController::class, 'store'])
        ->middleware('permission_or_user:coupons.create,1')
        ->name('admin.coupons.store');
    Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])
        ->middleware('permission_or_user:coupons.update,1')
        ->name('admin.coupons.edit');
    Route::put('/coupons/{coupon}', [CouponController::class, 'update'])
        ->middleware('permission_or_user:coupons.update,1')
        ->name('admin.coupons.update');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])
        ->middleware('permission_or_user:coupons.delete,1')
        ->name('admin.coupons.destroy');

    Route::get('/invoices', [AdminInvoiceController::class, 'index'])
        ->middleware('permission_or_user:invoices.view,1')
        ->name('admin.invoices.index');
    Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show'])
        ->middleware('permission_or_user:invoices.view,1')
        ->name('admin.invoices.show');
    Route::get('/invoices/{invoice}/download', [AdminInvoiceController::class, 'download'])
        ->middleware('permission_or_user:invoices.download,1')
        ->name('admin.invoices.download');

    Route::get('/support', [AdminSupportTicketController::class, 'index'])
        ->middleware('permission_or_user:support.view,1')
        ->name('admin.support.index');
    Route::get('/support/{ticket}', [AdminSupportTicketController::class, 'show'])
        ->middleware('permission_or_user:support.view,1')
        ->name('admin.support.show');
    Route::post('/support/{ticket}/reply', [AdminSupportTicketController::class, 'reply'])
        ->middleware('permission_or_user:support.reply,1')
        ->name('admin.support.reply');
    Route::put('/support/{ticket}', [AdminSupportTicketController::class, 'update'])
        ->middleware('permission_or_user:support.update,1')
        ->name('admin.support.update');

    Route::get('/help', [HelpArticleController::class, 'index'])
        ->middleware('permission_or_user:help.view,1')
        ->name('admin.help.index');
    Route::get('/help/create', [HelpArticleController::class, 'create'])
        ->middleware('permission_or_user:help.create,1')
        ->name('admin.help.create');
    Route::post('/help', [HelpArticleController::class, 'store'])
        ->middleware('permission_or_user:help.create,1')
        ->name('admin.help.store');
    Route::get('/help/{article}/edit', [HelpArticleController::class, 'edit'])
        ->middleware('permission_or_user:help.update,1')
        ->name('admin.help.edit');
    Route::put('/help/{article}', [HelpArticleController::class, 'update'])
        ->middleware('permission_or_user:help.update,1')
        ->name('admin.help.update');
    Route::delete('/help/{article}', [HelpArticleController::class, 'destroy'])
        ->middleware('permission_or_user:help.delete,1')
        ->name('admin.help.destroy');

});

Route::prefix('admin')->middleware(['auth', 'admin_or_user:1', 'permission_or_user:permissions.view,1'])->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::post('/permissions/reorder', [PermissionController::class, 'reorder'])
        ->middleware('permission_or_user:permissions.edit,1')
        ->name('admin.permissions.reorder');
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->middleware('permission_or_user:permissions.create,1')
        ->name('admin.permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])
        ->middleware('permission_or_user:permissions.create,1')
        ->name('admin.permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->middleware('permission_or_user:permissions.edit,1')
        ->name('admin.permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
        ->middleware('permission_or_user:permissions.edit,1')
        ->name('admin.permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('permission_or_user:permissions.delete,1')
        ->name('admin.permissions.destroy');
});
