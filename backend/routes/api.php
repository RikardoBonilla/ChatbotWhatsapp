<?php

use App\Presentation\Http\Controllers\WhatsApp\AnalyticsController;
use App\Presentation\Http\Controllers\WhatsApp\SendMessageController;
use App\Presentation\Http\Controllers\WhatsApp\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/send', SendMessageController::class);
Route::post('/webhook/whatsapp/incoming', WebhookController::class);

Route::prefix('analytics')->group(function () {
    Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
    Route::get('/keywords/popular', [AnalyticsController::class, 'popularKeywords']);
    Route::get('/daily', [AnalyticsController::class, 'dailyStats']);
});