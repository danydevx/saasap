<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $subscription = $request->user()->currentSubscription;

        $limits = $subscription?->plan?->limits ?? [];

        $activities = Activity::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(5)
            ->get(['type', 'description', 'created_at']);

        return Inertia::render('Member/Dashboard', [
            'onboardingCompletedAt' => $request->user()->onboarding_completed_at?->toDateTimeString(),
            'subscription' => $subscription ? [
                'plan_name' => $subscription->plan?->name,
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at?->toDateString(),
                'ends_at' => $subscription->ends_at?->toDateString(),
            ] : null,
            'limits' => $limits,
            'activities' => $activities->map(fn ($log) => [
                'type' => $log->type,
                'description' => $log->description,
                'created_at' => $log->created_at?->toDateTimeString(),
            ]),
        ]);
    }
}
