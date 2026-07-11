<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_path' => $this->logo_path,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'plan' => $this->whenLoaded('subscriptions', function () {
                $activeSubscription = $this->subscriptions?->where('status', 'active')->first();
                return $activeSubscription?->plan ? [
                    'id' => $activeSubscription->plan->id,
                    'name' => $activeSubscription->plan->name,
                ] : null;
            }),
            'modules_enabled' => $this->whenLoaded('modules', function () {
                return $this->modules
                    ->where('is_enabled', true)
                    ->pluck('module_key')
                    ->toArray();
            }),
        ];
    }
}
