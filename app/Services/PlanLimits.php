<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;

class PlanLimits
{
    public function currentPlan(User $user): ?Plan
    {
        return $user->currentSubscription?->plan;
    }

    public function limits(User $user): array
    {
        return $this->currentPlan($user)?->limits ?? [];
    }

    public function get(User $user, string $key, $default = null)
    {
        return $this->limits($user)[$key] ?? $default;
    }

    public function allowed(User $user, string $key): bool
    {
        return (bool) $this->get($user, $key, false);
    }

    public function max(User $user, string $key): ?int
    {
        $value = $this->get($user, $key);

        return $value === null ? null : (int) $value;
    }

    public function exceeded(User $user, string $key, int $current): bool
    {
        $max = $this->max($user, $key);

        if ($max === null) {
            return false;
        }

        return $current >= $max;
    }
}
