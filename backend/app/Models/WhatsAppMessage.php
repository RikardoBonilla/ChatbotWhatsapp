<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * WhatsAppMessage Eloquent Model
 *
 * Database representation of WhatsApp messages.
 * Bridge between domain entities and database storage.
 */
class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'to_phone',
        'content',
        'status',
        'twilio_sid',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}