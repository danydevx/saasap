<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\UserNotificationService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PasswordController extends Controller
{
    public function edit()
    {
        return Inertia::render('Member/Password/Edit');
    }

    public function update(Request $request, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/', 'confirmed'],
        ], [
            'password.regex' => 'Minimo 8 caracteres, con letras y numeros.',
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'La clave actual no es valida.',
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        $activity->log('user_password_changed', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => 'Cambio de password autenticado',
            'request' => $request,
        ]);

        $webhooks->dispatchUserEvent($user, 'user.password_changed', [
            'user_id' => $user->id,
        ]);

        $notifications->create(
            $user,
            'security',
            'Password actualizada',
            'Tu password fue actualizada correctamente.',
            '/member/password'
        );

        return back()->with('success', 'Password actualizada correctamente.');
    }
}
