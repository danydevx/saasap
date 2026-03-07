<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegister()
    {
        if (! $this->allowRegistration()) {
            return redirect('/login')->with('error', 'El registro esta deshabilitado.');
        }

        return Inertia::render('Auth/Register');
    }

    public function register(Request $request, ActivityService $activity)
    {
        if (! $this->allowRegistration()) {
            return back()->withErrors([
                'register' => 'El registro esta deshabilitado.',
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/', 'confirmed'],
            'company' => ['nullable', 'string', 'max:0'],
            'form_started_at' => ['required', 'integer', 'min:0'],
        ], [
            'password.regex' => 'Minimo 8 caracteres, con letras y numeros.',
        ]);

        if (! empty($data['company'])) {
            throw ValidationException::withMessages([
                'register' => 'No se pudo completar el registro.',
            ]);
        }

        if (now()->timestamp - (int) $data['form_started_at'] < 3) {
            throw ValidationException::withMessages([
                'register' => 'No se pudo completar el registro.',
            ]);
        }

        $user = User::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'is_active' => false,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'name' => $user->name,
        ]);

        $activity->log('user_registered', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => 'Usuario registrado',
            'request' => $request,
        ]);

        $role = Role::query()
            ->where('id', 10)
            ->orWhere('name', 'normal')
            ->first();

        if ($role) {
            $user->syncRoles([$role]);
        }

        $user->sendEmailVerificationNotification();

        Auth::login($user);

        return redirect('/email/verify')->with('success', 'Registro exitoso. Revisa tu correo para verificar tu cuenta.');
    }

    private function allowRegistration(): bool
    {
        $value = Setting::query()->where('key', 'system.allow_registration')->value('value');
        if ($value === null) {
            return true;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }

    private function requireUserApproval(): bool
    {
        $value = Setting::query()->where('key', 'system.require_user_approval')->value('value');
        if ($value === null) {
            return false;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }
}
