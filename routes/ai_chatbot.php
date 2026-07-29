<?php

use Modules\AiChatbot\Http\Controllers\Member\AiChatbotController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active', 'role:member'])
    ->prefix('member/businesses/{business}/ai-chatbot')
    ->name('member.business.ai-chatbot.')
    ->group(function () {
        Route::get('/', [AiChatbotController::class, 'index'])->name('index');
        Route::post('/settings', [AiChatbotController::class, 'saveSettings'])->name('settings');
        Route::post('/contexts', [AiChatbotController::class, 'storeContext'])->name('contexts.store');
        Route::put('/contexts/{contextId}', [AiChatbotController::class, 'updateContext'])->name('contexts.update');
        Route::delete('/contexts/{contextId}', [AiChatbotController::class, 'destroyContext'])->name('contexts.destroy');
        Route::post('/reindex', [AiChatbotController::class, 'reindex'])->name('reindex');
    });
