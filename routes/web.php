<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\ApiKeyController as AdminApiKeyController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Admin\HelpArticleController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PlanFeatureFlagController;
use App\Http\Controllers\Admin\QueueController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SecurityEventController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\SystemErrorController;
use App\Http\Controllers\Admin\SystemMonitorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\WebhookController as AdminWebhookController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\ApiKeyController as MemberApiKeyController;
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
use App\Http\Controllers\Member\PreferenceController as MemberPreferenceController;
use App\Http\Controllers\Member\SessionController as MemberSessionController;
use App\Http\Controllers\Member\SupportTicketController as MemberSupportTicketController;
use App\Http\Controllers\Member\WebhookController as MemberWebhookController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/health', HealthController::class)->name('health');

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
    ->middleware(['guest', 'throttle:login'])
    ->name('login.store');

Route::post('/register', [RegisterController::class, 'register'])
    ->middleware(['guest', 'throttle:register'])
    ->name('register.store');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])
    ->middleware('guest')
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware(['guest', 'throttle:password-email'])
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showVerifyCode'])
    ->middleware('guest')
    ->name('password.verify');
Route::post('/reset-password/{token}/verify-code', [PasswordResetController::class, 'verifyCode'])
    ->middleware(['guest', 'throttle:password-verify'])
    ->name('password.verify-code');
Route::get('/reset-password/{token}/new-password', [PasswordResetController::class, 'showResetPasswordForm'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/reset-password/{token}', [PasswordResetController::class, 'resetPassword'])
    ->middleware(['guest', 'throttle:password-reset'])
    ->name('password.update');

Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:email-verify'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:verification-resend'])
    ->name('verification.send');

