<?php

namespace App\Policies;

use App\Models\User;
use Modules\Gallery\Models\BusinessGalleryImage;
use Modules\Businesses\Models\Business;

class BusinessGalleryImagePolicy
{
    public function viewAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if (!$business->is_published || !$business->is_active) {
            return $user->id === $business->user_id;
        }

        return true;
    }

    public function create(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, BusinessGalleryImage $image): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $image->business->user_id;
    }

    public function delete(User $user, BusinessGalleryImage $image): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $image->business->user_id;
    }
}
