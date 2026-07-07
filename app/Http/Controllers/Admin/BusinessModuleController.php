<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanBusinessModule;
use Modules\Businesses\Models\Business;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BusinessModuleController extends Controller
{
    public function index(Request $request)
    {
        $businesses = Business::with(['user', 'modules.moduleDefinition'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/BusinessModules/Index', [
            'businesses' => $businesses,
        ]);
    }

    public function edit(Request $request, Business $business)
    {
        $business->load('modules.moduleDefinition');
        $business->load('user.subscriptions.plan');

        $user = $business->user;
        $planModules = $this->getPlanModulesForUser($user);

        return Inertia::render('Admin/BusinessModules/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'user' => $business->user ? [
                    'id' => $business->user->id,
                    'name' => $business->user->name,
                    'email' => $business->user->email,
                    'plan_name' => $this->getUserPlanName($business->user),
                ] : null,
                'modules' => $business->modules->map(fn ($m) => [
                    'id' => $m->id,
                    'module_key' => $m->moduleDefinition?->key,
                    'module_name' => $m->moduleDefinition?->name,
                    'is_enabled' => $m->is_enabled,
                    'settings' => $m->settings,
                    'allowed_by_plan' => $planModules[$m->moduleDefinition?->key] ?? false,
                ]),
            ],
        ]);
    }

    public function update(Request $request, Business $business, ActivityService $activity)
    {
        $data = $request->validate([
            'modules' => ['required', 'array'],
            'modules.*.id' => ['required', 'exists:business_modules,id'],
            'modules.*.is_enabled' => ['required', 'boolean'],
        ]);

        foreach ($data['modules'] as $moduleData) {
            $business->modules()
                ->where('id', $moduleData['id'])
                ->update(['is_enabled' => $moduleData['is_enabled']]);
        }

        $activity->log('business_modules_updated', [
            'actor' => $request->user(),
            'subject' => $business,
            'description' => 'Modulos de negocio actualizados',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Modulos actualizados correctamente.');
    }

    private function getPlanModulesForUser($user): array
    {
        if (!$user) {
            return [];
        }

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('ends_at', '>', now())
                    ->orWhereNull('ends_at');
            })
            ->latest()
            ->first();

        if (!$subscription) {
            return [];
        }

        return PlanBusinessModule::where('plan_id', $subscription->plan_id)
            ->where('is_enabled', true)
            ->with('moduleDefinition')
            ->get()
            ->pluck('moduleDefinition.key')
            ->flip()
            ->toArray();
    }

    private function getUserPlanName($user): string
    {
        if (!$user) {
            return 'Sin plan';
        }

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where('ends_at', '>', now())
                    ->orWhereNull('ends_at');
            })
            ->latest()
            ->first();

        if (!$subscription || !$subscription->plan) {
            return 'Sin plan';
        }

        return $subscription->plan->name;
    }
}
