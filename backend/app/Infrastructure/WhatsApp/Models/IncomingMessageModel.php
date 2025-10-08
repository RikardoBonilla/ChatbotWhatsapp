<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

final class IncomingMessageModel extends Model
{
    use HasUuids;

    protected $table = 'incoming_messages';

    protected $fillable = [
        'from_phone',
        'content',
        'twilio_sid',
        'processed',
        'response_message_id',
    ];

    protected $casts = [
        'processed' => 'boolean',
    ];
}