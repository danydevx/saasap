<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function destroy(Request $request, ActivityService $activity)
    {
        $user = $request->user();

        if ($user) {
            $activity->log('user_logout', [
                'user' => $user,
                'actor' => $user,
                'subject' => $user,
                'description' => 'Cierre de sesion',
                'request' => $request,
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
