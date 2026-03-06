<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function store(Request $request, ActivityLogger $activity)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            return redirect('/email/verify')->with('error', 'Debes verificar tu email para continuar.');
        }

        if (! $user->is_active) {
            Auth::logout();

            return redirect('/login')->with('error', 'Tu cuenta esta inactiva.');
        }

        $activity->log('user.login', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => 'Inicio de sesion',
            'request' => $request,
        ]);

        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return redirect()->intended(route('dashboard'));
        }

        if ($user->hasRole('member')) {
            return redirect()->intended(route('member.dashboard'));
        }

        return redirect('/login')->with('error', 'Tu cuenta aun no esta habilitada.');
    }
}
