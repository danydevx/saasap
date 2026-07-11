<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_path' => $this->logo_path,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'plan' => $this->whenLoaded('subscriptions', function () {
                $activeSubscription = $this->subscriptions?->where('status', 'active')->first();
                return $activeSubscription?->plan ? [
                    'id' => $activeSubscription->plan->id,
                    'name' => $activeSubscription->plan->name,
                    'limits' => $activeSubscription->plan->limits,
                ] : null;
            }),

            'modules' => $this->whenLoaded('modules', function () {
                return $this->modules->mapWithKeys(function ($module) {
                    return [$module->module_key => [
                        'enabled' => (bool) $module->is_enabled,
                        'module_name' => $module->moduleDefinition?->name,
                    ]];
                });
            }),
        ];
    }
}
