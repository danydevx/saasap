<?php

use Illuminate\Support\Facades\Route;
use Modules\Minisite\Http\Controllers\Public\MinisiteController;

Route::middleware(['web'])
    ->prefix('m')
    ->name('minisite.')
    ->group(function () {
        Route::get('/{slug}', [MinisiteController::class, 'show'])->name('show');
    });
