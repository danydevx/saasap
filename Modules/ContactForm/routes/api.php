<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactForm\Http\Controllers\ContactFormController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('contactforms', ContactFormController::class)->names('contactform');
});
