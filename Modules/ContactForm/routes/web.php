<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactForm\Http\Controllers\ContactFormController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('contactforms', ContactFormController::class)->names('contactform');
});
