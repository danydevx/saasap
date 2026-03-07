<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\UserNotificationService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        return Inertia::render('Admin/Profile/Edit', [
            'profile' => [
                'name' => $profile?->name ?? '',
                'phone' => $profile?->phone ?? '',
                'facebook' => $profile?->facebook ?? '',
                'instagram' => $profile?->instagram ?? '',
                'x' => $profile?->x ?? '',
            ],
        ]);
    }

    public function editMember(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        return Inertia::render('Member/Profile/Edit', [
            'profile' => [
                'name' => $profile?->name ?? '',
                'phone' => $profile?->phone ?? '',
                'facebook' => $profile?->facebook ?? '',
                'instagram' => $profile?->instagram ?? '',
                'x' => $profile?->x ?? '',
            ],
        ]);
    }

    public function update(Request $request, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'x' => ['nullable', 'url', 'max:255'],
        ]);

        $user->profile()->updateOrCreate([
            'user_id' => $user->id,
        ], [
            'name' => $data['name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'facebook' => $data['facebook'] ?? null,
            'instagram' => $data['instagram'] ?? null,
            'x' => $data['x'] ?? null,
        ]);

        $activity->log('user_updated', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user->profile,
            'description' => 'Perfil actualizado',
            'request' => $request,
        ]);

        $webhooks->dispatchUserEvent($user, 'user.updated', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $notifications->create(
            $user,
            'profile',
            'Perfil actualizado',
            'Tus datos de perfil fueron actualizados.',
            '/profile'
        );

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
