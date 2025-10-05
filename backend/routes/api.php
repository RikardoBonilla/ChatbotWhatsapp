<?php

use App\Presentation\Http\Controllers\WhatsApp\SendMessageController;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/send', SendMessageController::class);