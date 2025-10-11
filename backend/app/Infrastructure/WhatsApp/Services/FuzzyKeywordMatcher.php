<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Services;

use App\Domain\WhatsApp\Services\FuzzyKeywordMatcherInterface;

final readonly class FuzzyKeywordMatcher implements FuzzyKeywordMatcherInterface
{
    public function findFuzzyMatches(string $input, array $keywords, int $maxDistance = 2): array
    {
        $matches = [];
        $normalizedInput = strtolower(trim($input));

        foreach ($keywords as $keyword) {
            $normalizedKeyword = strtolower(trim($keyword));

            if ($this->areSimilar($normalizedInput, $normalizedKeyword, $maxDistance)) {
                $matches[] = $keyword;
            }
        }

        return $matches;
    }

    public function calculateDistance(string $string1, string $string2): int
    {
        return levenshtein(strtolower($string1), strtolower($string2));
    }

    public function areSimilar(string $string1, string $string2, int $threshold = 2): bool
    {
        if (str_contains($string1, $string2) || str_contains($string2, $string1)) {
            return true;
        }

        return $this->calculateDistance($string1, $string2) <= $threshold;
    }
}