Route::post('/logout', [LogoutController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin|superadmin'])
    ->name('dashboard');

Route::get('/member', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.dashboard');

Route::get('/member/account', [AccountController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.account.show');

Route::post('/member/billing/portal', [BillingController::class, 'portal'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'throttle:billing-portal'])
    ->name('member.billing.portal');

Route::post('/member/checkout/{plan}', [CheckoutController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'throttle:checkout-create'])
    ->name('member.checkout.create');
Route::post('/member/checkout/coupon/validate', [CheckoutController::class, 'validateCoupon'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'throttle:checkout-coupon'])
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

Route::get('/member/activity', [MemberActivityController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.activity.index');

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
    ->middleware(['auth', 'verified', 'active', 'role:member', 'throttle:ticket-create'])
    ->name('member.support.store');
Route::get('/member/support/{ticket}', [MemberSupportTicketController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.support.show');
Route::post('/member/support/{ticket}/reply', [MemberSupportTicketController::class, 'reply'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'throttle:ticket-reply'])
    ->name('member.support.reply');

Route::get('/member/api-keys', [MemberApiKeyController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:api-keys.manage'])
    ->name('member.api-keys.index');
Route::post('/member/api-keys', [MemberApiKeyController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:api-keys.manage', 'throttle:api-keys-create'])
    ->name('member.api-keys.store');
Route::put('/member/api-keys/{apiKey}', [MemberApiKeyController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:api-keys.manage'])
    ->name('member.api-keys.update');
Route::delete('/member/api-keys/{apiKey}', [MemberApiKeyController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:api-keys.manage'])
    ->name('member.api-keys.destroy');

Route::get('/member/webhooks', [MemberWebhookController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage'])
    ->name('member.webhooks.index');
Route::post('/member/webhooks', [MemberWebhookController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage'])
    ->name('member.webhooks.store');
Route::put('/member/webhooks/{webhook}', [MemberWebhookController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage'])
    ->name('member.webhooks.update');
Route::delete('/member/webhooks/{webhook}', [MemberWebhookController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage'])
    ->name('member.webhooks.destroy');
Route::post('/member/webhooks/{webhook}/test', [MemberWebhookController::class, 'test'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage'])
    ->name('member.webhooks.test');
Route::post('/member/webhooks/{webhook}/regenerate-secret', [MemberWebhookController::class, 'regenerateSecret'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage'])
    ->name('member.webhooks.regenerate-secret');
Route::get('/member/webhooks/{webhook}/deliveries', [MemberWebhookController::class, 'deliveries'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage'])
    ->name('member.webhooks.deliveries');
Route::post('/member/webhooks/deliveries/{delivery}/retry', [MemberWebhookController::class, 'retryDelivery'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage'])
    ->name('member.webhooks.deliveries.retry');

Route::get('/member/help', [MemberHelpArticleController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.help.index');
Route::get('/member/help/{slug}', [MemberHelpArticleController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.help.show');

Route::get('/member/preferences', [MemberPreferenceController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.preferences.edit');
Route::put('/member/preferences', [MemberPreferenceController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.preferences.update');

Route::get('/member/sessions', [MemberSessionController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.sessions.index');
Route::delete('/member/sessions/others', [MemberSessionController::class, 'destroyOthers'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.sessions.destroy-others');
Route::delete('/member/sessions/{session}', [MemberSessionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.sessions.destroy');

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

    Route::get('/activity', [AdminActivityController::class, 'index'])
        ->middleware('permission_or_user:activity.view,1')
        ->name('admin.activity.index');

    Route::get('/exports', [ExportController::class, 'index'])
        ->middleware('permission_or_user:exports.view,1')
        ->name('admin.exports.index');
    Route::get('/exports/users', [ExportController::class, 'users'])
        ->middleware('permission_or_user:exports.download,1')
        ->name('admin.exports.users');
    Route::get('/exports/subscriptions', [ExportController::class, 'subscriptions'])
        ->middleware('permission_or_user:exports.download,1')
        ->name('admin.exports.subscriptions');
    Route::get('/exports/payments', [ExportController::class, 'payments'])
        ->middleware('permission_or_user:exports.download,1')
        ->name('admin.exports.payments');
    Route::get('/exports/tickets', [ExportController::class, 'tickets'])
        ->middleware('permission_or_user:exports.download,1')
        ->name('admin.exports.tickets');
    Route::get('/exports/activities', [ExportController::class, 'activities'])
        ->middleware('permission_or_user:exports.download,1')
        ->name('admin.exports.activities');

    Route::get('/system-errors', [SystemErrorController::class, 'index'])
        ->middleware('permission_or_user:system-errors.view,1')
        ->name('admin.system-errors.index');
    Route::get('/system-errors/{error}', [SystemErrorController::class, 'show'])
        ->middleware('permission_or_user:system-errors.view,1')
        ->name('admin.system-errors.show');
    Route::put('/system-errors/{error}/resolve', [SystemErrorController::class, 'resolve'])
        ->middleware('permission_or_user:system-errors.update,1')
        ->name('admin.system-errors.resolve');

    Route::get('/api-keys', [AdminApiKeyController::class, 'index'])
        ->middleware('permission_or_user:api-keys.view,1')
        ->name('admin.api-keys.index');
    Route::get('/api-keys/{apiKey}', [AdminApiKeyController::class, 'show'])
        ->middleware('permission_or_user:api-keys.view,1')
        ->name('admin.api-keys.show');
    Route::put('/api-keys/{apiKey}/revoke', [AdminApiKeyController::class, 'revoke'])
        ->middleware('permission_or_user:api-keys.revoke,1')
        ->name('admin.api-keys.revoke');

    Route::get('/webhooks', [AdminWebhookController::class, 'index'])
        ->middleware('permission_or_user:webhooks.view,1')
        ->name('admin.webhooks.index');
    Route::get('/webhooks/{webhook}', [AdminWebhookController::class, 'show'])
        ->middleware('permission_or_user:webhooks.view,1')
        ->name('admin.webhooks.show');
    Route::get('/webhooks/{webhook}/deliveries', [AdminWebhookController::class, 'deliveries'])
        ->middleware('permission_or_user:webhooks.view,1')
        ->name('admin.webhooks.deliveries');

    Route::get('/queues', [QueueController::class, 'index'])
        ->middleware('permission_or_user:queues.view,1')
        ->name('admin.queues.index');
    Route::get('/failed-jobs', [QueueController::class, 'failed'])
        ->middleware('permission_or_user:queues.view,1')
        ->name('admin.queues.failed');
    Route::post('/failed-jobs/{id}/retry', [QueueController::class, 'retry'])
        ->middleware('permission_or_user:queues.retry,1')
        ->name('admin.queues.retry');
    Route::delete('/failed-jobs/{id}', [QueueController::class, 'destroy'])
        ->middleware('permission_or_user:queues.flush-failed,1')
        ->name('admin.queues.destroy');
    Route::post('/failed-jobs/retry-all', [QueueController::class, 'retryAll'])
        ->middleware('permission_or_user:queues.retry,1')
        ->name('admin.queues.retry-all');
    Route::delete('/failed-jobs/flush', [QueueController::class, 'flush'])
        ->middleware('permission_or_user:queues.flush-failed,1')
        ->name('admin.queues.flush');

    Route::get('/system-monitor', [SystemMonitorController::class, 'index'])
        ->middleware('permission_or_user:reports.view,1')
        ->name('admin.system-monitor.index');

    Route::get('/security-events', [SecurityEventController::class, 'index'])
        ->middleware('permission_or_user:security-events.view,1')
        ->name('admin.security-events.index');
    Route::get('/security-events/{event}', [SecurityEventController::class, 'show'])
        ->middleware('permission_or_user:security-events.view,1')
        ->name('admin.security-events.show');

    Route::get('/feature-flags', [FeatureFlagController::class, 'index'])
        ->middleware('permission_or_user:feature-flags.view,1')
        ->name('admin.feature-flags.index');
    Route::get('/feature-flags/create', [FeatureFlagController::class, 'create'])
        ->middleware('permission_or_user:feature-flags.create,1')
        ->name('admin.feature-flags.create');
    Route::post('/feature-flags', [FeatureFlagController::class, 'store'])
        ->middleware('permission_or_user:feature-flags.create,1')
        ->name('admin.feature-flags.store');
    Route::get('/feature-flags/{flag}/edit', [FeatureFlagController::class, 'edit'])
        ->middleware('permission_or_user:feature-flags.update,1')
        ->name('admin.feature-flags.edit');
    Route::put('/feature-flags/{flag}', [FeatureFlagController::class, 'update'])
        ->middleware('permission_or_user:feature-flags.update,1')
        ->name('admin.feature-flags.update');

    Route::get('/plans/{plan}/features', [PlanFeatureFlagController::class, 'edit'])
        ->middleware('permission_or_user:feature-flags.update,1')
        ->name('admin.plans.features.edit');
    Route::put('/plans/{plan}/features', [PlanFeatureFlagController::class, 'update'])
        ->middleware('permission_or_user:feature-flags.update,1')
        ->name('admin.plans.features.update');

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

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission_or_user:reports.view,1')
        ->name('admin.reports.index');

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
