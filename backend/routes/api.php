<?php

use App\Presentation\Http\Controllers\WhatsApp\SendMessageController;
use App\Presentation\Http\Controllers\WhatsApp\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/send', SendMessageController::class);
Route::post('/webhook/whatsapp/incoming', WebhookController::class);