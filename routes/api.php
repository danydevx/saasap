<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api_key', 'throttle:api-key'])->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'id' => $user?->id,
            'name' => $user?->name,
            'email' => $user?->email,
        ]);
    })->name('api.me');
});
