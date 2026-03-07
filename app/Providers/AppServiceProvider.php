<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email', ''));
            $key = $email !== '' ? $email.'|'.$request->ip() : $request->ip();

            return Limit::perMinute(5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'email');
            });
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip())->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'email');
            });
        });

        RateLimiter::for('password-email', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email', ''));
            $key = $email !== '' ? $email.'|'.$request->ip() : $request->ip();

            return Limit::perMinutes(15, 3)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'email');
            });
        });

        RateLimiter::for('password-verify', function (Request $request) {
            $token = (string) $request->route('token');
            $key = ($token !== '' ? $token.'|' : '').$request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'code');
            });
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $token = (string) $request->route('token');
            $key = ($token !== '' ? $token.'|' : '').$request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', 'password');
            });
        });

        RateLimiter::for('verification-resend', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinutes(15, 3)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', null, true);
            });
        });

        RateLimiter::for('email-verify', function (Request $request) {
            $userId = (string) $request->route('id');
            $key = ($userId !== '' ? $userId.'|' : '').$request->ip();

            return Limit::perMinutes(10, 6)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiados intentos. Intente de nuevo en unos minutos.', null, true);
            });
        });

        RateLimiter::for('ticket-create', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perHour(5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Has excedido el numero de solicitudes permitidas.', 'message');
            });
        });

        RateLimiter::for('ticket-reply', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perHour(10)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Has excedido el numero de solicitudes permitidas.', 'message');
            });
        });

        RateLimiter::for('billing-portal', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiadas solicitudes. Intente de nuevo en unos minutos.', null, true);
            });
        });

        RateLimiter::for('checkout-create', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Demasiadas solicitudes. Intente de nuevo en unos minutos.', null, true);
            });
        });

        RateLimiter::for('checkout-coupon', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinute(10)->by($key)->response(function () use ($request) {
                return $this->throttleResponse($request, 'Has excedido el numero de solicitudes permitidas.', null, true);
            });
        });

        RateLimiter::for('api-key', function (Request $request) {
            $keyId = $request->attributes->get('api_key_id');
            $identifier = $keyId ? 'key|'.$keyId : 'ip|'.$request->ip();

            return Limit::perMinute(60)->by($identifier)->response(function () {
                return response()->json([
                    'message' => 'Demasiadas solicitudes. Intente de nuevo en unos minutos.',
                ], 429);
            });
        });

        RateLimiter::for('api-keys-create', function (Request $request) {
            $userId = $request->user()?->id;
            $key = $userId ? 'user|'.$userId : $request->ip();

            return Limit::perMinutes(10, 5)->by($key)->response(function () use ($request) {
                return $this->throttleResponse(
                    $request,
                    'Has excedido el numero de solicitudes permitidas.',
                    'name'
                );
            });
        });
    }

    private function throttleResponse(Request $request, string $message, ?string $field = null, bool $useFlash = false)
    {
        if ($useFlash) {
            return back()->with('error', $message)->setStatusCode(429);
        }

        $payload = $field ? [$field => $message] : ['rate_limit' => $message];

        return back()
            ->withErrors($payload)
            ->withInput($request->except(['password', 'password_confirmation']))
            ->setStatusCode(429);
    }
}
