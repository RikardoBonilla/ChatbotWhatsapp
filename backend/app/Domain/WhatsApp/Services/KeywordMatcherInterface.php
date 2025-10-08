<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\Entities\KeywordRule;

interface KeywordMatcherInterface
{
    public function findMatches(string $content): array;
}