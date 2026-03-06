<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanSelectionController extends Controller
{
    public function show(Request $request)
    {
        $planId = $request->session()->get('selected_plan_id');
        $plan = null;

        if ($planId) {
            $plan = Plan::query()
                ->where('is_active', true)
                ->find($planId);
        }

        $subscription = $request->user()->currentSubscription;

        return Inertia::render('Member/PlanSelection/Show', [
            'plan' => $plan ? [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => $plan->price,
                'billing_period' => $plan->billing_period,
                'features' => $plan->features ?? [],
                'limits' => $plan->limits ?? [],
                'stripe_price_id' => $plan->stripe_price_id,
            ] : null,
            'subscription' => $subscription ? [
                'plan_name' => $subscription->plan?->name,
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at?->toDateString(),
                'ends_at' => $subscription->ends_at?->toDateString(),
            ] : null,
        ]);
    }

    public function clear(Request $request)
    {
        $request->session()->forget('selected_plan_id');

        return redirect('/member')->with('success', 'Seleccion de plan guardada. Te avisaremos cuando el checkout este listo.');
    }
}
