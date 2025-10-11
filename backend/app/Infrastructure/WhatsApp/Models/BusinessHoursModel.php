<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

final class BusinessHoursModel extends Model
{
    use HasUuids;

    protected $table = 'business_hours';

    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
        'timezone',
    ];

    protected $casts = [
        'open_time' => 'datetime',
        'close_time' => 'datetime',
        'is_closed' => 'boolean',
    ];
}