<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

interface FuzzyKeywordMatcherInterface
{
    public function findFuzzyMatches(string $input, array $keywords, int $maxDistance = 2): array;

    public function calculateDistance(string $string1, string $string2): int;

    public function areSimilar(string $string1, string $string2, int $threshold = 2): bool;
}