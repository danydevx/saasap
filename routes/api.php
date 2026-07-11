<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Locations\Http\Controllers\LocationDataController;

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

Route::get('v1/location-data/states', [LocationDataController::class, 'states']);
Route::get('v1/location-data/municipalities/{stateCode}', [LocationDataController::class, 'municipalities']);

require __DIR__ . '/api/v1/admin.php';
