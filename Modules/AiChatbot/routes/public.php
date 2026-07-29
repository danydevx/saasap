<?php

use Illuminate\Support\Facades\Route;
use Modules\AiChatbot\Http\Controllers\Public\ChatController;

Route::middleware(['web'])
    ->prefix('m/{slug}/ai-chatbot')
    ->name('minisite.ai-chatbot.')
    ->group(function () {
        Route::post('/chat', [ChatController::class, 'chat'])->name('chat');
        Route::get('/settings', [ChatController::class, 'getSettings'])->name('settings');
        Route::get('/conversation', [ChatController::class, 'conversation'])->name('conversation');
    });
