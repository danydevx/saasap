<?php

use Illuminate\Support\Facades\Route;
use Modules\AiChatbot\Http\Controllers\Member\AiChatbotController;

Route::get('/', [AiChatbotController::class, 'index'])->name('index');
Route::post('/settings', [AiChatbotController::class, 'saveSettings'])->name('settings');
Route::post('/contexts', [AiChatbotController::class, 'storeContext'])->name('contexts.store');
Route::put('/contexts/{contextId}', [AiChatbotController::class, 'updateContext'])->name('contexts.update');
Route::delete('/contexts/{contextId}', [AiChatbotController::class, 'destroyContext'])->name('contexts.destroy');
Route::post('/reindex', [AiChatbotController::class, 'reindex'])->name('reindex');
Route::post('/extract-url', [AiChatbotController::class, 'extractUrl'])->name('extract-url');
