<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

final class ConversationModel extends Model
{
    use HasUuids;

    protected $table = 'conversations';

    protected $fillable = [
        'phone_number',
        'current_state',
        'context',
        'last_message_at',
    ];

    protected $casts = [
        'context' => 'array',
        'last_message_at' => 'datetime',
    ];
}