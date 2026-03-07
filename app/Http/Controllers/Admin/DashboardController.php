<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $usersTotal = User::query()->count();
        $usersActive = User::query()->where('is_active', true)->count();
        $usersInactive = User::query()->where('is_active', false)->count();
        $usersVerified = User::query()->whereNotNull('email_verified_at')->count();
        $usersUnverified = User::query()->whereNull('email_verified_at')->count();
        $usersRecent = User::query()->where('created_at', '>=', now()->subDays(7))->count();

        $subscriptionsTotal = Subscription::query()->count();
        $subscriptionsActive = Subscription::query()->where('status', 'active')->count();
        $subscriptionsTrial = Subscription::query()->where('status', 'trial')->count();
        $subscriptionsCanceled = Subscription::query()->where('status', 'canceled')->count();
        $subscriptionsExpired = Subscription::query()
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->count();

        $plansActive = Plan::query()->where('is_active', true)->count();
        $topPlan = Subscription::query()
            ->select('plan_id', DB::raw('count(*) as total'))
            ->whereNotNull('plan_id')
            ->groupBy('plan_id')
            ->orderByDesc('total')
            ->first();
        $topPlanName = null;
        if ($topPlan) {
            $topPlanName = Plan::query()->where('id', $topPlan->plan_id)->value('name');
        }

        $paymentsTotal = Payment::query()->count();
        $paymentsPaid = Payment::query()->where('status', 'paid')->count();
        $paymentsFailed = Payment::query()->where('status', 'failed')->count();
        $paymentsAmountTotal = Payment::query()->where('status', 'paid')->sum('amount');
        $paymentsAmountMonth = Payment::query()
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $ticketsOpen = SupportTicket::query()->where('status', 'open')->count();
        $ticketsPending = SupportTicket::query()->where('status', 'pending')->count();
        $ticketsAnswered = SupportTicket::query()->where('status', 'answered')->count();
        $ticketsClosed = SupportTicket::query()->where('status', 'closed')->count();

        $recentTickets = SupportTicket::query()
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn ($ticket) => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'created_at' => $ticket->created_at?->toDateTimeString(),
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                    'email' => $ticket->user->email,
                ] : null,
            ]);

        $recentActivity = ActivityLog::query()
            ->with(['actor:id,name,email', 'user:id,name,email'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'event' => $log->event,
                'description' => $log->description,
                'created_at' => $log->created_at?->toDateTimeString(),
                'actor' => $log->actor ? [
                    'id' => $log->actor->id,
                    'name' => $log->actor->name,
                    'email' => $log->actor->email,
                ] : null,
            ]);

        return Inertia::render('Dashboard', [
            'metrics' => [
                'users' => [
                    'total' => $usersTotal,
                    'active' => $usersActive,
                    'inactive' => $usersInactive,
                    'verified' => $usersVerified,
                    'unverified' => $usersUnverified,
                    'recent' => $usersRecent,
                ],
                'subscriptions' => [
                    'total' => $subscriptionsTotal,
                    'active' => $subscriptionsActive,
                    'trial' => $subscriptionsTrial,
                    'canceled' => $subscriptionsCanceled,
                    'expired' => $subscriptionsExpired,
                ],
                'plans' => [
                    'active' => $plansActive,
                    'top_plan' => $topPlanName,
                ],
                'payments' => [
                    'total' => $paymentsTotal,
                    'paid' => $paymentsPaid,
                    'failed' => $paymentsFailed,
                    'amount_total' => $paymentsAmountTotal,
                    'amount_month' => $paymentsAmountMonth,
                ],
                'tickets' => [
                    'open' => $ticketsOpen,
                    'pending' => $ticketsPending,
                    'answered' => $ticketsAnswered,
                    'closed' => $ticketsClosed,
                ],
            ],
            'recentTickets' => $recentTickets,
            'recentActivity' => $recentActivity,
        ]);
    }
}
