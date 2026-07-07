<?php

use Illuminate\Support\Facades\Route;
use Modules\AiChatbot\Http\Controllers\AiChatbotController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('aichatbots', AiChatbotController::class)->names('aichatbot');
});
