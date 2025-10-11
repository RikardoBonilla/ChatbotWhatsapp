<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;

final class MessageAnalyticsModel extends Model
{
    protected $table = 'message_analytics';

    protected $fillable = [
        'date',
        'keyword_rule_id',
        'phone_number',
        'incoming_messages',
        'outgoing_messages',
        'successful_matches',
        'failed_matches',
        'avg_response_time_ms',
        'peak_hours',
    ];

    protected $casts = [
        'date' => 'date',
        'incoming_messages' => 'integer',
        'outgoing_messages' => 'integer',
        'successful_matches' => 'integer',
        'failed_matches' => 'integer',
        'avg_response_time_ms' => 'float',
        'peak_hours' => 'array',
    ];
}