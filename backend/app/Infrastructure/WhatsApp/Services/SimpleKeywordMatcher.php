<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Services;

use App\Domain\WhatsApp\Repositories\KeywordRuleRepositoryInterface;
use App\Domain\WhatsApp\Services\KeywordMatcherInterface;
use App\Domain\WhatsApp\Services\FuzzyKeywordMatcherInterface;

final readonly class SimpleKeywordMatcher implements KeywordMatcherInterface
{
    public function __construct(
        private KeywordRuleRepositoryInterface $keywordRepository,
        private FuzzyKeywordMatcherInterface $fuzzyMatcher
    ) {}

    public function findMatches(string $content): array
    {
        $activeRules = $this->keywordRepository->findActive();
        $matches = [];

        foreach ($activeRules as $rule) {
            if ($this->ruleMatches($rule, $content)) {
                $matches[] = $rule;
            }
        }

        return $matches;
    }

    private function ruleMatches($rule, string $content): bool
    {
        if ($rule->matches($content)) {
            return true;
        }

        if ($rule->getFuzzyMatch()) {
            $fuzzyMatches = $this->fuzzyMatcher->findFuzzyMatches($content, $rule->getKeywords());
            return !empty($fuzzyMatches);
        }

        return false;
    }
}