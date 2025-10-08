<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Services;

use App\Domain\WhatsApp\Repositories\KeywordRuleRepositoryInterface;
use App\Domain\WhatsApp\Services\KeywordMatcherInterface;

final readonly class SimpleKeywordMatcher implements KeywordMatcherInterface
{
    public function __construct(
        private KeywordRuleRepositoryInterface $keywordRepository
    ) {}

    public function findMatches(string $content): array
    {
        $activeRules = $this->keywordRepository->findActive();
        $matches = [];

        foreach ($activeRules as $rule) {
            if ($rule->matches($content)) {
                $matches[] = $rule;
            }
        }

        return $matches;
    }
}