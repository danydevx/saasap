<?php

use Illuminate\Support\Facades\Route;
use Modules\AiChatbot\Http\Controllers\AiChatbotController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('aichatbots', AiChatbotController::class)->names('aichatbot');
});
