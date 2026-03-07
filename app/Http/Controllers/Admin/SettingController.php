<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $defaults = [
            'app.name' => config('app.name'),
            'app.email' => config('mail.from.address'),
            'app.phone' => '',
            'app.description' => '',
            'regional.timezone' => config('app.timezone'),
            'regional.locale' => config('app.locale'),
            'system.allow_registration' => false,
            'system.require_user_approval' => false,
            'system.default_pagination' => 10,
        ];

        $stored = Setting::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key');

        $settings = [
            'app' => [
                'name' => $stored->get('app.name', $defaults['app.name']),
                'email' => $stored->get('app.email', $defaults['app.email']),
                'phone' => $stored->get('app.phone', $defaults['app.phone']),
                'description' => $stored->get('app.description', $defaults['app.description']),
            ],
            'regional' => [
                'timezone' => $stored->get('regional.timezone', $defaults['regional.timezone']),
                'locale' => $stored->get('regional.locale', $defaults['regional.locale']),
            ],
            'system' => [
                'allow_registration' => $this->toBool($stored->get('system.allow_registration', $defaults['system.allow_registration'])),
                'require_user_approval' => $this->toBool($stored->get('system.require_user_approval', $defaults['system.require_user_approval'])),
                'default_pagination' => (int) $stored->get('system.default_pagination', $defaults['system.default_pagination']),
            ],
        ];

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, ActivityService $activity)
    {
        $data = $request->validate([
            'app.name' => ['required', 'string', 'max:150'],
            'app.email' => ['nullable', 'email', 'max:150'],
            'app.phone' => ['nullable', 'string', 'max:50'],
            'app.description' => ['nullable', 'string', 'max:500'],
            'regional.timezone' => ['required', 'string', 'max:100'],
            'regional.locale' => ['required', 'string', 'max:10'],
            'system.allow_registration' => ['boolean'],
            'system.require_user_approval' => ['boolean'],
            'system.default_pagination' => ['required', 'integer', 'min:5', 'max:100'],
        ]);

        $this->saveValue('app.name', $data['app']['name']);
        $this->saveValue('app.email', $data['app']['email'] ?? '');
        $this->saveValue('app.phone', $data['app']['phone'] ?? '');
        $this->saveValue('app.description', $data['app']['description'] ?? '');
        $this->saveValue('regional.timezone', $data['regional']['timezone']);
        $this->saveValue('regional.locale', $data['regional']['locale']);
        $this->saveValue('system.allow_registration', $this->boolToString($data['system']['allow_registration'] ?? false));
        $this->saveValue('system.require_user_approval', $this->boolToString($data['system']['require_user_approval'] ?? false));
        $this->saveValue('system.default_pagination', (string) $data['system']['default_pagination']);

        $activity->log('settings_updated', [
            'actor' => $request->user(),
            'description' => 'Settings actualizados',
            'request' => $request,
        ]);

        return back()->with('success', 'Configuracion actualizada correctamente.');
    }

    private function saveValue(string $key, string $value): void
    {
        Setting::updateOrCreate([
            'key' => $key,
        ], [
            'value' => $value,
        ]);
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if ($value === null) {
            return false;
        }

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }

    private function boolToString(bool $value): string
    {
        return $value ? '1' : '0';
    }
}
