<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index()
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderBy('price')
            ->get()
            ->map(fn ($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => $plan->price,
                'billing_period' => $plan->billing_period,
                'features' => $plan->features ?? [],
                'limits' => $plan->limits ?? [],
            ]);

        $recommendedPlanId = $plans->first()['id'] ?? null;

        return Inertia::render('Public/Pricing/Index', [
            'plans' => $plans,
            'recommendedPlanId' => $recommendedPlanId,
        ]);
    }
}
