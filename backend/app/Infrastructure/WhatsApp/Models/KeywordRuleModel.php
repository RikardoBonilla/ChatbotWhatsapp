<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

final class KeywordRuleModel extends Model
{
    use HasUuids;

    protected $table = 'keyword_rules';

    protected $fillable = [
        'keyword',
        'response_template',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];
}