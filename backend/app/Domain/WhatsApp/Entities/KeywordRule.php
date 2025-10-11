<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Entities;

use App\Domain\WhatsApp\ValueObjects\MessageId;
use DateTimeImmutable;

final class KeywordRule
{
    private function __construct(
        private readonly MessageId $id,
        private readonly array $keywords,
        private readonly string $responseTemplate,
        private bool $isActive = true,
        private readonly int $priority = 0,
        private readonly bool $fuzzyMatch = false,
        private readonly string $triggerType = 'contains',
        private readonly ?array $variables = null,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        $this->validateKeywords($keywords);
        $this->validateResponseTemplate($responseTemplate);
        $this->validateTriggerType($triggerType);
    }

    public static function create(
        MessageId $id,
        array $keywords,
        string $responseTemplate,
        int $priority = 0,
        bool $fuzzyMatch = false,
        string $triggerType = 'contains',
        ?array $variables = null
    ): self {
        return new self($id, $keywords, $responseTemplate, true, $priority, $fuzzyMatch, $triggerType, $variables);
    }

    public function getId(): MessageId
    {
        return $this->id;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function getFuzzyMatch(): bool
    {
        return $this->fuzzyMatch;
    }

    public function getTriggerType(): string
    {
        return $this->triggerType;
    }

    public function getVariables(): ?array
    {
        return $this->variables;
    }

    public function getResponseTemplate(): string
    {
        return $this->responseTemplate;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function matches(string $content): bool
    {
        if (!$this->isActive) {
            return false;
        }

        $normalizedContent = strtolower(trim($content));

        foreach ($this->keywords as $keyword) {
            $normalizedKeyword = strtolower(trim($keyword));

            $matches = match ($this->triggerType) {
                'exact' => $normalizedContent === $normalizedKeyword,
                'starts_with' => str_starts_with($normalizedContent, $normalizedKeyword),
                'contains' => str_contains($normalizedContent, $normalizedKeyword),
                default => str_contains($normalizedContent, $normalizedKeyword),
            };

            if ($matches) {
                return true;
            }
        }

        return false;
    }

    private function validateKeywords(array $keywords): void
    {
        if (empty($keywords)) {
            throw new \InvalidArgumentException('Keywords array cannot be empty');
        }

        foreach ($keywords as $keyword) {
            if (empty(trim($keyword))) {
                throw new \InvalidArgumentException('Keyword cannot be empty');
            }

            if (strlen($keyword) > 100) {
                throw new \InvalidArgumentException('Keyword cannot exceed 100 characters');
            }
        }
    }

    private function validateTriggerType(string $triggerType): void
    {
        $validTypes = ['contains', 'exact', 'starts_with'];

        if (!in_array($triggerType, $validTypes)) {
            throw new \InvalidArgumentException('Invalid trigger type. Must be one of: ' . implode(', ', $validTypes));
        }
    }

    private function validateResponseTemplate(string $responseTemplate): void
    {
        if (empty(trim($responseTemplate))) {
            throw new \InvalidArgumentException('Response template cannot be empty');
        }

        if (strlen($responseTemplate) > 1600) {
            throw new \InvalidArgumentException('Response template cannot exceed 1600 characters');
        }
    }
}